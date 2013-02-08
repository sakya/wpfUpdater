<?
function xml_create(&$xml){
	$xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
}

function xml_addStartTag(&$xml, $tagName){
	if (strlen($xml) && substr($xml, -1, 1) != ">")
		$xml = $xml.">";
	$xml = $xml."\n<".$tagName.">";
}

function xml_addEndTag(&$xml, $tagName){
	$open = strripos($xml, "<");
	$close = strripos($xml, ">");

	if ($close > $open)
		$xml = $xml."</".$tagName.">";
	else
		$xml = $xml." />";
}

function xml_addAttribute(&$xml, $name, $value){
	if (substr($xml, -1, 1) == ">")
		$xml = rtrim($xml,">");
	$xml = $xml." ".$name."=\"".xmlentities($value)."\"";
}

function xml_addElement(&$xml, $name, $value){
	if (substr($xml, -1, 1) != ">")
		$xml = $xml.">";
	xml_addStartTag($xml, $name);
	$xml = $xml.xmlentities($value);
	xml_addEndTag($xml, $name);
}

function xmlentities($string) {
    return str_replace(array("&", "<", ">", "\"", "'"),
        array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"), $string);
}
?>