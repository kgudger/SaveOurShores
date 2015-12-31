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
	echo $db->send($lat,$lon,$nam);
  }
  elseif ($command == "getTally") {
	$name= $_REQUEST['namein'];
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
  else
    echo "command was not recognized";
?>
 
