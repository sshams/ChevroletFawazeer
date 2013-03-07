<?php

class Ramadan extends Model {
	
	function Ramadan() {
		parent::Model();
	}
	
	/*
	 * 		//Ramadan Date and Time (Variables to change depending upon the date)
	 * 		1. 	$uae_time = time() + (15 * 60 * 60) - (6 * 60); //if running on server
	 * 			$uae_time = time();	//if testing locally
	 * 		2.	mktime(hour, min, sec, mon, day, year)
	 * 			mktime(0, 0, 0, 8, 10, 2009) //starting date of Ramadan, change 5th param
	 * 		3.	Change in total number of days
	 * 		4. 	Remove extra dates in the calendar snippet from the beginning, (reset the numbering)
	 * 		5. 	Upload calendar snippet and ramadan.php model
	 * 	
	 */

	//1.
	function getDaysSinceRamadan(){ //tells you the day of Ramadan itself
		$uae_time = time() + (15 * 60 * 60) - (6 * 60);
		$days = floor(($uae_time - $this->getRamadanDate()) / (24 * 60 * 60)); 
		echo "<!--"; 
		echo $uae_time;
		echo "-->";
		return $days;
	}	

	//2. Start date of Ramadan
	function getRamadanDate(){
		return mktime(0, 0, 0, 8, 21, 2009); //starting date of Ramadan, change 5th param, should never less than 16
	}

	//3. Total Days of Ramadan, change if we have 29 days
	function getTotalDays(){
		return 28;
	}
	
	//Returning date itself as a digit 
	function getRamadanD(){
		return (int)date("d", $this->getRamadanDate());
	}
	
	function getTodayD(){
		return (int)date('d');
	}
	//TODO remove language from model
	function getFazzouraIDOfTheDay($language = 'en'){ //this function is tested on the server, works well
		if($this->ramadan->getDaysSinceRamadan() == 0){ //if it's the first day of Ramadan
			return 1;
		} else if($this->ramadan->getDaysSinceRamadan() >= $this->ramadan->getTotalDays()) { //if ramadan is over
			return $this->ramadan->getTotalDays();	
		} else {
			return $this->ramadan->getDaysSinceRamadan(); //the nth of Ramadan itself
		}		
	}

}

?>