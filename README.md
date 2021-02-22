# PHP MVC Attribute Router for Apache
Since the introduction of attribute decorators in PHP 8, it is now possible to create a routing system with simple method decorations. Further, you can do this in existing server setups that use Apache. It is also possible to do this in stand-alone PHP quite easily (such as Swoole).

## Routing

For a full code example, check out the `example` folder in the repository; and, more specifically, the HomeController.php in the `example/Controllers` directory.

```php
#Route("GET", "/")
public function homeGet(){
	header("content-type: text/html");

	// Setup the view renderer
	$renderer = new RenderEngine\Renderer(
		$this->viewSettings->getViewFilePath("home.php"),
		$this->viewSettings,
	);

	// Render and return the rendered file content
	return $renderer->getRenderedViewFile();
}
```

You can also easily handle POST requests. The following would echo the POST request back to the sender in JSON format.

```php
#Route("POST", "/")
public function homePostEcho(){
	header("content-type: application/json");

	return json_encode($_POST);
}
```

## Using

Download the repository, and rename the `example` directory to something like `site`. Then, go to `src/request.php` and adjust the directories for your static files, views, and controllers to mimic the new directory name change from `example`.

That's it!

## Scope Variables

When called the `getRenderedFile()` method of a RenderEngine/Renderer instance, you can pass an array as the first argument. To access this array in a view simply get the viewScope variable in the View file.

```php
<?php
	$text = $viewScope['text'];
?>
@Layout = "Layout.php"
@Head{
	<title>Hello!</title>
}
@Body{
	<h1><?= $text ?></h1>
}
```
## Custom Attributes

You can make your own attributes to decorate methods with. The router will call all attributes on a controller's method. Every attribute must be able to be instanced **and must** have a boolean property called `passed` that should be whether or not the attribute passed any desired checks - such as making sure a user is logged in.
