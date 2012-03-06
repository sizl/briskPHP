<?php
/**
 * Brisk_View
 * @author kevin
 */
class Brisk_View{

	private $view_path;
	private $view_layout;
	private $view_script;
	private $view_content;

	private $controller;
	private $page_title;

	public $_helper; //view helper

	public $view_set = true;

	// JS & CSS Links
	public $js_files 	 = array();
	public $css_files 	 = array();
	//Captured JS & CSS
	public $document_ready_captured  = array();
	public $style_captured = array();
	public $script_captured = array();
	public $meta_captured = array();
	public $override_view = false;


	protected static $_instance;

	/**
	 * @return Brisk_View
	 */
	public static function getInstance(){
        if (null === self::$_instance) {
            self::$_instance = new self(Brisk_Controller::getInstance());
        }
        return self::$_instance;
    }

	private function __construct($controller){
		$this->controller = $controller;
		$this->view_path = MODULE_PATH . '/' . MODULE . '/views';
		$this->_helper = new Brisk_View_Helper($this);
		$this->setLayout();
	}

	public function setLayout($layout='default'){
		$this->view_layout = $this->view_path . '/layout/' . $layout . '.phtml';
	}

	public function render($view, $controller = null){
		$this->view_set = true;
		$this->override_view = true;
		$this->setView($this->reverseNormalizeName($view),$controller);
	}

	public function setView($script='index', $controller = null){
		$controller = ($controller) ? $controller : $this->controller->controller;
		$this->view_script = sprintf('%s/templates/%s/%s.phtml',
			$this->view_path,
			$this->reverseNormalizeName($controller),
			$this->reverseNormalizeName($script)
		);
	}

	public function output(){
		$this->view_content = $this->_getFileContent($this->view_script);
		print($this->_getFileContent($this->view_layout));
	}

	private function _getFileContent($file){
		ob_start();
		require($file);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function reverseNormalizeName($str){
		return strtolower(preg_replace('/(.)([A-Z])/', '\\1-\\2', $str));
	}

	public function getContent(){
		return $this->view_content;
	}

	public function getViewPath(){
		return $this->view_path;
	}

	public function getTitle(){
		return $this->page_title;
	}

	public function setTitle($title = 'Untitled'){
		$this->page_title = htmlentities($title);
	}

	public function noRender(){
		$this->view_set = false;
	}

	protected function getViewEnabled(){
		return $this->view_set;
	}

	protected function partial($partial, array $vars=array()){

    	if(!empty($vars)){
    		foreach($vars as $variable=>$value){
    			$this->$variable = $value;
    		}
    	}

    	return $this->_getFileContent($this->view_path . '/partials/' . $partial);
    }

	public function appendScript($js){
		$this->js_files[] = $js;
		return $this;
	}

	public function appendStyle($css){
		$this->css_files[] = $css;
		return $this;
	}

    public function headScript(){

    	$scripts = PHP_EOL;

    	//external scripts
    	if(count($this->js_files) > 0){
    		$js_files = array_reverse($this->js_files);
    		foreach($js_files as $script){
    			$scripts .= '<script type="text/javascript" src="'. $script . '"></script>' . PHP_EOL;
    		}
    	}

    	$scripts .= '<script type="text/javascript">' . PHP_EOL;
    	$scripts .= '//<![CDATA['. PHP_EOL;

    	//static js
    	if(count($this->script_captured) > 0){
    		$script_captured = array_reverse($this->script_captured);
    		foreach($script_captured as $script_block){
	    		$scripts .= $script_block;
    		}
    	}

    	//document.ready
    	if(count($this->document_ready_captured) > 0){
    		$scripts .= PHP_EOL . '$(document).ready(function(){' . PHP_EOL;
    		$document_ready_captured = array_reverse($this->document_ready_captured);
    		foreach($document_ready_captured as $js_block){
	    		$scripts .= $js_block;
    		}
    		$scripts .= PHP_EOL . '});'. PHP_EOL;
    	}

    	$scripts .= PHP_EOL . '//]]>' . PHP_EOL;
    	$scripts .= PHP_EOL . '</script>' . PHP_EOL;

    	return $scripts;
    }

	public function headStyle($media='screen'){

		$styles = '';

    	if(count($this->css_files) > 0){
    		foreach($this->css_files as $style_sheet){
    			$styles .= PHP_EOL . '<link rel="stylesheet" type="text/css" media="'. $media . '" href="' . $style_sheet . '" />' . PHP_EOL;
    		}
    	}

    	if(count($this->style_captured) > 0){
    		$styles .= PHP_EOL . '<style type="text/css">' . PHP_EOL;
    		foreach($this->style_captured as $css_block){
	    		$styles .= $css_block;
    		}
    		$styles .=  PHP_EOL . '</style>' . PHP_EOL;
    	}

    	return $styles;
    }

	public function metaTags(){
		$meta_tags = '';
    	if(count($this->meta_captured) > 0){
    		foreach($this->meta_captured as $meta_tag){
    			$meta_tags .= $meta_tag . PHP_EOL;
    		}
    	}
    	return $meta_tags;
    }

    /**
     * Captures JS and puts it into document.ready
     */
    public function captureDocumentReadyStart(){
    	ob_start();
    }

	public function captureDocumentReadyEnd(){
    	$this->document_ready_captured[] = ob_get_contents();
		ob_end_clean();
    }

 	public function captureStyleStart(){
    	ob_start();
    }

	public function captureStyleEnd(){
    	$this->style_captured[] = ob_get_contents();
		ob_end_clean();
    }

	public function captureScriptStart(){
    	ob_start();
    }

	public function captureScriptEnd(){
    	$this->script_captured[] = ob_get_contents();
		ob_end_clean();
    }

	public function captureMetaStart(){
    	ob_start();
    }

	public function captureMetaEnd(){
    	$this->meta_captured[] = ob_get_contents();
		ob_end_clean();
    }
}