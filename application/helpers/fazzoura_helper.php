<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('getLoggedIndex'))
{
	function getLoggedIndex($language='en'){ 
		isset($_SESSION)? "" : session_start();
		$language = isset($_SESSION['language']) ? $_SESSION['language'] : "en";
		
		$data = array();
		$data['name'] = isset($_SESSION['first']) ? $_SESSION['first'] : "";

		$data['login'] = $this->getLogin($language); //get the form
		
		$fazzoura_id = $this->ramadan->getFazzouraIDOfTheDay($language);
		$data['fazzoura'] = $this->getLoggedFazzoura($fazzoura_id, $language);
		if ($fazzoura_id > 1) { //if it's not the first day of Ramadan
			$data ['previousFazzoura'] = $this->getLoggedFazzoura($fazzoura_id - 1, $language);
		} else {
			$this->lang->load('errors', $language);
			$data ['previousFazzoura'] = $this->lang->line('no_previous_fazzoura');
		}
		
		$data['report'] = $this->getReport($_SESSION['user_id'], $language);
		$data['calendar'] = $this->getCalendar($language);
		$this->parser->parse($language . '/fazzoura', $data); //parse main fazzoura page			
	}	
}

?>