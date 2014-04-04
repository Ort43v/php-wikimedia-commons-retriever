<?php
	
	spl_autoload_register(function($class){
		$file = __DIR__ . "/classes/$class.php";
		if (file_exists($file) && is_readable($file) && !is_dir($file)) require_once $file;
		if (class_exists($class, false)) return true;
		else return false;
	});
	
	$dir = __DIR__ . '/images'; // images are saved here
	
	$commons = new CommonsRetriever($dir);
	
	// source of image list
	$commons->loadImages('https://tools.wmflabs.org/catfood/catfood.php?category=Featured+pictures+on+Wikimedia+Commons');
	$commons->loadImages('https://commons.wikimedia.org/w/api.php?action=featuredfeed&feed=potd&feedformat=atom&language=en');
	
	$commons->setTargetResolution(2880, 1800);
	
	$commons->exec();
	
	$commons->deleteOldFiles(30);
	
	