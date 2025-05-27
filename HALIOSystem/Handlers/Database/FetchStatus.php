<?php
namespace Database;

function fetchAllStatus($dbhandler){
	$data = [];

	$query = $dbhandler->prepare("SELECT * FROM onlines");
	$query->execute();
	$currentData = $query->fetchAll();
	foreach($currentData as $value ){
		$data[$value["EmployeeNumber"]] = $value;
	}
	return $data;
}