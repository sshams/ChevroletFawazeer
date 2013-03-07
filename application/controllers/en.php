<?php

class en extends Controller {
	
	private $language = 'en';
	
	function en() {
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