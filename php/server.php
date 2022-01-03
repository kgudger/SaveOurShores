<?php
  $debug = false;
  require_once 'includes/Mlib.php';
  header('Content-type: application/json');
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
  header('Access-Control-Allow-Headers: Authorization');

  $db = new DB();

  /* Removes everything past a space, fixes problem with iOS requests */
//  print_r($_REQUEST);
  if ( is_array($_REQUEST) && count($_REQUEST) ) {
    if ($debug) {
      $my_file = 'alog.txt';
      $handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file); //open file for writing
      $data = json_encode($_REQUEST);
      fwrite($handle, $data);
      $data = "\n" . date("D, M d, Y H:i:s") . "\n";
      fwrite($handle, $data);
      fclose($handle);
    }
    end($_REQUEST); 
    $arend =  $_REQUEST[key($_REQUEST)];
    $arend = explode(" ",$arend) ;
    $arend = $arend[0] ;
    $_REQUEST[key($_REQUEST)] = $arend ;
    reset($_REQUEST) ; 
//  echo "<br>Last element is " . $arend . "<br>" ;
//  print_r($_REQUEST);
  }
  
  // get the command
  $command = $_REQUEST['command'];
  foreach ($_REQUEST as $key => $value) {
	$_REQUEST[$key] = urldecode($value);
  }

  // determine which command will be run
  if($command == "send") {
	$lat= $_REQUEST['lat'];
	$lon= $_REQUEST['lon'];
	$nam= $_REQUEST['namein'];
	$dat= $_REQUEST['datein'];
	$evnt= $_REQUEST['eventin'];
	$emal= $_REQUEST['emailin'];
	$hour= $_REQUEST['hoursin']?? '';
	$adul= $_REQUEST['adultsin']?? '';
	$yout= $_REQUEST['youthin']?? '';
	$area= $_REQUEST['areain']?? '';
	$ptrash= $_REQUEST['ptrashin']?? '';
	$precycle= $_REQUEST['precyclein']?? '';
	echo $db->send($lat,$lon,$nam,$dat,$evnt,$emal,$hour,$adul,$yout,$area,$ptrash,$precycle);
  }
  elseif ($command == "getTally") {
	$name= $_REQUEST['emailin'];
	echo $db->getTally(urldecode($name));
  }
  elseif ($command == "checkUname") {
	$name = $_REQUEST['namein'];
	$email= $_REQUEST['emailin'];
	echo $db->getUname(urldecode($name),urldecode($email));
  }
  elseif ($command == "getCats") {
	echo $db->getCats();
  }
  elseif ($command == "getCatLang") {
	$lang= $_REQUEST['lang'];
	echo $db->getCatLang($lang);
  }
  elseif ($command == "getText") {
	$lang= $_REQUEST['lang'];
	echo $db->getText($lang);
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
	if (isset($_REQUEST['lang'])) {
		echo $db->getEvent($_REQUEST['lang']);
	} else {
		echo $db->getEvent("");
	}
  }
  elseif($command == "Calc") {
	echo $db->getCalc();
  }
  else
    echo "command was not recognized";
?>
 
