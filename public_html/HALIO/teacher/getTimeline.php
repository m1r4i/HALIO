<?php
require_once __DIR__.'/../../../HALIOSystem/Handlers/Database/DatabaseConnection.php';
require_once __DIR__.'/../../../HALIOSystem/ToolKits.php';

try {
    $pdo = Database\db_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // URLパラメータ取得
    if (isset($_GET['id']) && isset($_GET['date'])) {
        $employeeNumber = $_GET['id'];
        $date = date("Y-m-d",strtotime($_GET['date']));

        // SQLクエリ
        $sql = "SELECT * FROM `log` WHERE EmployeeNumber = :employeeNumber AND `date` = :date ORDER BY `date` ASC, `time` ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['employeeNumber' => $employeeNumber, 'date' => $date]);

        // 結果を処理
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($records) > 0) {
            $result = [];
            $currentTime = date('Y-m-d H:i:s'); // 現在の日時

            // それぞれのレコードを処理
            for ($index = 0; $index < count($records); $index++) {
                $record = $records[$index];
                $start = $date . ' ' . $record['time'];

                // 次のレコードの終了時刻を設定
                if (isset($records[$index + 1])) {
                    $end = $date . ' ' . $records[$index + 1]['time'];
                } else {
                    // 最後のレコードの場合、終了時刻は現在時刻
                    $end = $currentTime;
                }

                // データの作成
                $result[] = [
                    'group' => 'data',
                    'content' => ToolKits\getContentById($record['type']),
                    'start' => $start,
                    'end' => $end,
                    'className' => 'c' . $record['type']
                ];
            }

            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'No records found']);
        }
    } else {
        echo json_encode(['error' => 'Invalid parameters']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}


?>
