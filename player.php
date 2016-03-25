<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$config = array(
	"source" => "media/",
	"libFile" => "lib.php",
	"playlist" => "playlist.txt",
);

if (!file_exists($config['libFile'])) {
	buildlibrary();
	echo("\033[33mLibrary was not existing, autogenerated one...\033[0m\n");
}

require_once($config['libFile']);

function readTags($f) {
	require_once('getid3/getid3.php');
	$getID3 = new getID3;
	return $getID3->analyze($f);
}

function read_and_delete_first_line($filename) {
  $file = file($filename);
  $output = $file[0];
  unset($file[0]);
  file_put_contents($filename, $file);
  return $output;
}

function getLines($file) {
 $f = fopen($file, 'rb');
 $lines = 0;
 while (!feof($f)) {
 	$lines += substr_count(fread($f, 8192), "\n");
 }
 	fclose($f);
	return $lines;
}

function play($f) {
	if (file_exists($f)) {
		$tags = readTags($f);
		if ($tags['tags']['id3v2']['artist'][0] != NULL && $tags['tags']['id3v2']['title'][0] != NULL) {
			echo ("\033[32mNow Playing: \033[34m".$tags['tags']['id3v2']['artist'][0]." - ".$tags['tags']['id3v2']['title'][0]."\033[0m \n");
		} else {
		
			$info = pathinfo($f);
			echo ("\033[32mNow Playing: \033[34m".basename($f,'.'.$info['extension'])."\033[0m \n");
		}
			exec("cvlc --play-and-exit \"".$f."\" > /dev/null");
		} else {
			echo("\033[31mFile does not exist: \033[34m".$f."\033[0m\n");
	}
}

function getDirContents($dir, &$results = array()){
    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            //$results[] = $path; //Disables Folders in the array
        }
    }
    return $results;
}

function PlayFile($f) {
	if ($f < 1) {
		die("\033[31mError: ID has to be 1 or geater! \033[0m\n");
	}
	global $config, $library;
	play($library[$f-1]);
}

function playRandomFile() {
	global $library;
	$playing = array_rand($library);
	play($library[$playing]);
}

function buildlibrary() {
	global $config;
	file_put_contents($config['libFile'],'<?php'."\n");
	file_put_contents($config['libFile'],'$library = array('."\n", FILE_APPEND);
	$lib = getDirContents($config['source']);
	foreach ($lib as &$value) {
		file_put_contents($config['libFile'],"	\"".$value."\",\n", FILE_APPEND);
		echo ("\033[32mAdded to library: \033[34m".$value."\033[0m\n");
	}
	unset($value);
	file_put_contents($config['libFile'],');', FILE_APPEND);
}

function genDatabase() {
	global $config, $library;
	file_put_contents('tags.txt',"");
	foreach ($library as &$value) {
		$tags = readTags($value);
		if($tags['tags']['id3v2']['artist'][0] != NULL) {
			file_put_contents('tags.txt',$tags['tags']['id3v2']['artist'][0].' - '.$tags['tags']['id3v2']['title'][0]."\n", FILE_APPEND);
			echo("\033[32mUpdated Metadata for \033[34m".$tags['tags']['id3v2']['artist'][0].' - '.$tags['tags']['id3v2']['title'][0]."\033[0m\n");
		} else {
			$info = pathinfo($value);
			file_put_contents('tags.txt',basename($value,'.'.$info['extension'])."\n", FILE_APPEND);
			echo("\033[31mUnable to read ID3-Tags for \033[34m".$value."\033[0m\n");
		}
	}
	unset($value);
}

function autonomous() {
	global $config;
	if(file_exists($config['playlist'])) {
		while (true) {
			$lines = file($config['playlist']);
			if ($lines[0] != NULL) {
				$lines = file($config['playlist']);
				echo("\033[33mPlaying song from playlist, ".(getLines($config['playlist'])-1)." remaining \033[0m\n");
				playFile($lines[0]);
				unset($lines);
				read_and_delete_first_line($config['playlist']);
			} else {
				echo("\033[33mPlaylist is empty, playing random song \033[0m\n");
				playRandomFile();
			}
		}
	} else {
		echo("\033[31mPlaylist file does not exist, pleae create \033[34m".$config['playlist']."\033[0m\n");
	}
}

if (isset($argv[1])) {
	switch($argv[1]) {
		case 'scan':
			buildlibrary();
			break;
		case 'play':
			switch($argv[2]) {
				default:
					playFile($argv[2]);
					break;
				case random:
					playRandomFile();
					break;
				case NULL:
					while(true) {
						playRandomFile();
					}
					break;
			}
			break;
		case 'auto':
			autonomous();
			break;
		case NULL:
			break;
		default:
			break;		
	}

} else {
	echo("\033[33mNo arguments passed, starting automatic mode \033[0m\n");
	autonomous();
}
