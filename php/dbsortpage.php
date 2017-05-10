<?php
/**
* @file dbsortpage.php
* Purpose: Sort Data Base
* Extends MainPage Class
*
* @author Keith Gudger
* @copyright  (c) 2015, Keith Gudger, all rights reserved
* @license    http://opensource.org/licenses/BSD-2-Clause
* @version    Release: 1.0
* @package    SOS
*
* @note Has processData and showContent, 
* main and checkForm in MainPage class not overwritten.
* 
*/

require_once("wp-content/plugins/leaderboard/includes/mainpage.php");
include_once "includes/util.php";
require_once 'includes/phplot-6.2.0/phplot.php';
$plot_data = array();

/**
 * Child class of MainPage used for user preferrences page.
 *
 * Implements processData and showContent
 */
  $radiolist = array("Amount"=>0,"Date"=>1,"Item"=>2,"Location"=>3);
  $radiolist2 = array(""=>0,"Top Names"=>1,"Most Recent Date"=>2,"Category"=>3,"Top Items"=>4,"Top Locations"=>5);

class dbSortPage extends MainPage {

/**
 * Process the data and insert / modify database.
 *
 * @param $uid is user id passed by reference.
 */
function processData(&$uid) {
  $radiolist = array("Amount"=>0,"Date"=>1,"Item"=>2,"Location"=>3);
  global $radiolist2;
  $uid = array($this->formL->getValue("cat"),$this->formL->getValue("subsort"),$this->formL->getValue("subsubsort"));
  if ( $this->formL->getValue("getFile")[0] == "yes" ) {
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
  global $radiolist;
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
	$sql = "SELECT Places.name, Places.lat, Places.lon, COALESCE(PN.Max, 0 ) AS Maxdate FROM Places
LEFT JOIN (SELECT C.cid AS cid,C.lat, C.lon, C.tdate, MAX(C.tdate) AS Max,
(   SELECT DISTINCT Places.name AS pname
                FROM Places
		ORDER BY 
		( 3959 * acos( cos( radians(C.lat) ) * 
                cos( radians( Places.lat ) ) * 
                cos( radians( Places.lon ) - radians(C.lon) ) + 
                     sin( radians(C.lat) ) * 
                sin( radians( Places.lat ) ) ) ) 
		LIMIT 1 ) AS pname
                FROM Collector AS C
                GROUP BY pname) AS PN
ON Places.name=pname
GROUP by Places.name";
  $result = $this->db->query($sql);
  $tdate = time(); // current Unix Time
  $t4week = $tdate - (4 * 7 * 24 * 60 * 60);
  $t8week = $tdate - (8 * 7 * 24 * 60 * 60);
//  echo "console.log('t4 is ' + " . $t4week . " + ' t8 is ' + " . $t8week . ");\n" ;
	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
		$pname = $row['name'];
		$plat  = $row['lat'];
		$plon  = $row['lon'];
		$pdate  = strtotime($row['Maxdate']);
		echo "myLatlng = new google.maps.LatLng(" . $plat . "," . $plon . ");\n";
		echo "var marker" . $n . "= new google.maps.Marker({ \n" ;
		echo "position: myLatlng, \n";
		echo "map: map, \n" ;
		if ( $pdate <= $t8week ) 
			echo "icon: 'http://maps.google.com/mapfiles/ms/icons/red.png',\n" ;
		else if ( $pdate <= $t4week )
			echo "icon: 'http://maps.google.com/mapfiles/ms/icons/yellow.png',\n" ;
		else 
			echo "icon: 'http://maps.google.com/mapfiles/ms/icons/green.png',\n" ;
		echo 'title: "' . $pname . '" });' . "\n" ;
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
<?php
	echo $this->formL->reportErrors();
	echo $this->formL->start('POST', "", 'name="databasesort"');
?>
<fieldset>
<legend>Please Select how you would like to sort the data.</legend>
<br>
<?php
  $radiolist = array("Total Amount"=>0,"Date"=>1,"Item"=>2,"Location"=>3);
  echo $this->formL->makeSelect('cat', $radiolist, "", "id='mainsort' onchange='newSort()'");?>
<input class="subbutton" type="submit" name="Submit" value="Submit">
</fieldset>
</form>
<script> newSort(); </script>
<?php
  if ( $uid[0] >= 0 ) {
    switch ($uid[0]) {
      case $radiolist["Name"]: // Name
         $this->nameTable($uid[1],$uid[2]);
       break;
      case $radiolist["Total Amount"]: // Amount 
         $this->amountTable($uid[1],$uid[2]);
       break;
      case $radiolist["Date"]: // Date
         $this->dateTable($uid[1],$uid[2]);
       break;
      case $radiolist["Category"]: 
         $this->categoryTable($uid[1],$uid[2]);
       break;
      case $radiolist["Item"]: 
         $this->itemTable($uid[1],$uid[2]);
       break;
      case $radiolist["Location"]: 
         $this->locationTable($uid[1],$uid[2]);
       break;
      default:
        echo $uid[0];
    }
  }
  else echo "No sort selected.";
  echo ($this->formL->getValue("getFile")[0]);
?>
</div>
<?php
$this->formL->finish();
?>
<h3>Neediest Beaches Map</h3>
<div id="map-canvas" style="height:400px; width:600px;"></div>
<div> Legend: <span style="color:green;">Green</span> beaches cleaned in the last 4 weeks, <br>
<span style="color:yellow;">Yellow</span> beaches cleaned 4 to 8 weeks ago,<br>
<span style="color:red;">Red</span> beaches have not been cleaned in over 8 weeks.</div>

<?php
//mysql_free_result($result);
}

/**
 * Display the name table
 *
 */
function nameTable($sub,$subsub) {

$sort_string = "" ;
// "Top Names"=>1,"Most Recent Date"=>2,"Category"=>3,"Top Items"=>4,"Top Locations"
  if ( $subsub != "" ) {
	if ( $sub == "By Category" ) {
	  $sort_string = " AND Categories.name = '$subsub' 
						AND Categories.catid = items.category";
	  echo "Sorted by Category '$subsub'<br><br>";
	}
	else if ( $sub == "By Date" ) {
	  $sort_string = " AND Collector.tdate = '$subsub'";
	  echo "Sorted by Date '$subsub'<br><br>";
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
  echo '<table class="volemail"> <tr> <th>Name</th>';
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $sql = "SELECT DISTINCT name FROM Collector ORDER BY tdate DESC";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    echo "<tr><td>" . $name . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector, Categories
                    WHERE (Collector.name = '$name') AND
                    Collector.cid = tally.cid AND
                    tally.iid = items.iid AND
                    items.recycle IS FALSE";
    $sql .= $sort_string;
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $trash = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($trash,2) . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector, Categories
                    WHERE (Collector.name = '$name') AND
                    Collector.cid = tally.cid AND
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
//  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
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

  echo "<img src='" . $plot->EncodeImage() . "'>";
  $this->write_csv("Name",$plot_data);
}
/**
 * Display the date table
 *
 */
function dateTable($sub,$subsub) {

$sort_string = "" ;
  if ( $subsub != "" ) {
	if ( $sub == "By Category" ) {
	  $sort_string = " AND Categories.name = '$subsub' 
						AND Categories.catid = items.category";
	  echo "Sorted by Category '$subsub'<br><br>";
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
  echo '<table class="volemail"> <tr> <th>Date</th>';
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $sql = "SELECT DISTINCT CAST(`tdate` AS DATE) AS dateonly 
             FROM Collector ORDER BY date DESC";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $date = $row["dateonly"];
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector, Categories
                    WHERE (CAST(Collector.tdate AS DATE) = '$date') AND
                    Collector.cid = tally.cid AND
                    tally.iid = items.iid AND
                    items.recycle IS FALSE";
    $sql .= $sort_string;
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $trash = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector, Categories
                    WHERE (CAST(Collector.tdate AS DATE) = '$date') AND
                    Collector.cid = tally.cid AND
                    tally.iid = items.iid AND
                    items.recycle IS TRUE";
    $sql .= $sort_string;
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $recycle = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
	
	if (round($trash,2) > 0 || round($recycle,2) > 0 ) {
		echo "<tr><td>" . $date . "</td>";
		echo '<td class="right">' . round($trash,2) . "</td>";
		echo '<td class="right">' . round($recycle,2) . "</td></tr>";
		$plot_data[] = array($date,round($trash,2),round($recycle,2));
	}
  } 
  echo "</table><br>";
  $plot = new PHPlot();
  $plot->SetImageBorderType('plain');

  $plot->SetPlotType('bars');
  $plot->SetDataType('text-data');
//  $plot->SetUseTTF(TRUE);
//  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
  $plot->SetDataColors(array('SkyBlue', 'DarkGreen'));
  $plot->SetDataValues($plot_data);
  $plot->SetFailureImage(False); // No error images
  $plot->SetPrintImage(False); // No automatic output


  # Main plot title:
  $plot->SetTitle('Trash and Recycling for each Date');

  # Make a legend for the 3 data sets plotted:  
  $plot->SetLegend(array('Trash', 'Recycling'));

  # Turn off X tick labels and ticks because they don't apply here:
  $plot->SetXTickLabelPos('none');
  $plot->SetXTickPos('none');

  $plot->DrawGraph();

  echo "<img src='" . $plot->EncodeImage() . "'>";
  $this->write_csv("Date",$plot_data);
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
//  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
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
function itemTable($sub,$subsub) {

  if ( $subsub != "" ) {
	if ( $sub == "By Category" ) {
	  $sort_string = " AND Categories.name = '$subsub' 
						AND Categories.catid = items.category";
	  echo "Sorted by Category '$subsub'<br><br>";
	}
	else if ( $sub == "By Date" ) {
	  $sort_string = " AND Collector.tdate = '$subsub'";
	  echo "Sorted by Date '$subsub'<br><br>";
	}
	else if ( $sub == "By Name" ) {
	  $sort_string = " AND Collector.name = '$subsub'";
	  echo "Sorted by Name '$subsub'<br><br>";
	}
	else if ( $sub == "By Location" ) {
	  $sort_string = "";
	  echo "Sorted by Location '$subsub'<br><br>";
	  echo "Not implemented yet.<br><br>";
	}
  }
  global $plot_data ;
  echo '<table class="volemail"> <tr> <th>Item</th>';
  echo '<th>Total Weight</th><th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $sql = "SELECT SUM(number*weight) AS Total,
			SUM(number*weight*(items.recycle)) AS Recycling,
			SUM(number*weight*(1-items.recycle)) AS Trash, 
			items.Item AS Item
			FROM tally, items, Collector
			WHERE tally.iid = items.iid AND
					items.Iid = tally.Iid
			GROUP BY items.Item
			ORDER BY Trash DESC LIMIT 10";
  $res2 = $this->db->query($sql);
  $row2 = $res2->fetch(PDO::FETCH_ASSOC);
  while ($row2 = $res2->fetch(PDO::FETCH_ASSOC)) {
	echo "<tr><td>" . $row2['Item'] . "</td>";
		echo '<td class="right">' . round($row2['Total'],2) . "</td>";
		echo '<td class="right">' . round($row2['Trash'],2) . "</td>";
		echo '<td class="right">' . round($row2['Recycling'],2) . "</td></tr>";
		$plot_data[] = array($row2['Item'],round($row2['Total'],2),round($row2['Trash'],2),round($row2['Recycling'],2));
  } 
  echo "</table><br>";
  $plot = new PHPlot();
  $plot->SetImageBorderType('plain');

  $plot->SetPlotType('bars');
  $plot->SetDataType('text-data');
//  $plot->SetUseTTF(TRUE);
//  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
  $plot->SetDataColors(array('SkyBlue', 'maroon', 'DarkGreen'));
  $plot->SetDataValues($plot_data);
  $plot->SetFailureImage(False); // No error images
  $plot->SetPrintImage(False); // No automatic output


  # Main plot title:
  $plot->SetTitle('Trash and Recycling for each Item');

  # Make a legend for the 3 data sets plotted:  
  $plot->SetLegend(array('Trash', 'Recycling'));

  # Turn off X tick labels and ticks because they don't apply here:
  $plot->SetXTickLabelPos('none');
  $plot->SetXTickPos('none');

  $plot->DrawGraph();

  echo "<img src='" . $plot->EncodeImage() . "'>";
  $this->write_csv("Item",$plot_data);
}
/**
 * Display the Location table
 *
 */
function locationTable($sub,$subsub) {

  global $plot_data ;
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
	else if ( $sub == "By Category" ) {
	  $sort_string = " AND Categories.name = '$subsub' 
						AND Categories.catid = items.category";
	  echo "Sorted by Category '$subsub'<br><br>";
	}
  }
  echo '<table class="volemail"> <tr> <th>Place</th>';
  echo '<th>Total Weight</th><th>Trash Weight</th><th>Recycle Weight</th></tr>';
  // Create an associative array with entries for items weight
  // Last entry is "Other"

  $Places = array();
  $sql = "SELECT name FROM Places";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    $Places["$name"] = array("Total" =>0, "Trash" => 0, "Recycle" => 0);
  }
  $Places["Other"] = array("Total" =>0, "Trash" => 0, "Recycle" => 0);
	$sql = "SELECT Collector.cid as CID, lat, lon, 
				SUM(number*weight) AS Total,
				SUM(number*weight*recycle) AS recycle_weight,
				SUM(number*weight*(1-recycle)) AS trash_weight 
				FROM Collector, tally, items, Categories
				WHERE tally.cid = Collector.cid
				AND tally.iid = items.iid";
    $sql .= $sort_string;
	$sql .= " GROUP BY Collector.cid"; 
/*  $sql = "SELECT name, lat, lon
             FROM Places";*/
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
//    $name = $row["name"];
    $cid = $row["CID"];
    $lat = $row["lat"];
    $lon = $row["lon"];
    $total = $row["Total"];
    $trash = $row["trash_weight"];
    $recycle = $row["recycle_weight"];

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
//    echo "Place name is " . $pname . "<br>" ;
    $Places["$pname"]["Total"] += $total;
    $Places["$pname"]["Trash"] += $trash;
    $Places["$pname"]["Recycle"] += $recycle; 
/*
    echo "<tr><td>" . $name . "</td>";
    $sql = "SELECT SUM(total_weight) FROM (
                SELECT (number*weight) AS total_weight,
                ( 3959 * acos( cos( radians(Collector.lat) ) 
                     * cos( radians( '$lat' ) ) * 
                cos( radians( '$lon' ) - radians(Collector.lon) ) + 
                     sin( radians(Collector.lat) ) * 
                sin( radians( '$lat' ) ) ) ) 
                AS distance 
                FROM tally, items, Collector
                WHERE tally.iid = items.iid AND
                Collector.cid = tally.cid AND
                items.recycle IS FALSE
                HAVING distance < 0.5) AS temp
		ORDER BY distance LIMIT 1"; 
    $trash = is_null($row2["SUM(total_weight)"]) ?
      0 : $row2["SUM(total_weight)"] ;
    echo '<td class="right">' . round($trash,2) . "</td>";
    $sql = "SELECT SUM(total_weight) FROM (
                SELECT (number*weight) AS total_weight,
                ( 3959 * acos( cos( radians(Collector.lat) ) 
                     * cos( radians( '$lat' ) ) * 
                cos( radians( '$lon' ) - radians(Collector.lon) ) + 
                     sin( radians(Collector.lat) ) * 
                sin( radians( '$lat' ) ) ) ) 
                AS distance 
                FROM tally, items, Collector
                WHERE tally.iid = items.iid AND
                Collector.cid = tally.cid AND
                items.recycle IS TRUE
                HAVING distance < 0.5) AS temp";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $recycle = is_null($row2["SUM(total_weight)"]) ?
      0 : $row2["SUM(total_weight)"] ;
    echo '<td class="right">' . round($recycle,2) . "</td></tr>"; */
  }
  ksort($Places);
  foreach ($Places as $name => $results) {
	  if ($results["Trash"] > 0 || $results["Recycle"]>0 ) {
		echo "<tr><td>" . $name . "</td>";
		echo "<td>" . round($results["Total"],2) . "</td>";
		echo "<td>" . round($results["Trash"],2) . "</td>";
		echo "<td>" . round($results["Recycle"],2) . "</td>";
		echo "</tr>";
		$plot_data[] = array($name,round($results["Total"],2),round($results["Trash"],2),round($results["Recycle"],2));
	  }
  } 
  echo "</table><br>";
  $plot = new PHPlot();
  $plot->SetImageBorderType('plain');

  $plot->SetPlotType('bars');
  $plot->SetDataType('text-data');
//  $plot->SetUseTTF(TRUE);
//  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
  $plot->SetDataColors(array('SkyBlue', 'maroon', 'DarkGreen'));
  $plot->SetDataValues($plot_data);
  $plot->SetFailureImage(False); // No error images
  $plot->SetPrintImage(False); // No automatic output


  # Main plot title:
  $plot->SetTitle('Total, Trash and Recycling for each Location');

  # Make a legend for the 3 data sets plotted:  
  $plot->SetLegend(array('Trash', 'Recycling'));

  # Turn off X tick labels and ticks because they don't apply here:
  $plot->SetXTickLabelPos('none');
  $plot->SetXTickPos('none');

  $plot->DrawGraph();

  echo "<img src='" . $plot->EncodeImage() . "'>";
  $this->write_csv("Place",$plot_data);
}

/**
 * Display the Amount table
 *
 */
function amountTable($sub,$subsub) {

  if ( $subsub != "" ) {
	if ( $sub == "By Category" ) {
	  $sort_string = " AND Categories.name = '$subsub' 
						AND Categories.catid = items.category";
	  echo "Sorted by Category '$subsub'<br><br>";
	}
	else if ( $sub == "By Date" ) {
	  $sort_string = " AND Collector.tdate = '$subsub'";
	  echo "Sorted by Date '$subsub'<br><br>";
	}
	else if ( $sub == "By Name" ) {
	  $sort_string = " AND Collector.name = '$subsub'";
	  echo "Sorted by Name '$subsub'<br><br>";
	}
	else if ( $sub == "By Location" ) {
	  $sort_string = "";
	  echo "Sorted by Location '$subsub'<br><br>";
	  echo "Not implemented yet.<br><br>";
	}
  }
  global $plot_data ;
  echo '<table class="volemail"> <tr> <th>User Name</th><th>Total Weight</th>';
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $name = $row["item"];
  $iid = $row["iid"];
  $sql = "SELECT SUM(number*weight) AS Total,
			SUM(number*weight*(items.recycle)) AS Recycling,
			SUM(number*weight*(1-items.recycle)) AS Trash, 
			Collector.name AS UserName
			FROM tally, items, Collector
			WHERE tally.iid = items.iid AND
					tally.cid = Collector.cid
			GROUP BY Collector.name
			ORDER BY Trash DESC";
  $res2 = $this->db->query($sql);
  $row2 = $res2->fetch(PDO::FETCH_ASSOC);
  while ($row2 = $res2->fetch(PDO::FETCH_ASSOC)) {
	echo "<tr><td>" . $row2['UserName'] . "</td>";
		echo '<td class="right">' . round($row2['Total'],2) . "</td>";
		echo '<td class="right">' . round($row2['Trash'],2) . "</td>";
		echo '<td class="right">' . round($row2['Recycling'],2) . "</td></tr>";
		$plot_data[] = array($row2['UserName'],round($row2['Total'],2),round($row2['Trash'],2),round($row2['Recycling'],2));
  } 
  echo "</table><br>";
/*  $plot = new PHPlot();
  $plot->SetImageBorderType('plain');

  $plot->SetPlotType('bars');
  $plot->SetDataType('text-data');
//  $plot->SetUseTTF(TRUE);
//  $plot->SetDefaultTTFont('/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf');
  $plot->SetDataColors(array('SkyBlue', 'peru', 'DarkGreen'));
  $plot->SetDataValues($plot_data);
  $plot->SetFailureImage(False); // No error images
  $plot->SetPrintImage(False); // No automatic output


  # Main plot title:
  $plot->SetTitle('Total, Trash and Recycling for the top 10');

  # Make a legend for the 3 data sets plotted:  
  $plot->SetLegend(array('Total', 'Trash', 'Recycling'));

  # Turn off X tick labels and ticks because they don't apply here:
  $plot->SetXTickLabelPos('none');
  $plot->SetXTickPos('none');

  $plot->DrawGraph();

  echo "<img src='" . $plot->EncodeImage() . "'>";*/
  $this->write_csv("Item",$plot_data);
}

function write_csv($title,$data) {

    $myfile = fopen("output.csv","w") or die("Unable to open file");
    $txt = array($title,"Trash Weight","Recycle Weight");
    fputcsv($myfile, $txt,";",' ');
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
