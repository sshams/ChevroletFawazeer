<?php

class Results extends Model{
	
	function Results () {
		parent::Model();
	}
	
	/*
	 * To find our if user has submitted the fazzoura or not, 
	 * used to display submit buttons or thankyou message
	 * 
	 */
	function isSubmit($fazzoura_id, $user_id){  //no lang
		$this->db->select('*');
		$this->db->from('results');
		$this->db->where('fazzoura_id', $fazzoura_id);
		$this->db->where('user_id', $user_id);
		$rsSubmit = $this->db->get();
		return $rsSubmit->num_rows();
	}
	
	function getFazzouraID($result_id){
		$data = array();
		$this->db->where('result_id', $result_id);
		$this->db->from('results');
		$this->db->limit(1);
		$rsResult = $this->db->get();
		if($rsResult->num_rows()){
			$data = $rsResult->row_array();
		}
		return $data['fazzoura_id'];
	}
	
	function insert($user_id, $fazzoura_id, $option_id){
		$this->db->set('user_id', $user_id);
		$this->db->set('fazzoura_id', $fazzoura_id);
		$this->db->set('option_id', $option_id);
		$this->db->insert('results');
		return $this->db->insert_id();	
	}
	
	
	/*
	 * To find out what option user submitted in the results table for fazzoura_Id
	 * Used in login.php to show him his (answer) and the correct one for visual comparison
	 * also used in login.php for legends
	 */
	
	function getResultsOptions($fazzoura_id, $user_id){ //no lang
		$this->db->from('results');
		$this->db->join('options', 'results.option_id = options.option_id');
		$this->db->where('results.fazzoura_Id', $fazzoura_id);
		$this->db->where('user_id', $user_id);
		$rsResultsOptions = $this->db->get();
		return $rsResultsOptions->row_array();
	}
	
	function getAllResultsOptions($user_id){ //no lang
		$this->db->from('results');
		$this->db->join('options', 'results.option_id = options.option_id');
		$this->db->where('user_id', $user_id);
		$rsResultsOptions = $this->db->get();
		return $rsResultsOptions->result_array();		
	}
	
	function getUserCount(){ //to get count of users if they have entry in the database
		$this->db->select('users.first');
		$this->db->from('results');
		$this->db->join('options', 'results.option_id = options.option_id');
		$this->db->join('users', 'results.user_id = users.user_id');
		$this->db->where('options.answer', TRUE);
		$this->db->group_by('users.user_id');
		$rsUsers = $this->db->get();
		return $rsUsers->num_rows();
	}
}

?>