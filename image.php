<?php
//no va
header('Content-type: image/png');

$images= new Imagick(glob('../images/*.png'));

foreach($images as $image){
	$image-> thumbnailImage(768,0);
}


$images-> writeImages('../images/out.png', false);
echo $image;

