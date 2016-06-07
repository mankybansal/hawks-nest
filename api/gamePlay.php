<?php

	//error_reporting(0);

	//INCLUDE HAWK ACHIEVEMENTS
	include 'achievements.php';

	function apiValidate($apiKey)
	{
		if($apiKey=="fUJxtW62tresIB7m")
			return true;
		else
			return false;
	}

	class response
	{
		private $httpCode;
		public $status, $responseType, $data;
		public function __construct($responseType, $data, $httpCode)
		{
			$this->responseType = $responseType;
			$this->data = $data;
			$this->httpCode = $httpCode;

			if($httpCode!=200)
			{
				$this->status = "bad";
			}
			else
			{
				$this->status = "good";
			}
		}
		public function sendToClient() {

			http_response_code($this->httpCode);
			echo json_encode($this);
		}
	}

	class connection
	{

		public $result;
		public $host = 'localhost'; // Host name
		public $user = 'root';
		public $password = 'sierrazulufoxtrotindia';
		public $db = 'hawkeye';
		public $dbc;
		function __construct($query="")
		{
			$con = mysqli_connect($this->host, $this->user, $this->password, $this->db);
			if(mysqli_errno($con))
				echo "THE HAWK COULD NOT REACH IT'S NEST (MYSQL ERROR).";
			else
			{

				$this->dbc = $con; // assign $con to $dbc

				if($query!="")
					$this->queryData($query);
				//echo "connected ";
			}
		}

		function queryData($query)
		{
			$this->result = $this->dbc->query($query);
		}
		function __destruct(){
			global $con;
			mysqli_close($con);
		}

	}

	class sessionInvalidException extends Exception
	{
		public function errorMessage() {
			$errorMsg = "SESSION INVALID";
			return $errorMsg;
		}
	}

	class player
	{
		public $ID, $NAME, $EMAIL, $PASSWORD, $PHONE, $COLLEGE, $LEVEL;
		public function __construct($sessionID)
		{

			try{
				$mysqli = new connection();
				$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
				$this->getInfo($sessionID);
			}
			catch(sessionInvalidException $e){
				throw new sessionInvalidException();
			}
		}

		//SET USER INFO
		public function setInfo($ID, $NAME, $EMAIL, $PASSWORD, $PHONE, $COLLEGE, $LEVEL)
		{
			$this->ID = $ID;
			$this->NAME = $NAME;
			$this->EMAIL = $EMAIL;
			$this->PASSWORD = $PASSWORD;
			$this->PHONE = $PHONE;
			$this->COLLEGE = $COLLEGE;
			$this->LEVEL = $LEVEL;
		}

		//GET USER INFO
		public function showInfo()
		{
			$myPlayer = array(
				'NAME'    	  => $this->NAME,
				'EMAIL'		  => $this->EMAIL,
				'PHONE' 	  => $this->PHONE,
				'COLLEGE' 	  => $this->COLLEGE,
				'LEVEL' 	  => $this->LEVEL
			);
			return $myPlayer;
		}

		public function getInfo($sessionID)
		{
			$mysqli = new connection();
			$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));

			$query = "SELECT * FROM sessions WHERE ID = '".$sessionID."'";
			$result = $mysqli->dbc->query($query);
			$count = $result->num_rows;

			if($count == 1)
			{
				$row = $result->fetch_assoc();
				$userID =  $row['USER_ID'];
				$explode = explode("-",$userID);
				$playerID = $explode[1];

				if($explode[0]!='P') throw new sessionInvalidException();

				$query = "SELECT * FROM players WHERE ID='".$playerID."'";
				$result = $mysqli->dbc->query($query);
				$row = $result->fetch_assoc();

				$this->setInfo($row['ID'],$row['NAME'],$row['EMAIL'],$row['PASSWORD'],$row['PHONE'],$row['COLLEGE'],$row['LEVEL']);

				$mysqli->dbc->close();
			}
			else
			{
				$mysqli->dbc->close();
				throw new sessionInvalidException();
			}

		}
	}

	class moderator
	{
		public $ID, $NAME, $EMAIL, $PASSWORD;
		public function __construct($sessionID)
		{

			try{
				$mysqli = new connection();
				$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
				$this->getInfo($sessionID);
			}
			catch(sessionInvalidException $e)
			{
				throw new sessionInvalidException();
			}
		}

		//SET USER INFO
		public function setInfo($ID, $NAME, $EMAIL, $PASSWORD)
		{
			$this->ID = $ID;
			$this->NAME = $NAME;
			$this->EMAIL = $EMAIL;
			$this->PASSWORD = $PASSWORD;
		}

		//GET USER INFO
		public function showInfo()
		{
			$myUser = array(
				'NAME'    	 => $this->NAME,
				'EMAIL'		 => $this->EMAIL
			);
			return $myUser;
		}

		public function getInfo($sessionID)
		{

			$mysqli = new connection();
			$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
			$query = "SELECT * FROM sessions WHERE ID = '".$sessionID."'";
			$result = $mysqli->dbc->query($query);
			$count = $result->num_rows;

			if($count == 1)
			{
				$row = $result->fetch_assoc();
				$userID =  $row['USER_ID'];
				$explode = explode("-",$userID);
				$moderatorID = $explode[1];

				if($explode[0]!='M') throw new sessionInvalidException();
				$query = "SELECT * FROM moderators WHERE ID='".$moderatorID."'";
				$result = $mysqli->dbc->query($query);
				$row = $result->fetch_assoc();

				$this->setInfo($row['ID'],$row['NAME'],$row['EMAIL'],$row['PASSWORD']);

				$mysqli->dbc->close();
			}
			else
			{
				$mysqli->dbc->close();
				throw new sessionInvalidException();
			}
		}
	}

	function getPlayer($sessionID)
	{

		try {
			// Connect to Database
			$mysqli = new connection();
			$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
			// Retrieve session data
			$myPlayer = new player($sessionID);

			$response = new response('getPlayer', $myPlayer->showInfo(), 200);
			return $response;
		}
		catch(sessionInvalidException $e) {
			$response = new response('getPlayer', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function getModerator($sessionID)
	{

		try {
			// Connect to Database
			$mysqli = new connection();
			$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
			// Retrieve session data
			$myModerator = new moderator($sessionID);

			$response = new response('getModerator', $myModerator->showInfo(), 200);
			return $response;
		}
		catch(sessionInvalidException $e) {
			$response = new response('getModerator', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function sessionValidate($sessionID)
	{

		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		$query = "SELECT * FROM sessions WHERE ID = '".$sessionID."'";
		$result = $mysqli->dbc->query($query);
		$count = $result->num_rows;
		if($count == 0) return false;
		else return true;
	}
	function getUserIP()
	{
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
			$MY_IP = $_SERVER['HTTP_CLIENT_IP'];
		elseif(!empty($_SERVER['REMOTE_ADDR']))
			$MY_IP = $_SERVER['REMOTE_ADDR'];
		else
			$MY_IP = "UNKNOWN";
		return $MY_IP;
	}

	function encryptIt($input)
	{
		$mysqli = new connection();
		$input = mysqli_real_escape_string($mysqli->dbc,stripslashes($input));
		$cryptKey  = $input;
		$qEncoded  = base64_encode(mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5($cryptKey), $input, MCRYPT_MODE_CBC, md5(md5($cryptKey))));
		return($qEncoded);
	}
	function decryptIt($input)
	{
		$mysqli = new connection();
		$input = mysqli_real_escape_string($mysqli->dbc,stripslashes($input));
		$cryptKey  = $input;
		$qDecoded  = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($input), MCRYPT_MODE_CBC, md5(md5($cryptKey))), "\0");
		return($qDecoded);
	}
	function playerLogin($myEmail, $myPassword)
	{
		$mysqli = new connection();
		$myEmail = mysqli_real_escape_string($mysqli->dbc,stripslashes($myEmail));
		$myPassword = mysqli_real_escape_string($mysqli->dbc,stripslashes($myPassword));
		$mysqli = new connection("SELECT * FROM players WHERE EMAIL='".$myEmail."' AND PASSWORD='".$myPassword ."' LIMIT 1");
		$result = $mysqli->result;
		$count = $result->num_rows;
		if($myEmail == "" || $myPassword == "")
		{
			$mysqli->dbc->close();

			$response = new response('playerLogin', "LOGIN-EMPTY", 200);
			return $response;
		}
		else if($count == 1)
		{
			$row = $result->fetch_assoc();

			//GET THE IP ADDRESS OF THE USER
			$playerIP = getUserIP();

			// Log out of Previous sessions
			$mysqli->queryData("DELETE FROM sessions WHERE USER_ID = 'P-".$row['ID']."'");

			// Save SESSION Data in DATABASE
			$mysqli->queryData("INSERT INTO sessions (USER_ID, USER_IP, LOGIN_TIMESTAMP) VALUES ('P-".$row['ID']."','".$playerIP."', NOW())");
			//GET SESSION ID
			$mysqli->queryData("SELECT * FROM sessions WHERE ID = LAST_INSERT_ID() AND USER_ID = 'P-".$row['ID']."'");
			$row = $mysqli->result->fetch_assoc();
			$sessionID = $row['ID'];

			$mysqli->dbc->close();

			$response = new response('playerLogin', array('sessionID' => $sessionID), 200);
			return $response;
		}
		else
		{
			//GET THE IP ADDRESS OF THE USER
			$MY_IP = getUserIP();

			$mysqli->dbc->close();
			// WRITE FAILED LOGIN CODE HERE

			$response = new response('playerLogin', "LOGIN-INVALID", 200);
			return $response;
		}

	}

	function moderatorLogin($myEmail, $myPassword)
	{

		$mysqli = new connection();

		$myEmail = mysqli_real_escape_string($mysqli->dbc,stripslashes($myEmail));
		$myPassword = substr(encryptIt(mysqli_real_escape_string($mysqli->dbc,stripslashes($myPassword))),0,8);
		$query = "SELECT * FROM moderators WHERE EMAIL='".$myEmail."' AND PASSWORD='".$myPassword ."' LIMIT 1";
		$result = $mysqli->dbc->query($query);
		$count = $result->num_rows;
		if($myEmail == "" || $myPassword == "")
		{
			$mysqli->dbc->close();

			$response = new response('moderatorLogin', "LOGIN-EMPTY", 200);
			return $response;
		}
		else if($count==1)
		{
			$row = $result->fetch_assoc();

			//GET THE IP ADDRESS OF THE USER
			$playerIP = getUserIP();

			// Log out of Previous sessions
			$query = "DELETE FROM sessions WHERE USER_ID = 'M-".$row['ID']."'";
			$mysqli->dbc->query($query);
			// Save SESSION Data in DATABASE
			$query = "INSERT INTO sessions (USER_ID, USER_IP, LOGIN_TIMESTAMP) VALUES ('M-".$row['ID']."','".$playerIP."', NOW())";
			$mysqli->dbc->query($query);

			//GET SESSION ID
			$query = "SELECT * FROM sessions WHERE ID = LAST_INSERT_ID() AND USER_ID = 'M-".$row['ID']."'";
			$result = $mysqli->dbc->query($query);

			$row = $result->fetch_assoc();
			$sessionID = $row['ID'];

			$mysqli->dbc->close();

			$response = new response('moderatorLogin', array('sessionID' => $sessionID), 200);
			return $response;
		}
		else
		{
			//GET THE IP ADDRESS OF THE USER
			$MY_IP = getUserIP();

			$mysqli->dbc->close();
			// WRITE FAILED LOGIN CODE HERE

			$response = new response('moderatorLogin', "LOGIN-INVALID", 200);
			return $response;
		}

	}

	function getQuestion($sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		try {
			// Retrieve session data
			$myPlayer = new player($sessionID);

			// Connect to Database
			$mysqli = new connection("SELECT * FROM questions WHERE LEVEL = '".$myPlayer->LEVEL."' LIMIT 1");
			$row = $mysqli->result->fetch_assoc();

			if($mysqli->result->num_rows==0)
			{
				$response = new response('getQuestion', "NO-MORE-QUESTIONS", 200);
				return $response;
			}

			$response = new response('getQuestion', array('questionText'=>$row['QUESTION'],'questionIMG'=>$row['IMG_LINK'],'questionTitle'=>$row['TITLE']), 200);
			return $response;
		}
		catch(sessionInvalidException $e) {
			$response = new response('getQuestion', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function setQuestion($myQuestion, $myAnswer, $imgLink, $sessionID)
	{

		// STRIPSLASHES
		$mysqli = new connection();
		$myQuestion = mysqli_real_escape_string($mysqli->dbc,stripslashes($myQuestion));
		$myAnswer = mysqli_real_escape_string($mysqli->dbc,stripslashes($myAnswer));
		$imgLink = mysqli_real_escape_string($mysqli->dbc,stripslashes($imgLink));

		if($myAnswer=="" || $myQuestion=="")
		{
			$response = new response('setQuestion', "EMPTY-QUESTION-SET", 200);
			return $response;
		}

		try {
			// Retrieve session data
			$myModerator = new moderator($sessionID);

			// Connect to Database

			if($imgLink=="") $imgLink = 'NA';

			$mysqli = new connection("SELECT * FROM questions WHERE QUESTION='".$myQuestion."' AND ANSWER='".$myAnswer."' LIMIT 1");

			if($mysqli->result->num_rows!=0)
			{
				$response = new response('setQuestion', "QUESTION-ALREADY-SET", 200);
				return $response;
			}


			$mysqli->queryData("SELECT LEVEL FROM questions ORDER BY LEVEL DESC LIMIT 1");
			$row = $mysqli->result->fetch_assoc();

			if($mysqli->result->num_rows!=0)
			{
				$mysqli->queryData("INSERT INTO questions (LEVEL,QUESTION,ANSWER,QUESTION_STATUS,IMG_LINK, TITLE) VALUES('".($row['LEVEL']+1)."','".$myQuestion."','".$myAnswer."','ACTIVE','".$imgLink."', 'FALSE')");
			}
			else
			{
				$mysqli->queryData("INSERT INTO questions (LEVEL,QUESTION,ANSWER,QUESTION_STATUS,IMG_LINK, TITLE) VALUES('1','".$myQuestion."','".$myAnswer."','ACTIVE','".$imgLink."',  'FALSE')");
			}

			$response = new response('setQuestion', "QUESTION-SET", 200);
			return $response;
		}
		catch(sessionInvalidException $e) {
			$response = new response('setQuestion', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function setHint($myHint, $level, $sessionID)
	{

		// STRIPSLASHES
		$mysqli = new connection();
		$myHint = mysqli_real_escape_string($mysqli->dbc,stripslashes($myHint));
		$level = mysqli_real_escape_string($mysqli->dbc,stripslashes(stripslashes($level)));
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		if($myHint=="" || $level=="")
		{
			$response = new response('setHint', "EMPTY-HINT", 200);
			return $response;
		}

		try {
			// Retrieve session data
			$myModerator = new moderator($sessionID);

			$mysqli = new connection("SELECT * FROM questions WHERE LEVEL = '".$level."' LIMIT 1");
			if($mysqli->result->num_rows!=0)
			{

				$row = $mysqli->result->fetch_assoc();

				$questionID = $row['ID'];



				$mysqli2 = new connection("SELECT * FROM hints WHERE QUESTION_ID='".$questionID."' AND HINT='".$myHint."' LIMIT 1");
				if($mysqli2->result->num_rows!=0)
				{
					$response = new response('setHint', "HINT-ALREADY-SET", 200);
					return $response;
				}


				$mysqli->queryData("SELECT HINT_LEVEL FROM hints WHERE QUESTION_ID='".$questionID."' LIMIT 1");

				if($mysqli->result->num_rows!=0)
				{
					$row = $mysqli->result->fetch_assoc();
					$mysqli->queryData("INSERT INTO hints (QUESTION_ID, HINT_LEVEL, HINT) VALUES('".$questionID."','".($row['HINT_LEVEL']+1)."', '".$myHint."')");
				}
				else
				{
					$mysqli->queryData("INSERT INTO hints (QUESTION_ID, HINT_LEVEL, HINT) VALUES('".$questionID."','0', '".$myHint."')");
				}

				$response = new response('setHint', "HINT-SET", 200);
				return $response;
			}

			$response = new response('setHint', "INVALID", 200);
			return $response;

		}
		catch(sessionInvalidException $e) {
			$response = new response('setHint', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function deleteQuestion($questionID, $sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));

		if($questionID=="")
		{
			$response = new response('deleteQuestion', "ID-EMPTY", 200);
			return $response;
		}

		try {
			// Retrieve session data
			$myModerator = new moderator($sessionID);


			$mysqli = new connection("SELECT * FROM questions WHERE ID=".$questionID." LIMIT 1");
			if($mysqli->result->num_rows!=0)
			{
				$mysqli->queryData("DELETE FROM questions WHERE ID=".$questionID." LIMIT 1");
				$response = new response('deleteQuestion', "DELETED", 200);
				return $response;
			}
			else
			{
				$response = new response('deleteQuestion', "ID-INVALID", 200);
				return $response;
			}
		}
		catch(sessionInvalidException $e) {
			$response = new response('deleteQuestion', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function deleteHint($hintID, $sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		if($hintID=="")
		{
			$response = new response('deleteHint', "ID-EMPTY", 200);
			return $response;
		}

		try {
			// Retrieve session data
			$myModerator = new moderator($sessionID);


			$mysqli = new connection("SELECT * FROM hints WHERE ID=".$hintID." LIMIT 1");
			if($mysqli->result->num_rows!=0)
			{
				$mysqli->queryData("DELETE FROM hints WHERE ID=".$hintID." LIMIT 1");
				$response = new response('deleteHint', "DELETED", 200);
				return $response;
			}
			else
			{
				$response = new response('deleteHint', "ID-INVALID", 200);
				return $response;
			}
		}
		catch(sessionInvalidException $e) {
			$response = new response('deleteHint', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function getAllQuestions($sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		try {

			// Get Moderator Data
			$myModerator = new moderator($sessionID);

			// Connect to Database
			$mysqli = new connection("SELECT * FROM questions");
			$result = $mysqli->result;
			if($result)
			{


				$questions = array();
				while($row = $result->fetch_assoc())
				{

					$mysqli2 = new connection("SELECT * FROM hints WHERE QUESTION_ID = '".$row['ID']."' ORDER BY HINT_LEVEL ASC");
					$result2 = $mysqli2->result;
					if($result2)
					{
						$hints = array();
						while($row2 = $result2->fetch_assoc())
							array_push($hints, array("ID" => $row2['ID'], "HINT" => $row2['HINT']));
					}

					array_push($questions, array("ID" => $row['ID'], "LEVEL" => $row['LEVEL'], "QUESTION" => $row['QUESTION'], "ANSWER" => $row["ANSWER"], "IMG_LINK" => $row["IMG_LINK"], "HINTS" => $hints));
				}
				$response = new response('getAllQuestions', $questions, 200);
				return $response;
			}
		}
		catch(sessionInvalidException $e) {
			$response = new response('getAllQuestions', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function getHints($sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		try {

			// Get Player Data
			$myPlayer = new player($sessionID);

			// Connect to Database
			$mysqli = new connection("SELECT * FROM questions WHERE LEVEL = '".$myPlayer->LEVEL."' LIMIT 1");
			$row = $mysqli->result->fetch_assoc();

			$mysqli->queryData("SELECT * FROM hints WHERE QUESTION_ID = '".$row['ID']."' ORDER BY HINT_LEVEL ASC");
			$result = $mysqli->result;
			if($result)
			{
				$hints = array();
				while($row = $result->fetch_assoc())
					array_push($hints, $row['HINT']);
				$response = new response('getHints', $hints, 200);
				return $response;
			}
		}
		catch(sessionInvalidException $e) {
			$response = new response('getHints', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function getRecent($sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		try {

			// Get Player Data
			$myPlayer = new player($sessionID);

			// Connect to Database
			$mysqli = new connection("SELECT * FROM questions WHERE LEVEL = '".$myPlayer->LEVEL."' LIMIT 1");
			$row = $mysqli->result->fetch_assoc();
			$mysqli->queryData("SELECT * FROM logs WHERE QUESTION_ID = '".$row['ID']."' AND USER_ID='".$myPlayer->ID."'  ORDER BY TIMESTAMP DESC LIMIT 5");
			$result = $mysqli->result;


			if($result)
			{
				$recents = array();
				while($row = $result->fetch_assoc())
					array_push($recents, $row['ANSWER']);
				$response = new response('getRecent', $recents, 200);
				return $response;
			}
		}
		catch(sessionInvalidException $e) {
			$response = new response('getRecent', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function getPreviousQuestions($sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		try {

			// Get Player Data
			$myPlayer = new player($sessionID);

			// Connect to Database
			$mysqli = new connection("SELECT * FROM questions WHERE LEVEL < '".$myPlayer->LEVEL."'");
			$result = $mysqli->result;

			if($result)
			{
				$questions = array();
				while($row = $result->fetch_assoc())
					array_push($questions, Array('question'=>$row['QUESTION'],'level'=>$row['LEVEL'],'image'=>$row['IMG_LINK'],'title'=>$row['TITLE']));
				$response = new response('getPreviousQuestions', $questions, 200);
				return $response;
			}
		}
		catch(sessionInvalidException $e) {
			$response = new response('getPreviousQuestions', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function checkAnswer($sessionID, $myAnswer)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		achievementResponse('cyberCow',$sessionID,null,1,false,false);
		achievementResponse('riteOfPassage',$sessionID,null,7,false,false);
		achievementResponse('justGettingStarted',$sessionID,null,8,false,false);
		achievementResponse('over9000',$sessionID,null,9,false,false);
		achievementResponse('gettingCloser',$sessionID,null,10,false,false);
		achievementResponse('feelingTheWind',$sessionID,null,11,false,false);
		achievementResponse('theHawksNest',$sessionID,null,12,false,false);

		// STRIPSLASHES
		$myAnswer = mysqli_real_escape_string($mysqli->dbc,stripslashes($myAnswer));

		if($myAnswer=="")
		{
			//ACHIEVEMENT BADGE hahaNoob (4)
			achievementResponse('hahaNoob',$sessionID,null,4,false,false);
			$response = new response('checkAnswer', "EMPTY-ANSWER", 200);
			return $response;
		}

		try {

			// Retrieve session data
			$myPlayer = new player($sessionID);

			// Connect to Database
			$mysqli = new connection("SELECT * FROM questions WHERE LEVEL = '".$myPlayer->LEVEL."' LIMIT 1");
			$row = $mysqli->result->fetch_assoc();
			$answer = $row['ANSWER'];

			$answer = trim(strtolower($answer));
			$myAnswer = trim(strtolower($myAnswer));

			$answer = str_replace(' ', '', $answer);
			$myAnswer = str_replace(' ', '', $myAnswer);
			if($answer==$myAnswer)
			{
				$mysqli->queryData("INSERT INTO logs (ANSWER,QUESTION_ID,USER_ID, TIMESTAMP) VALUES('".$myAnswer."','".$row['ID']."','".$myPlayer->ID."',NOW())");
				$mysqli->queryData("UPDATE players SET LEVEL = LEVEL + 1 WHERE ID='".$myPlayer->ID."'");

				//ACHIEVEMENT BADGE firstBlood (2)
				achievementResponse('firstBlood',$sessionID,$myPlayer->LEVEL,2,true,false);

				//ACHIEVEMENT BADGE weAreWatching (5)
				achievementResponse('weAreWatching',$sessionID,null,5,false,false);

				achievementResponse('riteOfPassage',$sessionID,null,7,false,false);
				achievementResponse('justGettingStarted',$sessionID,null,8,false,false);
				achievementResponse('over9000',$sessionID,null,9,false,false);
				achievementResponse('gettingCloser',$sessionID,null,10,false,false);
				achievementResponse('feelingTheWind',$sessionID,null,11,false,false);
				achievementResponse('theHawksNest',$sessionID,null,12,false,false);

				// INCREASE PLAYER LEVEL
				++$myPlayer->LEVEL;
				$response = new response('checkAnswer', true, 200);
				return $response;
			}
			else
			{

				$mysqli->queryData("INSERT INTO logs (ANSWER,QUESTION_ID,USER_ID, TIMESTAMP) VALUES('".$myAnswer."','".$row['ID']."','".$myPlayer->ID."',NOW())");

				$threshold  = 100;

				similar_text($answer,$myAnswer, $percentage);

				if(strlen($answer)>20)
					$threshold = 70;
				else if(strlen($answer)<20 && strlen($answer)>10)
					$threshold = 80;
				else if(strlen($answer)<=10)
					$threshold = 85;
				//ACHIEVEMENT BADGE epicFail (3)
				achievementResponse('epicFail',$sessionID,$myPlayer->LEVEL,3,true,false);

				if($percentage>=$threshold)
					$response = new response('checkAnswer', "ANSWER-CLOSE", 200);
				else
					$response = new response('checkAnswer', false, 200);
				return $response;
			}
		}
		catch(sessionInvalidException $e) {
			$response = new response('checkAnswer', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function getMyPosition($sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		try {
			// Connect to Database
			$mysqli = new connection();

			// Retrieve session data
			$myPlayer = new player($sessionID);


			$query = "SELECT * FROM players";
			$result = $mysqli->dbc->query($query);
			$total = $result->num_rows;
			//REMOVE CURRENT PLAYER FROM TOTAL COUNT
			$total -= 1;
			$query = "SELECT * FROM players WHERE LEVEL = ".$myPlayer->LEVEL."";
			$result = $mysqli->dbc->query($query);
			$same = $result->num_rows;
			//REMOVE CURRENT PLAYER FROM SAME COUNT
			$same -= 1;
			$query = "SELECT * FROM players WHERE LEVEL > ".$myPlayer->LEVEL."";
			$result = $mysqli->dbc->query($query);
			$leaders = $result->num_rows;
			$trailers = $total-$leaders-$same;

			$postion = array('leaders'=>$leaders,'same'=>$same,'trailers'=>$trailers);

			$response = new response('getMyPosition', $postion , 200);
			return $response;
		}
		catch(sessionInvalidException $e) {
			$response = new response('getMyPosition', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function register($name, $email, $password, $phone, $college)
	{
		$mysqli = new connection();
		if($name=="" || $email=="" || $password=="" || $phone=="" || $college=="" || strlen($phone)!=10)
		{
				$response = new response('register', "PARAMS-INVALID", 200);
				return $response;
		}
		else
		{
			// Connect to Database
			$name = mysqli_real_escape_string($mysqli->dbc,stripslashes($name));
			$email = mysqli_real_escape_string($mysqli->dbc,stripslashes($email));
			$password = mysqli_real_escape_string($mysqli->dbc,stripslashes($password));
			$phone = mysqli_real_escape_string($mysqli->dbc,stripslashes($phone));
			$college = mysqli_real_escape_string($mysqli->dbc,stripslashes($college));
			$mysqli = new connection("SELECT * FROM players WHERE EMAIL = '".$email."' OR PHONE = '".$phone."' LIMIT 1");
			$count = $mysqli->result->num_rows;

			if($count == 0)
			{
				// ADD PLAYER DATA TO DATABASE
				$mysqli->queryData("INSERT INTO players (NAME,EMAIL,PASSWORD,PHONE,COLLEGE) VALUES ('".$name."','".$email."','".$password."','".$phone."','".$college."')");

				$mysqli->dbc->close();
				$response = new response('register', null, 200);
				return $response;
			}
			else
			{
				$mysqli->dbc->close();
				$response = new response('register',"USER-EXISTS", 200);
				return $response;
			}
		}
	}

	function playerLogout($sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		try {
			$myPlayer = new player($sessionID);
			$mysqli = new connection("DELETE FROM sessions WHERE ID = '".$sessionID."'");
			$response = new response('logout', null , 200);
			return $response;
		}
		catch(sessionInvalidException $e) {
			$response = new response('logout', "SESSION-INVALID", 200);
			return $response;
		}
	}

	function moderatorLogout($sessionID)
	{
		$mysqli = new connection();
		$sessionID = mysqli_real_escape_string($mysqli->dbc,stripslashes($sessionID));
		try {

			$myModerator = new moderator($sessionID);

			$mysqli = new connection("DELETE FROM sessions WHERE ID = '".$sessionID."'");
			$response = new response('logout', null , 200);
			return $response;
		}
		catch(sessionInvalidException $e) {
			$response = new response('logout', "SESSION-INVALID", 200);
			return $response;
		}

	}

	function forgotPassword($email)
	{
		$mysqli = new connection();
		$email = mysqli_real_escape_string($mysqli->dbc,stripslashes($email));
		$mysqli = new connection("SELECT * FROM players WHERE EMAIL='".$email."'");
		if($mysqli->result->num_rows>0)
		{

			$CHARS = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			$password = substr(str_shuffle($CHARS),0,8);

			$msg = "<html>
					Hi <b>$email</b>,<br><br>

					We see that you've been having issues logging into your account.
					<br>Use the new password: <b>".$password."</b> to continue. <br>
					Contact one of the organizers if there are any further issues.<br><br>
					Enjoy the game!<br><br>

					Regards,<br>
					<b>HawkEye Team</b>
					</html>
					";

			$to = $email;
			$subject = "Reset Password";
			$txt = $msg;
			// Always set content-type when sending HTML email
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= "From: noreply@hawkeye.iecse.xyz";
			mail($to,$subject,$txt,$headers);

			$mysqli->queryData("UPDATE players SET PASSWORD = '".$password."' WHERE EMAIL='".$email."'");


			$response = new response('forgotPassword', "EMAIL-SENT", 200);
			return $response;
		}
		else
		{
			$response = new response('forgotPassword', "EMAIL-INVALID", 200);
			return $response;
		}

	}

	function getMessages()
	{
		$mysqli = new connection("SELECT * FROM players WHERE EMAIL!='sunny.bansal@gmail.com' ORDER BY LEVEL DESC  LIMIT 1");
		$row = $mysqli->result->fetch_assoc();
		$highestLevel = $row['LEVEL'];

		$mysqli = new connection("
			SELECT NAME,EMAIL,LEVEL FROM players
			WHERE EMAIL!='sunny.bansal@gmail.com'
				AND EMAIL!='hv.sawal@gmail.com'
				AND EMAIL!='siddu.druid@gmail.com'
				AND EMAIL!='karan1866@gmail.com'
			ORDER BY LEVEL DESC
			LIMIT 10
		");

		$top10 = array();

		while($row = $mysqli->result->fetch_assoc())
		{
			array_push($top10, $row);
		}
		$mysqli = new connection("SELECT * FROM sessions");
		$onlineCount = $mysqli->result->num_rows;

		$mysqli = new connection("SELECT * FROM logs");
		$wrongAnswers = $mysqli->result->num_rows;

		$mysqli = new connection("SELECT * FROM players WHERE LEVEL >= ".floor($highestLevel/2)." GROUP BY LEVEL ORDER BY COUNT(*) DESC LIMIT 1");
		$row = $mysqli->result->fetch_assoc();
		$maxPlayers = $row['LEVEL'];

		$response = new response('getMessages', array('result' => $top10 , 'onlineCount' => $onlineCount, 'highestLevel' => $highestLevel, 'wrongAnswers' => $wrongAnswers, 'maxPlayers' => $maxPlayers), 200);
		return $response;
	}

?>
