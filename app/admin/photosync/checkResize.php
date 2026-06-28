<?php

// if large enough, then resize
if(($orient=='wide' && $width>1000)
||($orient=='tall' && $height>600)){

	//include resize script
	include('resize1000.php');

}else{
	
	//include update only 
	include('updatePhoto.php');}