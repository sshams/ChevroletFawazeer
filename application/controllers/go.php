<?php

class Go extends Controller {
	
	private $offset = 50; //offset in fazzoura id's for arabic language
	
	function Go() {
		parent::Controller();
		session_save_path(realpath("/adminsesspath"));
	}
	
	function index(){ //default homepage, during deployment change here to index2 and html files
		$data = array();
		if($this->uri->segment(2) == "" || $this->uri->segment(2) == "en"){
			$this->en();
		} else {
			$this->ar();
		}
	}
	
	function en(){
		$language = 'en';
		switch($this->uri->segment(3)){
			case "":
				$this->getIndex($language);
				break;
			case "about": 
				$this->getAbout($language);
				break;
			case "fazzoura":
				$this->getFazzoura($language);
				break;
			case "what":
				$this->getWhatIsFazzoura($language);
				break;
			case "score":
				$this->getScore($language);
				break;
			case "offers":
				$this->getOffers($language);
				break;
			case "register":
				$this->getRegister($language);
				break;
			case "challenge":
				$this->getChallenge($language);
				break;
			case "terms":
				$this->getTerms($language);
				break;
			case "privacy":
				$this->getPrivacy($language);
				break;
			case "forgot":
				$this->getForgot($language);
				break;				
		}
	}
	
	function ar(){
		$language = 'ar';
		switch($this->uri->segment(3)){
			case "":
				$this->getIndex($language);
				break;
			case "about": 
				$this->getAbout($language);
				break;
			case "fazzoura":
				$this->getFazzoura($language);
				break;
			case "what":
				$this->getWhatIsFazzoura($language);
				break;
			case "score":
				$this->getScore($language);
				break;
			case "offers":
				$this->getOffers($language);
				break;
			case "register":
				$this->getRegister($language);
				break;
			case "challenge":
				$this->getChallenge($language);
				break;
			case "terms":
				$this->getTerms($language);
				break;
			case "privacy":
				$this->getPrivacy($language);
				break;
			case "forgot":
				$this->getForgot($language);
				break;				
		}
	}
	
	function getIndex($language){
		$data['login'] = $this->getLogin($language);
		$data['fazzoura'] = nl2br($this->getFazzouraOfTheDay($language));
		$data['scoreboard'] = $this->getScoreboard($language);
		$this->parser->parse($language . '/index', $data);
	}
	
	function getLogin($language){ //form area login
		if (!isset( $_SESSION)) {
			session_start();
		}
		if(!isset($_SESSION['user_id'])){ //if not logged in
			return $this->parser->parse($language . '/snippets/login.php', array(), TRUE);
		} else {
			return $this->parser->parse($language . '/snippets/logout.php', array(), TRUE);
		}
	}
	
	function getFazzouraOfTheDay($language="en"){ //fazzoura of the day to display at homepage
		$fazzouraIDOfTheDay = $this->ramadan->getFazzouraIDOfTheDay($language);
		$fazzouraIDOfTheDay += ($language == 'ar') ? $this->offset : 0;	
		$fazzouraOfTheDay = $this->fazzoura->getFazzoura($fazzouraIDOfTheDay, $language);
		return $fazzouraOfTheDay['fazzoura'];
	}
	
	function getPreviousFazzoura($language="en"){
		$previousFazzoura = $this->fazzoura->getFazzoura($this->ramadan->getFazzouraIDOfTheDay($language) - 1, $language);
		return $previousFazzoura['fazzoura'];
	}
	
	function getScoreboard($language){ //Mini for Homepage
		//TODO set direction inside, requires if check in the snippet itself because loop is inside
		$scoreboard = array();
		$scoreboard['scoreboard'] = $this->users->getScoreboard(0, 4);
		$scoreboard['total'] = $this->ramadan->getTotalDays();
		$scoreboard['dir'] = ($language == "en") ?  "rtl" : "ltr"; 
		return $this->parser->parse($language . '/snippets/score_home', $scoreboard, TRUE);		
	}
	
	function isAuthorized(){
		
		session_start();
		
		return isset($_SESSION['first']);
	}	
	
	function getAbout($language){
		$data['login'] = $this->getLogin($language);
		$this->parser->parse($language . '/about', $data);
	}
	
	function getFazzoura($language){
		if($this->isAuthorized()){
			$this->load->helper('url');
			redirect('/login/', 'refresh');
		} else {
			$data['login'] = $this->getLogin($language);			
			$data['fazzoura'] = nl2br($this->getFazzouraOfTheDay($language));

			$fazzoura_id = $this->ramadan->getFazzouraIDOfTheDay($language);
			$data['fazzoura'] = $this->getSimpleFazzoura($fazzoura_id, $language);
			
			if($fazzoura_id > 1){
				$data['previousFazzoura'] = $this->getSimpleFazzoura($fazzoura_id - 1, $language);
			} else {
				$this->lang->load('errors', $language);
				$data['previousFazzoura'] = $this->lang->line('no_previous_fazzoura');
			}
			$data['scoreboard'] = $this->getScoreboard($language);
			$this->parser->parse($language . '/fazzoura_day', $data);
		}
	}
	
	function getSimpleFazzoura($fazzoura_id, $language='en'){
		$data['fazzoura_day'] = $fazzoura_id; //1 out of 28 etc.
		$data['total'] = $this->ramadan->getTotalDays();

		$data['fazzoura_id'] = $fazzoura_id;
		$fazzoura_id += ($language == 'ar') ? $this->offset : 0;
		
		$fazzoura = $this->fazzoura->getFazzoura($fazzoura_id); //from table
		$data['fazzoura'] = nl2br($fazzoura['fazzoura']);
		
		$options = array();
		$this->load->model('options');
		$options = $this->options->getOptions($fazzoura_id);		
		
		for($i=0; $i<count($options); $i++) { //radio buttons, labels, fifty fifty
			$data['option' . $i] = $options[$i]['option'];
		}
		
		return $this->parser->parse($language . '/snippets/options_simple', $data, TRUE);
		
	}	
	
	function getWhatIsFazzoura($language){
		$data['login'] = $this->getLogin($language);
		$this->parser->parse($language . '/what', $data);		
	}
	
	function getScore($language='en'){
		$data['login'] = $this->getLogin($language);
		
		//Scoreboard
		$scoreboard['scoreboard'] = $this->users->getScoreboard($this->uri->segment(4) ? $this->uri->segment(4) : 0, 10); //populating scoreboard
		$scoreboard['total'] = $this->ramadan->getTotalDays();
		$data['scoreboard'] = $this->parser->parse($language . '/snippets/score', $scoreboard, TRUE);
		$data['total'] = $this->ramadan->getDaysSinceRamadan();
		
		$this->load->model('results');
		
		//Pagination		
		$this->load->library('pagination');
		
		$config['base_url'] = "/go/" . $language . "/score/";
		$config['total_rows'] = $this->results->getUserCount();
		$config['per_page'] = 10;
		$config['num_links'] = 5;
		$config['uri_segment'] = 4;
		$config['first_link'] = "First";
		$config['last_link'] = "Last";
		$config['next_link'] = "Next &gt;";
		$config['prev_link'] = "&lt; Back";
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();		
		
		$this->parser->parse($language . '/score', $data);
	}
	
	function getOffers($language){
		
		//check IP
		$this->load->model('country', '', TRUE);
		$data['login'] = $this->getLogin($language);
		$data['country_code2'] = $this->country->getCountry();
		
		switch($data['country_code2']){
			case "AE":
			case "SA";
			case "BH":
			case "QA":
				$data['offers'] = $this->parser->parse($language . '/snippets/offers_' . $data['country_code2'], array(), TRUE);
				break;
			default:
				$data['offers'] = $this->parser->parse($language . '/snippets/offers_SA', array(),  TRUE);
		}		
		
		$this->parser->parse($language . '/offers', $data);
		
	}
	
	function getRegister($language){
		$data['login'] = $this->getLogin($language);
		
		$data['message'] = "";
		
		$data['title'] = "";
		$data['first'] = "";
		$data['last'] = "";
		$data['email'] = "";
		$data['password'] = "";
		$data['language'] = "";
		$data['mobile'] = "xxx-xxxxxxxx";
		$data['pobox'] = "";
		$data['city'] = "";
		$data['country'] = "";
		$data['license'] = "";
		$data['how'] = "";
		$data['other'] = "";		
		$data['vehicles'] = "";
		$data['model'] = "";
		$data['year'] = "";
		$data['intention'] = "";
		
		$this->parser->parse($language . '/register', $data);
	}
	
	function getMessage($message, $language){
		$data['login'] = $this->getLogin($language);
		$data['message'] = $message;
		$this->parser->parse($language, '/message', $data);
	}
	
	
	function getChallenge($language){
		$data['login'] = $this->getLogin($language);
		$this->parser->parse($language . '/challenge', $data);
	}
	
	function getTerms($language){
		$this->load->model('country', '', TRUE);
		$data['login'] = $this->getLogin($language);
		$data['country_code2'] = $this->country->getCountry();
		switch($data['country_code2']){
			case "AE":
			case "SA";
			case "BH":
			case "QA":
				$data['terms'] = $this->parser->parse($language . '/snippets/terms_' . $data['country_code2'], array(), TRUE);
				break;
			default:
				$data['terms'] = $this->parser->parse($language . '/snippets/terms_SA', array(), TRUE);
		}
		echo $this->parser->parse($language . '/terms', $data, TRUE);	
	}
	
	function getPrivacy($language){
		$this->parser->parse($language . '/privacy', array());
	}
	
	function get() { //for the ajax function
		$data = array ();
		$language = $this->uri->segment(3); 
		$fazzoura_id = (int)$this->uri->segment(4); //casting if random chars in the url, sets to 0
		
		$this->lang->load('errors', $language);
		
		if($fazzoura_id > $this->ramadan->getDaysSinceRamadan ()) { //asking for fazzoura for future dates
			echo $this->lang->line('only_previous_fazzoura');
			return;
		}
		if ($fazzoura_id > $this->ramadan->getTotalDays ()) { //URL requests after Ramadan
			echo $this->lang->line('no_previous_fazzoura');
			return;
		}
		
		//Negative Number OR if day 1 clicked and its the first 1st day of ramadan 
		if (($fazzoura_id < 0) || ($fazzoura_id == 1 && $this->ramadan->getDaysSinceRamadan() == 1)) {
			echo $this->lang->line('no_previous_fazzoura');
			return;
		}
		
		echo $this->getSimpleFazzoura($fazzoura_id, $language);
	}	
}

?>