<?php

class Brisk_Controller_Request{

	public static function getInstance(){

		static $request = null;

		if ($request == null){
			$request = new Brisk_Controller_Request();
		}

		return $request;
	}

	private function __construct(){

		//get path of current url
		$url_path = parse_url('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$current_path = trim($url_path, '/');
		$path = explode('/', $current_path);

		$params = $_REQUEST;


		if(count($path) > 2){
			for($i=2;$i< count($path); $i++){
				if($i % 2){
					$params[$path[$i-1]] = $path[$i];
					$_REQUEST[$path[$i-1]] = $path[$i];
				}
			}
		}

		foreach($params as $key=>$value){
			$this->$key = $value;
		}


		return $this;
	}


	public function getPost(){

		return $_POST;
	}

	public function getQuery(){

		return $_GET;
	}

}