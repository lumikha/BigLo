<?php
	require_once 'dbConnect.php';

  $doc = new couchDocument($client_customers);
  $view = $client_customers->getView("customers","search");
   
  $result = "[";
   $array = $view->rows;
   $resultstr = array();
    foreach ($array as $key => $value) {
    	$resultstr[] = json_encode($value->value);
    }
    $result .= implode(",",$resultstr);
    $result .= "]";
    print_r($result);
?>