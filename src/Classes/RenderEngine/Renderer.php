<?php
	namespace RenderEngine;

	require_once __DIR__ . "/Exceptions/LayoutDoesNotExist.php";
	require_once __DIR__ . "/Parser.php";
	require_once __DIR__ . "/../ViewSettings.php";

	class Renderer{

		public ?string $fileLocation;
		public ?\ViewSettings $viewSettings;

		public function __construct(string $fileLocation, \ViewSettings $viewSettings){
			$this->fileLocation = $fileLocation;
			$this->viewSettings = $viewSettings;
		}

		public function getRenderedViewFile(){
			$fileLocation = $this->fileLocation;
			$parser = new Parser($fileLocation);
			$parser->parse();

			$layoutFileName = $parser->directives['@Layout'];
			$layoutFilePath = sprintf("%s/%s", $this->viewSettings->layoutsFolder, $layoutFileName);

			if (!realpath($layoutFilePath)){
				throw new \LayoutDoesNotExist(sprintf("The layout %s does not exist in the folder %s", $layoutFileName, $this->viewSettings->layoutsFolder));
			}

			$viewResult = "";
			$htmlBody = $parser->directives['@Body'];
			$htmlHead = $parser->directives['@Head'];
			ob_start();
			include($layoutFilePath);
			$viewResult = ob_get_contents();
			ob_end_clean();

			// Push the parsed view
			return $viewResult;
		}
	}
