<?php
	class FileBuffer{

		private $filePath = null;
		private $result = "";

		public function __construct(string $filePath){
			$this->filePath = $filePath;
		}

		/**
		* Buffers a file into the $result property
		*/
		public function buffer(){
			ob_start();
			include($this->filePath);
			$this->result = ob_get_contents();
			ob_end_clean();
		}

		/**
		* Gets the result of the buffer
		* @return string
		*/
		public function getResult(){
			return $this->result;
		}

	}
