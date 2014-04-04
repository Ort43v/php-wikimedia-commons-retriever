<?php
	
	/*
		This file is part of:
		php-wikimedia-commons-retriever - Download and resize Featured Pictures from Wikimedia Commons via RSS or Atom
		
		Copyright (C) 2014 Ort43v
		
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Affero General Public License as
		published by the Free Software Foundation, either version 3 of the
		License, or (at your option) any later version.
		
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Affero General Public License for more details.
		
		You should have received a copy of the GNU Affero General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.

	*/
	
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
	
	