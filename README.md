# PHP-Musicplayer

This is a simple script which can
* Build a Media library
* Play random files from it
* Play a playlist with ID's from the Library

## Intention
My idea is to make another script for this which allows you to have a LA(M)P-Server running which manages music requests. These could be added to a playlist.txt file on the server which gets downloaded by this client for a sort-of muiscbox for use i.e at bars.

## Requirements
* PHP (>= 5.3.0) with PHP-Cli installed
* Linux system (Windows and maybe MacOS X coming)
* installation of VLC Media Player

## Usage

### Update your library
Firstly you have to update your library, therefor simply execute

```bash
./scan.sh
```
or use
```bash
php player.php scan
```

Note: If the file **lib.php** does not exist, it will be generated automatically.

### Starting the player in automatic mode
Now, to start the player in auto mode simply execute 
```bash
autostart.sh
```
or use
```bash
php player.php (auto)
```
Note: If you don't include anything it will always launch auto mode.

### Playing a specific song
To play a specific file simply start
```bash
php player.php play 123
```
where you replaye 123 with the ID of the desired song.

### Plaing one random song
To play **one** random song type
```bash
php player.php play random
```

### Entering random mode
To play **multiple** random song enter
```bash
php player.php random
```
## Configuration
In the file player.php is a array called $config. There you cann change specific file paths, for example when your playlist is on a webserver.

## Bugs
If you find any bugs, please write an issue report. Thanks!
