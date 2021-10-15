<?php 
session_start();
include_once 'processes.php';
include_once 'class.TreeView.php';
$obj = new processes();


if(isset($_SESSION['uid'])){
	$user_SESSION =  $_SESSION['uid'];
	//setcookie("uid", $user_SESSION, time()+10*365*24*60*60, '/');	
}

function getLastF($value){
	global $obj; $user_last = 'not yet funded';
	$sql = $obj->generalSelectStatement("select * from mining where main_id='".$value."' order by date desc limit 1");
	if ($sql->_general_count > 0){
		$user_last = $sql->_general_result;
	}
	return $user_last;
}

function getStuff($value){
	global $obj; $user_last = [];
	$sql = $obj->generalSelectStatement("select * from ".$value);
	if ($sql->_general_count > 0){
		$user_last = $sql->_general_result;
	}
	return json_encode($user_last);
}
$output = '';

function getComments($clause){
	global $obj; $user_last = [];
	if (isset($_SESSION['count'])) {
		$_SESSION['count'] = $_SESSION['count'] + 1;
		$sql = $obj->generalSelectStatement("select * from comments where comment_id = ".$_SESSION['count']." and status ='confirmed' order by date desc limit ".$clause);
		if ($sql->_general_count > 0){
			$user_last = $sql->_general_result;
		}else{
			$_SESSION['count'] = 1;
			$sql = $obj->generalSelectStatement("select * from comments where comment_id = ".$_SESSION['count']." and status ='confirmed' order by date desc limit ".$clause);
			if ($sql->_general_count > 0){
				$user_last = $sql->_general_result;
			}
		}
	}else{
		$_SESSION['count'] = 1;
		$sql = $obj->generalSelectStatement("select * from comments where comment_id = ".$_SESSION['count']." and status ='confirmed' order by date desc limit ".$clause);
		if ($sql->_general_count > 0){
			$user_last = $sql->_general_result;
		}
	}
	return json_encode($user_last);
}

if (isset($_POST['send'])) {
	$to = $_POST['to'];
	$from = $_POST['from'];
	$mess = $_POST['message'];
	$subj = $_POST['subj'];

	$headers = "From: ".$from. "\r\n";
	$headers .= "Reply-To: No- Reply". "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$subject = $subj;
	$message = '<html><body style="margin: 0px; padding: 0px">';
	$message .= '<div style="width: 100%; text-align: center; margin: 0px auto;background-image: linear-gradient(blue, #a587cf, #ddd3ec, #ffffff)"><img src="http://localhost/per/user/images/logo.png" width="20%" height="100px"></div><div style="padding: 30px"><p>'.$mess.'</p></div>';
	$message .= '</body></html>';
	$retval = @mail($to,$subj,$message,$headers);
	if($retval = true){
		$output = 'ok';
		echo $output;
	}else{
		$output = 'mail not sent';
		echo $output;
	}
}
$dbLink = $obj->theConnector();
$treeView = new TreeView($dbLink);
?>