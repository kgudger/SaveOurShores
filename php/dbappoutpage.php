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

require_once("includes/mainpage.php");
include_once "includes/util.php";
require_once 'includes/phplot-6.2.0/phplot.php';
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
  if ( isset($this->formL->getValue("getFile")[0]) && 
			$this->formL->getValue("getFile")[0] == "yes" ) {
	  $this->sessnp = "yes";
  }
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
<script src="http://www.saveourshores.org/js/app2.js"></script>     
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
			echo "icon: 'http://maps.google.com/mapfiles/ms/icons/red.png',\n" ;
		else if ( $psum >= $thirds1 )
			echo "icon: 'http://maps.google.com/mapfiles/ms/icons/yellow.png',\n" ;
		else 
			echo "icon: 'http://maps.google.com/mapfiles/ms/icons/green.png',\n" ;
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
  $radiolist = array("Name"=>1,"Date"=>2,"Item"=>3,"Location"=>4,"Location Batched"=>5);
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
  echo$this->formL->makeCheckBoxes('getFile',array('Download File?'=>'yes'));
?></td>
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
  
  echo '<table class="volemail"><tr><th>Date</th><th>Name</th><th>Place</th>';
  echo '<th>Latitude</th><th>Longitude</th><th>Item</th><th>Amount</th></tr>';
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
		echo "<td>" . $name . "</td>";
		echo "<td>" . $pname . "</td>";
		echo "<td>" . $lat . "</td>";
		echo "<td>" . $lon . "</td>";
		echo "<td>" . $item . "</td>";
		echo "<td>" . $amt . "</td></tr>";
		$plot_data[] = array($cdate, $name,	$pname, $lat, $lon, $item, $amt);
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
  $title = array("Date", "Name", "Place", "Latitude", "Longitude", "Item", "Amount");

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
  echo '<table class="volemail"><tr><th>Item</th><th>Amount</th><th>Name</th><th>Date</th><th>Place</th>';
  echo '<th>Latitude</th><th>Longitude</th></tr>';
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
		$ItemsA[] = array('Place' => $pname, 'Name' => $name, 'Date' => $cdate,
						'lat' => $lat, 'lon' => $lon, 'Item' => $item, 
						'Amount' => $amt );
		$plot_data[] = array($item, $amt, $name, $cdate , $pname, $lat, $lon);
	}
  } 

  usort($ItemsA, function ($item1, $item2) {
    return strcmp($item1['Item'],$item2['Item']);
  }) ;

  foreach($ItemsA as $value) {
		echo "<tr><td>" . $value['Item'] . "</td>";
		echo "<td>" . $value['Amount'] . "</td>";
		echo "<td>" . $value['Name'] . "</td>";
		echo "<td>" . $value['Date'] . "</td>";
		echo "<td>" . $value['Place'] . "</td>";
		echo "<td>" . $value['lat'] . "</td>";
		echo "<td>" . $value['lon'] . "</td></tr>";
  }
  echo "</table><br>";
  $title = array("Item", "Amount", "Name", "Date", "Place", "Latitude", "Longitude" );

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
  echo '<table class="volemail"><tr><th>Date</th><th>Place</th>';
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
	if ( ($cdate == $oldDate) &&
			($pname == $oldPlace) ) {
				$pname = "" ;
				$cdate = "" ;
			} else {
				$oldPlace = $pname ;
				$oldDate  = $cdate ;
			}
    $Places[] = array('Place' => $pname, 'Date' => $cdate,
						'lat' => $lat, 'lon' => $lon, 'Item' => $iname, 'amt' => $tally);
  }
  
  foreach($Places as $value) {

		//$amt  = $row3["number"];
		echo "<tr><td>" . $value['Date'] . "</td>";
		echo "<td>" . $value['Place'] . "</td>";
		echo "<td>" . $value['Item'] . "</td>";
		echo "<td>" . $value['amt'] . "</td>";
		echo "</tr>";
		$plot_data[] = array($value['Date'], $value['Place'] , 
								$value['Item'], $value['amt']);
  } 
  echo "</table><br>";
  $title = array("Date", "Place", "Item", "Amount");

  $this->write_csv($title,$plot_data);
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
