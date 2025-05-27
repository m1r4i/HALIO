<?php
namespace Database;

include __DIR__."/../../SECURE_CONFIG.php";

function db_connect(){
    $dbh = new \PDO(DSN, DB_USERNAME, DB_PASSWORD);
    return $dbh;
}
// Returns Database Handler

