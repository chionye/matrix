<?php

include('../system/config.php');
include_once '../mailer/mail2.php';
$output = "";

function shareEarningPoint($id, $pointVal,$level = 0, $where, $newUser, $plan)
{
		global $obj;
		$status = false;
		$nums = count($id);
		$arr=[];
			for ($i=0; $i < $nums; $i++) { 
			$fid = $id[$i];
			$sql = $obj->generalSelectStatement("select * from referals where ref_id = '$fid'");
			if ($sql->_general_count > 0) {
				if ($sql->_general_count == 4) {
					$data = $sql->_general_result;
					foreach ($data as $key => $value) {
						$arr[] = $value->newCustomer_id;
					}
				}else{
					$status = true;
					$table = "main_table";
					$sql2 = $obj->insert("referals", array("newCustomer_id", "ref_id","ref_point","status", "self_ref_plan", "system_ref","entry"), array($newUser,$fid,"","","","",""));
					$ql3 = $obj->generalSelectStatement("select * from referals where ref_id = '$fid'");
					if ($ql3->_general_count > 0 && $ql3->_general_count == 4) {
						$kant = count($id);
						$checker = 0;
						for ($k=0; $k < $kant; $k++) {
							$getShit = $id[$k];
							$checkShit = $obj->generalSelectStatement("select * from referals where ref_id = '$getShit'");
							if ($checkShit->_general_count == 4) {
								$checker+=4;
							}
						}
						$obj->tree($fid,$fid,false);
						$data = $obj->arr;
						$dat = array_reverse(groupUp($data));
						array_unshift($dat, $fid);
						$count_level = count($dat);
						$mtc_arr = array(60,24.3,13.8,9.9,8.4,7.8,7.5,3.6,3.6,3.6,3.6);
						for ($i=0; $i < $count_level; $i++) {
							$udat = $dat[$i];
							$get = $obj->generalSelectStatement("select * from main_table where main_id = '$udat'");
							$match_point = $get->_general_result[0]->match_point;
							$level = $get->_general_result[0]->level;	
							if ($i == 0) {
								$mtc = $mtc_arr[$i] * 4;
								$sql4 = $obj->update($table, array("level","match_point"), array((int)$level + 1,$mtc + (int)$match_point), "main_id", $udat);
							}
							if ($i > 0 && $checker == pow(4, $count_level)) {
								$mtc = $mtc_arr[$i] * pow(4, $count_level);
								$sql4 = $obj->update($table, array("level","match_point"), array((int)$level + 1,$mtc + (int)$match_point), "main_id", $udat);
							}
						}
					}
					break;
				}
			}else{
				$status = true;
				$table = "main_table";
				$sql2 = $obj->insert("referals", array("newCustomer_id", "ref_id","ref_point","status", "self_ref_plan", "system_ref","entry"), array($newUser,$fid,"","","","",""));
				break;
			}
		}
		if ($status == false) {
			shareEarningPoint($arr,$pointVal,$level++,$where,$newUser,$plan
			);
		}
}

function getRandNum($num)
{
	global $obj;
	$pin = $obj->random_string2('alnum', $num);
	$sql = $obj->generalSelectStatement("SELECT * FROM main_table WHERE pin = '$pin'");
	if ($sql->_general_count > 0) {
		getRandNum($num);
	}else{
		return $pin;
	}
}

function groupUp($value)
{
	$count = count($value);
	$arr = array();
	for ($i=0; $i < $count; $i++) { 
		if (is_array($value[$i])) {
			$arr[] = array_keys($value[$i])[0];
		}
	}
	$recount = count($arr);$newarr = array();
	for ($i=0; $i < $recount; $i++) { 
		if (!in_array($arr[$i], $newarr)) {
			$newarr[] = $arr[$i];
		}
	}
	return $newarr;
}

if (isset($_POST['new'])){
	$alldata = isset($_COOKIE['user'])?json_decode($_COOKIE['user']):"";
	$firstname = (isset($_COOKIE['user']))? $alldata->fname: '';
	$lastname = (isset($_COOKIE['user'])) ? $alldata->lname: '';
	$email = (isset($_COOKIE['user']))? $alldata->email: '';
	$referrer = (isset($_COOKIE['user']))? $alldata->ref:'';
	$phone = (isset($_COOKIE['user']))? $alldata->phone:'';
	$password = (isset($_COOKIE['user']))? $alldata->pass1: '';
	$password2 = (isset($_COOKIE['user']))? $alldata->pass2: '';
	$country = (isset($_COOKIE['user']))? $alldata->country: '';
	$address = (isset($_COOKIE['user']))? $alldata->address: '';
	$pin = (isset($_POST['pin'])) ? trim(str_replace(" ", "", $_POST['pin'])) : '';
	$plan = (isset($_POST['plan'])) ? $_POST['plan'] : '';
	$status = 'active';
	//check for records in obj
	$email_fetch = $obj->generalSelectStatement("SELECT * FROM main_table WHERE email='$email'");
	if($email_fetch->_general_count > 0){
		$output = "account already exists";
		echo $output;
	}else{
		//check if fields are empty
		if($firstname =='' || $lastname =='' ||  $email == '' || $password == '' ||  $password2 == ''){
			$output = "fields cannot be empty";
			echo $output;
		}else{	
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$output = "not a valid email";
				echo $output;
			}else{
				if(strlen($password) < 6){
					$output = $password.' Your password must be up to six characters or more ';
					echo $output;
				}
				if(strlen($password2) < 6){
					$output = $password2.' Your password must be up to six characters or more';
					echo $output;
				}else{	
					if($password != $password2){
						$output = "passwords do not match";
						echo $output;
					}else{
						$hashed = hash('sha256', $password);
						$table = 'main_table';
						$fields = array('first_name', 'lastname','email','phone','status','password', 'profit', 'passport', 'ref', 'balance','active_deposit', 'picture', 'popup', 'otp','ref_bonus_classic', 'ref_bonus_exotic', 'ref_bonus_premium', 'point','sys_point','match_point', 'level', 'pin', 'code', 'acc_number', 'country', 'address', 'pincode', 'plan','sbp', 'system_bonus', 'slf');
						$values = array($firstname, $lastname, $email, $phone, $status, $hashed, '','','','','','','','',0,0,0,0,0,0,0, $pin, '', 0, $country, $address,'', $plan,0,0, 0);
						$sql = $obj->insert($table, $fields,$values);
						if(isset($sql->_sucMsg)){
							$sql = $obj->generalSelectStatement("SELECT * FROM main_table WHERE email = '$email'");
							if ($sql->_general_count > 0) {
								$id = $sql->_general_result[0]->main_id;
								$secret = mt_rand(000000,999999);
								$ref = $firstname."".$id;
								$tble = 'main_table';
								$flds = array('ref', 'otp');
								$val = array($ref, $secret);
								$cond = 'main_id';
								$clause = $id;
								$sql = $obj->update($tble,$flds, $val, $cond, $clause);
								if (isset($sql->sucMsg)) {
									if ($referrer == "") {	
										$sql333 = $obj->generalSelectStatement("SELECT * FROM main_table");
										if ($sql333->_general_count == 1) {
											echo "ok";
										}else{
											$sql333 = $obj->generalSelectStatement("SELECT * FROM main_table ORDER BY RAND() LIMIT 1");
											$referrer = $sql333->_general_result[0]->pincode;
										}
									}
									if ($referrer != "") {	
										$sql333 = $obj->generalSelectStatement("SELECT * FROM main_table WHERE pincode = '$referrer'");
										if ($sql333->_general_count > 0) {
											$sbp = (int)$sql333->_general_result[0]->sbp;
											$ref_level = (int)$sql333->_general_result[0]->level;
											$ref_plan = (int)$sql333->_general_result[0]->plan;
											$refid = $sql333->_general_result[0]->main_id;
											$where = array();
											switch ($plan) {
												case "Classic plan":
													$where[] = "ref_bonus_classic";
													$ptval = (int)$sql333->_general_result[0]->ref_bonus_classic;
													break;

												case "Exotic plan":
													$where[] = "ref_bonus_exotic";
													$ptval = (int)$sql333->_general_result[0]->ref_bonus_exotic;
													break;

												case "Premium plan":
													$where[] = "ref_bonus_premium";
													$ptval = (int)$sql333->_general_result[0]->ref_bonus_premium;
													break;

												default:
													echo "stopped";
											}
											
											$mtchptval = (int)$sql333->_general_result[0]->match_point;
											shareEarningPoint(array($refid), $ptval,$level = 0, $where, $id, $plan);
											$swq = $obj->generalSelectStatement("SELECT * FROM referals WHERE newCustomer_id = '$id'")->_general_result[0]->ref_id;
											$obj->tree($swq,$swq,false);
											$data = $obj->arr;
											$dat = array_reverse(groupUp($data));
											array_unshift($dat, $swq);
											$count_level = count($dat);
											if ($count_level <= 10 && $count_level != 0) {
												for ($i=0; $i < $count_level; $i++) {
													$parent_id = $dat[$i];
													$sql12 = $obj->generalSelectStatement("SELECT * FROM main_table WHERE main_id = '$parent_id'");
													if ($sql12->_general_count > 0) {
														$refpt = '';
														$refs_plan = $sql12->_general_result[0]->plan;
														$refs_slf = (float)$sql12->_general_result[0]->slf;
														$refs_lvl = $sql12->_general_result[0]->level;
														switch ($plan) {
															case "Classic plan":
																switch ($refs_plan) {
																	case 'Classic plan':
																		$arr = array(140, 56.7, 32.2, 23.1, 19.6, 18.2, 17.5, 8.4, 8.4, 8.4, 8.4);
																		$slf = array(90, 37.5, 20, 15.5, 12, 10, 8.5, 4.5, 4.5, 4.5, 4.5);
																		break;

																	case 'Exotic Plan':
																		$arr = array(140, 56.7, 32.2, 23.1, 19.6, 18.2, 17.5, 8.4, 8.4, 8.4, 8.4);
																		$slf = array(90, 37.5, 20, 15.5, 12, 10, 8.5, 4.5, 4.5, 4.5, 4.5);
																		break;

																	case 'Premium Plan':
																		$arr = array(140, 56.7, 32.2, 23.1, 19.6, 18.2, 17.5, 8.4, 8.4, 8.4, 8.4);
																		$slf = array(90, 37.5, 20, 15.5, 12, 10, 8.5, 4.5, 4.5, 4.5, 4.5);
																		break;
																	
																	default:
																		$arr = array(140, 56.7, 32.2, 23.1, 19.6, 18.2, 17.5, 8.4, 8.4, 8.4, 8.4);
																		$slf = array(90, 37.5, 20, 15.5, 12, 10, 8.5, 4.5, 4.5, 4.5, 4.5);
																		break;
																}
																$refpt = (float)$sql12->_general_result[0]->ref_bonus_classic;
																break;

															case "Exotic plan":
																switch ($refs_plan) {
																	case 'Classic plan':
																		$arr = array(200);
																		$slf = array(90, 37.5, 20, 15.5, 12, 10, 8.5, 4.5, 4.5, 4.5, 4.5);
																		break;

																	case 'Exotic Plan':
																		$arr = array(600);
																		$slf = array(132, 54, 30.6, 22, 18.6, 17.3, 16.6, 8, 8, 8, 8);
																		break;

																	case 'Premium Plan':
																		$arr = array(600);
																		$slf = array(132, 54, 30.6, 22, 18.6, 17.3, 16.6, 8, 8, 8, 8);
																		break;
																	
																	default:
																		$arr = array(600);
																		$slf = array(132, 54, 30.6, 22, 18.6, 17.3, 16.6, 8, 8, 8, 8);
																		break;
																}
																$refpt = (float)$sql12->_general_result[0]->ref_bonus_exotic;
																break;

															case "Premium plan":
																switch ($refs_plan) {
																	case 'Classic plan':
																		$arr = array(500);
																		$slf = array(90, 37.5, 20, 15.5, 12, 10, 8.5, 4.5, 4.5, 4.5, 4.5);
																		break;

																	case 'Exotic Plan':
																		$arr = array(1000);
																		$slf = array(132, 54, 30.6, 22, 18.6, 17.3, 16.6, 8, 8, 8, 8);
																		break;

																	case 'Premium Plan':
																		$arr = array(2000);
																		$slf = array(180, 81, 46, 33, 28, 26, 25, 12, 12, 12, 12);
																		break;
																	
																	default:
																		$arr = array(2000);
																		$slf = array(180, 81, 46, 33, 28, 26, 25, 12, 12, 12, 12);
																		break;
																}
																$refpt = (float)$sql12->_general_result[0]->ref_bonus_premium;
																break;

															default:
																echo "stopped";
														}
														if ($parent_id == $refid) {
															$totalpt = $plan == "Classic plan"?$refpt + $arr[$i]:$refpt + $arr[0];
															$sql13 = $obj->update("main_table", $where, array($totalpt), "main_id", $parent_id);
														}
														if ($parent_id != $refid) {
															$ans = $refs_slf + $slf[$refs_lvl];
															$sql13 = $obj->update("main_table",array("slf"), array($ans), "main_id", $parent_id);
														}
													}
												}
											}else{
													$refpt = '';
													$sql12 = $obj->generalSelectStatement("SELECT * FROM main_table WHERE main_id = '$swq'");
														$arr = array();
														switch ($plan) {
															case "Classic plan":
																$refpt = (int)$sql12->_general_result[0]->ref_bonus_classic;
																$arr = array(140, 56.7, 32.2, 23.1, 19.6, 18.2, 17.5, 8.4, 8.4, 8.4, 8.4);
																$what[] = "ref_bonus_classic";
																break;

															case "Exotic plan":
																$refpt = (int)$sql12->_general_result[0]->ref_bonus_exotic;
																$arr = array(600);
																$what[] = "ref_bonus_exotic";
																break;

															case "Premium plan":
																$refpt = (int)$sql12->_general_result[0]->ref_bonus_premium;
																$arr = array(2000);
																$what[] = "ref_bonus_premium";
																break;

															default:
																echo "stopped";
														}
														$totalpt = $ptval + $arr[$count_level];
														$sql13 = $obj->update("main_table", $what, array($totalpt), "main_id", $swq);
												}
												if (!empty($referrer)) {
													$sbparr = array();
													if ($plan != "Classic plan") {
														if ($ref_plan == "Premium plan" && $plan == "Premium plan") {
															$sbparr = array(140, 56.7, 32.2, 23.1, 19.6, 18.2, 17.5, 8.4, 8.4, 8.4, 8.4);
														}
														elseif ($ref_plan == "Premium plan" && $plan == "Exotic plan") {
															$sbparr = array(100, 50, 29, 20, 18, 17, 16, 7, 7, 7, 7);
														}
														elseif ($ref_plan == "Exotic plan" && $plan == "Exotic plan" || $ref_plan == "Exotic plan" && $plan == "Premium plan") {
															$sbparr = array(100, 50, 29, 20, 18, 17, 16, 7, 7, 7, 7);
														}
														$totalsbp = $sbp + $sbparr[$ref_level];
														$sql13 = $obj->update("main_table", array("sbp"), array($totalsbp), "main_id", $refid);
														}
												}
											$fullname = $firstname.' '.$lastname;
											$arr = (object)['email'=>$email, 'name'=>$fullname, 'secret'=>$secret, 'id'=>$id, "pin"=>$pin, "phone"=>$phone, "country"=>$country];
											$_SESSION['user'] = $arr;
											$output = "ok";
											echo $output; 
										}else{
											$sql = $obj->delete('main_table','email',$email);
											$output = "you entered a wrong referrer id";
											echo $output;
										}
									}
										
								}else{
									$output = $obj->_error;
									echo $output;
								}
							}else{
								$output = $obj->_error;
								echo $output;
							}
						}else{
							$output = $obj->error;
							echo $output;
						}
					}
				}
			}
		}
	}
}

if (isset($_POST['randStr'])) {
	$data = getRandNum(6);
	echo $data;
}
?>
