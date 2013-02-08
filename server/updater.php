<?
//////////////////////////Main Configuration ///////////////////////////
//Open mysql connection
include_once('opendb.php');
include_once('xml_helper.php');

//////////////////////////End Main Configuration ///////////////////////////

$appname=$_GET["appname"];
$platform=$_GET["platform"];
$startVersion=$_GET["version"];

$xml = "";
xml_create($xml);
xml_addStartTag($xml, "root");

$stmt = $db->prepare("Select id, name, version, released, filename, url, changelog, message ".
					 "from updater_applications ".
					 "where name = ? ".
					 "  and platform = ? ".
					 "order by released desc limit 1");
$stmt->bind_param('ss', $appname, $platform);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0){
    //Application not found:
    xml_addElement($xml, "ERRORCODE", "ERR001");
	$stmt->close();
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
	$stmt->close();

	// If a starting version is specified get the full changelog:
	$fullchangelog = "";
	if (strlen($startVersion) && $startVersion != $version){
		$vreleased = "";
		$vchangelog = "";
		$stmt = $db->prepare("Select changelog ".
							 "from updater_applications a ".
							 "where a.name = ? ".
							 "  and a.platform = ? ".
							 "  and a.released > (select b.released from updater_applications b ".
							 "					  where b.name = a.name ".
							 "                      and b.platform = a.platform ".
							 "                      and b.version = ?) ".
	                         "order by released");
		$stmt->bind_param('sss', $appname, $platform, $startVersion);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($vchangelog);
		while ($stmt->fetch()) {
			if (strlen($vchangelog))
				$fullchangelog .= "$vchangelog\n";
		}
		$stmt->close();	
	}

    xml_addStartTag($xml, "app");

    xml_addAttribute($xml, "id", $id);
    xml_addAttribute($xml, "name", $name);
    xml_addAttribute($xml, "version", $version);
    xml_addAttribute($xml, "releasedate", strtotime($released));
    xml_addAttribute($xml, "filename", $filename);
    xml_addAttribute($xml, "url", $url);

    xml_addElement($xml, "changelog", strlen($fullchangelog) ? $fullchangelog : $changelog);
    xml_addElement($xml, "message", $message);

    xml_addEndTag($xml, "app");
}

xml_addEndTag($xml, "root");
echo $xml;


//Close mysql connection
$db->close();
?>
