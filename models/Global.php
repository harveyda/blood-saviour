<?php  
	class GlobalMethods {
		protected $pdo;

		public function __construct(\PDO $pdo) {
			$this->pdo = $pdo;
		}

		// MULTIPLE ROW INSERTION
		public function multiple_insert($table, $data){
            foreach($data as $x => $x_value) {
			$i = 0; $fields=[]; $values=[];
			foreach ($x_value as $key => $value) {
				array_push($fields, $key);
				array_push($values, $value);
			}

			try {
				$ctr = 0;
				$sqlstr="INSERT INTO $table (";
				foreach ($fields as $value) {
					$sqlstr.=$value; $ctr++;
					if($ctr<count($fields)) {
						$sqlstr.=", ";
					} 	
				} 
				$sqlstr.=") VALUES (".str_repeat("?, ", count($values)-1)."?)";

				$sql = $this->pdo->prepare($sqlstr);
				$sql->execute($values);
			} catch (\PDOException $e) {
				$errmsg = $e->getMessage();
				$code = 403;
                return $this->encryptData(array("code"=>$code, "errmsg"=>$errmsg));
			}
        }
            return $this->encryptData(array("code"=>200, "remarks"=>"success"));
		}

		// SELECT
		public function exec_query($table, $filter_data) {

			$this->sql = "SELECT * FROM $table";

			if($filter_data != null && $table == "tbl_users") {
				$this->sql .= " WHERE usr_email='$filter_data'";
			}

			if($filter_data != null && $table == "tbl_events") {
				$this->sql .= " WHERE event_id=$filter_data";
			}

			if($filter_data != null && $table == "tbl_requests") {
				$this->sql .= " WHERE usr_email='$filter_data' ORDER BY tbl_requests.req_DateAdded DESC";
			}

			if($filter_data != null && $table == "tbl_donations") {
				$this->sql .= " WHERE usr_email='$filter_data' ORDER BY tbl_donations.donation_DateAdded DESC";
			}

			if($filter_data != null && $table == "tbl_topics") {
				$this->sql .= " WHERE topic_id=$filter_data";
			}
			if($filter_data != null && $table == "tbl_answers") {
				$this->sql .= " WHERE answer_id=$filter_data";
			}
			if($filter_data != null && $table == "tbl_courses") {
				$this->sql .= " WHERE course_id=$filter_data";
			}

			if($filter_data != null && $table == "tbl_answer_votes") {
				$this->sql .= " WHERE usr_uname='$filter_data'";
			}
			if($filter_data != null && $table == "tbl_saved_topics") {
				$this->sql .= " WHERE usr_uname='$filter_data'";
			}
			if($filter_data != null && $table == "tbl_tags"){
				$this->sql .= " WHERE tag_name LIKE '%$filter_data%'";
			}
			

			$data = array(); $code = 0; $msg= ""; $remarks = "";
			try {
				if ($res = $this->pdo->query($this->sql)->fetchAll()) {
					foreach ($res as $rec) { array_push($data, $rec);}
					$res = null; $code = 200; $msg = "Successfully retrieved the requested records"; $remarks = "success";
				}
			} catch (\PDOException $e) {
				$msg = $e->getMessage(); $code = 401; $remarks = "failed";
			}
			return $this->sendPayload($data, $remarks, $msg, $code);
		}

		public function insert($table, $data){
			$i = 0; $fields=[]; $values=[];
			foreach ($data as $key => $value) {
				array_push($fields, $key);
				array_push($values, $value);
			}
			try {
				$ctr = 0;
				$sqlstr="INSERT INTO $table (";
				foreach ($fields as $value) {
					$sqlstr.=$value; $ctr++;
					if($ctr<count($fields)) {
						$sqlstr.=", ";
					} 	
				} 
				$sqlstr.=") VALUES (".str_repeat("?, ", count($values)-1)."?)";

				$sql = $this->pdo->prepare($sqlstr);
				$sql->execute($values);
				return $this->encryptData(array("code"=>200, "remarks"=>"success"));
			} catch (\PDOException $e) {
				$errmsg = $e->getMessage();
				$code = 403;
			}
			return $this->encryptData(array("code"=>$code, "errmsg"=>$errmsg));
		}

		public function update($table, $data, $conditionStringPassed){
			$fields=[]; $values=[];
			$setStr = "";
			foreach ($data as $key => $value) {
				array_push($fields, $key);
				array_push($values, $value);
			}
			try{
				$ctr = 0;
				$sqlstr = "UPDATE $table SET ";
					foreach ($data as $key => $value) {
						$sqlstr .="$key=?"; $ctr++;
						if($ctr<count($fields)){
							$sqlstr.=", ";
						}
					}
					$sqlstr .= " WHERE ".$conditionStringPassed;
					$sql = $this->pdo->prepare($sqlstr);
					$sql->execute($values);
				return $this->encryptData(array("code"=>200, "remarks"=>"success"));	
			}
			catch(\PDOException $e){
				$errmsg = $e->getMessage();
				$code = 403;
			}
			return $this->encryptData(array("code"=>$code, "errmsg"=>$errmsg));
		}

		public function encryptData($data){
			$key="01234567890123456789012345678901";
			$iv="1234567890123412";
			$encrypted_data = openssl_encrypt(json_encode($data), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
			return base64_encode($encrypted_data);
		}

		function decryptData($data){
			$data = base64_decode($data);
			$key="01234567890123456789012345678901";
			$iv="1234567890123412";
			$decrypted_data = openssl_decrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
			return $decrypted_data;
		}

		public function sendPayload($payload, $remarks, $message, $code) {
			$status = array("remarks"=>$remarks, "message"=>$message);
			http_response_code($code);
			return $this->encryptData(array(
				"status"=>$status,
				"payload"=>$payload,
				'prepared_by'=>'Developed by The Wing Squad, Gordon College CCS',
				"timestamp"=>date_create()));
		} 
	}
?>