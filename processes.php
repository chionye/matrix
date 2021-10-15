<?php

class processes 
{
	private static $_instance = null;
	private $connect = "fish";
	public $arr = array();
	
		function __construct(){//construct method
			try{
			//$this->connect = new PDO('mysql:host=localhost;dbname=bkr','root','Cashroll@2017');
				if ($_SERVER['SERVER_NAME'] == 'localhost') {
					$this->connect = new PDO('mysql:host=localhost;dbname=networking','root','');
				}else{
					$this->connect = new PDO('mysql:host=localhost;dbname=citiihyc_db','citiihyc_user','cit@2020?');
				}
				$this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch( PDOException $e ){
				die($e->getMessage());
			}
			
		}
		
		public static function getInstance(){//class instance
			if(isset(self::$_instance)){
				return self::$_instance;
			}
			self::$_instance = new processes();
			return self::$_instance;
		}
		
		public function getAvailableBal($id){
			
			$query = "SELECT * FROM account_details WHERE main_id = '$id'";
			$this->generalSelectStatement($query);
			if($this->_general_count > 0){
				
				foreach($this->_general_result as $k => $result){
					
					$this->bal = $result->amount_in;
					
				}
				return $this;
				
			}
			
		}
		
		public function gettheDetails($id){
			
			$query = "SELECT * FROM main_table WHERE main_id = '$id'";
			$this->generalSelectStatement($query);
			if($this->_general_count > 0){
				
				foreach($this->_general_result as $k => $result){
					
					$this->email = $result->email;
					$this->full_name = $result->first_name." ".$result->lastname; 
					
				}
				return $this;
				
			}
			
		}public function randGenerator(){
			$randnum = rand('000000000001','9999999999999');
			$randpicker = rand(1,143);
			$pickerbox = array('RCA','RCB','RCC','RCD','RCE','RCF','RCG','RCH','RCI','RCJ','RCK','RCL','RCM','RCN','RCO','RCP','RCQ','RCR','RCS','RCT','RCU','RCV','RCW','RCX','RCY','RCZ','RTA','RTB','RT','RTC','RTD','RTE','RTF','RTG','RTH','RTI','RTJ','RTK','RTL','RTM','RTN','RTO','RTP','RTQ','RTR','RTS','RTT','RTU','RTV','RTW','RTX','RTY','RTZ','RPA','RPB','RPC','RPD','RPD','RPE','RPF','RPG','RPH','RPI','RPJ','RPK','RPL','RPM','RPN','RPO','RPP','RPQ','RPR','RPS','RPT','RPU','RPV','RPW','RPX','RPY','RPZ','RRR','REA','REB','REC','RED','REE','REF','REG','REH','REI','REJ','REK','REL','REM','REN','REO','REP','REQ','RER','RES','RET','REU','REV','REW','REX','REY','REZ','RDA','RDB','RDC','RDD','RDE',"RAA","RBH","RHJ","RKK","RWH","RBB","RFC","RGC","RHC","RJC","RKC","TLC","TZC","TXC","TCC","TVC","TBC","TNC","TDO","TDT","TTT","TAG","TAH","TAS","TAR","TAC","TAT","TAZ","TSY","TSB","TZX","TQO","TAP");
			$shuff = $pickerbox[$randpicker];
			$main = $shuff.$randnum;
			return $main; 
		}
		
		
		public function theConnector(){
			$databasename = 'networking';
			$username = 'root';
			$password = '';
			$host = 'localhost';
			$link = new mysqli($host, $username, $password, $databasename);
			return $link;
		}
		
		
		function generalSelectStatement($query){
			$call = processes::getInstance();
			$this->connect->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
			if($statement =$call->connect->prepare($query)){

				if($statement->execute()){
					$this->_general_result = $statement->fetchAll(PDO::FETCH_OBJ);
					$this->_general_count = $statement->rowCount();
					if($this->_general_count == 0){
						$this->_error = true;
						$this->_errMsg = "No results";	
					}else{
						$this->_general_result = $this->_general_result;
					}
				}else{
					$this->_error = true;
					$this->_errMsg = "An error occured";	
				}
			}else{
				$this->_error = true;
				$errInfo = $call->connect->errorInfo();
				$this->_errMsg = $errInfo[2];	
			}
			return $this;
				//echo json_encode($this->_results);
				//echo "myFunc(".json_encode($this->order_results).")";
		}
		
		public function getMyCurr($choosen_cur){

			$curr_symbol = array("€","£","A$", "NZ$", "c$", "¥", "Fr", "$","¥");
			$curr_symbol_1 = array("EUR","GBP","AUD", "NZD", "CAD", "JPY", "Fr", "$","Yuan");
			if($choosen_cur == 14){
				$this->cur_symbol = "$";
				$this->rate = 1;
			}else{

				$this->generalSelectStatement("SELECT * FROM rates WHERE id = '$choosen_cur'");
				if($this->_general_count > 0){

					foreach($this->_general_result as $k => $value){

						$this->rate = $value->exchange_rate;
						if($choosen_cur == 1){
							$this->cur_symbol = $curr_symbol_1[0];
						}else if($choosen_cur == 2){
							$this->cur_symbol = $curr_symbol_1[1];
						}else if($choosen_cur == 3){
							$this->cur_symbol = $curr_symbol_1[2];
						}else if($choosen_cur == 4){
							$this->cur_symbol = $curr_symbol_1[3];
						}else if($choosen_cur == 5){
							$this->cur_symbol = $curr_symbol_1[4];
						}else if($choosen_cur == 12){
							$this->cur_symbol = $curr_symbol_1[5];
						}else if($choosen_cur == 11){
							$this->cur_symbol = $curr_symbol_1[6];
						}else if($choosen_cur == 14){
							$this->cur_symbol = $curr_symbol_1[7];
						}else if($choosen_cur == 15){
							$this->cur_symbol = $curr_symbol_1[8];
						}


					}
					return $this;

				}

			}



		}
		
		public function insert($table,$fields = array(),$values = array()){
			if(is_array($fields) && is_array($values)){
				if(count($fields)&& count($values)){
					$this->_error = false;
					$db = processes::getInstance();
					
					$queryFields =  implode(",",$fields);
					$s = self::generateQuestionMark($fields);
					$this->connect->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
					$query = "INSERT INTO ".$table." (".$queryFields.") VALUES (".$s.");";
					if($statement = $this->connect->prepare($query)){
						$x= 1;
						foreach($values as $val){
							$statement->bindValue($x,$val);
							$x++;
						}
						if($statement->execute()){
							$this->_sucMsg = "Insertion was successful";
						}else{
							$this->_error = true;
							$this->_errMsg = "An error occured,please try again";
						}
					}else{
						$this->_error = true;
						$errInfo = $this->connect->errorInfo();
						$this->_errMsg = $errInfo[2];
					}
				}else{
					die("invalid parameters.Empty arrays");	
				}
				
			}else{
				die("invalid parameters.Parameters must be arrays");	
			}
			return $this;
		}
		
	function selectUser($query){// selects company email from company_info table for email validation
		
		$call = processes::getInstance();		
		$state = $query;
		if($statement = $call->connect->query($state)){
			
			while($fetch = $statement->fetch()){
				
				$this->user =  $fetch["Username"];
				$this->user_id =  $fetch["ID"];
				$this->admin_count = $statement->rowCount();
				
			}
			
		}else{
			$failure = $statement->errorInfo();
			print_r($failure);
		}
		return $this;
		
	}
	
	public function update($table,$fields = array(),$values = array(),$condition,$clause){
		if(is_array($fields) && is_array($values)){
			if(count($fields)&& count($values)){
				$this->_error = false;
				$db = processes::getInstance();
				
				$queryFields =  implode(",",$fields);
				$query = self::generateUpdateQuery($table,$fields,$condition,$clause);
				$this->connect->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
				if($statement  = $this->connect->prepare($query)){
					$x= 1;
					foreach($values as $val){
						$statement->bindValue($x,$val);
						$x++;
					}
					if($statement->execute()){
						$this->sucMsg = "Update was successful";
					}else{
						$this->_error = true;
						$this->_error = "An error occured,please try again";
					}
				}else{
					$this->_error = true;
					echo $this->connect->errorInfo();
				//$this->_errMsg = $errInfo[2];
				}
			}else{
				die("invalid parameters.Empty arrays");	
			}
			
		}else{
			die("invalid parameters.Parameters must be arrays");	
		}
		return $this;
	}
	
	
	private static function generateQuestionMark($arr){//generates question mark for insert
		$count = count($arr);
		$x = 0;
		$s = "";
		foreach($arr as $value){
			if($x === ($count - 1)){
				$s = $s."?";
			}else{
				$s = $s."?,";
			}
			$x++;
		}	
		return $s;
	}
	
	function getSessions(){//for user sessions
		
		if(isset($_SESSION['logged_in'])){
			
			$this->user_id = $_SESSION['user_id'];
			$this->logged = $_SESSION['logged_in'];
			$this->user_name = $_SESSION['user_name'];
			
		}
		return $this;
	}
	
	function getAdminSessions(){//for admin sessions
		
		if(isset($_SESSION['admin_logged'])){
			
			$this->admin_id = $_SESSION['admin_id'] ;
			$this->admin_logged = $_SESSION['admin_logged'];
			$this->admin_user_name = $_SESSION['admin_user_name'];
			
		}
		return $this;
	}
	
	
	private static function generateUpdateQuery($table,$arr,$condition,$clause){//generate update query
		$count = count($arr);
		$x = 0;
		$s = "UPDATE {$table} SET ";
		foreach($arr as $value){
			if($x === ($count - 1)){
				$s = $s."{$value} = ?";
			}else{
				$s = $s."{$value} = ?,";
			}
			$x++;
		}	
		return $s." WHERE {$condition} = '$clause'";
	}
	
	function delete($table, $cond, $cond_ans){//deletes row from any table
		$call = processes::getInstance();
		
		try{
			$delete = "DELETE FROM ".$table." WHERE ".$cond." = '$cond_ans'";
			$statement = $call->connect->prepare($delete);
			if($statement->execute() ){
				$this->sucMsg = "Row Succesfully Deleted";
			}else{
				$failure = $statement->errorInfo();
				print_r($failure);
			}
		}catch(PDOException $e){
			echo $delete . "<br>" . $e->getMessage();
		}
		return $this;
	}
	
		function sumArraysByKeys($main_counter){ //sums arrays by similar keys 
			
			$this->sumArray = array();

			foreach ($main_counter as $k => $subArray) {
				foreach ($subArray as $id => $value) {
					@$this->sumArray[$id]+=$value;
				}
			}
			
			return $this->sumArray;
		}
		
		function valid_email ( $str ){
			return ( ! preg_match ( "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str ) ) ? FALSE : TRUE;
		}

		function alpha_numeric ( $str ){
			return ( ! preg_match ( "/^([-a-z0-9])+$/i", $str ) ) ? FALSE : TRUE;
		}

		
		
		//lets generate alphanumeric key, we will use in session validation
		function random_string($length) {
			$this->key = '';
			$this->keys = array_merge(range(0, 3));

			for ($i = 0; $i < $length; $i++) {
				$this->key .= $this->keys[array_rand($this->keys)];
			}

			return $this->key;
		}
		
		function random_string2 ( $type = 'alnum', $len = 6 )
		{					
			switch ( $type )
			{
				case 'alnum'	:
				case 'numeric'	:
				case 'nozero'	:

				switch ($type)
				{
					case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					break;
					case 'numeric'	:	$pool = '0123456789';
					break;
					case 'nozero'	:	$pool = '123456789';
					break;
				}

				$str = '';
				for ( $i=0; $i < $len; $i++ )
				{
					$str .= substr ( $pool, mt_rand ( 0, strlen ( $pool ) -1 ), 1 );
				}
				return $str;
				break;
				case 'unique' : return md5 ( uniqid ( mt_rand () ) );
				break;
			}
		}
		
		public function p_amtCheck(){
			if ($this->_count == 0){
				$this->p_id_s = 0;
			}else{
				$this->p_id_s = $this->p_id;
			}		
		}

		
		function generatePamt(){//generates the p_amt no
			
			$selectP_amt = array("AV","CD","MV","PH","GH","HW","RV", "SV", "FT", "PT", "DS", "FG", "HU", "PO", "MT", "SP", "HG", "JU", "NO", "LM");
			
			$this->p_id_s++;
			
			$Amt_Pre = "P_";
			$randAmt = rand(0, 19);
			$pickAmt = $selectP_amt[$randAmt];
			
			$paddAmt = sprintf("%02d", $this->p_id_s);
			
			$this->P_amt = $Amt_Pre.$paddAmt.$pickAmt;		
		}
		
		function getP_amt(){
			echo $this->P_amt;
		}
		
		function generateRandomNo($prefix ){
			$surfix = array("AVS","CDD","GMV","PUH","GHY","HUW","REV", "SDV", "WFT", "PUT", "DIS", "FGO", "HUP", "POU", "MXT", "BSP", "HJG", "JUC", "NVO", "LLM");
			$random =rand(0, 19);
			$surfix = $surfix[$random];
			$surfix = str_shuffle($surfix);
			
			$No = rand(10, 99);
			$mid_No = sprintf("%04d", $No);
			
			
			$random_no = $prefix.$mid_No.$surfix;
			
			return $random_no;
		}
		
		
		function getDatetimeNow() {
			$tz_object = new DateTimeZone('UTC');
			//date_default_timezone_set('Brazil/East');

			$this->datetime = new DateTime();
			$this->datetime->setTimezone($tz_object);
			return $this->datetime->format('Y\-m\-d\ G:i:s');
		}
		
		function getTimeNow() {
			$tz_object = new DateTimeZone('UTC');
			//date_default_timezone_set('Brazil/East');

			$this->datetime = new DateTime();
			$this->datetime->setTimezone($tz_object);
			//return $datetime->format('Y\-m\-d\ G:i:s');
			return $this->datetime->format('G:i:s');
		}
		
		function getDateNow() {
			$tz_object = new DateTimeZone('UTC');
			//date_default_timezone_set('Brazil/East');

			$this->datetime = new DateTime();
			$this->datetime->setTimezone($tz_object);
			return $this->datetime->format('Y-m-d');
			//return $this->datetime->format('G:i:s');
		}
		
		function dateAdder(){
			date_default_timezone_set('UTC');

			$dated=date('Y-m-d H:i');
			$dates=date('Y\-m\-d\ G:i:s', strtotime($dated . " +24 hours"));
			//$dates=date('Y-m-d H:i', strtotime($dated . " +2 minutes"));

			return $dates;
		}
		
		function timeOnlyAdder(){
			date_default_timezone_set('UTC');

			$dated=date('Y-m-d H:i');
			$dates=date('G:i:s', strtotime($dated . " +5 minutes"));
				//$dates=date('Y-m-d H:i', strtotime($dated . " +2 minutes"));

			return $dates;
		}
		
		function generateInvoiceNumber(){//generates the invoice no
			$inv = "INV_";
			$select = array("AV","CD","MV","PH","GH","HW","RV", "SV", "FT", "PT", "DS", "FG", "HU", "PO", "MT", "SP", "HG", "JU", "NO", "LM");
			$rand = rand(0, 19);
			$pick = $select[$rand];
			
			$this->invoice_id_s++;
			
			
			
			$a_paded = sprintf("%04d", $this->invoice_id_s);
			
			$this->main_inv = $inv.$a_paded.$pick;
			
			
			
			$selectOrder = array("AV","CD","MV","PH","GH","HW","RV", "SV", "FT", "PT", "DS", "FG", "HU", "PO", "MT", "SP", "HG", "JU", "NO", "LM");
			
			$orderPre = "ORD";
			$randOrder = rand(0, 19);
			$pickOrder = $selectOrder[$randOrder];
			
			$paddOrder = sprintf("%03d", $this->invoice_id_s);
			
			$this->OrderNo = $orderPre.$paddOrder.$pickOrder;
			
			
		}
		
		function noFormatter($no){
			
			$this->_no = number_format($no, 2, '.', ',');
			return $this;
		}
		
		function sendMail($to, $subject, $txt, $headers){
			
			
			ini_set("SMTP", "aspmx.l.google.com");
			ini_set("sendmail_from", "assammico66@gmail.com");
			
			mail($to,$subject,$txt,$headers);
			
			echo "Check your email now....<BR/>";
			/*$to = "somebody@example.com";  $subject = "My subject";  $txt = "Hello world!";  $headers = "From: webmaster@example.com" . "\r\n" ."CC: somebodyelse@example.com"; */
		}

    function setArr($parent, $val, $ref)
    {
        $this->arr[] = $val;
    }

   function tree($id, $start, $children = true)
{
	global $obj;
	if ($children) {
		$sql = self::generalSelectStatement("select * from referals where ref_id = '$id'");
		if ($sql->_general_count > 0) {
			$result = $sql->_general_result;
			foreach ($result as $key => $res) {
				// print_r($res->ref_id);
				$arr = [];
				if ($res->ref_id != null) {
					$arr[$res->ref_id] = $res->newCustomer_id;
					self::setArr($start,$arr, $res->ref_id);
					self::tree($res->newCustomer_id, $start);
				}
			}
		}
	}else{
		$sql =  self::generalSelectStatement("select * from referals where newCustomer_id = '$id'");
		if ($sql->_general_count > 0) {
			$result = $sql->_general_result;
			foreach ($result as $key => $res) {
				// print_r($res);
				if ($res->newCustomer_id != null) {
					$arr[$res->ref_id] = $res->newCustomer_id;
					self::setArr($res->ref_id,$arr, $res->ref_id);
					self::tree($res->ref_id, $start, false);
				}
			}
		}
	}
}

public function getCurrency($currency){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.exchangeratesapi.io/latest?base=USD&symbols='.$currency,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "UTF-8",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        print_r($response);
        switch($currency){
            case 'ZAR':
            $ret = array("htmlCode"=>"&#82;", "rate"=>1);
            break;

            case 'USD':
            $ret = array("htmlCode"=>"&#36;", "rate"=>$response->rates->USD);
            break;

            case 'GBP':
            $ret = array("htmlCode"=>"&#163;", "rate"=>$response->rates->GBP);
            break;

            case 'NGN':
            $ret = array("htmlCode"=>"&#8358;", "rate"=>$response->rates->NGN);
            break;

            default:
            $ret = array("htmlCode"=>"&#82;", "rate"=>1);
            break;
        }
        $ret = json_encode($ret);
        return $ret;
    }

    function checkIfSessionIsSet($page, $main_session, $expire){
			
			
			if(isset($main_session)){// for session time
				
				$now = time(); // Checking the time now when home page starts.

				if ($now > $expire) {
					session_destroy();
					$error = "Your session has expired, please login again";
					$pic = "";
					$color = "";
					header("location:".$page.$error."&&pic=".$pic."&&color=".$color);
				}

			}else{
				$error = "please login first";
				$pic = "";
				$color = "";
				header("location:".$page.$error."&&pic=".$pic."&&color=".$color);
			}
			
		}
		
		//select distinct country,year from table1 where year=(select year from table  
//where country='turkey') and country !=turkey;


	}//class ends
	?>