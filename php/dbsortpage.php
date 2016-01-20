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

require_once("includes/mainpage.php");
include_once "includes/util.php";

/**
 * Child class of MainPage used for user preferrences page.
 *
 * Implements processData and showContent
 */
  $radiolist = array(""=>0,"Name"=>1,"Date"=>2,"Category"=>3,"Item"=>4,"Location"=>5);

class dbSortPage extends MainPage {

/**
 * Process the data and insert / modify database.
 *
 * @param $uid is user id passed by reference.
 */
function processData(&$uid) {
  global $radiolist;
  $uid = $this->formL->getValue("cat");
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
?>

<div class="preamble" id="SOS-preamble" role="article">
<h3>Select type of Data Base Sort.</h3><p></p>
<?php
	echo $this->formL->reportErrors();
	echo $this->formL->start('POST', "", 'name="databasesort"');
?>
<fieldset>
<legend>Please Select the sort type below</legend>
<br>
<table>
<tr>
<td class="inputcell">
<?php
  echo $this->formL->makeSelect('cat', $radiolist, "");?>
</td>
</tr>
<tr><td>
<input class="subbutton" type="submit" name="Submit" value="Submit">
</td></tr>
</table>
</fieldset>

</form>
<?php
  if ( $uid > 0 ) {
    switch ($uid) {
      case $radiolist["Name"]: // Name
         $this->nameTable();
       break;
      case $radiolist["Date"]: // Date
         $this->dateTable();
       break;
      case $radiolist["Category"]: 
         $this->categoryTable();
       break;
      case $radiolist["Item"]: 
         $this->itemTable();
       break;
      case $radiolist["Location"]: 
         $this->locationTable();
       break;
      default:
        echo $uid;
    }
  }
  else echo "No sort selected.";
?>
</div>

<?php
$this->formL->finish();
//mysql_free_result($result);
}

/**
 * Display the name table
 *
 */
function nameTable() {

  echo '<table class="volemail"> <tr> <th>Name</th>';
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $sql = "SELECT DISTINCT name FROM Collector ORDER BY date DESC";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["name"];
    echo "<tr><td>" . $name . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector
                    WHERE (Collector.name = '$name') AND
                    Collector.cid = tally.cid AND
                    tally.iid = items.iid AND
                    items.recycle IS FALSE";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $trash = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($trash,2) . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector
                    WHERE (Collector.name = '$name') AND
                    Collector.cid = tally.cid AND
                    tally.iid = items.iid AND
                    items.recycle IS TRUE";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $recycle = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($recycle,2) . "</td></tr>";
  }
  echo "</table>";
}
/**
 * Display the date table
 *
 */
function dateTable() {

  echo '<table class="volemail"> <tr> <th>Date</th>';
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $sql = "SELECT DISTINCT CAST(`date` AS DATE) AS dateonly 
             FROM Collector ORDER BY date DESC";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $date = $row["dateonly"];
    echo "<tr><td>" . $date . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector
                    WHERE (CAST(Collector.date AS DATE) = '$date') AND
                    Collector.cid = tally.cid AND
                    tally.iid = items.iid AND
                    items.recycle IS FALSE";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $trash = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($trash,2) . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items, Collector
                    WHERE (CAST(Collector.date AS DATE) = '$date') AND
                    Collector.cid = tally.cid AND
                    tally.iid = items.iid AND
                    items.recycle IS TRUE";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $recycle = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($recycle,2) . "</td></tr>";
  }
  echo "</table>";
}
/**
 * Display the Category table
 *
 */
function categoryTable() {

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
                    FROM tally, items
                    WHERE (items.category = '$catid') AND
                    tally.iid = items.iid AND
                    items.recycle IS FALSE";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $trash = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($trash,2) . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items
                    WHERE (items.category = '$catid') AND
                    tally.iid = items.iid AND
                    items.recycle IS TRUE";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $recycle = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($recycle,2) . "</td></tr>";
  }
  echo "</table>";
}
/**
 * Display the Item table
 *
 */
function itemTable() {

  echo '<table class="volemail"> <tr> <th>Item</th>';
  echo '<th>Trash Weight</th><th>Recycle Weight</th></tr>';
  $sql = "SELECT item, iid 
             FROM items";
  $result = $this->db->query($sql);
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $name = $row["item"];
    $iid = $row["iid"];
    echo "<tr><td>" . $name . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items
                    WHERE (items.iid = '$iid') AND
                    tally.iid = items.iid AND
                    items.recycle IS FALSE";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $trash = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($trash,2) . "</td>";
    $sql = "SELECT SUM(number*weight)
                    FROM tally, items
                    WHERE (items.iid = '$iid') AND
                    tally.iid = items.iid AND
                    items.recycle IS TRUE";
    $res2 = $this->db->query($sql);
    $row2 = $res2->fetch(PDO::FETCH_ASSOC);
    $recycle = is_null($row2["SUM(number*weight)"]) ?
      0 : $row2["SUM(number*weight)"] ;
    echo '<td class="right">' . round($recycle,2) . "</td></tr>";
  }
  echo "</table>";
}
/**
 * Display the Location table
 *
 */
function locationTable() {

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
				FROM Collector, tally, items
				WHERE tally.cid = Collector.cid
				AND tally.iid = items.iid
				GROUP BY Collector.cid"; 
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
    echo "<tr><td>" . $name . "</td>";
    echo "<td>" . round($results["Trash"],2) . "</td>";
    echo "<td>" . round($results["Recycle"],2) . "</td>";
    echo "</tr>";
  } 
  echo "</table>";
}
}
?>
<?php
/*
SELECT cid FROM Collector WHERE cid NOT IN
( SELECT cid FROM Collector WHERE
( 3959 * acos( cos( radians(Collector.lat) ) 
                     * cos( radians( 37.0067 ) ) * 
                cos( radians( '-121.962' ) - radians(Collector.lon) ) + 
                     sin( radians(Collector.lat) ) * sin( radians( '37.0067' ) ) ) ) < 0.5)
*/
?>
