<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.

class response 
	{
		private $httpCode;
		public 	$responseType, $data;

		public function __construct($responseType, $data, $httpCode)
		{
			$this->responseType = $responseType;
			$this->data = $data;
			$this->httpCode = $httpCode;
		}

		public function sendToClient() {
			http_response_code($this->httpCode);
			echo json_encode($this);
		}
	}
	

$uploaddir = 'uploads/';
$temp = explode(".", $_FILES["userfile"]["name"]);
$newfilename = round(microtime(true)) . '.' . end($temp);

if (move_uploaded_file($_FILES['userfile']['tmp_name'],  $uploaddir.$newfilename)) {
    $response = new response("imageUpload", $newfilename, 200);
	$response->sendToClient();
} else {
    $response = new response("imageUpload", "FAILED", 400);
	$response->sendToClient();
}
?>