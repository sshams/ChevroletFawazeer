<?php


class Options extends Model {
	
	function Options(){
		parent::Model();
	}
	
	/*
	 * To get All the 4 options for a specific fazzoura
	 */
	function getOptions($fazzoura_id){ ////no need for language, since belongs to specific id
		$this->db->where('fazzoura_id', $fazzoura_id);
		$this->db->limit(4);
		$rsOptions = $this->db->get('options');
		return $rsOptions->result_array();
	}
	
	/*
	 * Get the Correct Option out of 4 for a fazzoura
	 */
	function getCorrectOption($fazzoura_id){ ////no need for language, since belongs to specific id
		$this->db->where('fazzoura_id', $fazzoura_id); //TODO it was missing, check else where
		$this->db->where('answer', 1);
		$this->db->limit(1);
		$rsCorrectOption = $this->db->get('options');
		return $rsCorrectOption->row_array();
	}
	
	function getFiftyOptions($fazzoura_id){ ////no need for language, since belongs to specific id
		$data = array();
		$this->db->select('option_id');
		$this->db->where('fazzoura_id', $fazzoura_id);
		$this->db->where('fifty_fifty', FALSE);
		$rsFiftyOptions = $this->db->get('options');
		if($rsFiftyOptions->num_rows()){
			$data = $rsFiftyOptions->result_array();	
		}
		return $data;
		
	}
	
	/*
	 * Checks if the given option is correct in the record
	 */
	function isCorrect($option_id){ ////no need for language, since belongs to specific id
		$this->db->where('option_id');
		$this->db->get('options');
		$rsIsCorrect = $this->db->row_array();
		return $rsIsCorrect['answer'];
	}
}

?>