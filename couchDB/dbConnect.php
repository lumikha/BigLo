<?php
/* CouchDB connection settings */
$couch_dsn = "http://localhost:5984/";
$couch_db_users = "bigloco-users";
$couch_db_customers = "bigloco-customers";

require_once "PHP-on-Couch-master/lib/couch.php";
require_once "PHP-on-Couch-master/lib/couchClient.php";
require_once "PHP-on-Couch-master/lib/couchDocument.php";


$client_users = new couchClient($couch_dsn,$couch_db_users);
$client_customers = new couchClient($couch_dsn,$couch_db_customers);

/* Getting database information */

try {
	$info_users = $client_users->getDatabaseInfos();
	$info_customers = $client_customers->getDatabaseInfos();
} catch (Exception $e) {
	echo "Error:".$e->getMessage()." (errcode=".$e->getCode().")\n";
	exit(1);
}
//echo "<pre>"; print_r($info_users); echo "</pre>";
//echo "<pre>"; print_r($info_customers); echo "</pre>";
//echo "<br /><br />";

/* Retrieving a data from the database */

/*
try {
	$doc = $client->getDoc('a9276cb74c3354ff2ad0111f2a002d49');
} catch (Exception $e) {
	if ( $e->code() == 404 ) {
		echo "Document not found\n";
	} else {
		echo "Error: ".$e->getMessage()." (errcode=".$e->getCode().")\n";
	}
	exit(1);
}
*/

//echo "<pre>"; print_r($doc); echo "</pre>";
//echo "<br /><br />";

?>