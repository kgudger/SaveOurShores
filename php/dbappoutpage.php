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
  $radiolist = array("Unsorted"=>1,"Date"=>2,"Item"=>3,"Location"=>4);
  global $radiolist2;
  $uid = array($this->formL->getValue("cat"),$this->formL->getValue("sortstart"),$this->formL->getValue("sortend"));
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
	 <th>Limit Start</th>
     <th>Limit End</th></tr>
<tr>
<td class="inputcell">
<?php
  $radiolist = array("Unsorted"=>1,"Date"=>2,"Item"=>3,"Location"=>4);
  echo $this->formL->makeSelect('cat', $radiolist, "", "id='mainsort' onchange='newSort()'");?>
</td>
</tr>
<tr><td>
<input class="subbutton" type="submit" name="Submit" value="Submit">
</td>
<td></td><td>
	<?php echo$this->formL->makeCheckBoxes('getFile',array('Download File?'=>'yes'));
?></td></tr>
</table>
</fieldset>

</form>
<script> newSort(); </script>
<?php
//   $radiolist = array("Total Amount"=>0,"Date"=>1,"Item"=>2,"Location"=>3);

  if ( $uid[0] > 0 ) {
    switch ($uid[0]) {
      case $radiolist["Unsorted"]: // Unsorted
         $this->UnsortTable($uid[1],$uid[2]);
       break;
      case $radiolist["Date"]: // Date
         $this->dateTable($uid[1],$uid[2]);
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
//  echo ($this->formL->getValue("getFile")[0]);
?>
</div>

<?php
$this->formL->finish();
//mysql_free_result($result);
}

/**
 * Display the tables unsorted
 *
 */
function UnsortTable($sub,$subsub) {

$sort_string = "" ;
// "Top Names"=>1,"Most Recent Date"=>2,"Category"=>3,"Top Items"=>4,"Top Locations"
  echo '<table class="volemail"><tr><th>Name</th><th>Date</th><th>Place</th>';
  echo '<th>Latitude</th><th>Longitude</th><th>Item</th><th>Amount</th></tr>';
  $sql = "SELECT cid, name, tdate, lat, lon FROM Collector ORDER BY tdate DESC";
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
function dateTable($sub,$subsub) {

$sort_string = "" ;
  echo '<table class="volemail"><tr><th>Date</th><th>Name</th><th>Place</th>';
  echo '<th>Latitude</th><th>Longitude</th><th>Item</th><th>Amount</th></tr>';
  $sql = "SELECT cid, name, tdate, lat, lon FROM Collector ORDER BY tdate DESC";
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
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $sql = "SELECT item, iid 
             FROM items";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["item"];
    $iid = $row["iid"];
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector, Categories
                    WHERE (items.iid = '$iid') AND
                    tally.iid = items.iid AND
                    items.recycle IS FALSE";
    $sql .= $sort_string;
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $trash = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector, Categories
                    WHERE (items.iid = '$iid') AND
                    tally.iid = items.iid AND
                    items.recycle IS TRUE";
    $sql .= $sort_string;
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $recycle = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
	if (round($trash,2) > 0 || round($recycle,2) > 0 ) {
		echo "<tr><td>" . $name . "</td>";
		echo '<td class="right">' . round($trash,2) . "</td>";
		echo '<td class="right">' . round($recycle,2) . "</td></tr>";
		$plot_data[] = array($name,round($trash,2),round($recycle,2));
	  }
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
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  // Create an associative array with entries for items weight
  // Last entry is "Other"

  $Places = array();
  $sql = "SELECT name FROM Places";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    $Places["$name"] = array("Trash" => 0, "Recycle" => 0);
  }
  $Places["Other"] = array("Trash" => 0, "Recycle" => 0);
	$sql = "SELECT Collector.cid as CID, lat, lon, 
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
  foreach ($Places as $name => $results) {
	  if ($results["Trash"] > 0 || $results["Recycle"]>0 ) {
		echo "<tr><td>" . $name . "</td>";
		echo "<td>" . round($results["Trash"],2) . "</td>";
		echo "<td>" . round($results["Recycle"],2) . "</td>";
		echo "</tr>";
		$plot_data[] = array($name,round($results["Trash"],2),round($results["Recycle"],2));
	  }
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
  $plot->SetTitle('Trash and Recycling for each Location');

  # Make a legend for the 3 data sets plotted:  
  $plot->SetLegend(array('Trash', 'Recycling'));

  # Turn off X tick labels and ticks because they don't apply here:
  $plot->SetXTickLabelPos('none');
  $plot->SetXTickPos('none');

  $plot->DrawGraph();

  echo "<img src='" . $plot->EncodeImage() . "'>";
  $this->write_csv("Place",$plot_data);
}

function write_csv($title,$data) {

    $myfile = fopen("output.csv","w") or die("Unable to open file");
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
