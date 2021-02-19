<?php

	require_once __DIR__ . "/../../src/Classes/ViewSettings.php";
	require_once __DIR__ . "/../../src/Classes/RenderEngine/Renderer.php";

	class HomeController{

		private ?ViewSettings $viewSettings;

		/**
		* @param ViewSettings $viewSettings
		*/
		public function __construct(ViewSettings $viewSettings){
			$this->viewSettings = $viewSettings;
		}

		/**
		* @return string
		*/
		#[Route("GET", "/")]
		public function homePage(){
			header("content-type: text/html");

			// Get the view file
			$renderer = new RenderEngine\Renderer($this->viewSettings->getViewFilePath("home.php"), $this->viewSettings);
			return $renderer->getRenderedViewFile();
		}

	}
