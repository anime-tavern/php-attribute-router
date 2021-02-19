<?php
	namespace RenderEngine;

	class Parser{

		private ?string $head;
		private ?string $body;
		private ?string $fileLocation;
		private ?string $fileContents;
		public array $directives = [];

		public function __construct(string $fileLocation){
			$this->fileLocation = $fileLocation;
			// Include the file to parse its inner PHP code
			$viewResult = "";
			ob_start();
			include($fileLocation);
			$viewResult = ob_get_contents();
			ob_end_clean();
			$this->fileContents = $viewResult;
		}

		/**
		* Lexically parses the file for @ directive tokens
		*/
		public function parse(){
			$contents = $this->fileContents;
			$index = 0;
			$char = null;

			$directives = [];
			$parseState = "";
			$prevParserState = "";
			$prevDirectiveName = "";
			$tokenDelimiter = "";
			$buffer = "";

			while ( isset($contents[$index]) ){
				$char = $contents[$index];
				switch ($parseState){
					case "":
						if ($char === "@"){
							$parseState = "PARSE_DIRECTIVE_NAME";
							$buffer .= $char;
						}elseif ($char === "=" && $prevParserState === "PARSE_DIRECTIVE_NAME"){
							$prevParserState = "";
							$parseState = "PARSE_DIRECTIVE_SHORT_VALUE";
						}elseif ($char === "{" && $prevParserState === "PARSE_DIRECTIVE_NAME"){
							$prevParserState = "";
							$parseState = "PARSE_DIRECTIVE_LONG_VALUE";
						}
						break;
					case "PARSE_DIRECTIVE_LONG_VALUE":
						if ($char === $tokenDelimiter){
							$prevParserState = "PARSE_DIRECTIVE_LONG_VALUE";
							$parseState = "";
							$tokenDelimiter = "";
							$directives[$prevDirectiveName] = $buffer;
							$buffer = "";
						}else{
							$buffer .= $char;
						}
						break;
					case "PARSE_DIRECTIVE_SHORT_VALUE":
						if ($char === "\""){
							$prevParserState = "PARSE_DIRECTIVE_SHORT_VALUE";
							$parseState = "PARSE_DIRECTIVE_SHORT_VALUE_TOKEN";
							$tokenDelimiter = $char;
							$buffer .= $char;
						}
						break;
						case "PARSE_DIRECTIVE_SHORT_VALUE_TOKEN":
							if ($char === $tokenDelimiter){
								$prevParserState = "PARSE_DIRECTIVE_SHORT_VALUE_TOKEN";
								$parseState = "";
								$buffer .= $char;

								// Clear the delimiter from the start and end of the buffer
								$buffer = trim($buffer, $tokenDelimiter);

								$directives[$prevDirectiveName] = $buffer;
								$buffer = "";
								$tokenDelimiter = "";
							}elseif ($char === "\n"){
								throw new Exception("Parse error. Unexpected EOL when parsing directive string value.");
							}else{
								$buffer .= $char;
							}
							break;
					case "PARSE_DIRECTIVE_NAME";
						if ($char === " "){
							$prevParserState = "PARSE_DIRECTIVE_NAME";
							$parseState = "";
							$directives[$buffer] = "";
							$prevDirectiveName = $buffer;
							$buffer = "";
						}elseif ($char === "{"){
							$prevParserState = "PARSE_DIRECTIVE_NAME";
							$parseState = "PARSE_DIRECTIVE_LONG_VALUE";
							$directives[$buffer] = "";
							$prevDirectiveName = $buffer;
							$tokenDelimiter = "}"; // What to expect
							$buffer = "";
						}elseif ($char === "="){
							print("Dumping");
							$prevParserState = "PARSE_DIRECTIVE_NAME";
							$parseState = "PARSE_DIRECTIVE_SHORT_VALUE";
							$directives[$buffer] = "";
							$prevDirectiveName = $buffer;
							$buffer = "";
						}else{
							$buffer .= $char;
						}
					default:
						break;
				}

				++$index;
			}

			$this->directives = $directives;
		}
	}
