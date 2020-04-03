<?php
include("config.php");

$sqlQueryTasks = VoxisRunQuery($VoxisSqlLink, true, "SELECT * FROM tasks;");

foreach($sqlQueryTasks as $task) {
	if(($task["status"] == "running") && ((time()+86400) >= $task["date_start"]) && (time() <= $task["date_end"])) {
		$cards_amount = json_decode($task["cards_amount"], true);
		
		if(!isset($cards_amount["last_time_modified"]) || (time() - $cards_amount["last_time_modified"]) >= 86400) {
			$ratio[80] = rand(($cards_amount["everyday"]/100)*80, $cards_amount["everyday"]);
			$ratio[20] = rand(0, ($cards_amount["everyday"]/100)*20);
			
			if($task["day_or_night"] == "day") {
				$cards_amount["left_today"]["day"] = $ratio[80];
				$cards_amount["left_today"]["night"] = $ratio[20];
			} else {
				$cards_amount["left_today"]["day"] = $ratio[20];
				$cards_amount["left_today"]["night"] = $ratio[80];
			}
			
			// Interval in secs
			$cards_amount["interval"] = array("day" => (12.5/$cards_amount["left_today"]["day"])*60*60, "night" => (10.5/$cards_amount["left_today"]["night"])*60*60);
			$cards_amount["last_time_modified"] = time();
			
			VoxisRunQuery($VoxisSqlLink, false, "UPDATE tasks SET `cards_amount` = '".json_encode($cards_amount)."' WHERE `id` = ".$task["id"].";");
		}
		
		if($task["last_run"] == null || (time() - $task["last_run"]) >= $cards_amount["interval"]["day"]  || (time() - $task["last_run"]) >= $cards_amount["interval"]["night"]) {
			$sqlQueryCards		= VoxisRunQuery($VoxisSqlLink, true, "SELECT * FROM cards WHERE base='".$task["base"]."' AND info='not_used' LIMIT 1;");
			$sqlQueryGateway	= VoxisRunQuery($VoxisSqlLink, true, "SELECT * FROM payment_gateways WHERE id='".$task["task_gateway"]."' LIMIT 1;");
			
			$gatewayParameters	= json_decode($sqlQueryGateway[0]["params"], true);
			
			$creditCard["firstName"]		= $sqlQueryCards[0]["firstName"];
			$creditCard["lastName"]			= $sqlQueryCards[0]["lastName"];
			$creditCard["number"]			= $sqlQueryCards[0]["number"];
			$creditCard["expiryMonth"]		= $sqlQueryCards[0]["expiryMonth"];
			$creditCard["expiryYear"]		= $sqlQueryCards[0]["expiryYear"];
			$creditCard["startMonth"]		= $sqlQueryCards[0]["startMonth"];
			$creditCard["startYear"]		= $sqlQueryCards[0]["startYear"];
			$creditCard["cvv"]				= $sqlQueryCards[0]["cvv"];
			$creditCard["issueNumber"]		= $sqlQueryCards[0]["issueNumber"];
			$creditCard["type"]				= $sqlQueryCards[0]["type"];
			$creditCard["billingAddress1"]	= $sqlQueryCards[0]["billingAddress1"];
			$creditCard["billingAddress2"]	= $sqlQueryCards[0]["billingAddress2"];
			$creditCard["billingCity"]		= $sqlQueryCards[0]["billingCity"];
			$creditCard["billingPostcode"]	= $sqlQueryCards[0]["billingPostcode"];
			$creditCard["billingState"]		= $sqlQueryCards[0]["billingState"];
			$creditCard["billingCountry"]	= $sqlQueryCards[0]["billingCountry"];
			$creditCard["billingPhone"]		= $sqlQueryCards[0]["billingPhone"];
			$creditCard["shippingAddress1"]	= $sqlQueryCards[0]["shippingAddress1"];
			$creditCard["shippingAddress2"]	= $sqlQueryCards[0]["shippingAddress2"];
			$creditCard["shippingCity"]		= $sqlQueryCards[0]["shippingCity"];
			$creditCard["shippingPostcode"]	= $sqlQueryCards[0]["shippingPostcode"];
			$creditCard["shippingState"]	= $sqlQueryCards[0]["shippingState"];
			$creditCard["shippingCountry"]	= $sqlQueryCards[0]["shippingCountry"];
			$creditCard["shippingPhone"]	= $sqlQueryCards[0]["shippingPhone"];
			$creditCard["company"]			= $sqlQueryCards[0]["company"];
			$creditCard["email"]			= $sqlQueryCards[0]["email"];
			
			$makePayment = VoxisMakePayment($sqlQueryGateway[0]["gateway"], $gatewayParameters, $creditCard, $task["charge_per_card"], $task["currency"]);
			
			if($makePayment["status"] == "successful") {
				VoxisSqlLog($VoxisSqlLink, "cronjob", array("status" => 1, "info" => array("card" => $sqlQueryCards[0]["number"], "gateway" => $sqlQueryGateway[0]["nick"], "gateway_response" => $makePayment["response"])));
				VoxisRunQuery($VoxisSqlLink, false, "UPDATE cards SET `info` = '".json_decode(array("status" => "used", "charged" => $task["charge_per_card"]))."' WHERE `id` = '".$sqlQueryCards[0]["id"]."';");
				VoxisRunQuery($VoxisSqlLink, false, "UPDATE statistics SET `val` = `val` + ". $task["charge_per_card"] ." WHERE `key` = 'profit';");
				VoxisRunQuery($VoxisSqlLink, false, "UPDATE statistics SET `val` = `val` + ". $task["charge_per_card"] ." WHERE `key` = '".$task["base"]."|profit';");
				VoxisRunQuery($VoxisSqlLink, false, "UPDATE tasks SET `profit` = `profit` + ". $task["charge_per_card"] ." WHERE `id` = '".$task["id"]."';");
			} else {
				VoxisSqlLog($VoxisSqlLink, "cronjob", array("status" => 0, "info" => array("card" => $sqlQueryCards[0]["number"], "gateway" => $sqlQueryGateway[0]["nick"], "gateway_response" => $makePayment["response"])));
				VoxisRunQuery($VoxisSqlLink, false, "UPDATE cards SET `info` = '".json_decode(array("status" => "failed"))."' WHERE `id` = '".$sqlQueryCards[0]["id"]."';");
			}
			
			VoxisRunQuery($VoxisSqlLink, false, "UPDATE tasks SET `last_run` = '". time() ."' WHERE `id` = '".$task["id"]."';");
			
			$timeNow = getdate();
			if($timeNow["hours"] >= 8 && $timeNow["hours"] <= 21) {
				$nowDayOrNight = "day";
			} else {
				$nowDayOrNight = "night";
			}
			
			$cards_amount["left_today"][$nowDayOrNight]--;
			
			VoxisRunQuery($VoxisSqlLink, false, "UPDATE tasks SET `cards_amount` = '". json_encode($cards_amount) ."' WHERE `id` = '".$task["id"]."';");
		}
	}
}
?>