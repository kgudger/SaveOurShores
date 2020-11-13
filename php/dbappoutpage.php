<?php
/**
* @file dbappoutpage.php
* Purpose: Sort and Output Data Base
* Extends MainPage Class
*
* @author Keith Gudger
* @copyright  (c) 2016, Keith Gudger, all rights reserved
* @license    http://opensource.org/licenses/BSD-2-Clause
* @version    Release: 1.0
* @package    SOS
*
* @note Has processData and showContent, 
* main and checkForm in MainPage class not overwritten.
* 
*/

require_once("/var/www/html/wp-content/plugins/app_output/includes/mainpage.php");
include_once "/var/www/html/includes/util.php";
require_once '/var/www/html/includes/phplot-6.2.0/phplot.php';
$plot_data = array();

/**
 * Child class of MainPage used for user preferrences page.
 *
 * Implements processData and showContent
 */
  $radiolist = array(""=>0,"Name"=>1,"Date"=>2,"Category"=>3,"Item"=>4,"Location"=>5);
  $radiolist2 = array(""=>0,"Top Names"=>1,"Most Recent Date"=>2,"Category"=>3,"Top Items"=>4,"Top Locations"=>5);

class dbAppOutPage extends MainPage {

/**
 * Process the data and insert / modify database.
 *
 * @param $uid is user id passed by reference.
 */
function processData(&$uid) {
  $radiolist = array("Name"=>1,"Date"=>2,"Item"=>3,"Location"=>4,"Location Batched"=>5);
  global $radiolist2;
  $uid = array($this->formL->getValue("cat"),
				$this->formL->getValue("startd"),
				$this->formL->getValue("endd"));
/*
  if ( isset($this->formL->getValue("getFile")[0]) && 
			$this->formL->getValue("getFile")[0] == "yes" ) {
	  $this->sessnp = "yes";
  }*/
    // Process the verified data here.
}

/**
 * Display the content of the page.
 *
 * @param $title is page title.
 * @param $uid is user id passed by reference.
 */
function showContent($title, &$uid) {

// Put HTML after the closing PHP tag
  global $radiolist2;
?>
<script src="https://www.saveourshores.org/js/app2.js"></script>     
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=
AIzaSyAUAzdEbG4JtsNuNhq30xqqGpV7QRW7_hE&sensor=false"></script>
<script>
	function initialise() {
		
		var myLatlng = new google.maps.LatLng(36.962,-122.001); // Add the coordinates
		var mapOptions = {
			zoom: 10, // The initial zoom level when your map loads (0-20)
			zoomControl:true, // Set to true if using zoomControlOptions below, or false to remove all zoom controls.
			zoomControlOptions: {
  				style:google.maps.ZoomControlStyle.DEFAULT // Change to SMALL to force just the + and - buttons.
			},
			center: myLatlng, // Centre the Map to our coordinates variable
			mapTypeId: google.maps.MapTypeId.ROADMAP, // Set the type of Map
			scrollwheel: false, // Disable Mouse Scroll zooming (Essential for responsive sites!)
			// All of the below are set to true by default, so simply remove if set to true:
			panControl:false, // Set to false to disable
			mapTypeControl:false, // Disable Map/Satellite switch
			scaleControl:false, // Set to false to hide scale
			streetViewControl:false, // Set to disable to hide street view
			overviewMapControl:false, // Set to false to remove overview control
			rotateControl:false // Set to false to disable rotate control
	  	}
		var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions); // Render our map within the empty div
<?php 
	$sql = "SELECT Places.name, Places.lat, Places.lon, COALESCE(PN.Sum, 0 ) AS CSum FROM Places
LEFT JOIN ( SELECT C.lat, C.lon, SUM(tally.number) AS Sum,
(   SELECT DISTINCT Places.name AS pname
                FROM Places
		ORDER BY 
		( 3959 * acos( cos( radians(C.lat) ) * 
                cos( radians( Places.lat ) ) * 
                cos( radians( Places.lon ) - radians(C.lon) ) + 
                     sin( radians(C.lat) ) * 
                sin( radians( Places.lat ) ) ) ) 
		LIMIT 1 ) AS placename
                FROM Collector AS C, tally
                WHERE tally.cid = C.cid
                GROUP BY placename) AS PN
ON Places.name = placename
ORDER BY CSum DESC";
  $result = $this->db->query($sql);
  $n = 0;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$pname = $row['name'];
		$plat  = $row['lat'];
		$plon  = $row['lon'];
		$psum  = $row['CSum'];
		if ( $n == 0 ) {
			$thirds2 = 2*$psum/3 ;
			$thirds1 = $psum/3 ;
		}
		echo "myLatlng = new google.maps.LatLng(" . $plat . "," . $plon . ");\n";
		echo "var marker" . $n . "= new google.maps.Marker({ \n" ;
		echo "position: myLatlng, \n";
		echo "map: map, \n" ;
		if ( $psum >= $thirds2 ) 
			echo "icon: 'https://maps.google.com/mapfiles/ms/icons/red.png',\n" ;
		else if ( $psum >= $thirds1 )
			echo "icon: 'https://maps.google.com/mapfiles/ms/icons/yellow.png',\n" ;
		else 
			echo "icon: 'https://maps.google.com/mapfiles/ms/icons/green.png',\n" ;
		echo 'title: "' . $pname . '" });' . "\n" ;
		$n++ ;
	}
?>
/*		var marker = new google.maps.Marker({ // Set the marker
			position: myLatlng, // Position marker to coordinates
			map: map, // assign the marker to our map variable
			icon: 'http://maps.google.com/mapfiles/ms/icons/green.png',
			title: 'Twin Lakes State Beach' // Marker ALT Text
		}); */
		
		// 	google.maps.event.addListener(marker, 'click', function() { // Add a Click Listener to our marker 
		//		window.location='http://www.snowdonrailway.co.uk/shop_and_cafe.php'; // URL to Link Marker to (i.e Google Places Listing)
		// 	});
		
		var infowindow = new google.maps.InfoWindow({ // Create a new InfoWindow
  			content:"<h3>Snowdown Summit Cafe</h3><p>Railway Drive-through available.</p>" // HTML contents of the InfoWindow
  		});
		google.maps.event.addListener(marker, 'click', function() { // Add a Click Listener to our marker
  			infowindow.open(map,marker); // Open our InfoWindow
  		});
		
		google.maps.event.addDomListener(window, 'resize', function() { map.setCenter(myLatlng); }); // Keeps the Pin Central when resizing the browser on responsive sites
	}
	google.maps.event.addDomListener(window, 'load', initialise); // Execute our 'initialise' function once the page has loaded. 
</script>
<div class="preamble" id="SOS-preamble" role="article">
<h3>Select type of Data Base Sort.</h3><p></p>
<?php
	echo $this->formL->reportErrors();
	echo $this->formL->start('POST', "", 'name="appoutsort"');
?>
<fieldset>
<legend>Please Select the sort type below</legend>
<br>
<table>
<tr> <th>Sort By</th>
<td class="inputcell">
<?php
  $radiolist = array("Name"=>1,"Date"=>2,"Item"=>3,"Location"=>4,"Location Batched"=>5,"Location Batched Spread Sheet"=>6);
  $dates = array(); 
  $sql = "SELECT DISTINCT tdate FROM Collector ORDER BY tdate DESC";
  $result = $this->db->query($sql);
  $n = 0;
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $dates[$row["tdate"]] = $n++;
  }  
  echo $this->formL->makeSelect('cat', $radiolist/*, "", "id='mainsort' onchange='newSort()'"*/); 
  echo "</td><td>from " . $this->formL->makeSelect('startd', $dates, $n-1);
  echo "</td><td>to " . $this->formL->makeSelect('endd', $dates, 0);
?>
</td>
</tr>
<tr><td>
<input class="subbutton" type="submit" name="Submit" value="Submit">
</td>
<td></td><td>
<?php
//  echo$this->formL->makeCheckBoxes('getFile',array('Download File?'=>'yes'));
?>
<a href="https://saveourshores.org/output.csv">Click here to download the data as output.csv</a>
</td>
</tr>
</table>
</fieldset>

</form>
<script> newSort(); </script>
<?php
//   $radiolist = array("Total Amount"=>0,"Date"=>1,"Item"=>2,"Location"=>3);

  if ( $uid[0] > 0 ) {
    switch ($uid[0]) {
      case $radiolist["Name"]: // Name
         $this->nameTable($uid[1],$uid[2],$dates);
       break;
      case $radiolist["Date"]: // Date
         $this->dateTable($uid[1],$uid[2],$dates);
       break;
      case $radiolist["Item"]: 
         $this->itemTable($uid[1],$uid[2],$dates);
       break;
      case $radiolist["Location"]: 
         $this->locationTable($uid[1],$uid[2],$dates);
       break;
      case $radiolist["Location Batched"]: 
         $this->locBatchTable($uid[1],$uid[2],$dates);
       break;
      case $radiolist["Location Batched Spread Sheet"]: 
         $this->locBatchTableSS($uid[1],$uid[2],$dates);
       break;
      default:
        echo $uid[0];
    }
  }
  else echo "No sort selected.";
//  echo ($this->formL->getValue("getFile")[0]);
?>
</div>

<?php
$this->formL->finish();
?>
<h3>Beach Status Map</h3>
<div id="map-canvas" style="height:400px; width:600px;"></div>
<?php
//mysql_free_result($result);
}

/**
 * Display the tables unsorted
 *
 */
function nameTable($sub,$subsub,$dates) {

$sort_string = "" ;
  $startd = array_search($sub,$dates);
  $endd = array_search($subsub,$dates);
// "Top Names"=>1,"Most Recent Date"=>2,"Category"=>3,"Top Items"=>4,"Top Locations"
  echo '<table class="volemail"><tr><th>Name</th><th>Date</th><th>Place</th>';
  echo '<th>Latitude</th><th>Longitude</th><th>Item</th><th>Amount</th></tr>';
  $sql = "SELECT cid, name, tdate, lat, lon FROM Collector " ;
  $sql.= empty($startd) ? "" : " WHERE `tdate` >= '" . $startd . "' ";
  $sql.= empty($endd) ? "" : " AND `tdate` <= '" . $endd . "' " ;
  $sql.= " ORDER BY name ASC";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    $cdate = $row["tdate"];
    $lat   = $row["lat"];
    $lon   = $row["lon"];
    $cid   = $row["cid"];  // cid for next query.

	$sql = "SELECT DISTINCT Places.name AS pname, 
				( 3959 * acos( cos( radians('$lat') ) 
                     * cos( radians( Places.lat ) ) * 
                cos( radians( Places.lon ) - radians('$lon') ) + 
                     sin( radians('$lat') ) * 
                sin( radians( Places.lat ) ) ) ) 
                AS distance 
                FROM Places, Collector
				WHERE Collector.cid = '$cid'
				HAVING distance < 1.0
				ORDER BY distance
				LIMIT 1";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $pname = $row2["pname"];
    if ( $pname == "" ) {
      $pname = "Other" ;
    }
    
    $sql = "SELECT items.item AS name, tally.number AS number 
                    FROM tally, items
                    WHERE tally.cid = $cid AND
                    tally.iid = items.iid";
    $res3 = $this->db->query($sql);
	while ($row3 = $res3->fetch(PDO::FETCH_ASSOC)) {
		$item = $row3["name"];
		$amt  = $row3["number"];
		echo "<tr><td>" . $name . "</td>";
		echo "<td>" . $cdate . "</td>";
		echo "<td>" . $pname . "</td>";
		echo "<td>" . $lat . "</td>";
		echo "<td>" . $lon . "</td>";
		echo "<td>" . $item . "</td>";
		echo "<td>" . $amt . "</td></tr>";
		$plot_data[] = array($name,	$cdate , $pname, $lat, $lon, $item, $amt);
		$name = $cdate = $pname = $lat = $lon = "";
/*    echo '<td class="right">' . round($trash,2) . "</td>";
    echo '<td class="right">' . round($recycle,2) . "</td></tr>";*/
	}
  } 
  echo "</table><br>";
/*  $plot = new PHPlot();
  $plot->SetImageBorderType('plain');

  $plot->SetPlotType('bars');
  $plot->SetDataType('text-data');
//  $plot->SetUseTTF(TRUE);
  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
  $plot->SetDataColors(array('SkyBlue', 'DarkGreen'));
  $plot->SetDataValues($plot_data);
  $plot->SetFailureImage(False); // No error images
  $plot->SetPrintImage(False); // No automatic output


  # Main plot title:
  $plot->SetTitle('Trash and Recycling for each Person');

  # Make a legend for the 3 data sets plotted:  
  $plot->SetLegend(array('Trash', 'Recycling'));

  # Turn off X tick labels and ticks because they don't apply here:
  $plot->SetXTickLabelPos('none');
  $plot->SetXTickPos('none');

  $plot->DrawGraph();

  echo "<img src='" . $plot->EncodeImage() . "'>";*/
  $title = array("Name", "Date", "Place", "Latitude", "Longitude", "Item", "Amount");

  $this->write_csv($title,$plot_data);
}
/**
 * Display the date table
 *
 */
function dateTable($sub,$subsub,$dates) {

$sort_string = "" ;
  $startd = array_search($sub,$dates);
  $endd = array_search($subsub,$dates);
  
  echo '<table class="volemail"><tr><th>Date</th><th>Place</th><th>Name</th>';
  echo '<th>Item</th><th>Amount</th></tr>';
  $sql = "SELECT cid, name, tdate, lat, lon FROM Collector " ;
  $sql.= empty($startd) ? "" : " WHERE `tdate` >= '" . $startd . "' ";
  $sql.= empty($endd) ? "" : " AND `tdate` <= '" . $endd . "' " ;
  $sql.= " ORDER BY tdate DESC";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    $cdate = $row["tdate"];
    $lat   = $row["lat"];
    $lon   = $row["lon"];
    $cid   = $row["cid"];  // cid for next query.

	$sql = "SELECT DISTINCT Places.name AS pname, 
				( 3959 * acos( cos( radians('$lat') ) 
                     * cos( radians( Places.lat ) ) * 
                cos( radians( Places.lon ) - radians('$lon') ) + 
                     sin( radians('$lat') ) * 
                sin( radians( Places.lat ) ) ) ) 
                AS distance 
                FROM Places, Collector
				WHERE Collector.cid = '$cid'
				HAVING distance < 1.0
				ORDER BY distance
				LIMIT 1";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $pname = $row2["pname"];
    if ( $pname == "" ) {
      $pname = "Other" ;
    }
    
    $sql = "SELECT items.item AS name, tally.number AS number 
                    FROM tally, items
                    WHERE tally.cid = $cid AND
                    tally.iid = items.iid";
    $res3 = $this->db->query($sql);
	while ($row3 = $res3->fetch(PDO::FETCH_ASSOC)) {
		$item = $row3["name"];
		$amt  = $row3["number"];
		echo "<tr><td>" . $cdate . "</td>";
		echo "<td>" . $pname . "</td>";
		echo "<td>" . $name . "</td>";
		echo "<td>" . $item . "</td>";
		echo "<td>" . $amt . "</td></tr>";
		$plot_data[] = array($cdate, $pname, $name, $item, $amt);
//		$name = $cdate = $pname = $lat = $lon = "";
/*    echo '<td class="right">' . round($trash,2) . "</td>";
    echo '<td class="right">' . round($recycle,2) . "</td></tr>";*/
	}
  } 
  echo "</table><br>";
/*  $plot = new PHPlot();
  $plot->SetImageBorderType('plain');

  $plot->SetPlotType('bars');
  $plot->SetDataType('text-data');
//  $plot->SetUseTTF(TRUE);
  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
  $plot->SetDataColors(array('SkyBlue', 'DarkGreen'));
  $plot->SetDataValues($plot_data);
  $plot->SetFailureImage(False); // No error images
  $plot->SetPrintImage(False); // No automatic output


  # Main plot title:
  $plot->SetTitle('Trash and Recycling for each Person');

  # Make a legend for the 3 data sets plotted:  
  $plot->SetLegend(array('Trash', 'Recycling'));

  # Turn off X tick labels and ticks because they don't apply here:
  $plot->SetXTickLabelPos('none');
  $plot->SetXTickPos('none');

  $plot->DrawGraph();

  echo "<img src='" . $plot->EncodeImage() . "'>";*/
  $title = array("Date", "Place", "Name", "Item", "Amount");

  $this->write_csv($title,$plot_data);
}
/**
 * Display the Category table
 *
 */
function categoryTable($sub,$subsub) {

$sort_string = "" ;
  if ( $subsub != "" ) {
	if ( $sub == "By Date" ) {
	  $sort_string = " AND Collector.tdate = '$subsub'";
	  echo "Sorted by Date '$subsub'<br><br>";
	}
	else if ( $sub == "By Name" ) {
	  $sort_string = " AND Collector.name = '$subsub'";
	  echo "Sorted by Name '$subsub'<br><br>";
	}
	else if ( $sub == "By Item" ) {
	  $sort_string = " AND items.item = '$subsub'";
	  echo "Sorted by Item '$subsub'<br><br>";
	}
	else if ( $sub == "By Location" ) {
	  $sort_string = "";
	  echo "Sorted by Location '$subsub'<br><br>";
	  echo "Not implemented yet.<br><br>";
	}
  }
  echo '<table class="volemail"> <tr> <th>Category</th>';
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $sql = "SELECT name, catid 
             FROM Categories ";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    $catid = $row["catid"];
    echo "<tr><td>" . $name . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector
                    WHERE (items.category = '$catid') AND
                    tally.iid = items.iid AND
                    items.recycle IS FALSE";
    $sql .= $sort_string;
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $trash = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($trash,2) . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector
                    WHERE (items.category = '$catid') AND
                    tally.iid = items.iid AND
                    items.recycle IS TRUE";
    $sql .= $sort_string;
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $recycle = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($recycle,2) . "</td></tr>";
	$plot_data[] = array($name,round($trash,2),round($recycle,2));
  } 
  echo "</table><br>";
  $plot = new PHPlot();
  $plot->SetImageBorderType('plain');

  $plot->SetPlotType('bars');
  $plot->SetDataType('text-data');
//  $plot->SetUseTTF(TRUE);
  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
  $plot->SetDataColors(array('SkyBlue', 'DarkGreen'));
  $plot->SetDataValues($plot_data);
  $plot->SetFailureImage(False); // No error images
  $plot->SetPrintImage(False); // No automatic output


  # Main plot title:
  $plot->SetTitle('Trash and Recycling for each Category');

  # Make a legend for the 3 data sets plotted:  
  $plot->SetLegend(array('Trash', 'Recycling'));

  # Turn off X tick labels and ticks because they don't apply here:
  $plot->SetXTickLabelPos('none');
  $plot->SetXTickPos('none');

  $plot->DrawGraph();

  echo "<img src='" . $plot->EncodeImage() . "'>";
  $this->write_csv("Category",$plot_data);
}
/**
 * Display the Item table
 *
 */
function itemTable($sub,$subsub,$dates) {

  global $plot_data ;
  $startd = array_search($sub,$dates);
  $endd = array_search($subsub,$dates);

  $ItemsA = array();
  echo '<table class="volemail"><tr><th>Item</th><th>Amount</th><th>Place</th></tr>';
  $sql = "SELECT T.iid AS item, lat, lon, sum(number) as total FROM Collector, tally AS T " ;
  $sql.= "WHERE Collector.cid = T.cid";
//  $sql = "SELECT items.item AS item, sum(number) as total, Collector.lat AS lat, Collector.lon AS lon" ;
//  $sql.= "FROM `items` AS I INNER JOIN `tally` AS T ON `T.iid` = `I.iid` ";
//  $sql.= "INNER JOIN `Collector` AS CO ON `CO,cid` = `T.cid` ";
//  $sql.= " WHERE tally.iid = items.iid CO.cid = tally.cid";
//  $sql.= "WHERE items.iid = tally.iid AND Collector.cid = tally.cid ";
  $sql.= empty($startd) ? "" : " AND `tdate` >= '" . $startd . "' ";
  $sql.= empty($endd) ? "" : " AND `tdate` <= '" . $endd . "' " ;
  $sql.= " GROUP BY lat, lon, item";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $lat   = $row["lat"];
    $lon   = $row["lon"];
    $total = $row["total"];
    $item  = $row["item"];

	$sql = "SELECT DISTINCT Places.name AS pname, 
				( 3959 * acos( cos( radians('$lat') ) 
                     * cos( radians( Places.lat ) ) * 
                cos( radians( Places.lon ) - radians('$lon') ) + 
                     sin( radians('$lat') ) * 
                sin( radians( Places.lat ) ) ) ) 
                AS distance 
                FROM Places
				HAVING distance < 1.0
				ORDER BY distance
				LIMIT 1";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $pname = $row2["pname"];
    if ( $pname == "" ) {
      $pname = "Other" ;
    }
    
    $sql = "SELECT item FROM items WHERE iid = $item";
    $res3 = $this->db->query($sql);
    $row3 = $res3->fetch(PDO::FETCH_ASSOC);
    $item = $row3["item"];
    
	$ItemsA[] = array('Place' => $pname, 'Item' => $item, 
						'Amount' => $total );
		$plot_data[] = array($item, $total , $pname);
	}

  usort($ItemsA, function ($item1, $item2) {
    return strcmp($item1['Item'],$item2['Item']);
  }) ;

  usort($ItemsA, function ($item1, $item2) {
    return strcmp($item1['Place'],$item2['Place']);
  }) ;

  foreach($ItemsA as $value) {
		echo "<tr><td>" . $value['Item'] . "</td>";
		echo "<td>" . $value['Amount'] . "</td>";
		echo "<td>" . $value['Place'] . "</td></tr>";
  }
  echo "</table><br>";
  $title = array("Item", "Amount", "Place" );

  $this->write_csv($title,$plot_data);
}
/**
 * Display the Location table
 *
 */
function locationTable($sub,$subsub,$dates) {

  global $plot_data ;
  $startd = array_search($sub,$dates);
  $endd = array_search($subsub,$dates);
  $Places = array();
  $sort_string = "" ;
  echo '<table class="volemail"><tr><th>Place</th><th>Name</th><th>Date</th>';
  echo '<th>Latitude</th><th>Longitude</th><th>Item</th><th>Amount</th></tr>';

  $sql = "SELECT cid, name, tdate, lat, lon FROM Collector " ;
  $sql.= empty($startd) ? "" : " WHERE `tdate` >= '" . $startd . "' ";
  $sql.= empty($endd) ? "" : " AND `tdate` <= '" . $endd . "' " ;
  $sql.= " ORDER BY name ASC";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    $cdate = $row["tdate"];
    $lat   = $row["lat"];
    $lon   = $row["lon"];
    $cid   = $row["cid"];  // cid for next query.

	$sql = "SELECT DISTINCT Places.name AS pname, 
				( 3959 * acos( cos( radians('$lat') ) 
                     * cos( radians( Places.lat ) ) * 
                cos( radians( Places.lon ) - radians('$lon') ) + 
                     sin( radians('$lat') ) * 
                sin( radians( Places.lat ) ) ) ) 
                AS distance 
                FROM Places, Collector
				WHERE Collector.cid = '$cid'
				HAVING distance < 1.0
				ORDER BY distance
				LIMIT 1";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $pname = $row2["pname"];
    if ( $pname == "" ) {
      $pname = "Other" ;
    }
    
    $Places[] = array('Place' => $pname, 'Name' => $name, 'Date' => $cdate,
						'lat' => $lat, 'lon' => $lon, 'CID' => $cid);
  }
  
  usort($Places, function ($item1, $item2) {
    return strcmp($item1['Place'],$item2['Place']);
  }) ;
  
  foreach($Places as $value) {

	$cid = $value['CID'];
    $sql = "SELECT items.item AS name, tally.number AS number 
                    FROM tally, items
                    WHERE tally.cid = $cid AND
                    tally.iid = items.iid";
    $res3 = $this->db->query($sql);
	while ($row3 = $res3->fetch(PDO::FETCH_ASSOC)) {
		$item = $row3["name"];
		$amt  = $row3["number"];
		echo "<tr><td>" . $value['Place'] . "</td>";
		echo "<td>" . $value['Name'] . "</td>";
		echo "<td>" . $value['Date'] . "</td>";
		echo "<td>" . $value['lat'] . "</td>";
		echo "<td>" . $value['lon'] . "</td>";
		echo "<td>" . $item . "</td>";
		echo "<td>" . $amt . "</td></tr>";
		$plot_data[] = array($value['Place'], $value['Date'] , $value['Name'],
								$value['lat'], $value['lon'], $item, $amt);
		$value['Name'] = $value['Date'] = $value['Place'] = $value['lat'] = $value['lon'] = "";
	}
  } 
  echo "</table><br>";
  $title = array("Place", "Name", "Date", "Latitude", "Longitude", "Item", "Amount");

  $this->write_csv($title,$plot_data);
}

/**
 * Display the Location by batch table
 *
 */
function locBatchTable($sub,$subsub,$dates) {

  global $plot_data ;
  $startd = array_search($sub,$dates);
  $endd = array_search($subsub,$dates);
  $Places = array();
  $sort_string = "" ;
  echo '<table class="volemail"><tr><th>Place</th><th>Date</th>';
  echo '<th>Item</th><th>Amount</th></tr>';

  $sql = "SELECT C.cid AS cid,C.lat, C.lon, C.tdate,
		(   SELECT DISTINCT Places.name AS pname
                FROM Places
				ORDER BY 
				( 3959 * acos( cos( radians(C.lat) ) * 
                cos( radians( Places.lat ) ) * 
                cos( radians( Places.lon ) - radians(C.lon) ) + 
                     sin( radians(C.lat) ) * 
                sin( radians( Places.lat ) ) ) ) 
				LIMIT 1 ) AS pname,
            items.item AS Iname,
            SUM(tally.number)
            FROM Collector AS C, items, tally
            WHERE tally.cid = C.cid" ;
  $sql.= empty($startd) ? "" : " AND C.tdate >= '" . $startd . "' ";
  $sql.= empty($endd) ? "" : " AND C.tdate <= '" . $endd . "' " ;
  $sql.= "  AND items.iid = tally.iid
            GROUP BY C.tdate, pname, Iname
            ORDER BY C.tdate DESC" ;
//  echo $sql ;
  $result = $this->db->query($sql);
  $oldPlace = "";
  $oldDate  = "";
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
//	print_r($row);
    $cdate = $row["tdate"];
    $lat   = $row["lat"];
    $lon   = $row["lon"];
    $cid   = $row["cid"];  // cid for next query.
    $pname = $row["pname"];
    if ( $pname == "" ) {
      $pname = "Other" ;
    }
	$tally = $row["SUM(tally.number)"];
	$iname = $row["Iname"];
/*	if ( ($cdate == $oldDate) &&
			($pname == $oldPlace) ) {
				$pname = "" ;
				$cdate = "" ;
			} else {
				$oldPlace = $pname ;
				$oldDate  = $cdate ;
			}*/
    $Places[] = array('Place' => $pname, 'Date' => $cdate,
						'lat' => $lat, 'lon' => $lon, 'Item' => $iname, 'amt' => $tally);
  }
  
  foreach($Places as $value) {

		//$amt  = $row3["number"];
		echo "<tr><td>" . $value['Place'] . "</td>";
		echo "<td>" . $value['Date'] . "</td>";
		echo "<td>" . $value['Item'] . "</td>";
		echo "<td>" . $value['amt'] . "</td>";
		echo "</tr>";
		$plot_data[] = array($value['Place'] , $value['Date'], 
								$value['Item'], $value['amt']);
  } 
  echo "</table><br>";
  $title = array("Place", "Date", "Item", "Amount");

  $this->write_csv($title,$plot_data);
}

/**
 * Display the Location by batch table for SpreadSheets
 *
 */
function locBatchTableSS($sub,$subsub,$dates) {

  global $plot_data ;
  $startd = array_search($sub,$dates);
  $endd = array_search($subsub,$dates);
  $sort_string = "" ;
  $thead = $this->ssHead();
  echo '<table class="volemail"><tr>';
  foreach ($thead as $hd) {
	  echo "<th>" . $hd . "</th>";
  }
  echo '</tr>';

  $sql = "SELECT cid, name, lat, lon, tdate, date, email, hour, eid, adult, youth, pTrash, pRecycle,
		(   SELECT DISTINCT Places.name AS pname
                FROM Places
				ORDER BY 
				( 3959 * acos( cos( radians(C.lat) ) * 
                cos( radians( Places.lat ) ) * 
                cos( radians( Places.lon ) - radians(C.lon) ) + 
                     sin( radians(C.lat) ) * 
                sin( radians( Places.lat ) ) ) ) 
				LIMIT 1 ) AS pname
			FROM `Collector` AS C 
			WHERE cid " ;
  $sql.= empty($startd) ? "" : " AND C.tdate >= '" . $startd . "' ";
  $sql.= empty($endd) ? "" : " AND C.tdate <= '" . $endd . "' " ;
  $sql.= "  ORDER BY C.tdate DESC" ;
//  echo $sql ;
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$rData = array();
//	print_r($row);
    $cdate = $row["tdate"];
    $date  = $row["date"];
    $lat   = $row["lat"];
    $lon   = $row["lon"];
    $cid   = $row["cid"];  // cid for next query.
    $pname = $row["pname"];
    $nname = $row["name"];
    $email = $row["email"];
    $hours = $row["hour"];
    $teid  = $row["eid"];
    $adult = $row["adult"];
    $youth = $row["youth"];
    $trash = $row["pTrash"];
    $recyc = $row["pRecycle"];
    if ( $pname == "" ) {
      $pname = "Other" ;
    }
    $sql = "SELECT IT.item AS Item,
			TA.number AS Total
			FROM `tally` AS TA, 
			`items` AS IT
			WHERE cid = $cid
			AND TA.iid = IT.iid";    
    $result2 = $this->db->query($sql);
    $itms = array();
	while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) { //items
		$itms[$row2["Item"]] = $row2["Total"];
	}
    
	echo "<tr><td>" . $date . "</td>";
	$rData[] = $date;
	echo "<td>" . $cdate . "</td>";
	$rData[] = $cdate;
	echo "<td>" . $pname . "</td>"; // 'Cleanup Site*',
	$rData[] = $pname;
	echo "<td>" . $email . "</td>"; // instead of first name
	$rData[] = $email;
	echo "<td>" . $nname . "</td>"; // instead of last name, whole name
	$rData[] = $nname;
	echo "<td>" . $hours . "</td>"; // hours total of cleanup
	$rData[] = $hours;
	echo "<td>" . "</td>"; // 'What time did the event start?',
	$rData[] = "";
	echo "<td>" . "</td>"; // 'What time did the event end?'
	$rData[] = "";
		
	$sql = "SELECT name
				FROM `Event`
				WHERE eid = $teid";
	$result4 = $this->db->query($sql);
	while ($row4 = $result4->fetch(PDO::FETCH_ASSOC)) { // type of event
		echo "<td>" . $row4["name"] . "</td>";
		$rData[] = $row4["name"];
	}

	$sql = "SELECT CityCounty, City, County 
				FROM `Places`, `CityCounty` AS CC
				WHERE name = " ;
	$sql.= 		'"' . $pname . '"' ; // there are "'" in places
	$sql.=		"AND CityCounty = CC.cid";
	$result3 = $this->db->query($sql);
	while ($row3 = $result3->fetch(PDO::FETCH_ASSOC)) { //,'City/County where the event was held?*',
		echo "<td>" . $row3["City"] . ", " . $row3["County"] . "</td>";
		$rData[] = $row3["City"] . ", " . $row3["County"];
	}

	// 

/*	echo "<td>" . "</td>"; // 'Estimated Cleanup Area (in square miles)?*',
	$rData[] = "";*/
	echo "<td>" . $adult . "</td>"; // 'Number of Adults*',
	$rData[] = $adult;
	echo "<td>" . $youth . "</td>"; // 'Number of Youth*',
	$rData[] = $youth;
	echo "<td>" . $trash . "</td>"; // 'Pounds of Trash Collected*',
	$rData[] = $trash;
	echo "<td>" . $recyc . "</td>"; // 'Pounds of Recycling Collected*'
	$rData[] = $recyc;
    $sql = "SELECT item FROM `items` 
				WHERE used = 1 AND items.order IS NOT NULL
				ORDER BY items.category, items.order";
	$result5 = $this->db->query($sql);
	while ($row5 = $result5->fetch(PDO::FETCH_ASSOC)) { // get titles of used items
		$t = isset($itms[$row5['item']]) ? 
			$itms[$row5['item']] : 0;
		echo "<td>" . $t . "</td>";
		$rData[] = $t;
	}	
/*	$t = isset($itms["Cigarette Butts"]) ? 
		$itms["Cigarette Butts"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Plastic Pieces"]) ? 
		$itms["Plastic Pieces"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Plastic Food Wrappers"]) ? 
		$itms["Plastic Food Wrappers"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Polystyrene Pieces"]) ? $itms["Polystyrene Pieces"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Plastic To-Go Items"]) ? $itms["Plastic To-Go Items"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Paper Pieces"]) ? $itms["Paper Pieces"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Shopping bags"]) ? 
		$itms["Shopping bags"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Balloons"]) ? $itms["Balloons"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Plastic Bottles"]) ? $itms["Plastic Bottles"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Bottle Caps and Rings"]) ? 
		$itms["Bottle Caps and Rings"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;*/
/*	$t = isset($itms["Plastic Cups, plates etc."]) ? 
		$itms["Plastic Cups, plates etc."] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;*/
/*	$t = isset($itms["Polystyrene Foodware (foam)"]) ? 
		$itms["Polystyrene Foodware (foam)"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;*/
/*	$t = isset($itms["Styrofoam food containers"]) ? 
		$itms["Styrofoam food containers"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Plastic fishing line, lures, floats (non-commercial)"]) ?
		$itms["Plastic fishing line, lures, floats (non-commercial)"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;*/
/*	$t = isset($itms["Straws and stirrers"]) ?
		$itms["Straws and stirrers"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Toys and Beach Accessories"]) ? $itms["Toys and Beach Accessories"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Glass Bottles"]) ? $itms["Glass Bottles"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Pieces and Chunks"]) ? $itms["Pieces and Chunks"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Cardboard"]) ? $itms["Cardboard"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Food containers, cups, plates, bowls"]) ? 
		$itms["Food containers, cups, plates, bowls"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Beer Cans"]) ? $itms["Beer Cans"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Soda Cans"]) ? $itms["Soda Cans"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Bottle Caps"]) ? $itms["Bottle Caps"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Bandaids"]) ? $itms["Bandaids"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Batteries"]) ? $itms["Batteries"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
*/
/*	$t = isset($itms["Cardboard, newspapers, magazines"]) ?
		$itms["Cardboard, newspapers, magazines"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	echo "<td>" . "</td>"; // "Newspapers/Magazines"
	$rData[] = "";
	echo "<td>" . "</td>"; // "’Can Pulls/Tabs'"
	$t = isset($itms["Nails"]) ? 
		$itms["Nails"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	echo "<td>" . "</td>"; // "Soda Cans"
	$rData[] = "";
	$t = isset($itms["Condoms"]) ? $itms["Condoms"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Diapers"]) ? $itms["Diapers"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;*/
/*	$t = isset($itms["Personal Hygiene"]) ? $itms["Personal Hygiene"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Disposable lighters"]) ? 
		$itms["Disposable lighters"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Syringes or needles"]) ? 
		$itms["Syringes or needles"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Smoking, tobacco, vape items (not butts)"]) ? 
	$itms["Smoking, tobacco, vape items (not butts)"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Bagged Pet Waste"]) ? 
		$itms["Bagged Pet Waste"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Personal Protective Equipment"]) ? 
		$itms["Personal Protective Equipment"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Wood pallets, pieces and processed wood"]) ? 
	$itms["Wood pallets, pieces and processed wood"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Fishing gear (lures, nets, etc.)"]) ? 
		$itms["Fishing gear (lures, nets, etc.)"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Clothes, cloth"]) ? 
		$itms["Clothes, cloth"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;

	$t = isset($itms["Other, large"]) ? $itms["Other, large"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;
	$t = isset($itms["Other, small"]) ? $itms["Other, small"] : 0;
	echo "<td>" . $t . "</td>";
	$rData[] = $t;*/
	$rData[] = ""; //General Comments about the Cleanup:

	echo "</tr>";
	$plot_data[] = $rData;
  }
  
  echo "</table><br>";


  $this->write_csv($thead,$plot_data);
}

function write_csv($title,$data) {
	$myfile = fopen("output.csv","w") or die("Unable to open file");
	$sep = array("sep=;");
	fputcsv($myfile,$sep);
    fputcsv($myfile, $title,";",'"');
    foreach ($data as $fields) {
       fputcsv($myfile,$fields,";",'"');
    }
    fclose($myfile);
}
function ssHead () {
//    $head = array('Timestamp','First Name*','Last Name*','What was the date of the event?*','What time did the event start?','What time did the event end?','City/County where the event was held?*','Cleanup Site*','Estimated Cleanup Area (in square miles)?*','Number of Adults*','Number of Youth*','Pounds of Trash Collected*','Pounds of Recycling Collected*','Cigarette Butts','Plastic Pieces (larger than 5mm)','Plastic Food Wrappers','Polystyrene Pieces (Styrofoam)','Paper Pieces','Bags (shopping variety)','Balloons','Bottles','Bottle Caps/Rings','Cups, Lids, Plates, Utensils','Polystyrene Cups, Plates, Bowls ','Polystyrene Food "To-Go" Containers','Fishing Line','Straws/Stirrers','Toys','Bottles','Pieces/Chunks','Cardboard ','Food Containers/Cups/Plates/Bowls','Newspapers/Magazines','Beer Cans','Bottle Caps','Can Pulls/Tabs','Fishing Hooks/Lures','Nails','Soda Cans','Band-Aids  ','Batteries','Condoms','Diapers','Disposable Lighters','Feminine Hygiene','Syringes/Needles','Other Items (Please list items below)','General Comments about the Cleanup:');
    $head = array('Timestamp',
    'Date of Cleanup Event/Fecha',
    'Cleanup Site/Sitio de limpieza',
    'FIRST Name (Site captain name/Nombre de coordinador)',
    'LAST Name (Site captain name/Nombre de coordinador)',
    'Total Cleanup Duration (hrs)',
    'Cleanup Start Time',
    'Cleanup End Time',
    'Type of Cleanup',
    'County where the event was held?',
    'Adult Volunteers',
    'Youth Volunteers',
    'Pounds of Trash',
    'Pounds of Recycling');
    $sql = "SELECT item FROM `items` 
				WHERE used = 1 AND items.order IS NOT NULL
				ORDER BY items.category, items.order";
	$result = $this->db->query($sql);
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) { // get titles of used items
		array_push($head, $row['item']);
	} // should put all items in in order.
/*    'Cigarette Butts/Colillas de Cigarrillo',
    'Plastic Pieces/Pedazos de PlÇ­stico (>5mm)',
    'Plastic Food Wrappers/Envoltorios de Comida',
    'Polystyrene Pieces (i.e. foam)/Pedazos de Poliestireno (i.e. unicel)',
    'Plastic To-Go Items/Envases de Comida Õ?Para levarÕ?/PlÇ­stico',
    'Paper Pieces/Pedazos de Paper',
    'shopping bags (plastic)/bolsas de comestibles (de plÇ­stico)',
    'balloons/globos',
    'Plastic bottles (plastic)/botellas (de plÇ­stico)',
    'bottle caps and rings (plastic)/taparroscas y tapas de botellas (de plÇ­stico)',
    'polystyrene foodware (foam)/vasos y platos de poliestireno (unicel)',
    'straws and stirrers/popotes y mezcladores',
    'toys & beach accessories (plastic)/juguetes y accesorios de playa (de plÇ­stico)',
    'bottles (glass)/botellas de vidrio',
    'pieces and chunks (glass)/pedazos y trozos de vidrio',
    'cardboard/cartulinas',
    'food containers (paper): cups, plates, bowls/envases de comida (de papel): vasos,platos',
    'beer cans/latas de cerveza',
    'soda cans/latas de refresco',
    'bottle caps (metal)/corcholatas (de metal)',
    'band-aids/curitas',
    'batteries/baterÇðas',
    'personal hygiene/artÇðculos de higiene personal',
    'disposable lighters/encendedores',
    'syringes, needles/jeringuilla',
    'smoking, tobacco, vape items (NOT butts)/ artÇðculos de fumar (tabaco, no colillas de cigarrillo)',
    'Bagged Pet Waste',
    'Personal Protective Equipment',
    'wood pallets, pieces, and processed wood/paletas de madera, piezas trozos de madera',
    'fishing gear (lures, nets, etc.)/avÇðos de pesca', 
    'clothes, cloth/ropa, paÇño',
    'other. large/otros objetos grandes',
    'other, small/otros objetos pequeÇños',*/
    array_push($head,'Supplies lost/broken/used up?');
    array_push($head,'Challenges or general feedback?');
    array_push($head,'Issues with the location (e.g., parking, bathrooms, trash/recycling bins)?');
    array_push($head,'Awe-inspiring moments, cute stories, heartwarming experiences?');
    return $head;
}
}
?>
<?php
/*
    $myfile = fopen("cklist.csv","w") or die("Unable to open file");
    $txt = array("Title","category","duration","viewcount","likecount","commentcount","latitude","longitude","link");
    fputcsv($myfile, $txt,";",' ');
    foreach ($videos as $fields) {
       fputcsv($myfile,$fields,";",'"');
    }
    fclose($myfile);

SELECT cid FROM Collector WHERE cid NOT IN
( SELECT cid FROM Collector WHERE
( 3959 * acos( cos( radians(Collector.lat) ) 
                     * cos( radians( 37.0067 ) ) * 
                cos( radians( '-121.962' ) - radians(Collector.lon) ) + 
                     sin( radians(Collector.lat) ) * sin( radians( '37.0067' ) ) ) ) < 0.5)
*/
?>
