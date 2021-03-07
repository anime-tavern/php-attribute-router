<?php
	require_once __DIR__ . "/../loader.php";

	$requestPath = $_GET['requestPath'];
	if (empty($requestPath)){
		$requestPath = "/";
	}else{
		// Force it to begin with a forward slash
		$requestPath = sprintf("/%s", $requestPath);
	}

	/**
	* Set up the directory for static file serving
	*/
	$staticFileHandler = new AttrRouter\StaticFileHandler;
	$staticFileHandler->setStaticFilesDirectory(__DIR__ . "/../../Static");
	$staticFileHandler->setCacheConfig(__DIR__ . "/cache.json");

	/**
	* Set the views folder where Controllers
	* will search for view files
	*/
	$viewSettings = new AttrRouter\ViewSettings;
	$viewSettings->setLayoutsFolder(__DIR__ . "/../../Layouts");
	$viewSettings->setViewsFolder(__DIR__ . "/../../Views");

	/**
	* Initialize the router and set
	* the folder for Controller classes
	*/
	$router = new AttrRouter\Router;
	$router->setControllersFolder(__DIR__ . "/../../Controllers");
	$router->loadMVCControllers($viewSettings);
	$viewResult = AttrRouter\RequestHandler::process($requestPath, $_SERVER['REQUEST_METHOD'], $router, $staticFileHandler);

	// Is the view result a Redirect?
	if ($viewResult instanceof \AttrRouter\HttpHelpers\Redirect){
		http_response_code($viewResult->statusCode);
		header(sprintf("location: %s", $viewResult->path));
		exit();
	}elseif ($viewResult !== null){
		print($viewResult);
	}else{
		// TODO Check for registered 404 page
		http_response_code(404);
		exit();
	}
