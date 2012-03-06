<?php

class Brisk_Controller {

	public $controller;
	public $action;

	protected static $_instance;

	/**
	 * @return Brisk_Controller
	 */
	public static function getInstance(){

        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

	private function __construct(){

		//get path of current url
		$url_path = parse_url('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$current_path = trim($url_path, '/');

		if(empty($current_path)){
			$this->controller = 'Index';
			$this->action = 'index';
		}else{
			$path = explode('/', $current_path);
			$this->controller = ucfirst($this->normalizeName($path[0]));
			$this->action = (count($path) == 1) ? 'index' : $this->normalizeName($path[1]);
		}

		return $this;
	}


	public function getControllers(){

		$controllers = array();
		$controller_files = glob(MODULE_PATH . '/' . MODULE . '/controllers/*Controller.php');
		foreach($controller_files as $file){
			if(preg_match('/controllers\/(.*?)Controller.php/', $file, $match)){
				$controllers[] = $match[1];
			}
		}

		return $controllers;
	}

	public function getAction(){
		return $this->action;
	}

	public function setAction($action){
		$this->action = $this->normalizeName($action);
	}

	public function setController($controller){
		$this->controller = ucfirst($this->normalizeName($controller));
	}

	public function normalizeName($name){

		$name_parts = explode('-', str_replace(array('_','.'), '-', strtolower($name)));
		$name = array_shift($name_parts);

		foreach($name_parts as $part){
			$name .= ucfirst($part);
		}

		return $name;
	}

	public function run(){

		//check if controller exist
		if(in_array($this->controller, $this->getControllers())){

			//set include path for module/controllers
			set_include_path(get_include_path() . PATH_SEPARATOR .
				MODULE_PATH . '/' . MODULE . '/controllers'
			);

			//init controller and execute requested action
			$controller_class = $this->controller . 'Controller';
			$controller = new $controller_class();
			$action = $this->action . 'Action';

			if(method_exists($controller_class, $action)){

				$controller->init();
				$controller->$action();

				$view = Brisk_View::getInstance();

				//set default view if not set yet
				if($view->override_view == false){
					$view->setView($this->action);
				}

				if($view->view_set){
					$view->output();
				}

			}else{
				die('Invalid action <b>"' . $this->action . '"</b> requested in ' . $controller_class);
			}

		}else{
			die('Invalid controller <b>"' . $this->controller . '"</b> requested in <b>"' . MODULE . '"</b> module');
		}
	}
}
