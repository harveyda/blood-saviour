<?php  
	class Get {
		protected $pdo;

		public function __construct(\PDO $pdo) {
			$this->pdo = $pdo;
            $this->gm = new GlobalMethods($pdo);
		}

        public function requestsJoin($filter_data) {
            
            $this->sql = "SELECT
            tbl_requests.req_bloodtype,
            tbl_requests.req_concern,
            tbl_requests.req_status,
            tbl_requests.req_dateAdded,
            tbl_requests.req_dateUpdated,
            tbl_requests.req_date,
            tbl_requests.usr_email,
            tbl_users.usr_fname,
            tbl_users.usr_mname,
            tbl_users.usr_lname,     
            tbl_users.usr_email,
            tbl_requests.req_id
            from tbl_requests 
            LEFT JOIN tbl_users on tbl_users.usr_email = tbl_requests.usr_email";

			if($filter_data != null) {
				$this->sql .= " WHERE tbl_requests.req_id = '$filter_data'";
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
			return $this->gm->sendPayload($data, $remarks, $msg, $code);
        }

        public function donationsJoin($filter_data) {
            
            $this->sql = "SELECT
            tbl_donations.usr_email,
            tbl_donations.donation_reason,
            tbl_donations.donation_bloodtype,
            tbl_donations.donator_weight,
            tbl_donations.donator_bloodpressure,
            tbl_donations.donator_pulserate,
            tbl_donations.donation_status,
            tbl_donations.donation_date,
            tbl_donations.donation_dateAdded,
            tbl_donations.donation_dateUpdated,
            tbl_users.usr_fname,
            tbl_users.usr_mname,
            tbl_users.usr_lname,     
            tbl_users.usr_email,
            tbl_donations.donation_id
            from tbl_donations 
            LEFT JOIN tbl_users on tbl_users.usr_email = tbl_donations.usr_email";

			if($filter_data != null) {
				$this->sql .= " WHERE tbl_donations.donation_id = '$filter_data'";
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
			return $this->gm->sendPayload($data, $remarks, $msg, $code);
        }

        // SELECT
		public function filter_query($table, $filter_data) {

            $this->sql = "SELECT * FROM $table";

			if($filter_data != null && $table == "tbl_events") {
				$this->sql .= " WHERE event_name LIKE '%$filter_data%'
				OR event_desc LIKE '%$filter_data%' OR event_status LIKE '%$filter_data%'";
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
			return $this->gm->sendPayload($data, $remarks, $msg, $code);
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