# Tiny Route

One small single file with Router, Request and Response classes for tiny PHP Apps.

TinyRoute provides:

- Registration of Post and Get routes.
- `mod_rewrite` and regex based route matching
- An Http Request class that queries `$_POST` and `$_GET`
- An Http Response class that returns Json encoded replies.

## Usage

### Download the file and include it

You can simply download the [TinyRoute.php](https://raw.githubusercontent.com/aoloe/php-tiny-route/master/src/TinyRoute.php) file, put it somewhere on your disk and include it from your script.

```php
include('TinyRoute.php`);
```

### Get the Github repository and load it through Composer

You can get the repository from Github: <https://github.com/aoloe/php-tiny-rest>...

... and then link it in your projects `composer.json`by the path on your computer:

```json
"repositories": [
    {
        "type": "path",
        "url": "/your/path/to/php-tiny-route"
    }
],
"require": {
    "aoloe/tiny-route": "@dev"
}
```

See the test script below for a basic usage (and TinyRest cannot do much more than that...).

### Let Composer get TinyRoute from Github

You can also tell Composer to get the TinyRoute from Github:

```json
"repositories": [
    {
        "type": "vcs",
        "url":  "git@github.com:aoloe/php-tiny-route.git"
    },
],
"require": {
    "aoloe/tiny-route": "dev-master"
}
```

See the test script below for a basic usage (and TinyRoute cannot do much more than that...).

## A test script

```php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('vendor/autoload.php');

$router = new Aoloe\TinyRoute\Router();

$request = Aoloe\TinyRest\HttpRequest::create();
$response = new Aoloe\TinyRest\HttpResponse();

$router->get('/(\w+)', function($name) use($response) {
    $response->respond('<html><body><p>Hello '.$bame.'</body></html>');
});

if (!$router->run($request)) {
    $respond->error_404();
}
```

If you're using the php internal server, you can [use this mod_rewrite router](https://stackoverflow.com/a/38926070/5239250) and test the script with:

```
http://localhost:8080/Arthur
```
