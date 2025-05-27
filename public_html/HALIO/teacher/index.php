<?php
require_once __DIR__.'/../../../HALIOSystem/Handlers/Database/DatabaseConnection.php';
require_once __DIR__.'/../../../HALIOSystem/TeacherDetail/TimeTableMaker.php';
require_once __DIR__.'/../../../HALIOSystem/TeacherDetail/BitsToLabel.php';
require_once __DIR__.'/../../../HALIOSystem/ToolKits.php';

require_once __DIR__.'/../../../HALIOSystem/config.php';

$id = htmlspecialchars($_GET["id"]);
$spDate = htmlspecialchars($_GET["date"]); // 隠し要素: 下部に表示のタイムラインを当日以外のデータにする時用
$pdo = Database\db_connect();

// 過去{config.php/MONTH_USE_FOR_RATIO_CALC}ヶ月分のデータを取得（日曜日を除外）
    $sql = "
        SELECT 
            EmployeeNumber,
            type,
            date,
            time,
            DAYOFWEEK(date) as weekday
        FROM log
        WHERE date >= DATE_SUB(CURDATE(), INTERVAL :month MONTH)
        AND DAYOFWEEK(date) != 1
        AND EmployeeNumber = :id
        ORDER BY date, time ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
	    ':month' => MONTH_USE_FOR_RATIO_CALC,
        ]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 時間割
    $schedule = $config_schedule; // config.phpより取得

    // 曜日マッピング
    $weekdays = $config_weekdays; // config.phpより取得

    $percentages = TeacherDetail\TimeTableMake($schedule, $weekdays, $data);


$query = "SELECT * FROM onlines WHERE EmployeeNumber = :id";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':id' => $id,
]); // 教員情報を取得
        
$statusList = $config_statusList; // configより取得

$data = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $data["Name"];
$type = $data["type"];
$status = $data["status"];
$back = $data["back"];
$leave = $data["leave"];
$course = $data["course"];
$update = $data["lastUpdate"];
$comment = $data["comment"];
$prevwork = $data["prevwork"];
$callid = $data["call_id"];

if($name == ""){
    exit("無効なIDです。");
}
// よりスマートにしたい

$tags = $config_tags; // configより取得

$result = TeacherDetail\BitsToLabel($tags, $course); 

$query = "SELECT * FROM log WHERE date = :today AND EmployeeNumber = :id AND (type = 1 OR type = 2) ORDER BY time ASC LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute([
    ':id' => $id,
    ':today' => date("Y-m-d"),
]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

$joined = "";
$joined = $data["time"];
if($joined == ""){
    $joined = "今日まだ記録がありません。";
} else {
    $joined = "今日の出勤時刻: ".$joined;
}
// 一番最初の入力を出勤として扱う

/*
設計上DBからデータを取得するのも別ファイルにすべきなのか...
各種データもオブジェクトとしてまとめるべきか...

機能の量などを考えて検討予定
リアルタイムで変動しない項目についてはここで一括取得...
(テンプレート化も検討?)
*/
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <title><?= $name ?> | HAL大阪在籍情報</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="manifest" href="/HALIO/assets/manifest.json">
    <script>      
        if (navigator.serviceWorker) {
          navigator.serviceWorker.register ('/HALIO/assets/js/sw.js').then(() => {
          });
        }
    </script>
    <link rel="stylesheet" href="/HALIO/assets/css/root.css">
    <link rel="stylesheet" href="/HALIO/assets/css/teacher.css">

    <link rel="stylesheet" href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css">
    <script src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>
</head>
<body>
    <div id="container">
        <header>
            <h1><span class="status status-<?= $status; ?>" title="現在の状態: <?= $statusList[$status]; ?>"><?= $statusList[$status]; ?></span> <?= $name; ?></h1>
            <div class="tags">
                <?= implode(" ", $result); ?>
            </div>
            <p id="time"><?= $status == 1 ? "復帰: $back" : "退出: $leave" ?></p>
        </header>
        <a href="/HALIO" class="back-button">← 戻る</a>
        <section id="update-info">
            <p id="joined"><?= $joined; ?></p>
            <p id="update">最終更新: <?= $update; ?></p>
            <?php if($callid !== "") echo "<p>内線番号: $callid </p>"; ?>
            <?php if($prevwork !== "") echo "<p>前職: $prevwork </p>"; ?>
            <?php if($comment !== "") echo "<p>備考: $comment </p>"; ?>
        </section>

        <hr>

        <section id="onlinePerDayWeek">
            <h2>曜日別時間帯ごとの教務室在室率（過去<?= MONTH_USE_FOR_RATIO_CALC; ?>ヶ月）</h2>
            <table>
                <tr>
                    <th>時間帯</th>
                    <?php foreach ($weekdays as $dayName) echo "<th>{$dayName}</th>"; ?>
                </tr>
                <?php foreach ($schedule as $period => $times): ?>
                <tr>
                    <td><?= $period ?></td>
                    <?php foreach ($weekdays as $dayName): 
                        $percent = isset($percentages[$dayName][$period]) ? $percentages[$dayName][$period] : 0;
                    ?>
                        <td style="background:<?= ToolKits\getColor($percent); ?>" class="timetable" title="<?= $percent ?>%"><?= $percent ?>%</td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <hr>

        <section id="timeline">
            <h2>タイムライン</h2>
            <div id="visualization"></div>
        </section>
    </div>

    <script src="/HALIO/assets/js/config.js"></script>
    <script src="/HALIO/assets/js/teacher.js"></script>
    <script>
    let timeline = null;
    async function fetchTimelineData() {
        try {
            const response = await fetch(`https://m1rr.info/HALIO/teacher/getTimeline.php?id=<?= $id; ?>&date=<?= $spDate? $spDate : date("Y-m-d");?>`);
            const items = await response.json();

            if (!Array.isArray(items)) return;
            const container = document.getElementById('visualization');
            if (timeline) timeline.destroy();

            const groups = [{ id: 'data', content: 'データ' }];
            const options = {
                start: '<?= $spDate? $spDate : date("Y-m-d");?> 8:00',
                end: '<?= $spDate? $spDate : date("Y-m-d");?> 22:00',
                zoomable: false,
                orientation: 'top',
                tooltip: { delay: 50 }
            };

            items.forEach((o, i) => {
                o.id = i + 1;
                o.title = o.end ? `${o.start} - ${o.end}` : o.start;
            });
            // タイムライン用のデータに整形

            timeline = new vis.Timeline(container, items, groups, options); // 作成
        } catch (error) {
            console.error('Error fetching timeline data:', error);
        }
    }

    fetchTimelineData();
    setInterval(fetchTimelineData, 60000);
    // Todo: PHPに依存してしまっているので、データ結合に変えて依存性を下げたい
    </script>
</body>
</html>