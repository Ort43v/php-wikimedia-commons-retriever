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
	
	class CommonsRetriever extends cURLWrapper {
		const ERROR_FILE = '.commons.log'; // starting with '.' so that it won't be deleted by glob('*') match
		
		protected $stderr;
		protected $error = '';
		protected $saveDir = false;
		
		protected $targetWidth = 2560;
		protected $targetHeight = 1440;
		
		protected $images = array();
		
		public function __construct($saveDir, $stderr = true) {
			$this->setSaveDir($saveDir);
			$this->stderr = $stderr;
			$this->curlInit();
			$date = gmdate('r');
			$this->error("\n$date " . __CLASS__ . " starting configuration...");
			
			$this->setCurlOption(CURLOPT_RETURNTRANSFER, true);
			$this->setCurlOption(CURLOPT_AUTOREFERER, true);
			$this->setCurlOption(CURLOPT_FOLLOWLOCATION, true);
			$this->setCurlOption(CURLOPT_MAXREDIRS, 10);
		}
		
		public function setTargetResolution($width, $height) {
			if ($width && is_numeric($width)) $this->targetWidth = max(1, (int) $width);
			if ($height && is_numeric($height)) $this->targetHeight = max(1, (int) $height);
			$this->error("Target resolution is set to {$this->targetWidth}×{$this->targetHeight}px");
			return true;
		}
		
		protected function error($str) {
			$str = "$str\n";
			$this->error .= $str;
			$path = $this->getPath(self::ERROR_FILE);
			file_put_contents($path, $str, FILE_APPEND | LOCK_EX);
			if (!$this->stderr) return false;
			return fwrite(STDERR, $str);
		}
		
		protected function request($uri) {
			/*
				This is intended to be a daemon;
				We wait patiently.
			*/
			
			if (!$uri) {
				$uri = print_r($uri, true);
				throw new RuntimeException("Malformed uri: $uri");
			}
			
			if (substr($uri, 0, 2) == '//') $uri = "https:{$uri}"; // most protocol-relative server supports HTTPS
			
			$this->error("Starting request: '$uri'");
			
			$this->setCurlOption(CURLOPT_URL, $uri);
			
			while (true) {
				$str = $this->curlExec();
				if (!$this->curlErrno()) break;
				
				$this->error("cURL error happend:");
				$this->error($this->curlError());
				$this->error("delaying...");
				sleep(1);
				$this->error("trying again...");
			}
			
			$this->error("Done");
			return $str;
		}
		
		// from Atom 1.0 or RSS 2.0
		// finds images from Wikimedia Commons and returns an array of URIs
		protected function findImages($uri) {
			
			$str = $this->request($uri);
			
			if (!$str) return false;
			
			$doc = new DOMDocument();
			$doc->loadXML($str);
			
			$text = array();
			
			$atom = $doc->getElementsByTagName('summary');
			if ($atom->length > 0) {
				foreach ($atom as $summary) {
					$text[] = $summary->nodeValue;
				}
			} else {
				$rss = $doc->getElementsByTagName('description');
				foreach ($rss as $description) {
					$text[] = $description->nodeValue;
				}
			}
			
			$images = array();
			foreach ($text as $html) {
				$doc = new DOMDocument();
				$doc->loadHTML($html);
				$imageElements = $doc->getElementsByTagName('img');
				foreach ($imageElements as $imageElement) {
					$src = $imageElement->getAttribute('src');
					if ($src) $images[] = $src;
				}
			}
			
			return $images;
		}
		
		public function loadImages($uri) {
			$images = $this->findImages($uri);
			
			if (is_array($images)) {
				$this->images = array_merge($this->images, $images);
				$count = count($this->images);
				$this->error("findImages(): successful, currently $count image(s) listed");
				
				return true;
			} else {
				$this->error("findImages(): failed!");
				
				return false;
			}
		}
		
		public function setSaveDir($dir) {
			if (!file_exists($dir)) {
				if (!mkdir($dir)) {
					return false;
				}
			} else if (!is_dir($dir) || !is_writable($dir)) return false;
			
			$this->saveDir = $dir;
			
			$this->error("saveDir set to '$dir'");
			
			return true;
		}
		
		// convert thumbnail URI to full size URI
		public function toFullImageURI($uri) {
			if (strpos("$uri", '/thumb/') === false) return $uri; // not supported URI
			$uri = explode('/', "$uri");
			array_pop($uri);
			$uri = implode('/', $uri);
			$uri = str_replace('/thumb/', '/', $uri);
			return $uri;
		}
		
		// convert full size URI to thumbnail URI
		public function toThumbURI($uri, $width) {
			$width = $this->targetWidth;
			
			if (!is_numeric($width)) {
				throw new RuntimeException('Please specify the thumbnail size you want');
			}
			
			$width = (int) $width;
			$width = max(1, $width);
			
			if (strpos("$uri", '/thumb/') !== false) return $uri;
			$uri = str_replace('/commons/', '/commons/thumb/', $uri);
			$uri = explode('/', "$uri");
			$filename = array_pop($uri);
			$uri[] = $filename;
			if (substr($filename, -4) == '.svg') $filename .= '.png';
			$uri[] = "{$width}px-{$filename}";
			return implode('/', $uri);
		}
		
		protected function getPath($filename) {
			if ($this->saveDir === false) {
				throw new RuntimeException('Please set the destination using setSaveDir()');
			}
			
			return $this->saveDir . '/' . $filename;
		}
		
		protected function fileExists($filename) {
			try {
				$path = $this->getPath($filename);
				if (file_exists($path)) throw new RuntimeException("File already exists");
				
				$path = $this->getPath("$filename.png");
				if (file_exists($path)) throw new RuntimeException("File already exists");
				
			} catch (Exception $e) {
				$message = $e->getMessage();
				$size = filesize($path);
				$basename = basename($path);
				$this->error("File '$basename' already exists ({$size}B)");
				
				return $path;
			}
			
			return false;
		}
		
		/**
			@param string $uri thumbnail URI
		*/
		protected function getImage($uri) {
			$extension = explode('.', $uri);
			$extension = strtolower(array_pop($extension));
			$isJPEG = ($extension == 'jpg' || $extension == 'jpeg');
			$isSVG = substr($uri, -8) == '.svg.png';
			
			$uri = $this->toFullImageURI($uri);
			
			// the file name to save the image as
			$filename = explode('/', $uri);
			$filename = urldecode(end($filename));
			
			if ($isSVG) {
				$uri = $this->toThumbURI($uri, $this->targetWidth); // We don't support SVG so we rely on Wikimedia's thumbnailing
				$filename .= '.png'; // thumbnail is a PNG file
			}
			
			// Skip existing image
			if ($this->fileExists($filename) !== false) return false;
			
			ini_set('memory_limit', -1);
			
			// Request the actual image here
			$str = $this->request($uri);
			$size = strlen($str);
			
			// Start processing of the image 
			try {
				if (!function_exists('imagecreatefromstring')) throw new RuntimeException('imagecreatefromstring() is not supported');
				
				set_error_handler(function($errno, $errstr, $errfile, $errline ) {});
				
				$image = imagecreatefromstring($str);
				
				restore_error_handler();
				
				if (!$image) throw new RuntimeException('Unsupported file (passed)');
				
				if (!$isJPEG && $extension != 'png') {
					$filename .= '.png'; // since we save all images except JPEG as PNG
				}
			} catch (Exception $e) {
				$this->error($e->getMessage());
				
				file_put_contents($this->getPath($filename), $str);
				$this->error("Saved as: '$filename' ({$size}B)");
				
				return true;
			}
			
			$x = imagesx($image);
			$y = imagesy($image);
			
			$this->error("Image: '$filename': {$x}×{$y}px ({$size}B)");
			
			if ($x <= $this->targetWidth || $y <= $this->targetHeight) {
				// do nothing
			} else {
				$xRatio = $this->targetWidth / $x;
				$yRatio = $this->targetHeight / $y;
				$ratio = max($xRatio, $yRatio);
				
				$width = max(1, round($x * $ratio));
				$height = max(1, round($y * $ratio));
				
				$this->error("Resized: '$filename': {$width}×{$height}px");
				
				//$filename = "{$x}×{$y}px-{$filename}"; // indicate original size
				
				$resized = imagecreatetruecolor($width, $height);
				imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $x, $y);
				imagedestroy($image);
				$image = $resized;
				unset($resized);
			}
			
			$path = $this->getPath($filename);
			if ($isJPEG) {
				imagejpeg($image, $path);
			} else {
				imagepng($image, $path, 9);
			}
			
			imagedestroy($image);
			$size = filesize($path);
			$this->error("Saved as: '$filename' ({$size}B)");
			
			return true;
		}
		
		public function exec() {
			$this->error("Requesting images...");
			$start = microtime(true);
			
			foreach ($this->images as $uri) {
				$this->getImage($uri);
			}
			
			$date = gmdate('r');
			$time = microtime(true) - $start;
			$this->error("$date All request(s) finished succesfully in {$time}s");
		}
		
		public function deleteOldFiles($maxNumberOfFile) {
			if (!is_numeric($maxNumberOfFile)) return false;
			
			$max = max(1, round($maxNumberOfFile));
			unset($maxNumberOfFile);
			
			$files = glob("{$this->saveDir}/*");
			$deleted = 0;
			
			if (is_array($files)) {
				$fileList = array();
				
				foreach ($files as $file) {
					$file = realpath($file);
					
					if (!file_exists($file) || is_dir($file) || !is_writable($file)) continue;
					
					$lastModified = filemtime($file);
					$fileList[$lastModified] = $file;
				}
				
				ksort($fileList);
				
				for ($count = 0; $count < $max; $count++) {
					array_pop($fileList); // save newer files
				}
				
				foreach ($fileList as $oldFile) {
					$this->error("Deleting: '$oldFile'\n");
					unlink($oldFile);
					$deleted++;
				}
			}
			
			return $deleted;
		}
		
		public function __destruct() {
			$this->curlClose();
		}
		
		public function __toString() {
			return $this->error;
		}
	}
	