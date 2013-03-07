<?php

class Country extends Model {
	
	private $second_db;
	
	function Country() {
		parent::Model();
		$this->second_db = $this->load->database('second_db', TRUE);
	}
	
	function getCountry(){
		$data = array();
		$query_rsIP = sprintf("SELECT * FROM country_name WHERE IP_FROM <= inet_aton(%s) AND IP_TO >= inet_aton(%s)", $this->GetSQLValueString($_SERVER['REMOTE_ADDR'], "text"), $this->GetSQLValueString($_SERVER['REMOTE_ADDR'], "text"));
		
		$rsCountry = $this->second_db->query($query_rsIP);
		if($rsCountry->num_rows()){
			$data = $rsCountry->row_array();
			return $data['COUNTRY_CODE2'];
		}
	}
	
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  if (PHP_VERSION < 6) {
	    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	  }
	
	  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
	
	  switch ($theType) {
	    case "text":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;    
	    case "long":
	    case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	    case "double":
	      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
	      break;
	    case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	    case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	  }
	  return $theValue;
	}
	
}

?>