<?
//////////////////////////Main Configuration ///////////////////////////
//Open mysql connection
include_once('opendb.php');
include_once('xml_helper.php');

//////////////////////////End Main Configuration ///////////////////////////

$appname=$_GET["appname"];
$platform=$_GET["platform"];

$xml = "";
xml_create($xml);
xml_addStartTag($xml, "root");

$stmt = $db->prepare("Select id, name, version, released, filename, url, changelog, message ".
					 "from updater_applications ".
					 "where name = ? ".
					 "  and platform = ? ".
					 "order by released desc");
$stmt->bind_param('ss', $appname, $platform);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0){
    //Application not found:
    xml_addElement($xml, "ERRORCODE", "ERR001");
}else{
    //Application found:
	$id = 0;
	$name = "";
	$version = "";
	$released = 0;
	$filename = "";
	$url = "";
	$changelog = "";
	$message = "";
	$stmt->bind_result($id, $name, $version, $released, $filename, $url, $changelog, $message);
	$stmt->fetch();

    xml_addStartTag($xml, "app");

    xml_addAttribute($xml, "id", $id);
    xml_addAttribute($xml, "name", $name);
    xml_addAttribute($xml, "version", $version);
    xml_addAttribute($xml, "releasedate", strtotime($released));
    xml_addAttribute($xml, "filename", $filename);
    xml_addAttribute($xml, "url", $url);

    xml_addElement($xml, "changelog", $changelog);
    xml_addElement($xml, "message", $message);

    xml_addEndTag($xml, "app");
}

xml_addEndTag($xml, "root");
echo $xml;

$stmt->close();

//Close mysql connection
$db->close();
?>
