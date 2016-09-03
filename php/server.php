<?php
  require_once 'includes/Mlib.php';
  header('Content-type: text/html');
  header('Access-Control-Allow-Origin: *');
  $db = new DB();

  // get the command
  $command = $_REQUEST['command'];


  // determine which command will be run
  if($command == "send") {
	$lat= $_REQUEST['lat'];
	$lon= $_REQUEST['lon'];
	$nam= $_REQUEST['namein'];
	$dat= $_REQUEST['datein'];
	$evnt= $_REQUEST['eventin'];
	$emal= $_REQUEST['emailin'];
	echo $db->send($lat,$lon,$nam,$dat,$evnt,$emal);
  }
  elseif ($command == "getTally") {
	$name= $_REQUEST['emailin'];
	echo $db->getTally(urldecode($name));
  }
  elseif ($command == "getCats") {
	echo $db->getCats();
  }
  elseif($command == "getPlace") {
	$lat= $_REQUEST['latin'];
	$lon= $_REQUEST['lonin'];
	echo $db->getPlace($lat,$lon);
  }
  elseif($command == "getName") {
	echo $db->getName();
  }
  elseif($command == "getDate") {
	echo $db->getDate();
  }
  elseif($command == "getCategory") {
	echo $db->getCategory();
  }
  elseif($command == "getItem") {
	echo $db->getItem();
  }
  elseif($command == "getEvent") {
	echo $db->getEvent();
  }
  else
    echo "command was not recognized";
?>
 
