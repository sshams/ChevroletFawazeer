<?php

class ar extends Controller {
	
	private $language = 'ar';
	
	function ar() {
		parent::Controller();
	}
	
	function index(){ 
		$this->load->view($this->language . '/index.php');
	}
	
	function about(){
		$this->load->view($this->language . '/about.php');
	}
	
	function what(){
		$this->load->view($this->language . '/what.php');
	}
	
}

?>