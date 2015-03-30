#!/bin/bash
# Author: Cifro Nix, http://about.me/Cifro
#
# Script for generating thumbnails, called from PHP
#
# usage in PHP:
# $screenshot = trim(shell_exec(SERVER_CONTENT_PATH. "getRandomThumbnail.sh " . WOWZA_CONTENT_PATH ." & $video_name & $thumbnail_name & " . THUMBNAILS_PATH));
videoDir=$1
thumbnail_name=$3
tnDir=$4
preview="100x100"
# filename: <videoDir>/<id>_<yyyy-mm-dd>_<video-title>.<flv|mp4|f4v>
#vid=`echo "$videoDir/$2" 2>&1 | cut -d '_' -f 1 | cut -d '/' -f 6`;
size=`ffmpeg -i "$videoDir/$2" 2>&1 | grep 'Video: ' | cut -d ',' -f 3 | cut -d '[' -f 1  | sed 's/^ *\(.*\) *$/\1/'`;

fulltime=`ffmpeg -i "$videoDir/$2" 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//`;
hour=`echo $fulltime | cut -d ':' -f 1`;
minute=`echo $fulltime | cut -d ':' -f 2`;
second=`echo $fulltime | cut -d ':' -f 3 | cut -d '.' -f 1`;

seconds=`expr 3600 \* $hour + 60 \* $minute + $second`;
#ss=`expr $seconds / 2`; # from the middle of video
random=$RANDOM
let "random %= $seconds"
ss=$random
#echo "$vid: $ss / $seconds";

#custom time from command line
#	if [ "$#" -eq 2 ]; then
#	    ss=$2;
#	fi

# create thumbnail from middle of video
# thumbnails will be allways overwrited
#output=`ffmpeg  -ss $ss -i "$videoDir/$2" -f image2 -vframes 1 -s $preview "$tnDir/$thumbnail_name" 2>&1`;
output=`ffmpeg  -ss $ss -i "$videoDir/$2" -f image2 -vframes 1 -vf crop=ih:ih -s $preview "$tnDir/$thumbnail_name" 2>&1`;