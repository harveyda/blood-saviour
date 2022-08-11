<?php  
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=utf-8; image/jpeg;");
	header("Access-Control-Allow-Methods: POST, GET");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-Auth-User");
	// ini_set('display_errors', '0');
	date_default_timezone_set("Asia/Manila");
	set_time_limit(1000);

	require_once("./models/Global.php");
	require_once("./models/Auth.php");
	require_once("./models/Get.php");
	require_once("./models/Post.php");
	require_once("./models/SendEmail.php");
	require_once("./models/upload.php");

	define("DBASE", "db_blood_saviour");
	define("USER", "root");
	define("PW", "");
	define("SERVER", "localhost");
	define("CHARSET", "utf8");
	define("SECRET", base64_encode("2xHGziJK2z"));

	class Connection {
		protected $constring = "mysql:host=".SERVER.";dbname=".DBASE.";charset=".CHARSET;
		protected $options = [
			\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8, time_zone = "+8:00";',
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_EMULATE_PREPARES => false
		];

		public function connect() {
			return new \PDO($this->constring, USER, PW, $this->options);
		}
	}
?>