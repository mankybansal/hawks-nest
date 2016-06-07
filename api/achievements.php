<?php 

	//SET DEFAULT SERVER TIMEZONE TO INDIA
	date_default_timezone_set('Asia/Calcutta');

	
	//INCLUDE HAWK ACHIEVEMENTS
	//include 'gamePlay.php';
	
	function getAchievements($sessionID)
	{
		try {
			
			// Get Player Data
			$myPlayer = new player($sessionID);
	
			// Connect to Database
			$mysqli = new connection("SELECT * FROM achievements NATURAL JOIN badges WHERE PLAYER_ID = '".$myPlayer->ID."'");
			$result = $mysqli->result;
			
			if($result)
			{
				$achievements = array();
				while($row = $result->fetch_assoc())
					array_push($achievements, Array(
						'ACHIEVEMENT_ID'=>$row['ACHIEVEMENT_ID'],
						'BADGE_ID'=>$row['BADGE_ID'],
						'BADGE_NAME'=>$row['BADGE_NAME'],
						'BADGE_DESC'=>$row['BADGE_DESC'],
						'BADGE_PIC'=>$row['BADGE_PIC'],
						'PARAMS'=>$row['PARAMS']
					));
				$response = new response('getAchievements', $achievements, 200);
				return $response;
			}	
		}
		catch(sessionInvalidException $e) {
			$response = new response('getAchievements', "SESSION-INVALID", 400);
			return $response;
		}	
	}
	
	function achievementResponse($achievement,$sessionID,$level,$badgeID,$levelCheck,$dayCheck)
	{
		try {	
			// Get Player Data
			$myPlayer = new player($sessionID);
			
			if($levelCheck)
				$mysqli = new connection("SELECT * FROM achievements WHERE BADGE_ID = ".$badgeID." AND PARAMS = 'LEVEL=".$level."' AND PLAYER_ID =".$myPlayer->ID);
			else if($dayCheck)
			{
				
				$date = new DateTime('NOW');
					
				$date1 = new DateTime('2016-04-01T00:00:00');
				$date2 = new DateTime('2016-04-02T00:00:00');
				$date3 = new DateTime('2016-04-03T00:00:00');
				$date4 = new DateTime('2016-04-04T00:00:00');
				$date5 = new DateTime('2016-04-05T00:00:00');
				
				if($date > $date2 && $date < $date3){
					$mysqli = new connection("SELECT * FROM achievements WHERE BADGE_ID = ".$badgeID." AND PARAMS = 'DAY=1' AND PLAYER_ID =".$myPlayer->ID);		
				}
				else if($date > $date3 && $date < $date4){
					$mysqli = new connection("SELECT * FROM achievements WHERE BADGE_ID = ".$badgeID." AND PARAMS = 'DAY=2' AND PLAYER_ID =".$myPlayer->ID);
	
				}
				else if($date > $date4 && $date < $date5){
					$mysqli = new connection("SELECT * FROM achievements WHERE BADGE_ID = ".$badgeID." AND PARAMS = 'DAY=3' AND PLAYER_ID =".$myPlayer->ID);
				}	
				else {
					return false;
				}
			}
			else
				$mysqli = new connection("SELECT * FROM achievements WHERE BADGE_ID = ".$badgeID." AND PLAYER_ID =".$myPlayer->ID);
			
			$count = $mysqli->result->num_rows;
			if($count < 1) {
				if($achievement($myPlayer,$sessionID,$level)){
					$response = new response($achievement, "UNLOCKED", 200);
					return $response;;
				}
			}
			$response = new response($achievement, "NA", 200);
			return $response;
		}catch(sessionInvalidException $e){
			$response = new response($achievement, "SESSION-INVALID", 400);
			return $response;
		}
	}
	
	function cyberCow($myPlayer,$sessionID,$level)
	{
		$mysqli = new connection("INSERT INTO achievements (BADGE_ID,PLAYER_ID) VALUES ('1',".$myPlayer->ID.")");
		return true;
	}
	
	function firstBlood($myPlayer,$sessionID,$level)
	{
		$mysqli = new connection("SELECT * FROM questions WHERE LEVEL=".$level);
		$row = $mysqli->result->fetch_assoc();
		if($row['FIRST_PLAYER_ID'] == 'NA')
		{
			$mysqli->queryData("INSERT INTO achievements(BADGE_ID,PLAYER_ID,PARAMS) values('2',".$myPlayer->ID.",'LEVEL=".$level."')");
			$mysqli->queryData("UPDATE questions SET FIRST_PLAYER_ID = ".$myPlayer->ID." WHERE LEVEL = ".$level);
			
			return true;
		}
		return false;
	}
	
	function epicFail($myPlayer,$sessionID,$level)
	{
			$mysqli = new connection("SELECT * FROM questions WHERE LEVEL=".$level);
			$row = $mysqli->result->fetch_assoc();
			$mysqli->queryData("SELECT * FROM logs WHERE QUESTION_ID = ".$row['ID']." AND USER_ID=".$myPlayer->ID);
			$count = $mysqli->result->num_rows;
			if($count > 25)
			{
				$mysqli->queryData("INSERT INTO achievements(BADGE_ID,PLAYER_ID,PARAMS) values('3',".$myPlayer->ID.",'LEVEL=".$level."')");
				return true;
			}	
			return false;
	}
	
	function riteOfPassage($myPlayer,$sessionID,$level)
	{	
		if($myPlayer->LEVEL >= 2)
		{
			$mysqli = new connection("INSERT INTO achievements(BADGE_ID,PLAYER_ID) values('7',".$myPlayer->ID.")");
			return true;
		}
		return false;
	}
	
	function justGettingStarted($myPlayer,$sessionID,$level)
	{
		if($myPlayer->LEVEL >= 6)
		{
			$mysqli = new connection("INSERT INTO achievements(BADGE_ID,PLAYER_ID) values('8',".$myPlayer->ID.")");
			return true;
		}
		return false;
	}
	
	function over9000($myPlayer,$sessionID,$level)
	{
		if($myPlayer->LEVEL >= 11)
		{
			$mysqli = new connection("INSERT INTO achievements(BADGE_ID,PLAYER_ID) values('9',".$myPlayer->ID.")");
			return true;
		}
		return false;
	}
	
	function gettingCloser($myPlayer,$sessionID,$level)
	{
		if($myPlayer->LEVEL >= 16)
		{
			$mysqli = new connection("INSERT INTO achievements(BADGE_ID,PLAYER_ID) values('10',".$myPlayer->ID.")");
			return true;
		}
		return false;
	}
	
	function feelingTheWind($myPlayer,$sessionID,$level)
	{
		if($myPlayer->LEVEL >= 21)
		{
			$mysqli = new connection("INSERT INTO achievements(BADGE_ID,PLAYER_ID) values('11',".$myPlayer->ID.")");
			return true;
		}
		return false;
	}
	
	function theHawksNest($myPlayer,$sessionID,$level)
	{
		if($myPlayer->LEVEL >= 25)
		{
			$mysqli = new connection("INSERT INTO achievements(BADGE_ID,PLAYER_ID) values('12',".$myPlayer->ID.")");
			return true;
		}
		return false;
	}
	
	function hahaNoob($myPlayer,$sessionID,$level)
	{
		$mysqli = new connection("INSERT INTO achievements(BADGE_ID,PLAYER_ID) values('4',".$myPlayer->ID.")");
		return true;	
	}
		
	function theDailyProphet($myPlayer,$sessionID,$level)
	{
		return false;
	}
	
	function metTheHawk($myPlayer,$sessionID,$level)
	{
		return false;
	}
	
	function weAreWatching($myPlayer,$sessionID,$level)
	{
		$mysqli = new connection("
			SELECT 
				QUESTION_ID, MAX(TIMESTAMP) AS `TIME`
			FROM
				(SELECT 
					QUESTION_ID, TIMESTAMP
				FROM
					logs
				WHERE
					USER_ID = ".$myPlayer->ID.") AS `myLog`
			GROUP BY QUESTION_ID
			ORDER BY QUESTION_ID ASC;
		");

		$result = $mysqli->result;
		
		$array = array();
		while($row = $result->fetch_assoc())
		{
			array_push($array,array("QUESTION_ID" => $row['QUESTION_ID'], "TIME" => $row['TIME']));
		}
		$flag=0;
		for($i=0;$i<count($array); $i++)
		{
			$phpdate1 = strtotime($array[$i]['TIME']."+30 minutes");
			$time1 = date('Y-m-d H:i:s', $phpdate1);
			
			for($j=$i+1; $j<count($array);$j++)
			{
					$phpdate2 = strtotime($array[$j]['TIME']);
					$time2 = date('Y-m-d H:i:s', $phpdate2);
					$diff = $j - $i;
					if($time2<$time1 &&  $diff<5){
						continue;
					}else{
						if($diff >= 5) {
							$flag=1;
							break 2;
						}
						break;
					}
			}
		}
		
		if($flag==1){
			$mysqli->queryData("INSERT INTO ACHIEVEMENTS (BADGE_ID,PLAYER_ID) VALUES (5,".$myPlayer->ID.")");
			
			return true;
		}
		return false;
	}
	
	function hawksDNA($myPlayer,$sessionID,$level)
	{
		$dateNow = new DateTime('NOW');
	
		$date1 = new DateTime('2016-04-01T00:00:00');
		$date2 = new DateTime('2016-04-02T00:00:00');
		$date3 = new DateTime('2016-04-03T00:00:00');
		$date4 = new DateTime('2016-04-04T00:00:00');
		$date5 = new DateTime('2016-04-05T00:00:00');
			

		if($dateNow > $date2 && $dateNow < $date3)
		{
			// CHECK FOR 2016-04-01
			$mysql = new connection("SELECT DISTINCT(QUESTION_ID) FROM LOGS WHERE TIMESTAMP BETWEEN '".$date1->format('Y-m-d H:i:s')."' AND '".$date2->format('Y-m-d H:i:s')."' AND USER_ID=".$myPlayer->ID);
			$count = $mysql->result->num_rows;
			if($count >= 6){
				$mysql->queryData("INSERT INTO achievements(BADGE_ID,PLAYER_ID,PARAMS) values('14',".$myPlayer->ID.",'DAY=1')");
				return true;
			}
		}
		else if($dateNow > $date3 && $dateNow < $date4)
		{
			// CHECK FOR 2016-04-02
			$mysql = new connection("SELECT DISTINCT(QUESTION_ID) FROM LOGS WHERE TIMESTAMP BETWEEN '".$date2->format('Y-m-d H:i:s')."' AND '".$date3->format('Y-m-d H:i:s')."' AND USER_ID=".$myPlayer->ID);
			$count = $mysql->result->num_rows;
			if($count >= 6){
				$mysql->queryData("INSERT INTO achievements(BADGE_ID,PLAYER_ID,PARAMS) values('14',".$myPlayer->ID.",'DAY=2')");
				return true;
			}
		}
		else if($dateNow > $date4 && $dateNow < $date5)
		{
			// CHECK FOR 2016-04-01
			$mysql = new connection("SELECT DISTINCT(QUESTION_ID) FROM LOGS WHERE TIMESTAMP BETWEEN '".$date3->format('Y-m-d H:i:s')."' AND '".$date4->format('Y-m-d H:i:s')."' AND USER_ID=".$myPlayer->ID);
			$count = $mysql->result->num_rows;
			if($count >= 6){
				$mysql->queryData("INSERT INTO achievements(BADGE_ID,PLAYER_ID,PARAMS) values('14',".$myPlayer->ID.",'DAY=3')");
				return true;
			}
		}
		
		// DATE FOR CHECKING EXPIRED
		return false;
	}

	function dayOfReckoning($myPlayer,$sessionID,$level)
	{
		$date1 = new DateTime('NOW');
		$date2 = new DateTime('2016-04-03T00:00:00');
		
		if($date1>=$date2)
		{
			$mysqli = new connection("INSERT INTO ACHIEVEMENTS (BADGE_ID,PLAYER_ID) VALUES (15,".$myPlayer->ID.")");
			return true;
		}
		return false;
	}
	
	/*	
	var_dump(achievementResponse('metTheHawk',$sessionID,null,6,false,false));	
	var_dump(achievementResponse('theDailyProphet',$sessionID,null,13,false,true));
	var_dump(achievementResponse('hawksDNA',$sessionID,null,14,false,true));
	*/	
				
 ?>