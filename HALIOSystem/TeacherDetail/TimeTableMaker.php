<?php
namespace TeacherDetail;

function TimeTableMake($schedule, $weekdays, $data){
        // 初期化
    $timeSum = [];
    $totalDays = [];

    foreach ($schedule as $period => $times) {
        foreach ($weekdays as $dayNum => $dayName) {
            $timeSum[$dayName][$period] = 0;
            $totalDays[$dayName] = [];
        }
    }
    // 在室時間の計算
    $prevRow = null;
    foreach ($data as $row) {
        $date = $row['date'];
        $time = $row['time'];
        $type = $row['type'];
        $weekday = $row['weekday'];

        if (!isset($weekdays[$weekday])) continue;

        $dayName = $weekdays[$weekday];
        if (!isset($totalDays[$dayName][$date])) {
            $totalDays[$dayName][$date] = true;
        }

        // 前の行が在室状態(type=1)の場合のみ、時間差を計算
        if ($prevRow && $prevRow['type'] == 1 && $prevRow['date'] == $date) {
            $startTime = $prevRow['time'];
            $endTime = $time;

            // 各時間帯ごとに在室時間を加算
            foreach ($schedule as $period => $times) {
                $periodStart = $times[0];
                $periodEnd = $times[1];

                // 在室期間が時間帯に重なっている場合
                if ($endTime > $periodStart && $startTime < $periodEnd) {
                    $overlapStart = max($startTime, $periodStart);
                    $overlapEnd = min($endTime, $periodEnd);
                    $diff = (strtotime($overlapEnd) - strtotime($overlapStart)) / 60; // 分単位
                    $timeSum[$dayName][$period] += $diff;
                }
            }
        }
        $prevRow = $row;
    }

    // 百分率の計算
    $percentages = [];
    foreach ($timeSum as $dayName => $periods) {
        foreach ($periods as $period => $minutes) {
            $totalMinutes = (strtotime($schedule[$period][1]) - strtotime($schedule[$period][0])) / 60;
            $totalDaysCount = count($totalDays[$dayName]);
            $percentages[$dayName][$period] = ($totalDaysCount > 0) ? round(($minutes / ($totalMinutes * $totalDaysCount)) * 100, 2) : 0;
        }
    }

    return $percentages;
}