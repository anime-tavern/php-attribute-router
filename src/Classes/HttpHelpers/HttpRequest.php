<?php

	namespace AttrRouter\HttpHelpers;

	/**
	* An abstraction for the request payload
	*/
	class HttpRequest{

		/**
		* Fetches a value from the POST payload. Safe-checks with isset
		* @param string $name
		* @param mixed $default Will return this if the POST[$name] is not set
		* @return mixed
		*/
		public function getPostValue(string $name, mixed $default){
			if (isset($_POST[$name])){
				return $_POST[$name];
			}else{
				return $default;
			}
		}

		/**
		* Fetches a value from the GET query parameters. Safe-checks with isset
		* @param string $name
		* @param mixed $default Will return this if the GET[$name] is not set
		* @return mixed
		*/
		public function getGetValue(string $name, mixed $default){
			if (isset($_GET[$name])){
				return $_GET[$name];
			}else{
				return $default;
			}
		}

		/**
		* Fetches a value from the cookie string in the request. Safe-checks with isset
		* @param string $name
		* @param mixed $default Will return this if the GET[$name] is not set
		* @return mixed
		*/
		public function getCookieValue(string $name, mixed $default){
			if (isset($_COOKIE[$name])){
				return $_COOKIE[$name];
			}else{
				return $default;
			}
		}

		/**
		* Old method of fetching files from the FILE payload.
		* @deprecated
		*/
		public function getFileValue(string $value){

			if (empty($_FILES)){
				return false;
			}

			if (!isset($_FILES[$value])){
				return false;
			}

			// tmp_name can be an array for multiple files
			if (is_array($_FILES[$value]['tmp_name'])){
				foreach($_FILES[$value]['tmp_name'] as $tmp_name){
					if (!is_uploaded_file($tmp_name)){
						return false;
					}
				}
			}else{
				if (!is_uploaded_file($_FILES[$value]['tmp_name'])){
					return false;
				}
			}

			// Error can also be an array
			if (is_array($_FILES[$value]['error'])){
				foreach($_FILES[$value]['error'] as $error){
					if ($error != 0){
						return false;
					}
				}
			}else{
				if ($_FILES[$value]['error'] != 0){
					return false;
				}
			}

			return $_FILES[$value];
		}

		/**
		* Attempts to fetch the IP of the originating request.
		* @return string Can be blank for no IP
		*/
		public function getIP(){
			if (!empty($_SERVER['HTTP_CLIENT_IP'])){
				// IP is from shared internet
				return $_SERVER['HTTP_CLIENT_IP'];
			}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				// IP is from a proxy
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}else{
				// IP is from a remote address
				return $_SERVER['REMOTE_ADDR'];
			}

			return "";
		}

	}
