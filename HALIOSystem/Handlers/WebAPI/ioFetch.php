<?php
namespace WebAPI;

function fetchData(){
	$json = file_get_contents("http://io.osaka.hal.ac.jp/userlist?schooltypes%5B%5D=%EF%BC%A8%EF%BC%A1%EF%BC%AC%E5%A4%A7%E9%98%AA");
	$data = json_decode($json,true);
	return $data;
}