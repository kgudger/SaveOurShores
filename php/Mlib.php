<?php

//error_reporting(E_ALL | E_STRICT);

class DB
{
    private $db;
    private $debug;
	function __construct()
	{
    		$db = $this->connect();
    		$this->debug = false;
	}

	function connect()
	{
	    if ($this->db == 0)
	    {
	        require_once("db2convars.php");
		try {
	        /* Establish database connection */
	        	$this->db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpwd);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (Exception $e) {
			echo "Unable to connect: " . $e->getMessage() ."<p>";
			die();
		}


	    }
	    return $this->db ;
	}

	function send($lat,$lon,$nam,$dat,$evnt,$email,$hour,$adult,$youth,$area,$ptrash,$precycle) 
	{
		$nam = strtoupper($nam);
		$sql = "INSERT INTO `Collector` 
			(`name`, `lat`, `lon`, `tdate`, `eid`, `email`, `hour`, `adult`, `youth`, `area`, `pTrash`, `pRecycle`)
			VALUES(?, ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array($nam,$lat,$lon,$dat,$evnt,$email,$hour,$adult,$youth,$area,$ptrash,$precycle));
		$lastId = $this->db->lastInsertId();
		$iid = 1;
		$data = array();
		
//		echo "Collector Entered";
		$sql = "INSERT INTO `tally`
			(`cid`, `iid`,`number`)
			(SELECT ?, `iid`, ?
				FROM `items` 
				WHERE `aname` = ? )";
		$stmt = $this->db->prepare($sql);
		foreach ( $_REQUEST as $key => $value )
		{	if ( is_numeric($value) ) {
				if ( strpos($key,'-in') ) {
					if ($value > 0) 
					{//	echo $key . " = " . $value ;
						$data[] = array($key=>$value);
						$stmt->execute(array($lastId,$value,$key));
					}
				}
			}
		}
	  if ($this->debug) {
	      $my_file = 'stlog.txt';
	      $handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file); //open file for writing
	      $dataout = print_r($data,true);
	      fwrite($handle, $dataout);
	      fclose($handle);
	  }
		$this->getTally($email);
    }

	function getTally($name)
	{
	  $output = array();
	  if ( $name != "" ) {
		$sql = "SELECT SUM(number * weight) 
			FROM tally, items, Collector 
			WHERE (Collector.email = ?) AND
				Collector.cid = tally.cid AND
				tally.iid = items.iid AND
				items.recycle IS FALSE";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(strtoupper($name)));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
//		$output[] = $row[0] / 50 ;
//		echo ($row["SUM(number)"]) ;
		$trash = is_null($row["SUM(number * weight)"]) ?
			0 : $row["SUM(number * weight)"] ;
		$output["trash"] = round($trash, 0, PHP_ROUND_HALF_UP);
	
		$sql = "SELECT SUM(number * weight) 
			FROM tally, items, Collector 
			WHERE (Collector.email = ?) AND
				Collector.cid = tally.cid AND
				tally.iid = items.iid AND
				items.recycle IS TRUE";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array($name));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
//		echo ($row["SUM(number)"]) ;
//		$output[] = $row[0] / 50 ;
		$recycle = is_null($row["SUM(number * weight)"]) ?
			0 : $row["SUM(number * weight)"] ;
		$output["recycle"] = round($recycle, 0, PHP_ROUND_HALF_UP);
	  }
	  else {
		$output["trash"] = 0 ;
		$output["recycle"] = 0;
	  }
	  if ($this->debug) {
	      $my_file = 'mtlog.txt';
	      $handle = fopen($my_file, 'a') or die('Cannot open file:  '.$my_file); //open file for writing
	      $data = json_encode($output);
	      fwrite($handle, $data);
	      $data = "\n" . date("D, M d, Y H:i:s") . "\n";
	      fwrite($handle, $data);
	      fclose($handle);
	  }
	  echo json_encode($output) ;
    }

	function getUname($name,$email)
	{
	  $output = array();
	  if ( ($name != "") && ($email != "") ) {
		$new_name = $oname = $name ;
		$sql = "SELECT name
				FROM Collector 
				WHERE (name   = ?) AND
						(email != ?)" ;
		$stmt = $this->db->prepare($sql);
		$i = 1 ;
		do {
			$name = $new_name ;
			$new_name = $oname . $i++ ; // adds integer to original name
			$stmt->execute(array((strtoupper($name)),$email));
		}
		while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) );
			// check to see if user name exists
		$output["uname"] = $name ;
	  }
	  echo json_encode($output);
	}
		
	function getCats()
	{
		$sql = "SELECT name, item, aname, 
			Categories.used AS Cused, items.used AS Iused
			FROM `items`, `Categories` 
			WHERE items.category = Categories.catid
            	AND Categories.used 
                AND items.used
			ORDER BY Categories.order, items.order";
		$result = $this->db->query($sql);
		$output = array();
		$cname = "" ;
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ( $row['Cused'] ) { // only if category is used
				if ( $row['name'] != $cname ) {
					if ( $cname != "" ) {
						$output[$cname] = $o2; // output cat name if new and used
					}
					$o2 = array();
					$cname = $row['name'];
				}
				if ( $row['Iused'] ) // this item is used
					$o2[$row['item']] = $row['aname'];
			}
		}
		$output[$cname] = $o2 ;
		echo json_encode($output) ;
    }


	function getCatLang($lang)
	{
		if ($lang == "english") { // PDO substitution with "AS" doesn't work
			$lang1 = "name";
			$lang2 = "item";
		} else if ($lang == "spanish") { // so this is the only way to sanitize it
			$lang1 = "c" . $lang ;
			$lang2 = "i" . $lang ;
		}
		$sql = "SELECT " . $lang1 . " AS cname, " . $lang2 . " AS iitem, aname, 
			C.used AS Cused, I.used AS Iused
			FROM `items` AS I, `Categories` AS C
			WHERE I.category = C.catid
            	AND C.used 
                AND I.used
			ORDER BY C.order, I.order";
		
		$result = $this->db->query($sql);
		$output = array();
		$o2 = array();
		$cname = "" ;
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$row = array_map('utf8_encode', $row);
			if ( $row['Cused'] ) { // only if category is used
				if ( $row['cname'] != $cname ) {
					if ( $cname != "" ) {
						$output[$cname] = $o2; // output cat name if new and used
					}
					$o2 = array();
					$cname = $row['cname'];
				}
				if ( $row['Iused'] ) // this item is used
					$o2[$row['iitem']] = $row['aname'];
			}
		}
		$output[$cname] = $o2 ;
		$output['language'] = $lang;
		echo json_encode($output) ;
    }

	function getText($lang)
	{
		$output = array();
		if (   ($lang == "english")  // PDO substitution with "AS" doesn't work
			|| ($lang == "spanish") ) { // so this is the only way to sanitize it

			$sql = "SELECT name, $lang FROM `text` ";
			$result = $this->db->query($sql);
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$row = array_map('utf8_encode', $row);
				if (($row['name'] == "SummaryWords" ) || 
				   ( $row['name'] == "ErrorWords" ) )
					$row[$lang] = json_decode($row[$lang]); // decode object first
				$output[$row['name']] = $row[$lang] ;
			}
		}
		echo json_encode($output) ;
	}

	function getPlace($lat,$lon)
	{
        $sql = "SELECT pid,
        ( 3959 * acos( cos( radians(lat) ) * cos( radians( ? ) ) * 
		cos( radians( ? ) - radians(lon) ) + sin( radians(lat) ) * 
		sin( radians( ? ) ) ) ) 
		AS distance 
		FROM `Places` HAVING distance < 1.0
		ORDER BY distance
		LIMIT 1";
		 
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array($lat,$lon,$lat));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$output = array();
		if ( is_null($result['pid']) ) {
			$output['place'] = 0 ;
		} else {
			$output['place']=$result['pid'];
		}
		$temp = array();
		$sql = "SELECT pid, name, lat, lon
			FROM `Places`
			ORDER BY name";
		$result = $this->db->query($sql);
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[$row['name']] = array('pid'=>$row['pid'],'lat'=>$row['lat'],'lon'=>$row['lon']);
		}
		$output['places'] = $temp ;
		echo json_encode($output) ;
        }

	function getCategory()
	{
		$sql = "SELECT name
			FROM `Categories`
			ORDER BY name";
		$result = $this->db->query($sql);
		$output = array();
		$output['Category'] = "Category" ;
		$temp = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[] = $row['name'] ;
		}
		$output['results'] = $temp ;
		echo json_encode($output) ;
        }

	function getName()
	{
		$sql = "SELECT DISTINCT name
			FROM `Collector`
			ORDER BY name";
		$result = $this->db->query($sql);
		$output = array();
		$output['Name'] = "Name" ;
		$temp = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[] = $row['name'] ;
		}
		$output['results'] = $temp ;
		echo json_encode($output) ;
        }

	function getDate()
	{
		$sql = "SELECT DISTINCT CAST(`tdate` AS DATE) AS dateonly 
			FROM `Collector`
			ORDER BY dateonly";
		$result = $this->db->query($sql);
		$output = array();
		$output['Date'] = "Date" ;
		$temp = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[] = $row['dateonly'] ;
		}
		$output['results'] = $temp ;
		echo json_encode($output) ;
        }

	function getItem()
	{
		$sql = "SELECT item
			FROM `items`
			ORDER BY item";
		$result = $this->db->query($sql);
		$output = array();
		$output['Item'] = "Item" ;
		$temp = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$temp[] = $row['item'] ;
		}
		$output['results'] = $temp ;
		echo json_encode($output) ;
        }

	function getEvent($lang)
	{
		if ($lang == "spanish") { // PDO substitution with "AS" doesn't work
			$lang1 = "spanish";
		} else {
			$lang1 = "name" ;
		}

		$sql = "SELECT $lang1, eid
			FROM `Event`
			ORDER BY name";
		$result = $this->db->query($sql);
		$output = array();
		$output['Event'] = "Event" ;
		$temp = array();
		while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
			$row = array_map('utf8_encode', $row);
			$temp[$row['eid']] = $row[$lang1] ;
		}
		$output['results'] = $temp ;
		echo json_encode($output) ;
        }

     function getVoid($lat,$lon)
     {
        $sql = "SELECT id 
		,( 3959 * acos( cos( radians(37) ) * cos( radians( '$lat' ) ) * 
		cos( radians( '$lon' ) - radians(-122) ) + sin( radians(37) ) * 
		sin( radians( '$lat' ) ) ) ) 
		AS distance 
		FROM `kill` HAVING distance < 5"; 
        $result = $this->db->query($sql);
        $output = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
          $output[] = $row ;
        }
		$sql = "DELETE FROM `kill`
				WHERE ? > 0 ";
		$stmt = $this->db->prepare($sql);
//		$stmt->execute(array(1));

        echo json_encode($output) ;
     }
     
     // calculates trash weight
     function getCalc()
     {
		 $output  = array(); // output array
		 $trash   = 0 ; // calculated trash
		 $recycle = 0 ; // calculated recycle weight
		 
		$sql = "SELECT weight, recycle 
					FROM `items` 
					WHERE `aname` = ? ";
		$stmt = $this->db->prepare($sql);
		foreach ( $_REQUEST as $key => $value ) 
		{	if ( is_numeric($value) ) {
				if ( strpos($key,'-in') ) {
					if ($value > 0) 
					{//	echo $key . " = " . $value ;
						$stmt->execute(array($key));
						$row = $stmt->fetch(PDO::FETCH_ASSOC);
						
						if ($row["recycle"] == 0) { // trash 
							$trash += is_null($row["weight"]) ?
								0 : $row["weight"] * $value ;
						} else {
							$recycle += is_null($row["weight"]) ?
								0 : $row["weight"] * $value ;
						} // else is recycled value
					}
				}
			}
		}
		$output["ctrash"]   = round($trash,   2, PHP_ROUND_HALF_UP);
		$output["crecycle"] = round($recycle, 2, PHP_ROUND_HALF_UP);
		echo json_encode($output) ;
	}
}
/* Below is Haversine select for distance of 25 miles
 *
 SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( lat ) ) * 
                cos( radians( lng ) - radians(-122) ) + sin( radians(37) ) * 
                sin( radians( lat ) ) ) ) 
                AS distance 
                FROM markers HAVING distance < 25 
                ORDER BY distance LIMIT 0 , 20;
 *
 */
/* possible weight calculation
 		$sql = "SELECT SUM(number * weight) 
					FROM tally, items
					WHERE tally.cid = $lastId AND
					tally.iid = items.iid AND
					items.recycle IS FALSE";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array());
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$trash = is_null($row["SUM(number * weight)"]) ?
			0 : $row["SUM(number * weight)"] ;
		$output["trash"] = round($trash, 0, PHP_ROUND_HALF_UP);

		$sql = "SELECT SUM(number * weight) 
					FROM tally, items
					WHERE tally.cid = $lastId AND
					tally.iid = items.iid AND
					items.recycle IS TRUE";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array());
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$recycle = is_null($row["SUM(number * weight)"]) ?
			0 : $row["SUM(number * weight)"] ;
		$output["recycle"] = round($recycle, 0, PHP_ROUND_HALF_UP);
		echo json_encode($output) ;
*/
?>
