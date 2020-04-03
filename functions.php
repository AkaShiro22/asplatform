<?php
use Omnipay\Omnipay;
use Omnipay\Common;
use Omnipay\Common\CreditCard;

function VoxisMakePayment($gatewayName, $gatewayParameters, $creditCard, $amount, $currency) {
	global $gatewayLogins;
	
	$gateway = Omnipay::create($gatewayName);
	$gateway->initialize($gatewayParameters);

	$response = $gateway->purchase(array("amount" => $amount, "currency" => $currency, "card" => $creditCard))->send();

	if ($response->isSuccessful()) {
		return array("status" => "successful", "response" => $response);
	} else {
		return array("status" => "failed", "response" => $response->getMessage());
	}
}

function VoxisSqlLog($link, $action, $array) {
	return VoxisRunQuery($link, false, "INSERT INTO logs (time, status, action, text) VALUES ('". date("d/m/y H:i:s") ."', '". $array["status"] ."', '". $action ."', '". json_encode($array["info"]) ."');");
}

function VoxisRunQuery($link, $returnResults, $query) {
	$tempResults = mysqli_query($link, $query);

	if($tempResults) {
		if($returnResults == true) {
			while($row = mysqli_fetch_assoc($tempResults)) {
				$returnValue[] = $row;
			}
		} elseif($returnResults == false) {
			$returnValue = $tempResults;
		}
		
		return $returnValue;
	} else {
		return false;
	}
}

function VoxisInsertArray($link, $array, $tableName) {
	$placeholders = array_fill(0, count($array), '?');

	$keys   = array(); 
	$values = array();
	
	foreach($array as $k => $v) {
		$keys[] = $k;
		$values[] = !empty($v) ? $v : null;
	}

	$query	= 	"INSERT INTO $tableName ".
				'('.implode(', ', $keys).') VALUES '.
				'('.implode(', ', $placeholders).'); '; 

	$stmt = mysqli_prepare($link, $query);
	$params = array(); 
	
	foreach($array as &$value) { 
		$params[] = &$value;
	}
	
	$types  = array(str_repeat('s', count($params))); 
	$values = array_merge($types, $params); 
	
	call_user_func_array(array($stmt, 'bind_param'), $values); 

	$success = mysqli_stmt_execute($stmt);

	return $success;
}

function VoxisShowAlert($type, $header, $text) {
	return "<div class=\"alert-".$type." alert-dismissable\">
				<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\"></button>
				<strong>".$header."</strong> ".$text."
			</div>";
}

function VoxisGetAllGateways() {
	return Omnipay::find();
}

function VoxisGetDefaultParameters($gatewayName) {
	$gateway = Omnipay::create($gatewayName);
	return $gateway->getParameters();
}

function VoxisGetCardsParameters() {
	return CreditCard::getParameters();
}

function VoxisFullzParser($fullz, $separator) {
	return explode($separator, $fullz);
}

function check0mnipay() {
	// @next version surprise here! ;)
	return true;
}
?>