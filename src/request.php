<?php
	require_once __DIR__ . "/Classes/ViewSettings.php";
	require_once __DIR__ . "/Classes/RequestHandler.php";
	require_once __DIR__ . "/Classes/StaticFileHandler.php";
	require_once __DIR__ . "/Classes/Router.php";

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
	$staticFileHandler = new StaticFileHandler;
	$staticFileHandler->setStaticFilesDirectory(__DIR__ . "/../example/Static");
	$staticFileHandler->setCacheConfig(__DIR__ . "/cache.json");

	/**
	* Set the views folder where Controllers
	* will search for view files
	*/
	$viewSettings = new ViewSettings;
	$viewSettings->setLayoutsFolder(__DIR__ . "/../example/Layouts");
	$viewSettings->setViewsFolder(__DIR__ . "/../example/Views");

	/**
	* Initialize the router and set
	* the folder for Controller classes
	*/
	$router = new Router;
	$router->setControllersFolder(__DIR__ . "/../example/Controllers");
	$router->loadMVCControllers($viewSettings);
	$viewResult = RequestHandler::process($requestPath, $_SERVER['REQUEST_METHOD'], $router, $staticFileHandler);

	if ($viewResult !== null){
		print($viewResult);
	}else{
		http_response_code(404);
		print("");
	}
