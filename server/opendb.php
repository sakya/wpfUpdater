<?
/////////////////////////////////////////////////////
//Open database
/////////////////////////////////////////////////////
//mysql Host
define("db_host","...");
//Database Name
define ("db_name","...");
//Database Username
define("db_user","...");
//Database Password
define("db_pass","...");

$db = new mysqli(db_host, db_user, db_pass, db_name);

?>