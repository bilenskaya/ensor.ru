<?php
include("fckeditor.php") ;
?>

<?php
// Automatically calculates the editor base path based on the _samples directory.
// This is usefull only for these samples. A real application should use something like this:
//$oFCKeditor->BasePath = '/admin/vis/' ;	// '/FCKeditor/' is the default value.
$sBasePath = '/admin/vis/' ;
//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;

$oFCKeditor = new FCKeditor('FCKeditor1', @$imgpath, @$imgpath_www) ;
$oFCKeditor->BasePath	= $sBasePath ;
$oFCKeditor->Value		= @$content;
$oFCKeditor->Create() ;

?>



