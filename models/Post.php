<?php
    class Post{
        protected $pdo;

        public function __construct(\PDO $pdo) {
			$this->pdo = $pdo;
            $this->gm = new GlobalMethods($pdo);
		}

        public function sendPayload($payload, $remarks, $message, $code) {
			$status = array("remarks"=>$remarks, "message"=>$message);
			http_response_code($code);
			return $this->gm->encryptData(array(
				"status"=>$status,
				"payload"=>$payload,
				'prepared_by'=>'The Wing Squad, Gordon College CCS',
				"timestamp"=>date_create()));
		} 
    }
?>