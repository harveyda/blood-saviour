<?php
	class Auth {
		protected $gm, $pdo;
		private $status =array();
		private $data = array();
		private $token = array();
		public function __construct(\PDO $pdo) {
			$this->pdo = $pdo;
			$this->gm = new GlobalMethods($pdo);
		}

		# JWT
		protected function generateHeader() {
			$h = [
				"typ"=>"JWT",
				"alg"=>"HS256",
				"app"=>"Blood Saviour",
				"dev"=>"The Developer"
			];
			return str_replace("=", "", base64_encode(json_encode($h)));
		}

		protected function generatePayload($uc, $ue, $ito) {
			$p = [
				"uc"=>$uc,
				"ue"=>$ue,
				"ito"=>$ito,
				"iby"=>"The Developer",
				"ie"=>"thewingsquadgcccs@gmail.com",
				"exp"=>date("Y-m-d H:i:s") //date_create()
			];
			return str_replace("=", "", base64_encode(json_encode($p)));
		}

		protected function generateToken($usercode, $useremail, $fullname) {
			$header = $this->generateHeader();
			$payload = $this->generatePayload($usercode, $useremail, $fullname);
			$signature = hash_hmac("sha256", "$header.$payload", base64_encode(SECRET));
			return "$header.$payload." .str_replace("=", "", base64_encode($signature));
		}

		#./JWT

		public function showToken(){
			return $this->generateToken($user_data[0], $user_data[1], $user_data[2]);
		}


		//ENCRYPT PASSWORD
		function encryptPassword($pword): ?string{
            $hashFormat ="$2y$10$";
            $saltLength =22;
            $salt = $this->generateSalt($saltLength);
            return crypt($pword, $hashFormat.$salt);
        }

		//GENERATE SALT
		function generateSalt($len){
            $urs=md5(uniqid(mt_rand(), true));
            $b64string = base64_encode($urs);
            $mb64string = str_replace('+','.', $b64string);
            return substr($mb64string, 0, $len);
        }

		// REGISTER USER USING POSITIONAL PLACEHOLDERS PDO
		function resetPassword($dt){
            $payload = $dt;
            $encryptedPassword = $this->encryptPassword($dt->usr_password);
			$email = $dt->usr_email;

			try{
				$sqlstr = "UPDATE tbl_users SET usr_password ='$encryptedPassword' WHERE usr_email='$email'";
				
				$this->pdo->exec($sqlstr);
				return $this->gm->encryptData(array("code"=>200, "remarks"=>"success"));
			} catch (\PDOException $e) {
				$errmsg = $e->getMessage();
				$code = 403;
			}
			return $this->gm->encryptData(array("code"=>$code, "errmsg"=>$errmsg));
        }

		function register_user($dt){
            $payload = $dt;
            $encryptedPassword = $this->encryptPassword($dt->usr_password);

            $payload = array(
                'usr_email'=>$dt->usr_email,
                'usr_password'=>$encryptedPassword
            );
            
			try{
				$sqlstr = "INSERT INTO tbl_users(usr_email, usr_password, usr_fname, usr_mname, usr_lname, usr_bloodtype, usr_postal)
                VALUES ('$dt->usr_email', '$encryptedPassword', '$dt->usr_fname', '$dt->usr_mname', '$dt->usr_lname', '$dt->usr_bloodtype', '$dt->usr_postal')";
				$this->pdo->exec($sqlstr);
				return $this->gm->encryptData(array("code"=>200, "remarks"=>"success"));
			} catch (\PDOException $e) {
				$errmsg = $e->getMessage();
				$code = 403;
			}
			return $this->gm->encryptData(array("code"=>$code, "errmsg"=>$errmsg));
        }

		function login_user($dt){
			$usr_email = $dt->usr_email;
			$usr_password = $dt->usr_password;

			$sqlstr="SELECT * FROM tbl_users WHERE usr_email='$usr_email' LIMIT 1";    
			if($result=$this->pdo->query($sqlstr)){
				if($result->rowCount() > 0){
					$res=$result->fetch();
					if($this->pwordCheck($usr_password, $res['usr_password'])){
						http_response_code(200);
						$this->data = array(
							'usr_email'=>$res['usr_email'],
							'usr_fname'=>$res['usr_fname'],
							'usr_lname'=>$res['usr_lname'],           
							'usr_role'=>$res['usr_role']               
						);
						$this->token = $this->generateToken($res['usr_email'], $res['usr_lname'], $res['usr_fname']);
						$this->status = array(
							'remarks'=>'success',
							'message'=>'successfully logged in'
						);
					} else {
						http_response_code(200);
						$this->status = array(
							'remarks'=>'failed',
							'message'=>'Incorrect username or password'
						);
					}

				} else {
					http_response_code(200);
					$this->status = array(
						'remarks'=>'failed',
						'message'=>'Incorrect username or password'

					);
				}
			} else {
				http_response_code(200);
				$this->status = array(
					'remarks'=>'failed',
					'message'=>'Incorrect username or password'

				);
			} 
			return $this->gm->encryptData(array(
				'token'=>$this->token,
				'status'=>$this->status,
				'payload'=>$this->data,
				'prepared_by'=>'The Wing Squad, Gordon College CCS',
				'timestamp'=>date('D M j, Y G:i:s T')
			));
		}
		
		function pwordCheck($pw, $existingpw){
			$hash=crypt($pw, $existingpw);
			if($hash === $existingpw) {return true;} else {return false;}
		}

		function checkEmail($dt){
			$usr_email = $dt->usr_email;

			$sqlstr="SELECT * FROM tbl_users WHERE usr_email='$usr_email' LIMIT 1"; 
			try {
				if($result=$this->pdo->query($sqlstr)){
					if($result->rowCount() == 0){
						http_response_code(200);
						$this->status = array(
							'remarks'=>'valid',
							'message'=>'Email does not exist'
						);
					} else {
						http_response_code(200);
						$res=$result->fetch();
						$this->token = $this->generateToken($res['usr_uname'], $res['usr_email'], $res['usr_fname']);
						$this->data = array(
							'usr_uname'=>$res['usr_uname'],                 
						);
						$this->status = array(
							'remarks'=>'exists',
							'message'=>'Email already exists'
						);
					}
				} else {
					http_response_code(200);
					$this->status = array(
						'remarks'=>'exists',
						'message'=>'Email already exists'
					);
				}
				return $this->gm->encryptData(array(
					'token'=>$this->token,
					'status'=>$this->status,
					'payload'=>$this->data,
					'prepared_by'=>'The Wing Squad, Gordon College CCS',
					'timestamp'=>date('D M j, Y G:i:s T')
				));
			} catch (\PDOException $e) {
				//throw $th;
			}
		}

		function checkUsername($dt){
			$usr_uname = $dt->usr_uname;

			$sqlstr="SELECT * FROM tbl_users WHERE usr_uname='$usr_uname' LIMIT 1"; 
			try {
				if($result=$this->pdo->query($sqlstr)){
					if($result->rowCount() == 0){
						http_response_code(200);
						$this->status = array(
							'remarks'=>'valid',
							'message'=>'Username does not exist'
						);
					} else {
						http_response_code(200);
						$this->status = array(
							'remarks'=>'exists',
							'message'=>'Username already exists'
						);
					}
				} else {
					http_response_code(200);
					$this->status = array(
						'remarks'=>'exists',
						'message'=>'Username already exists'
					);
				}
				return $this->gm->encryptData(array(
					'status'=>$this->status,
					'prepared_by'=>'The Wing Squad, Gordon College CCS',
					'timestamp'=>date('D M j, Y G:i:s T')
				));
			} catch (\PDOException $e) {
				//throw $th;
			}
		}
	}
?>