<?php


class Fazzoura extends Model {
	
	function Fazzoura(){
		parent::Model();
	}
	
	/*
	 * Get a fazzoura with an id, typically used for the fazzoura of the day
	 * 
	 */
	//TODO remove language thing at the end, controller needs to decide this, not model
	function getFazzoura($fazzoura_id, $language='en'){		//language thing added, needs test
		$this->db->where('fazzoura_id', $fazzoura_id);
		$rsFazzoura = $this->db->get('fazzoura');
		return $rsFazzoura->row_array();
	}
	
	function getLanguage($fazzoura_id){
		$data = array();
		$this->db->where('fazzoura_id', $fazzoura_id);
		$rsFazzoura = $this->db->get('fazzoura');
		if($rsFazzoura->num_rows()){
			$data = $rsFazzoura->row_array();	
		}
		return $data['language'];
	}
	
	function getTotalFifty(){
		return 5;
	}
	
	/*
	 * Get all four options for a fazzoura
	 */
	
	function getFazzouraOptions($fazzoura_id){ //no need for language, since belongs to specific id
		$this->db->select('fazzoura.fazzoura');
		$this->db->select('options.option');
		$this->db->from('fazzoura');
		$this->db->join('options', 'fazzoura.fazzoura_id = options.fazzoura_id');
		$this->db->where('fazzoura.fazzoura_id', $fazzoura_id);
		$this->db->where('options.answer', TRUE);
		$rsFazzoura = $this->db->get();
		return $rsFazzoura->row_array();
	}
	
	function getHint($fazzoura_id){ ////no need for language, since belongs to specific id
		$data = array();
		$this->db->where('fazzoura_id', $fazzoura_id);
		$rsFazzoura = $this->db->get('fazzoura');
		if($rsFazzoura->num_rows()){
			$data = $rsFazzoura->row_array();
			return $data['hint']; 
		} else {
			return "";
		}
	}
	
	/*
	 * 
	 * Get Records from Fazzoura where there's an an option for a fazzoura in the 
	 * results table (correct or wrong) submitted in results table for user_id
	 * used in calendar table to set legends
	 */
	function getResults($user_id){
		$this->db->select('fazzoura.fazzoura_id, options.answer');
		$this->db->from('fazzoura');
		$this->db->join('options', 'fazzoura.fazzoura_id = options.fazzoura_id', 'left');
		$this->db->join('results', 'options.option_id = results.option_id', 'left');
		$this->db->join('users', 'results.user_id = users.user_id', 'left');
		$this->db->order_by('fazzoura.fazzoura_id');
		$this->db->where('users.user_id', $user_id);
		$rsResults = $this->db->get();
		return $rsResults->result_array();
	}
	
}


?>