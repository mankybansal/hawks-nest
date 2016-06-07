
<?php

	//error_reporting(0);

	date_default_timezone_set('Asia/Calcutta');

	//INCLUDE HAWK GAMEPLAY
	include 'gamePlay.php';

	$_GET['SECURITY'] = "OFF";

	if(isset($_GET['SECURITY']) && $_GET['SECURITY'] == "OFF")
	{
	}
	else
	{
		$whitelist = array('::1','1.186.11.243','172.0.0.1','104.236.74.216/');
		$servers = array('localhost','librorum.in','cyberhawk.iecsemanipal.com');

		if(!in_array($_SERVER['REMOTE_ADDR'],$whitelist) && !in_array($_SERVER['SERVER_NAME'],$servers) )
		{
			$response = new response('apiError',"REQUEST-SERVER-UNAUTHORIZED", 200);
			$response->sendToClient();
			exit;
		}
	}

	if(isset($_GET['API_KEY'])==false || !apiValidate($_GET['API_KEY']))
	{
		$response = new response('apiError',"INVALID-KEY", 200);
		$response->sendToClient();
		exit;
	}


	function rateLimit($remoteAddress)
	{
		$mysqli = new connection("SELECT * FROM requests WHERE REQUEST_IP = '".$remoteAddress."' ORDER BY ID DESC LIMIT 1");

		if($mysqli->result->num_rows>0)
		{
			$row = $mysqli->result->fetch_assoc();
			$seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($row['TIMESTAMP']);

			if($seconds<1)
			{
				$response = new response('apiError',"TOO-MANY-REQUESTS", 200);
				$response->sendToClient();
				exit;
			}
		}

		$mysqli->queryData("SELECT * FROM requests WHERE REQUEST_IP = '".$remoteAddress."'");
		if($mysqli->result->num_rows>=20)
			$mysqli->queryData("DELETE FROM requests WHERE REQUEST_IP='".$remoteAddress."'");

		$mysqli->queryData("INSERT INTO requests (REQUEST_IP,TIMESTAMP) VALUES ('".$remoteAddress."',NOW())");
	}



	if(isset($_GET['REQUEST']))
	{
		if($_GET['REQUEST']=='playerLogin')
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			if(isset($_GET['EMAIL']) && isset($_GET['PASSWORD']))
			{
				$response = playerLogin($_GET['EMAIL'],$_GET['PASSWORD']);
				$response->sendToClient();
			}
			else invalidParams("playerLogin");
		}
		else if($_GET['REQUEST']=='forgotPassword')
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			if(isset($_GET['EMAIL']))
			{
				$response = forgotPassword($_GET['EMAIL']);
				$response->sendToClient();

			}
			else invalidParams("forgotPassword");
		}
		else if($_GET['REQUEST']=='moderatorLogin')
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			if(isset($_GET['EMAIL']) && isset($_GET['PASSWORD']))
			{
				$response = moderatorLogin($_GET['EMAIL'],$_GET['PASSWORD']);
				$response->sendToClient();
			}
			else invalidParams("moderatorLogin");
		}
		else if($_GET['REQUEST']=='setQuestion' && isset($_GET['sessionID']))
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			if(isset($_GET['QUESTION']) && isset($_GET['ANSWER']) && isset($_GET['IMG_LINK']))
			{
				$response = setQuestion($_GET['QUESTION'],$_GET['ANSWER'],$_GET['IMG_LINK'],$_GET['sessionID']);
				$response->sendToClient();
			}
			else invalidParams("setQuestion");
		}
		else if($_GET['REQUEST']=='setHint' && isset($_GET['sessionID']))
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			if(isset($_GET['HINT']) && isset($_GET['QUESTION_ID']))
			{
				$response = setHint($_GET['HINT'], $_GET['QUESTION_ID'] ,$_GET['sessionID']);
				$response->sendToClient();
			}
			else invalidParams("setQuestion");
		}
		else if($_GET['REQUEST']=='getPlayer' && isset($_GET['sessionID']))
		{
				$response = getPlayer($_GET['sessionID']);
				$response->sendToClient();
		}
		else if($_GET['REQUEST']=='getModerator' && isset($_GET['sessionID']))
		{
				$response = getModerator($_GET['sessionID']);
				$response->sendToClient();
		}
		else if($_GET['REQUEST']=='register')
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			if(isset($_GET['NAME']) && isset($_GET['EMAIL']) && isset($_GET['PASSWORD']) && isset($_GET['PHONE']) && isset($_GET['COLLEGE']))
			{
				$response = register($_GET['NAME'],$_GET['EMAIL'],$_GET['PASSWORD'],$_GET['PHONE'],$_GET['COLLEGE']);
				$response->sendToClient();
			}
			else invalidParams("register");
		}
		else if($_GET['REQUEST']=='deleteQuestion' && isset($_GET['sessionID']))
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			if(isset($_GET['QUESTION_ID']))
			{
				$response = deleteQuestion($_GET['QUESTION_ID'],$_GET['sessionID']);
				$response->sendToClient();
			}
			else invalidParams("deleteQuestion");
		}
		else if($_GET['REQUEST']=='getStats')
		{
			$response =  getMessages();
			$response->sendToClient();
		}
		else if($_GET['REQUEST']=='deleteHint' && isset($_GET['sessionID']))
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			if(isset($_GET['HINT_ID']))
			{
				$response = deleteHint($_GET['HINT_ID'],$_GET['sessionID']);
				$response->sendToClient();
			}
			else invalidParams("deleteQuestion");
		}
		else if($_GET['REQUEST']=='playerLogout' && isset($_GET['sessionID']))
		{
				$response = playerLogout($_GET['sessionID']);
				$response->sendToClient();
		}
		else if($_GET['REQUEST']=='getAllQuestions' && isset($_GET['sessionID']))
		{
				$response = getAllQuestions($_GET['sessionID']);
				$response->sendToClient();
		}
		else if($_GET['REQUEST']=='moderatorLogout' && isset($_GET['sessionID']))
		{
				$response = moderatorLogout($_GET['sessionID']);
				$response->sendToClient();
		}
		else if($_GET['REQUEST']=='getMyPosition' && isset($_GET['sessionID']))
		{
				$response = getMyPosition($_GET['sessionID']);
				$response->sendToClient();
		}
		else if($_GET['REQUEST']=='getQuestion' && isset($_GET['sessionID']))
		{
				$response = getQuestion($_GET['sessionID']);
				$response->sendToClient();
		}
		else if($_GET['REQUEST']=='getPreviousQuestions' && isset($_GET['sessionID']))
		{
				$response = getPreviousQuestions($_GET['sessionID']);
				$response->sendToClient();
		}
		else if($_GET['REQUEST']=='getHints' && isset($_GET['sessionID']))
		{
			$response = getHints($_GET['sessionID']);
			$response->sendToClient();
		}
		else if($_GET['REQUEST']=='getAchievements' && isset($_GET['sessionID']))
		{
			$response = getAchievements($_GET['sessionID']);
			$response->sendToClient();
		}
		else if($_GET['REQUEST']=='getRecent' && isset($_GET['sessionID']))
		{
			$response = getRecent($_GET['sessionID']);
			$response->sendToClient();
		}
		else if($_GET['REQUEST']=='checkAnswer' && isset($_GET['sessionID']))
		{
			rateLimit($_SERVER['REMOTE_ADDR']);
			$response = checkAnswer($_GET['sessionID'],$_GET['ANSWER']);
			$response->sendToClient();
		}
		else
		{
			$response = new response('apiError',"INVALID-METHOD", 200);
			$response->sendToClient();
			exit;
		}
	}
	else echo "What do you want?";

	function invalidParams($function)
	{
		$response = new response($function,"PARAMS-INVALID", 200);
		$response->sendToClient();
		exit;
	}



?>
