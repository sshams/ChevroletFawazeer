<?php


class Users extends Model {

	function Users() {
		parent::Model();
	}
	
	function getUser($email, $password){
		$data = array();
		$this->db->where('email', $email);
		$this->db->where('password', $password);
		$this->db->limit(1);
		
		$rsUsers = $this->db->get('users');
		if($rsUsers->num_rows()){
			$data = $rsUsers->row_array();
		}
		return $data;
	}
	
	function getUserRow($user_id){
		$data = array();
		$this->db->where('user_id', $user_id);
		$this->db->from('users');
		$rsUser = $this->db->get();
		if($rsUser->num_rows()){
			$data = $rsUser->row_array();
		}
		return $data;		
	}
	
	function getPassword($email){
		$data = array();
		$this->db->where('email', $email);
		$this->db->limit(1);
		$rsUser = $this->db->get('users');
		if($rsUser->num_rows()){
			$data = $rsUser->row_array();
		}
		return $data;	
		
	}
	
	function getLanguage($user_id){
		$this->db->where('user_id', $user_id);
		$this->db->from('users');
		$data = $this->db->query();
		return $data['language'];
	}
	
	function insert($user){
		$this->db->insert('users', $user);
	}
	
	function getCount(){
		$this->db->group_by('user_id');
		$rsUsers = $this->db->get('results');		
		return $rsUsers->num_rows();
	}
	
	function checkNewEmail($email){
		$this->db->where('email', $email);
		$this->db->from('users');
		$rsUser = $this->db->get();
		return $rsUser->num_rows();
	}
	
	function checkNewLicense($license, $country){
		$this->db->where('license', $license);
		$this->db->where('country', $country);
		$this->db->from('users');
		$rsUser = $this->db->get();
		return $rsUser->num_rows();
	}
	
	function getFiftyLeft($user_id){
		$this->db->where('user_id', $user_id);
		$rsUser = $this->db->get('users');
		if($rsUser->num_rows()){
			$data = $rsUser->row_array();
			return (int)$data['fifty'];
		} else {
			return 0;
		}
	}
	
	function setFifty($fifty, $user_id){
		$this->db->set('fifty', $fifty);
		$this->db->where('user_id', $user_id);
		$this->db->update('users');
	}
	
	function isSubmit($fazzoura_id, $option_id, $user_id){
		$this->db->where('fazzoura_id', $fazzoura_id);
		$this->db->where('option_id', $option_id);
		$this->db->where('user_id', $user_id);
		$data = $this->db->get('results');
		return $data->num_rows();
	}	
	
	function getReport($user_id){
		$data = array();

		$this->db->from('users');
		$this->db->join('results', 'users.user_id = results.user_id');
		$this->db->join('options', 'results.option_id = options.option_id');
		$this->db->where('options.answer', TRUE);
		$this->db->where('users.user_id', $user_id);
		
		$data['correct'] = $this->db->count_all_results();
		
		$this->db->from('users');
		$this->db->join('results', 'users.user_id = results.user_id', 'left');
		$this->db->join('options', 'results.option_id = options.option_id', 'left');
		$this->db->where('options.answer', FALSE);
		$this->db->where('users.user_id', $user_id);		
		
		$data['wrong'] = $this->db->count_all_results();
		
		return $data;		
	}
	
	function getScoreboard($offset=0, $limit=0, $language='en'){
		
		$data = array();
		$this->db->query("Set @n =" . $offset);
		
		$query_rsScoreboard = 	"SELECT @n:= @n + 1 AS n, `users`.user_id, `users`.first, `users`.last, `users`.language, count(`options`.answer) AS t " . 
								"FROM `users` " .
								"JOIN `results` " .
								"ON `users`.user_id = `results`.user_id " .
								"JOIN `options` " .
								"ON `results`.option_id = `options`.option_id " .
								"AND `options`.answer = true " .
								"GROUP BY `users`.user_id " .
								"ORDER by t DESC, `users`.first ASC, `users`.last DESC "; 
		
		if($limit != 0){ //if limit has been specified and not coming as default value
			//$query_limit_rsScoreboard = sprintf("%s LIMIT %d, %d", $query_rsScoreboard, $offset, $limit);
			//$rsScoreboard = $this->db->query($query_limit_rsScoreboard);
			$query_rsScoreboard .= "LIMIT $offset, $limit";
			$rsScoreboard = $this->db->query($query_rsScoreboard);
		} else {
			$rsScoreboard = $this->db->query($query_rsScoreboard);
		}
		
		return $rsScoreboard->result_array();
	}

	function getTopScore(){
		$data = array();
		$query_rsTopScore = "Select results.result_id, results.user_id, options.option_id, options.option, count(options.answer) AS top " .
		"from results " .
		"join options " .
		"where results.option_id = options.option_id " .
		"AND options.answer = TRUE " .
		"group by results.user_id " .
		"order by top desc ";
		
		$rsTopScore = $this->db->query($query_rsTopScore);
		if($rsTopScore->num_rows()){
			$data = $rsTopScore->row_array();
			return $data['top']; 
		} else {
			return 0;
		}
	}
}
?>