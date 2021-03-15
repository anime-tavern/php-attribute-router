<?php

	require_once __DIR__ . "/../../loader.php";

	class HomeController{

		private ?\AttrRouter\ViewSettings $viewSettings;

		/**
		* @param \AttrRouter\ViewSettings $viewSettings
		*/
		public function __construct(\AttrRouter\ViewSettings $viewSettings){
			$this->viewSettings = $viewSettings;
		}

		/**
		* @return string
		*/
		#[Route("GET", "/")]
		public function homePageView(){
			header("content-type: text/html");

			// Get the view file
			$renderer = new AttrRouter\RenderEngine\Renderer(
				$this->viewSettings->getViewFilePath("home.php"),
				$this->viewSettings
			);
			return $renderer->getRenderedViewFile([]);
		}

	}
