<?php
/*
リアルタイム情報取得 getData.php
ユーザーアクセス: Ajaxのみ
データをリアルタイムにAPIから取得しデータベース上のメタデータを合わせて表示
*/
// ini_set("display_errors",1);


require_once __DIR__.'/../../HALIOSystem/Handlers/Database/DatabaseConnection.php';
require_once __DIR__.'/../../HALIOSystem/Handlers/Database/FetchStatus.php';
require_once __DIR__.'/../../HALIOSystem/Handlers/WebAPI/ioFetch.php';

// アクセス元ドメインを制御
header("Access-Control-Allow-Origin: https://m1rr.info https://halio.m1r4i.com");

// 学内のAPIからデータ取得
$data = WebAPI\fetchData(); 

// データベースハンドラを取得
$dbhandler = Database\db_connect();
$fetchedData = Database\fetchAllStatus($dbhandler);

// 結果保存用変数を初期化
$result = [];

foreach($data as $key => $value){
    if($value["EmployeeNumber"] == "99999999") continue; // 区切り文字をスキップ
    $id = $value["EmployeeNumber"];
    $status = 0;
    $currentTime = date("Y/m/d H:i:s");
    $isOn = false;

    switch($value["Presence"]){
        case "red":
            $status = 2; // 学内
            break;
        case "green":
            $status = 1; // 教務室内
            $isOn = true; // 教務室内フラグ
            break;
        case "gray":
            $status = 0; // 退勤済み
            break;
        case "orange":
            $status = 3; // 学外
            break;
    }

    $currentData = $fetchedData[$id];
    $result[$id]["id"] = $currentData["EmployeeNumber"];
    $result[$id]["name"] = $currentData["Name"];
    $result[$id]["type"] = $value["Group"];
    $result[$id]["status"] = $currentData["status"];
    $result[$id]["back"] = $currentData["back"];
    $result[$id]["leave"] = $currentData["leave"];
    $result[$id]["course"] = $currentData["course"];
    $result["update"] = $currentData["lastUpdate"];
    // 教員ごとのステータス情報を取得, 格納
}
if($_GET["encode"]){
    $resJson = json_encode($result);
} else {
    $resJson = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
echo $resJson;
?>
