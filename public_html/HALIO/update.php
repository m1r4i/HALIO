<?php
require_once __DIR__.'/../../HALIOSystem/Handlers/Database/DatabaseConnection.php';
require_once __DIR__.'/../../HALIOSystem/Handlers/WebAPI/ioFetch.php';

// 学内のAPIからデータ取得
$data = WebAPI\fetchData(); 

// データベースハンドラを取得
$dbhandler = Database\db_connect();

foreach($data as $key => $value){
    if($value["EmployeeNumber"] == "99999999") continue;
    $id = $value["EmployeeNumber"];
    $status = 0;
    $currentTime = date("Y/m/d H:i:s");
    $isOn = false;

    // Determine the new status
    switch($value["Presence"]){
        case "red":
            $status = 2;
            break;
        case "green":
            $status = 1;
            $isOn = true;
            break;
        case "gray":
            $status = 0;
            break;
        case "orange":
            $status = 3;
            break;
    }

    $query = $dbhandler->prepare("SELECT status FROM onlines WHERE EmployeeNumber = :id");
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $currentDbStatus = $query->fetch(PDO::FETCH_ASSOC)['status'];
    // if status has changed
    if ($status != $currentDbStatus) {
        if ($isOn) {
            // Update back time if online
            $statement = $dbhandler->prepare("UPDATE onlines SET status=:status, back=:date WHERE EmployeeNumber = :id;");
        } else {
            // Update leave time if offline
            $statement = $dbhandler->prepare("UPDATE onlines SET status=:status, `leave`=:date WHERE EmployeeNumber = :id;");
        }

        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':date', $currentTime, PDO::PARAM_STR);
        $statement->bindValue(':status', $status, PDO::PARAM_INT);

        $statement->execute(); // Update Status Table

        $statement = $dbhandler->prepare("INSERT INTO log(EmployeeNumber, type, date, time) values(:id,:status,:date,:time)");
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':status', $status, PDO::PARAM_INT);
            $statement->bindValue(':date', date("Y/m/d"), PDO::PARAM_STR);
            $statement->bindValue(':time', date("H:i"), PDO::PARAM_STR);
        $statement->execute(); // Save the status log
    } else {
        $statement = $dbhandler->prepare("UPDATE onlines SET status=:status, lastUpdate=:date WHERE EmployeeNumber = :id;");
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':date', $currentTime, PDO::PARAM_STR);
        $statement->bindValue(':status', $status, PDO::PARAM_INT);
        $statement->execute(); // just save current status and lastUpdate time.
    }
    echo "Processing: ".$id."<br>"; // Log for debug 
}
?>
