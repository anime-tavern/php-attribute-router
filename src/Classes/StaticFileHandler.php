<?php
	require_once __DIR__ . "/MimeTypes.php";

	class StaticFileHandler{
		public string $staticDirectory = "";
		public string $cacheFile = "";
		public array $cacheConfig = [];

		public function setStaticFilesDirectory(string $directoryPath){
			$this->staticDirectory = $directoryPath;
		}

		/**
		* Fetches the full path to a static file
		* @param string $filePath
		* @return string
		*/
		public function getFullStaticFilePath(string $filePath){
			return sprintf("%s/%s", $this->staticDirectory, $filePath);
		}

		/**
		* Whether or not a static file exists at the path
		* @param string $filePath
		* @return bool
		*/
		public function doesStaticFileExist(string $filePath){
			$fullPath = $this->getFullStaticFilePath($filePath);
			return file_exists($fullPath) && !is_dir($fullPath);
		}

		/**
		* Sets the cache file to use for static file MIMEs
		* @param string $cacheFilePath The full file path
		* @return
		*/
		public function setCacheConfig(string $cacheFilePath){
			$this->cacheFile = $cacheFilePath;

			/**
			* Do not use Swoole's coroutine API for this.
			* The cache is expected to be set after this method is called
			*/
			$this->cacheConfig = json_decode(file_get_contents($cacheFilePath), true);
		}

		/**
		* Gets the cache time, in seconds, of a MIME type.
		* Will be null if no cache config exists for the given mime
		* @param string $mime
		* @return int|null
		*/
		public function getCacheTimeForMime(string $mime){
			if (isset($this->cacheConfig[$mime])){
				return (int) $this->cacheConfig[$mime];
			}

			return null;
		}

		/**
		* Gets the mime type of the file based on the extension
		* @param string $filePath
		* @return string|null
		*/
		public function getStaticFileMime(string $filePath){
			$extension = pathinfo($filePath, PATHINFO_EXTENSION);
			if ($extension !== ""){
				if (isset(MimeTypes::RECOGNIZED_EXTENSIONS[$extension])){
					return MimeTypes::RECOGNIZED_EXTENSIONS[$extension];
				}else{
					return null;
				}
			}else{
				return null;
			}
		}

		/**
		* Gets the mime type of the file based on the extension
		* @param string $filePath
		* @param Swoole\Coroutine\Channel $channel The file contents will be pushed onto the coroutines stack
		*/
		public function getStaticFileContents(string $filePath){
			$fullPath = $this->getFullStaticFilePath($filePath);
			return file_get_contents($fullPath);
		}
	}
