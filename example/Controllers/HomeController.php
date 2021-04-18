<?php

	require_once __DIR__ . "/../../loader.php";

	class HomeController{

		private ?\AttrRouter\ViewSettings $viewSettings;

		public function __construct(\AttrRouter\ViewSettings $viewSettings){
			$this->viewSettings = $viewSettings;
		}

		#[Route("GET", "/")]
		public function homePageView(): string{
			header("content-type: text/html");

			// Get the view file
			$renderer = new AttrRouter\RenderEngine\Renderer(
				$this->viewSettings->getViewFilePath("home.php"),
				$this->viewSettings
			);
			return $renderer->getRenderedViewFile([]);
		}

	}
