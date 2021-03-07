<?php

	namespace AttrRouter;

	class ViewSettings{
		public $viewsFolder = "";
		public $layoutsFolder = "";

		public function setViewsFolder(string $directoryPath){
			$this->viewsFolder = $directoryPath;
		}

		public function setLayoutsFolder(string $directoryPath){
			$this->layoutsFolder = $directoryPath;
		}

		public function getViewFilePath(string $fileName){
			return sprintf("%s/%s", $this->viewsFolder, $fileName);
		}
	}
