<?php

namespace Acceptic;

use \Twig_Loader_Filesystem as Twig_Loader_Filesystem;
use \Twig_Environment as Twig_Environment;
use \Exception as Exception;

class Core {

	public $config;

	public $twig;

	function __construct() {
		$config = __DIR__ . "/Configs/config.php";
		if (file_exists($config)) {
			$this->config = include($config);
		}
		else {
			exit('Файл конфигурации не найден');
		}
	}

	public function handleRequest($uri) {

		$request = explode('/', $uri);

		$className = '\Acceptic\Controllers\\' . ucfirst(array_shift($request));

		if (!class_exists($className)) {
			$controller = new Controllers\Home($this);
		}
		else {
			$controller = new $className($this);
		}

		$action = array_shift($request);
		
		if(strlen($action) === 0){
			$action = 'index';
		}
		if(!method_exists($controller, $action)){
	        header('HTTP/1.1 404 Not Found');
			header("Status: 404 Not Found");
			header('Location:/');
		}

		$response = $controller->$action();
		
		
	}

	public function getTwig() {
		if (!$this->twig) {
			
				if (!file_exists(TEMPLATES_CACHE)) {
					mkdir(TEMPLATES_CACHE);
				}
				$loader = new Twig_Loader_Filesystem(VIEWS_PATH);
				$this->twig = new Twig_Environment($loader, array(
					'auto_reload' => TWIG_AUTO_RELOAD,
					'templatesCache' => TEMPLATES_CACHE,
				));
			
		}

		return $this->twig;
	}

	public function loadModel($modelName){
		$modelName = '\Acceptic\Models\\' . ucfirst($modelName);
		$model = new $modelName($this->config['database']);
		return $model;
	}

}