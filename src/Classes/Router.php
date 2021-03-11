<?php
	namespace AttrRouter;

	require_once __DIR__ . "/../Attributes/Route.php";

	class Router{

		private $controllersFolder = "";
		private $controllers = [];

		/** @property ReflectionMethod[] $routableMethods */
		public $routableMethods = [];

		/**
		* Sets the controllers folder
		*/
		public function setControllersFolder(string $path){
			$this->controllersFolder = $path;
		}

		/**
		* Loads the MVC controller classes
		* from the controllers folder
		*/
		public function loadMVCControllers(ViewSettings $viewSettings, string $innerDirectory = ""){
			if ($innerDirectory === ""){
				$fileNames = array_diff(scandir($this->controllersFolder), ['.','..']);
			}else{
				$fileNames = array_diff(scandir(sprintf("%s/%s", $this->controllersFolder, $innerDirectory)), ['.','..']);
			}

			foreach ($fileNames as $controllerFileName){
				if ($innerDirectory === ""){
					$controllerPath = sprintf("%s/%s", $this->controllersFolder, $controllerFileName);
				}else{
					$controllerPath = sprintf("%s/%s/%s", $this->controllersFolder, $innerDirectory, $controllerFileName);
				}

				if (is_dir($controllerPath)){
					$this->loadMVCControllers($viewSettings, sprintf("%s/%s", $innerDirectory, $controllerFileName));
				}else{
					// The class name _must_ be the file name minus the extension
					$className = pathinfo($controllerFileName, PATHINFO_FILENAME);
					require($controllerPath);
					$classReflector = new \ReflectionClass($className);
					$controllerMethods = $classReflector->getMethods(\ReflectionMethod::IS_PUBLIC);
					$this->routableMethods[] = [new $className($viewSettings), $controllerMethods];
				}
			}
		}

		/**
		* @return string|null
		*/
		public function route(string $requestMethod, string $uri){

			// Go through all the methods collected from the controller classes
			foreach ($this->routableMethods as $methodData){
				$classInstance = $methodData[0];
				$methods = $methodData[1];

				// The router will first find all methods
				// that have a matching route.
				// Then, later, it will verify any additional attributes
				// also pass. Otherwise, no route is returned/invoked
				$routeMethodsToAttempt = [];

				// Loop through the methods
				foreach($methods as $method){

					// Get the attributes (if any) of the method
					$attributes = $method->getAttributes();

					/**
					* To be defined eventually...
					*/
					$routeClass = null;
					$routeMethod = null;
					$attemptRouting = false;

					// Loop through attributes and only check the route here
					foreach ($attributes as $attribute){
						$attrName = $attribute->getName();

						// Check if this attribute name is "Route"
						if ($attrName === "Route"){
							$routeAttribute = $attribute->newInstance();

							// Check if the first argument (request method arg)
							// matches the server request method
							if (strtolower($routeAttribute->method) === strtolower($requestMethod)){

								// Is the route a regular expression?
								if ($routeAttribute->isRegex === false){
									// No, it is a plain string match
									if ($routeAttribute->uri === $uri){
										$routeMethodsToAttempt[] = $method;
									}
								}else{
									// Yes, it needs to be matched against the URI
									$didMatch = preg_match_all($routeAttribute->uri, $uri, $matches);
									if ($didMatch === 1){
										// Add the matches to the requests GET array
										foreach ($matches as $name=>$match){
											if (is_string($name)){
												if (isset($match[0])){
													$_GET[$name] = $match[0];
												}
											}
										}

										$routeMethodsToAttempt[] = $method;
									}
								}
							}
						}
					}

					// Loop through the methods that routes matched
					// and run their additional attributes, if any.
					// The first one to pass all should be invoked as the correct
					// route.
					$acceptedRoutes = [];
					foreach ($routeMethodsToAttempt as $method){
						$attributes = $method->getAttributes();

						// Check everything except the route
						$passedAttributes = 0;
						$neededToRoute = count($attributes) - 1;

						foreach ($attributes as $attribute){
							$attrName = $attribute->getName();

							if ($attrName !== "Route"){
								$attrInstance = $attribute->newInstance();
								if ($attrInstance->passed){
									++$passedAttributes;
								}else{
									// This attribute failed. This method is not routable
									// Move on to the next, break this inner for loop
									break 1;
								}
							}
						}

						if ($passedAttributes === $neededToRoute){
							return $method->invoke($classInstance);
						}
					}
				}

			}
			return null;
		}
	}
