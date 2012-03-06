<?php

class Brisk_Controller_Action {

	protected $view;

	public function __construct(){
		$this->view = Brisk_View::getInstance();
	}
	
	protected function _getParam($param, $default_value = NULL){
		return isset($_REQUEST[$param]) ? $_REQUEST[$param] : $default_value;
	}

	protected function _redirect($url){
		header("Location: $url"); exit;
	}
	
	protected function isPost(){
		return ('POST' == $_SERVER['REQUEST_METHOD']);
	}

	protected function getPost(){
		return $_POST;
	}
	
	protected function getQuery(){
		return $_GET;
	}
	
	public function json($data){
		
		if(!is_array($data)){
			exit($data);
		}else{
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Content-type: application/json');
			print(json_encode($data));
			exit();
		}
	}
}
