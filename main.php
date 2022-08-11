<?php 
	require_once("./config/Config.php");

	$db = new Connection();
	$pdo = $db->connect();
	$gm = new GlobalMethods($pdo);
	$send = new SendEmail($pdo);
	$auth = new Auth($pdo);
	$post = new Post($pdo);
	$get = new Get($pdo);

	if (isset($_REQUEST['request'])) {
		$req = explode ('/', rtrim(base64_decode($_REQUEST['request']), '/'));
	} else {
		$req = array("errorcatcher");
	}
	$d = json_decode(base64_decode(file_get_contents("php://input")));

	switch($_SERVER['REQUEST_METHOD']) {
		case 'POST':

			switch($req[0]) {		
				case 'register':
					echo json_encode($auth->register_user($d));
					break;

				case 'check_email':

					echo json_encode($auth->checkEmail($d));
					break;
				case 'check_username':
					echo json_encode($auth->checkUsername($d));
					break;

				case 'reset_password':
					echo json_encode($auth->resetPassword($d));
				break;

				case 'login':
					echo json_encode($auth->login_user($d));
					break;

				case 'users':
					if(count($req)>1){
						echo json_encode($gm->exec_query('tbl_'.$req[0], $req[1]),JSON_PRETTY_PRINT);
					} else {
						echo json_encode($gm->exec_query('tbl_'.$req[0], null),JSON_PRETTY_PRINT);
					}
				break;

				case 'requests':
					if(count($req)>1){
						echo json_encode($gm->exec_query('tbl_'.$req[0], $req[1]),JSON_PRETTY_PRINT);
					} else {
						echo json_encode($gm->exec_query('tbl_'.$req[0], null),JSON_PRETTY_PRINT);
					}
				break;

				case 'requests_join':
					if(count($req)>1){
						echo json_encode($get->requestsJoin($req[1]), JSON_PRETTY_PRINT);
					} else {
						echo json_encode($get->requestsJoin(null), JSON_PRETTY_PRINT);
					}
				break;

				case 'update_request':
					echo json_encode($gm->update("tbl_requests", $d, "req_id=$req[1]"), JSON_PRETTY_PRINT);
				break;

				case 'donations':
					if(count($req)>1){
						echo json_encode($gm->exec_query('tbl_'.$req[0], $req[1]),JSON_PRETTY_PRINT);
					} else {
						echo json_encode($gm->exec_query('tbl_'.$req[0], null),JSON_PRETTY_PRINT);
					}
				break;

				case 'donations_join':
					if(count($req)>1){
						echo json_encode($get->donationsJoin($req[1]), JSON_PRETTY_PRINT);
					} else {
						echo json_encode($get->donationsJoin(null), JSON_PRETTY_PRINT);
					}
				break;

				case 'update_donation':
					echo json_encode($gm->update("tbl_donations", $d, "donation_id=$req[1]"), JSON_PRETTY_PRINT);
				break;

				case 'update_user':
					echo json_encode($gm->update("tbl_users", $d, "usr_email='$req[1]'"), JSON_PRETTY_PRINT);
				break;

				case 'update_event':
					echo json_encode($gm->update("tbl_events", $d, "event_id=$req[1]"), JSON_PRETTY_PRINT);
				break;

				case 'send_email':
					echo json_encode($send->send_email($d), JSON_PRETTY_PRINT);
				break;

				case 'insert_event':
					echo json_encode($gm->insert("tbl_events", $d), JSON_PRETTY_PRINT);
				break;
				
				case 'events':
					if(count($req)>1){
						echo json_encode($gm->exec_query('tbl_'.$req[0], $req[1]),JSON_PRETTY_PRINT);
					} else {
						echo json_encode($gm->exec_query('tbl_'.$req[0], null),JSON_PRETTY_PRINT);
					}
				break;

				case 'insert_request':
					echo json_encode($gm->insert("tbl_requests", $d), JSON_PRETTY_PRINT);
				break;
				
				case 'insert_donation':
					echo json_encode($gm->insert("tbl_donations", $d), JSON_PRETTY_PRINT);
				break;

				case 'filter_events':
					if(count($req)>1){
						echo json_encode($get->filter_query('tbl_events', $req[1]),JSON_PRETTY_PRINT);
					} else {
						echo json_encode($get->filter_query('tbl_events', null),JSON_PRETTY_PRINT);
					}
				break;

				case 'uploaduser':
					echo json_encode($auth->upload_user_image($d), JSON_PRETTY_PRINT);
				break;
				default:
					http_response_code(400);
					echo "Please contact the Systems Administrators";
				break;
			}
		break;

		case 'GET':

		default:
			http_response_code(403);
			echo "Please contact the Systems Administrator";
		break;
	}
?>