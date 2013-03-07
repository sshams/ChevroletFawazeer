<?php

class Login extends Controller {
	
	private $offset = 50; //offset in fazzoura id's for arabic language
	
	function Login() {
		parent::Controller();
		//$this->output->enable_profiler(TRUE);
		session_save_path(realpath("/adminsesspath"));
	}
	
	function index(){

		session_start();
		if(isset($_SESSION['first'])){
			$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
			$this->getLoggedIndex($language);
		} else {
			$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
			$this->getSimpleIndex($language);	
		}
	}
	
	function register(){
		$language = $this->uri->segment(2) == "ar" ? $this->uri->segment(2) : "en";

		if($this->validate($language) == ""){
	
			
			$this->users->insert($this->getPostData());
			$data = $this->users->getUserRow($this->db->insert_id());
			isset($_SESSION)? "" : session_start();
			$_SESSION['user_id'] = $data['user_id'];
			$_SESSION['first'] = $data['first'];
			$_SESSION['language'] = $data['language'];
			
				
			$this->saveToCoversManager();
			
			$this->getLoggedIndex($_SESSION['language']);
		} else {			
			$data = $this->getPostData();
			$data['login'] = $this->getLogin();			
			$data['message'] = nl2br($this->validate());
			$this->parser->parse($language . '/register', $data);
		}
	}
	
	function validate($language='en'){
		$this->lang->load('errors', $language);
		$message = "";
		
		if($_POST['email'] == "" || $_POST['password'] == ""){
			$message = ($_POST['email'] == "") ? $this->lang->line('email_empty') : "";
			$message .= $_POST['password'] == "" ? "\n" . $this->lang->line('password_empty') : ""; 
			$message .= $_POST['license'] == "" ? "\n" . $this->lang->line('license_empty') : "";
			$message .= $this->input->post('terms') == "" ? "\n" . $this->lang->line('terms') : "";
			return $message;
		} else {
			if($this->users->checkNewEmail($this->input->post('email'))){
				$message = $this->lang->line('email_exists');
			}	
			if($this->users->checkNewLicense($this->input->post('license'), $this->input->post('country'))){
				$message .= "\n" . $this->lang->line('license_exists');
			}
			return $message;
		}
	}	
	
	function get() { //for the ajax function
		$data = array ();
		 
		$language = $this->uri->segment(3);
		$fazzoura_id = (int)$this->uri->segment(4); //casting if random chars in the url, sets to 0
		
		$this->lang->load('errors', $language);
		
		if($fazzoura_id >= $this->ramadan->getDaysSinceRamadan ()) { //asking for fazzoura for future dates
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
		
		if ($this->isAuthorized()){
			isset($_SESSION) ? "" : session_start();
			$language = isset($_SESSION['language']) ? $_SESSION['language'] : "en";
			//this will take care
			echo $this->getLoggedFazzoura($fazzoura_id, $language);
		} else {
			echo $this->getSimpleFazzoura($fazzoura_id, $language);
		}
	}
	
	function submit() { //TODO check if language is okay
		isset($_SESSION)? "" : session_start();
		
		$language = $_SESSION['language'];
		
		//if it's not submitted
		if (!$this->users->isSubmit($this->input->post('fazzoura_id'), $this->input->post('option_id'), $_SESSION ['user_id'] )) {
			if($this->fazzoura->getLanguage($this->input->post('fazzoura_id')) == $_SESSION['language']){ //if language is right
				
				$this->load->model('results');
				$previous_result_id = $this->results->insert($_SESSION['user_id'], $this->input->post('fazzoura_id'), $this->input->post('option_id'));
				
				$previous_fazzoura_id = $this->results->getFazzouraID($previous_result_id);
				
				$previous_fazzoura_id -= ($language == 'ar') ? $this->offset : 0; //decrement back since to display, display function will auto increment
				
				$data['name'] = $_SESSION['first'];
				$data['login'] = $this->getLogin($language);
				
				$fazzoura_id = $this->ramadan->getFazzouraIDOfTheDay($language); //to do delete language here
				
				$data['fazzoura'] = $this->getLoggedFazzoura($fazzoura_id, $language);
				
				if($previous_fazzoura_id >= 1){
					$data['previousFazzoura'] = $this->getLoggedFazzoura($previous_fazzoura_id, $language);
				} else {
					$this->lang->load('errors', $language);
					$data['previousFazzoura'] = $this->lang->line('no_previous_fazzoura');
				}
				
				$data['report'] = $this->getReport($_SESSION['user_id'], $language);
				$data['calendar'] = $this->getCalendar($language);
				$this->parser->parse($language . '/fazzoura', $data); //parse main fazzoura page
			} else {
				$this->lang->load('errors', $language);
				$data['message'] = $this->lang->line('fazzoura_language');
				$this->parser->parse($language . '/message', $data);
			}
		} else {
			$this->getLoggedIndex();
		}
	}


	function getSimpleIndex($language='en'){
		$data = array();
		
		$data['login'] = $this->getLogin($language); //get the form
		
		$fazzoura_id = $this->ramadan->getFazzouraIDOfTheDay($language);
		$data['fazzoura'] = $this->getSimpleFazzoura($fazzoura_id, $language);
		
		if ($fazzoura_id > 1) { //if it's not the first day of Ramadan
			$data ['previousFazzoura'] = $this->getSimpleFazzoura($fazzoura_id - 1, $language);
		} else {
			$this->lang->load('errors', $language);
			$data ['previousFazzoura'] = $this->lang->line('no_previous_fazzoura');
		}
		$data['scoreboard'] = $this->getScoreboard($language);
		$this->parser->parse($language . '/fazzoura_day', $data); //parse main fazzoura page	
	}	
	
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
	
	function getSimpleFazzoura($fazzoura_id, $language='en'){
		$data['fazzoura_day'] = $fazzoura_id;
		$data['fazzoura_id'] = $fazzoura_id; //1 out of 28 etc.
		$data['total'] = $this->ramadan->getTotalDays ();

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
	
	function getLoggedFazzoura($fazzoura_id, $language='en') {
		isset($_SESSION) ? "" : session_start();

		$data['fazzoura_day'] = $fazzoura_id;//1 out of 28 etc. 
		$data['total'] = $this->ramadan->getTotalDays ();

		$fazzoura_id += ($language == 'ar') ? $this->offset : 0;
		$data['fazzoura_id'] = $fazzoura_id;
		
		$fazzoura = $this->fazzoura->getFazzoura($fazzoura_id); //from table
		$data['fazzoura'] = nl2br($fazzoura['fazzoura']);
		
		$options = array();
		$this->load->model('options');
		$options = $this->options->getOptions($fazzoura_id);
		
		for($i=0; $i<count($options); $i++) { //radio buttons, labels, fifty fifty
			$data['option' . $i] = $options[$i]['option'];
			$data['option_id' . $i] = $options[$i]['option_id'];
			$data['answer' . $i] = $options [$i]['fifty_fifty'];
		}
		
		$this->load->model('results');
		if (!$this->results->isSubmit($fazzoura_id, $_SESSION['user_id'])){ //if not submitted
			 //Hint Section
			//to check if first fazzoura or previous
			//store get Days since ramadan in a field, 
			//compare if language = en, compare if language = ar 
			
			if($language == 'en'){
				if($fazzoura_id == $this->ramadan->getDaysSinceRamadan()){ //if its fazzoura of the day TODO hint check
					$data['hint_n'] = 1;
					$data['fifty'] = 1;
					$data['form'] = 1;
				} else { //if it's previous fazzoura
					$data['hint_n'] = 2;
					$data['fifty'] = 2;
					$data['form'] = 2;
				}
			} else {
				if($fazzoura_id == $this->ramadan->getDaysSinceRamadan() + $this->offset){ //if its fazzoura of the day TODO hint check
					$data['hint_n'] = 1;
					$data['fifty'] = 1;
					$data['form'] = 1;					
				} else {
					$data['hint_n'] = 2;
					$data['fifty'] = 2;
					$data['form'] = 2;
				} 
			}
			
			$this->lang->load('errors', $language);
			$this->load->model('users');
			$data['f'] = $this->users->getFiftyLeft($_SESSION['user_id']); //see if 50/50 are left
			if($data['f'] > 0){
				$data['onClick'] = "getFifty(2, '/login/fifty/4'); return false;";
				$data['image'] = "/" . $language . "/images/half.gif";
				$data['title'] = $this->lang->line('fifty');
				$data['fifty_link'] = 'href="javascript:;"';
				$data['fifty_total'] = "/" . $this->fazzoura->getTotalFifty();
			} else {
				$data['onClick'] = "";
				$data['fifty'] = "7";
				$data['fifty_link'] = '';
				$data['image'] = "/" . $language . "/images/half_used.gif";
				$data['title'] = $this->lang->line('fifty_used');
				$data['f'] = "";
				$data['fifty_total'] = "";
			}
			return $this->parser->parse($language . '/snippets/options_enabled', $data, TRUE);

		} else { //if submitted, show disabled
			$answer = array();
			$answer = $this->results->getResultsOptions($fazzoura_id, $_SESSION['user_id']);
			
			$correct = $this->options->getCorrectOption($fazzoura_id);			
			
			$data['correctOption'] = $correct['option'];
			$data['option'] = $answer['option'];
			
			$this->lang->load('errors', $language);
			$data ['greet'] = ($answer ['answer']) ? $this->lang->line('congrats') : $this->lang->line('sorry');

			return $this->parser->parse($language . '/snippets/options_disabled', $data, TRUE);
		}
	}	

	function getLogin($language='en'){
		isset($_SESSION) ? "" : session_start();
		if(!isset($_SESSION['user_id'])){ //login with form
			return $this->parser->parse($language . '/snippets/login.php', array(), TRUE);
		} else { 
			return $this->parser->parse($language . '/snippets/logout.php', array(), TRUE);
		}
	}

	function getScoreboard($language){ //Mini for Homepage
		$scoreboard = array();
		$scoreboard['scoreboard'] = $this->users->getScoreboard(0, 4);
		$scoreboard['total'] = $this->ramadan->getTotalDays();
		return $this->parser->parse($language . '/snippets/score_day', $scoreboard, TRUE);		
	}
	
	function getReport($user_id, $language='en'){ //report panel
		$data = array();
		$report = $this->users->getReport($user_id);
		$data ['correct'] = $report ['correct'];
		$data ['wrong'] = $report ['wrong'];
		$data ['not'] = ($this->ramadan->getDaysSinceRamadan ()) - ($data ['correct'] + $data ['wrong']);

			
		$previousTotal = $this->users->getTopScore(); //Calculating rank GET TO THE TOP HERE
		//$topScore = $this->users->getTopScore();
		$rank = 0;
		//TODO fix this
		foreach($this->users->getScoreBoard(0) as $row) {
			if($previousTotal != $row['t']) { //if next score is different
				//echo "PREVIOUS: " . $previousTotal . " " . "rOW TOTAL: " . $row['t'] . "<BR>";
				$rank++;
				if($row ['user_id'] == $_SESSION['user_id']) { //for the logged in user
					$data['rank'] = $rank;
					break;
				} else {
					$data['rank'] = 0;
				}
			}
		}
		if(!isset($data['rank'])) { //if user haven't submitted any results, rank gets null
			$data['rank'] = 0;
		}
		return $this->parser->parse($language. '/snippets/report', $data, TRUE);
	}
	
	function getRank(){
		
	}
	
	function getCalendar($language='en'){ //Sets all the legends
		isset($_SESSION) ? "" : session_start();
		
		$legends = array ();
		$allResultsOptions = $this->results->getAllResultsOptions($_SESSION['user_id']);
		
		if($language == 'en'){
			foreach($allResultsOptions as $row){
				$legends['d' . $row['fazzoura_id'] . 'c'] = ($row['answer'] == TRUE) ? "right" : "wrong";
			}
		} if($language == 'ar') {
			foreach($allResultsOptions as $row){
				$legends['d' . ($row['fazzoura_id'] - $this->offset) . 'c'] = ($row['answer'] == TRUE) ? "right" : "wrong";
			}	
		}
		
		for($i=0; $i<=35;$i++){	//setting the rest as blanks for
			if(!isset($legends['d' . $i . 'c'])){
				$legends['d' . $i . 'c'] = "";
			}
		}
		return $this->parser->parse($language . '/snippets/calendar', $legends, TRUE);		
	}
	
	function fifty(){
		isset($_SESSION)? "" : session_start();
		$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
		if($this->isAuthorized()){
			$data = array ();
			
			$fazzoura_id = (int)$this->uri->segment(3); //casting if random chars in the url, sets to 0

			$data['fifty_left'] = $this->users->getFiftyLeft($_SESSION['user_id']); //see if options are left
			if($data['fifty_left'] > 0){ 
				$this->load->model('options');
				//$data['fifty_options'] = $this->options->getFiftyOptions($fazzoura_id); //change to options table
				$data['fifty_left']--; 
				$this->users->setFifty($data['fifty_left'], $_SESSION['user_id']); //decremented
				
				$a = $this->options->getFiftyOptions($fazzoura_id); //change to options table
				echo $a[0]['option_id'] . "," . $a[1]['option_id'];
			} else {
				isset($_SESSION['language']) ? $_SESSION['language'] : "en";
				$this->lang->load('errors', $_SESSION['language']);
				echo $this->lang->line('no_fifty');
			}
		}
	}	
	
	function hint(){
		if($this->isAuthorized()) {
			$data = array();
			$fazzoura_id = (int)$this->uri->segment(3); //casting if random chars in the url, sets to 0
			$data['hint'] = $this->fazzoura->getHint($fazzoura_id);
			echo $data['hint']; //display hint below fazzoura options
		}
	}

	function isSession(){
		isset($_SESSION)? "" : session_start();	
		return isset($_SESSION['user_id']);
	}
	
	function authorize($language='en') {
		session_start();
		$user = array();
		if(isset($_POST['email']) && isset($_POST['password'])){
			$user = $this->users->getUser($_POST['email'], $_POST['password']); //check his record
		}
		if (count($user)) {
			$_SESSION['user_id'] = $user['user_id'];
			$_SESSION['first'] = $user['first'];
			$_SESSION['language'] = $user['language'];
			
			$this->getLoggedIndex($_SESSION['language']);
		} else {
			$this->destroySession();
			$this->lang->load('errors', $language);
			$this->getMessage($this->lang->line('invalid_credentials'), $language);
		}
	}
	
	function destroySession() {
		isset($_SESSION)? "" : session_start();
		$_SESSION = array();
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time () - 42000, '/' );
		}
		session_destroy();
	}
		
	function logout() {
		isset($_SESSION)? "" : session_start();
		$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
		$this->destroySession();
		$this->getSimpleIndex($language);
		//this->load->helper('url');
		//redirect('/go/en/', 'refresh');
	}
	
	function isAuthorized($language='en'){ 
		isset($_SESSION)? "" : session_start();
		return isset($_SESSION['user_id']);
	}
	
	function getMessage($message, $language){
		$data['login'] = $this->getLogin($language);
		$data['message'] = $message;
		$this->parser->parse($language . '/message', $data);
	}	

	function getPostData(){		
		$data['title'] = $this->input->post('title');
		$data['first'] = $this->input->post('first');
		$data['last'] = $this->input->post('last');
		$data['email'] = $this->input->post('email');
		$data['password'] = $this->input->post('password');
		$data['language'] = $this->input->post('language');
		$data['mobile'] = $this->input->post('mobile');
		$data['pobox'] = $this->input->post('pobox');
		$data['city'] = $this->input->post('city');
		$data['country'] = $this->input->post('country');
		$data['license'] = $this->input->post('license');
		$data['how'] = $this->input->post('how');
		$data['other'] = $this->input->post('other');
		$data['vehicles'] = $this->input->post('vehicles');
		$data['model'] = $this->input->post('model');
		$data['year'] = $this->input->post('year');
		$data['intention'] = $this->input->post('intention');
		$data['own'] = $this->input->post('own');
		$data['terms'] = $this->input->post('terms');
		
		return $data;
	}
	
	function forgot(){
		$email = $this->input->post('email');
		$language = $this->input->post('language');
		
		$this->lang->load('errors', $language);
		
		if($email != ""){
			$this->load->library('email');
			$this->load->model('users');
			$data = $this->users->getPassword($email);
			$data['login'] = $this->getLogin($language);
			if(isset($data['password'])){
				$this->load->library('email');
				
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				
				$this->email->from('no-reply@fawazeerchevrolet.com', 'Chevrolet Fawazeer');
				$this->email->to($email);					
			
				$this->email->subject($this->lang->line('forgot_subject'));
				$this->email->message($this->parser->parse($language . '/snippets/forgot_email', $data, TRUE));

				$this->email->send();
			
				$data['message'] = $this->lang->line('password_sent');
				$this->parser->parse($language . '/message', $data);
				
				//echo $this->email->print_debugger();
								
			} else {
				$data['message'] = $this->lang->line('no_email');
				$this->parser->parse($language . '/message', $data);
			}	
		}
	}	
	
	function saveToCoversManager(){
		$d = $this->getPostData();
		$dbc = @mysql_connect("localhost", "gm", "gx0fm2!");
		@mysql_select_db ("cm_gm-conversemanager-com", $dbc);
		
		$sql = "INSERT INTO _data_113 VALUES(NULL, '".$d['title']."', '".$d['first']."', '".$d['last']
			."', '".$d['email']."', '".$d['mobile']."', '".$d['pobox']."', '".$d['city']
			."', '".$d['country']."', '".$d['license']."', '".$d['how']."', '".$d['other']
			."', '".$d['own']."', '".$d['vehicles']."', '".$d['model']."', '".$d['year']
			."', '".$d['intention']."', '".$d['terms']."', '".$d['language']."', UNIX_TIMESTAMP()); ";
		@mysql_query($sql, $dbc);
		
		@mysql_close($dbc);
	}
	
}

?>