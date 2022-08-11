<?php 
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
    class SendEmail {
        protected $gm, $pdo;
        public function __construct(\PDO $pdo) {
			$this->pdo = $pdo;
			$this->gm = new GlobalMethods($pdo);
		}

        function send_email($dt) {
            include_once 'PHPMailer/src/Exception.php';
			include_once 'PHPMailer/src/PHPMailer.php';
			include_once 'PHPMailer/src/SMTP.php';

            $email = $dt->email;
            $message = $dt->message;
            $date = $dt->date;
            $name = $dt->fullname;

            $mail = new PHPMailer(TRUE);

            $mail->isSMTP();                                  
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;                          
            $mail->Username = "thewingsquadgcccs@gmail.com";                 
            $mail->Password = "nsbcvgajckrbokez";                           
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                          
            $mail->Port = 465;                
            $mail->From = "thewingsquadgcccs@gmail.com";
            $mail->FromName = "Blood Saviour";
            $mail->addAddress("$email");

            $mail->isHTML(true);

            $mail->Subject = "Blood Saviour";
            $mail->Body = "
            <html>
                                <head></head>
                                    <body>
                                        <div style='display: flex; 
                                                    flex-direction: column;
                                                    justify-content: center;'>
                                                    <div style='margin: 0 auto; width: 25%;'>
                                                    <div style='width: fit-content;
                                                                padding: 25px;
                                                                border: 1px solid #585858;
                                                                border-radius: 20px;
                                                                text-align: center;'>
                                                                <img src='https://i.ibb.co/SXp4NwF/bloodsaviour.png'>
                                                                <p style='font-size: 18px'>$name</p>
                                                               
                                    
                                                                <i style='font-size: 15px'>$message</i>
                                                                <h3><b style='color: #880808;'>Philippine Red Cross, Olongapo City</b></h3>
                                                                <b>Date: $date</b>
                                                                <br><hr>
                                                                <p>any concerns please email us at thewingsquad.2021@gmail.com.</p>
                                                    </div>
                                                    </div>
                                        </div>
            
                                    </body>
                            </html>
            ";
            $mail->AltBody = "";

            $mail->send();

            http_response_code(200);
            return $this->gm->encryptData(array(
				'status'=>'Email sent sucessfully',
				'email'=>$email,
				'prepared_by'=>'The Wing Squad, Gordon College CCS',
				'timestamp'=>date('D M j, Y G:i:s T')
            ));
        }
    }
?>