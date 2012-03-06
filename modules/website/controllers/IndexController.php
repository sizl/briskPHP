<?php

class IndexController extends Brisk_Controller_Action{

	public function init(){
		$this->view->me = "kevin";
	}

	public function indexAction(){
		
		//to switch views within the same controller
		//$this->view->render('splash');
		
		
	}
}
