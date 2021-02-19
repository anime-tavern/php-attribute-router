<?php

	require_once __DIR__ . "/ViewSettings.php";
	require_once __DIR__ . "/Router.php";

	class RequestHandler{

		/**
		* Processes and routes a Swoole request
		* @param ViewSettings $viewSettings
		* @param Router $router
		*/
		public static function process(string $requestPath, string $requestType, Router $router, StaticFileHandler $staticFileHandler){
			$clientIP = $_SERVER['REMOTE_ADDR'];
			$requestType = $_SERVER['REQUEST_METHOD'];

			if ($requestType === "GET"){

				// Check for a static file

				if ($staticFileHandler->doesStaticFileExist($requestPath)){
					$mimeType = $staticFileHandler->getStaticFileMime($requestPath);
					if ($mimeType === null){
						$mimeType = "text/plain";
					}

					/**
					* Set the cache-control header if there is a cache config for
					* the given mime type
					*/
					$cacheTime = $staticFileHandler->getCacheTimeForMime($mimeType);
					if ($cacheTime !== null){
						header(sprintf("cache-control: max-age=%d", $cacheTime));
					}

					header("content-type: $mimeType");
					return $staticFileHandler->getStaticFileContents($requestPath);
				}
			}

			return $router->route($requestType, $requestPath);
		}

	}
