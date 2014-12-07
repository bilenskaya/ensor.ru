<?php
if(isset($_REQUEST['image'])) $image=$_REQUEST['image'];
if(isset($_REQUEST['mw'])) $mw=$_REQUEST['mw'];
if(isset($_REQUEST['mh'])) $mh=$_REQUEST['mh'];

if (!isset($max_width))
  $max_width = $mw;
if (!isset($max_height))
  $max_height = $mh;

$image_name=basename($image);
$mime_ext=pathinfo($image);
switch($mime_ext["extension"])
  {
  case "jpg": $mime_type="image/jpeg"; break;
  case "JPG": $mime_type="image/jpeg"; break;
  case "jpeg": $mime_type="image/jpeg"; break;
  case "JPEG": $mime_type="image/jpeg"; break;
  case "gif": $mime_type="image/gif"; break;
  case "GIF": $mime_type="image/gif"; break;
  case "png": $mime_type="image/png"; break;
  case "PNG": $mime_type="image/png"; break;
  }

$size = GetImageSize($image);
$width = $size[0];
$height = $size[1];

$x_ratio = $max_width / $width;
$y_ratio = $max_height / $height;

if ( ($width <= $max_width) && ($height <= $max_height) )
  {
  $tn_width = $width;
  $tn_height = $height;
  }
else if (($x_ratio * $height) < $max_height)
  {
  $tn_height = ceil($x_ratio * $height);
  $tn_width = $max_width;
  }
else
  {
  $tn_width = ceil($y_ratio * $width);
  $tn_height = $max_height;
  }

switch($mime_type)
  {
  case "image/jpeg": $src = ImageCreateFromJpeg($image); break;
  case "image/gif": $src = ImageCreateFromGif($image); break;
  case "image/png": $src = ImageCreateFromPng($image); break;
  }

$dst = ImageCreateTrueColor($tn_width,$tn_height);
ImageCopyResampled($dst, $src, 0, 0, 0, 0,
    $tn_width, $tn_height, $width, $height);

switch($mime_type)
  {
  case "image/jpeg": header("Content-type: image/jpeg");  ImageJpeg($dst, null, 100);  break;
  case "image/gif":  header("Content-type: image/gif"); ImageGif($dst, null, 100);  break;
  case "image/png": header("Content-type: image/png");  ImagePng($dst, null, 100);  break;
  }

ImageDestroy($src);
ImageDestroy($dst);

?>
