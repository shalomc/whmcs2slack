<?php

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

function whmcs2slack_config() {
    $configarray = array(
    "name" => "WHMCS to Slack",
    "description" => "Get data about WHMCS into your Slack account",
    "version" => "0.1",
    "author" => "Shalom Carmel",
    "language" => "english",
    "fields" => array(
        "url" => array ("FriendlyName" => "Slack URL", "Type" => "text", "Size" => "50", "Description" => "Your Slack URL", "Default" => "", ),
        "botname" => array ("FriendlyName" => "Slack bot name", "Type" => "text", "Size" => "50", "Description" => "", "Default" => "WHMCSbot", ),		

        "orders_channel" => array ("FriendlyName" => "Channel for new orders", "Type" => "text", "Size" => "50", "Description" => "Clear to disable", "Default" => "#orders", ),		
        "orders_emoji" => array ("FriendlyName" => "Orders emoji", "Type" => "dropdown", "Options" => ":japanese_ogre:,:japanese_goblin:,:ghost:,:angel:,:alien:,:space_invader:,:imp:,:skull:,:information_desk_person:,:guardsman:,:kiss:,:love_letter:,:broken_heart:,:cupid:,:moneybag:,:question:", "Description" => "Select emoji", "Default" => ":question:", ),

        "client_channel" => array ("FriendlyName" => "Channel for new clients", "Type" => "text", "Size" => "50", "Description" => "Clear to disable", "Default" => "#clients", ),		
        "client_emoji" => array ("FriendlyName" => "Clients emoji", "Type" => "dropdown", "Options" => ":japanese_ogre:,:japanese_goblin:,:ghost:,:angel:,:alien:,:space_invader:,:imp:,:skull:,:information_desk_person:,:guardsman:,:kiss:,:love_letter:,:broken_heart:,:cupid:,:moneybag:,:question:", "Description" => "Select emoji", "Default" => ":angel:", ),

        "tickets_channel" => array ("FriendlyName" => "Channel for new tickets and responses", "Type" => "text", "Size" => "50", "Description" => "Clear to disable", "Default" => "#tickets", ),		
        "tickets_emoji" => array ("FriendlyName" => "Tickets emoji", "Type" => "dropdown", "Options" => ":japanese_ogre:,:japanese_goblin:,:ghost:,:angel:,:alien:,:space_invader:,:imp:,:skull:,:information_desk_person:,:guardsman:,:kiss:,:love_letter:,:broken_heart:,:cupid:,:moneybag:,:question:", "Description" => "Select emoji", "Default" => ":space_invader:", ),

        "payments_channel" => array ("FriendlyName" => "Channel for new payments", "Type" => "text", "Size" => "50", "Description" => "Clear to disable", "Default" => "#payments", ),		
        "payments_emoji" => array ("FriendlyName" => "Payments emoji", "Type" => "dropdown", "Options" => ":japanese_ogre:,:japanese_goblin:,:ghost:,:angel:,:alien:,:space_invader:,:imp:,:skull:,:information_desk_person:,:guardsman:,:kiss:,:love_letter:,:broken_heart:,:cupid:,:moneybag:,:question:", "Description" => "Select emoji", "Default" => ":moneybag:", ),

		"debug" => array ("FriendlyName" => "Debug whmcs2slack", "Type" => "yesno",  "Description" => "Slack requests and responses will be written to the activity log." ),		
		
    ));
    return $configarray;
}


function whmcs2slack_activate() {

	$result = true ;
}

function whmcs2slack_deactivate() {
	$result = true;
}

function whmcs2slack_output($vars) {
	
	//===========================
	echo "<p><a href='addonmodules.php?module=whmcs2slack&disable=1'>Disable WHMCS to slack</a></p>";
	echo '<p> Nothing to do here</p>'; 
    
	echo json_encode($vars);
	

}
