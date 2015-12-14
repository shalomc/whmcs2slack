<?php

function whmcs2slack_module_settings() {
	$fields = "module,setting,value";
	$where = array("module"=>"whmcs2slack");
	$result = select_query('tbladdonmodules',$fields,$where);
	$whmcs2slack_configuration = array ();
	while ($row = mysql_fetch_array($result,  MYSQL_ASSOC)) {
		$whmcs2slack_configuration[$row["setting"]]= $row["value"];
			
	}		
		return $whmcs2slack_configuration ;
/*
Array
(
    [version] => 0.1
    [url] => https://hooks.slack.com/services/someslackapitoken
    [botname] => WHMCSbot
    [orders_channel] => @shalom
    [orders_emoji] => :question:
    [tickets_channel] => @shalom
    [tickets_emoji] => :space_invader:
    [payments_channel] => @shalom
    [payments_emoji] => :moneybag:
    [client_channel] => @shalom
    [client_emoji] => :moneybag:
    [access] => 4,1,10
	[debug] => on
)
*/		
}


function slack_ClientAdd($vars) {
	global $customadminpath, $CONFIG;
	$slack = whmcs2slack_module_settings();
	if ( !empty($slack["client_channel"] ) ) {
		$shortmessage= 'A new client has signed up! ' . $CONFIG['SystemURL'].'/'.$customadminpath.'/clientssummary.php?userid='.$vars['userid'] ; 
		$longmessage=''; 
		sendslack(  $slack['url'],
					$slack['botname'],
					$slack['client_channel'],
					$slack['client_emoji'],
					$shortmessage, 
					$longmessage,
					$slack['debug']
				);
	}
	
}

function slack_AddTransaction($vars) {
	global $customadminpath, $CONFIG;
	$LT = '<';
	$GT = '>';
	$PIPE = '|'; 
	$slack = whmcs2slack_module_settings();
	if ( !empty($slack["payments_channel"] ) ) {
		$shortmessage='Amount '.$vars["amountin"].' on Invoice #'.$LT. $CONFIG['SystemURL'].'/'.$customadminpath.'/invoices.php?action=edit&id='.$vars['invoiceid'].$PIPE.$vars['invoiceid'].$GT.' has been paid. ' ; 
		sendslack(  $slack['url'],
					$slack['botname'],
					$slack['payments_channel'],
					$slack['payments_emoji'],
					$shortmessage, 
					'',
					$slack['debug']
				);
	}
	
}

function slack_TicketOpen($vars) {
	global $customadminpath, $CONFIG;

	$slack = whmcs2slack_module_settings();
	if ( !empty($slack["tickets_channel"] ) ) {
		 $companyname=''; 
		 if (!empty($vars["userid"])) {
			$adminuser = "apiapi";
			$responsetype = "json";  // Probably unnecessary
			 // Get customer details
			 $command = "getclientsdetails";
			 $values["clientid"] = $vars['userid'];
			 $values["stats"] = false;
			 $values["responsetype"] = $responsetype ; 

			 $getclientsdetails = localAPI($command,$values,$adminuser);
			 $companyname=$getclientsdetails["companyname"];
		 }
		$shortmessage='New '.$vars['priority'] .' priority ticket: '. $vars['subject'] ; 
		$longmessage =$CONFIG['SystemURL'].'/'.$customadminpath.'/supporttickets.php?action=viewticket&id='.$vars['ticketid'] . PHP_EOL 
				. 'Company: ' . $companyname . PHP_EOL 
				. 'Message: ' . PHP_EOL . $vars['message'] ; 
		sendslack(  $slack['url'],
					$slack['botname'],
					$slack['tickets_channel'],
					$slack['tickets_emoji'],
					$shortmessage,
					$longmessage,
					$slack['debug']
				);
	}
	

}

function slack_TicketUserReply($vars) {
	global $customadminpath, $CONFIG;
	$slack = whmcs2slack_module_settings();
	if ( !empty($slack["tickets_channel"] ) ) {
		 $companyname=''; 
		 if (!empty($vars["userid"])) {
			$adminuser = "apiapi"; // set your own admin user
			$responsetype = "json";  // Probably unnecessary
			 // Get customer details
			 $command = "getclientsdetails";
			 $values["clientid"] = $vars['userid'];
			 $values["stats"] = false;
			 $values["responsetype"] = $responsetype ; 

			 $getclientsdetails = localAPI($command,$values,$adminuser);
			 $companyname=$getclientsdetails["companyname"];
		 }
		$shortmessage='Ticket '.$vars['ticketid'].' update: '. $vars['subject'] ; 
		$longmessage =$CONFIG['SystemURL'].'/'.$customadminpath.'/supporttickets.php?action=viewticket&id='.$vars['ticketid'] . PHP_EOL 
				. 'Company: ' . $companyname . PHP_EOL 
				. 'Message: ' . PHP_EOL . $vars['message'] ; 

				sendslack(  $slack['url'],
					$slack['botname'],
					$slack['tickets_channel'],
					$slack['tickets_emoji'],
					$shortmessage,
					$longmessage,
					$slack['debug']
				);
	}

}


function slack_AcceptOrder($vars) {
	global $customadminpath, $CONFIG;
	$slack = whmcs2slack_module_settings();
	if ( !empty($slack["orders_channel"] ) ) {
		$shortmessage='New order: '. $vars['orderid'] .' '. $CONFIG['SystemURL'].'/'.$customadminpath.'/orders.php?action=view&id='.$vars['orderid'] ; 
		sendslack(  $slack['url'],
					$slack['botname'],
					$slack['orders_channel'],
					$slack['orders_emoji'],
					$shortmessage ,
					'',
					$slack['debug']
				);
	}

}


function sendslack( $url, $botname, $channel, $emoji, $message, $longmessage = '', $debug='') 
{
	if ( empty($longmessage)) {
		$slack_message = array( 
			"channel" => $channel, 
			"username" => $botname, 
			"icon_emoji" => $emoji, 
			"text" => $message
			);
	} else {
		$fields = array(); 
		$fields[]=array ( 
				"title" => $message, 
				"value" => $longmessage,
				"short" => false
				);
		$attachments=array(); 
		$attachments[]=array(
				"fallback" => $message,
				"pretext" => $message,
				"color" => "warning",
				"fields" => $fields
				);
				
		$slack_message = array( 
				"channel" => $channel, 
				"username" => $botname, 
				"icon_emoji" => $emoji, 
				"attachments" => $attachments
				);
	}
		
	$slack_json = json_encode($slack_message); 
    $ch = curl_init();  
	
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
			));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $slack_json );
    curl_setopt($ch,CURLOPT_ENCODING , "gzip");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
    $output=curl_exec($ch);
    $GLOBALS['http_status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
	if ($debug == 'on' ){
		$command = "logactivity";
		$adminuser = "apiapi";
		$responsetype = "json";  // Probably unnecessary
		$values["description"] = "Slack request: ".$slack_json;
		$values["responsetype"] = $responsetype ; 
		$results = localAPI($command,$values,$adminuser);
		$values["description"] = "Slack response: ".$output;
		$values["responsetype"] = $responsetype ; 
		$results = localAPI($command,$values,$adminuser);

	}
    return $output;

}

add_hook("ClientAdd",999,"slack_ClientAdd");
add_hook("AcceptOrder",999,"slack_AcceptOrder");
add_hook("AddTransaction",999,"slack_AddTransaction");
add_hook("TicketOpen",999,"slack_TicketOpen");
add_hook("TicketOpenAdmin",999,"slack_TicketOpen");
add_hook("TicketUserReply",999,"slack_TicketUserReply");

