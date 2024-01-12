<?php
namespace Aoloe\TinyRoute;

class Router {
    private $actions = ['post'=> [], 'get' => []];
    public $error_message = '';

    public function post($action, $function) {
        $this->actions['post'][$action] = $function;
    }
    public function get($action, $function) {
        $this->actions['get'][$action] = $function;
    }

    public function run($request) {
        $actions = null;
        if ($request->is_post()) {
            $actions = $this->actions['post'];
        } else if ($request->is_get()) {
            $actions = $this->actions['get'];
        }

        if (is_null($actions)) {
            $this->error_message = 'invalid method '.HttpRequest::get_method();
            return false;
        }

        foreach ($actions as $key => $value) {
            if (preg_match('/^'.str_replace('/', '\/', $key).'$/', $request->uri, $m)) {
                // echo('<pre>'.print_r($m, 1).'</pre>');
                array_shift($m);
                $value(...$m);
                return true;
            }
        }
        // $this->error_message = 'invalid '.HttpRequest::get_method().' action '.$action;
        return false;
    }
}

class HttpRequest {
    public static function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return new HttpRequestGet();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return new HttpRequestPost();
        } else {
            // TODO: return a HTTPReqeuestInvalid
        }
    }

    public static function is_request($key) {
        return array_key_exists($key, $_REQUEST);
    }

    public static function is_method_get() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public static function is_method_post() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public static function get_method() {
        return $_SERVER['REQUEST_METHOD'];
    }
}

abstract class HttpRequestAbstract {
    public $basedir = '';
    public $uri = '';
    public $url = '';
    protected $data = [];
    public function __construct() {
        $this->basedir = dirname($_SERVER['SCRIPT_NAME']);
        $this->uri = substr($_SERVER['REQUEST_URI'], strlen($this->basedir));
        // echo('<pre>'.print_r($this->basedir, 1).'</pre>');
    }
    public function is_get() {return false;}
    public function is_post() {return false;}
    public function has($key) {
        return array_key_exists($key, $this->data);
    }
    public function get($key) {
        return array_key_exists($key, $this->data) ? $this->data[$key] : null;
    }
    public function get_url($path = null) {
        return  $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$this->basedir.(isset($path) ? '/'.$path : '');
    }
}

class HttpRequestGet extends HttpRequestAbstract {
    public function __construct() {
        parent::__construct();
        $this->data = $_GET;
    }
    public function is_get() {
        return true;
    }
}

class HttpRequestPost extends HttpRequestAbstract {
    public function __construct() {
        parent::__construct();
        // first get _GET, if any (they can be overwritten)
        $this->data = $_GET;
        // post variables sent by axios are json in the body
        if (empty($_POST)) {
            $body = file_get_contents('php://input');

            if (!empty($body)) {
                $data = json_decode($body, true);
                if (!json_last_error()) {
                    $this->data = array_merge($this->data, $data);
                }
            }
        } else {
            $this->data = array_merge($this->data, $_POST);
        }
    }

    public function is_post() {
        return true;
    }
}

class HttpResponse {
    // public $content_type = 'text/html; charset=UTF-8';
    public $content_type = 'text/html; charset=UTF-8';
    public function error_404($response = null) {
        http_response_code(404);
        echo(isset($response) ? $response : '404');
    }
    public function pipe_file($path, $type = null) {
        header('Content-Type: '.(isset($type) ? $type : $this->content_type));
        readfile($path);
    }
    public function respond($response, $type = null) {
        header('Content-Type: '.(isset($type) ? $type : $this->content_type));
        echo($response);
    }
}
