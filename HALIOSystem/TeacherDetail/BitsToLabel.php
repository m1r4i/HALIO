<?php
namespace TeacherDetail;

function BitsToLabel($bits, $course){
    $result = [];
    foreach ($bits as $bit => $label) {
        if (($course & (int)$bit) !== 0) {
            $result[] = "<span class=\"tag\">$label</span>";
        }
    }

    // 担当科目がない場合の処理
    if (empty($result)) {
        $result[] = "<span class=\"tag\">担当科目データなし</span>";
    }

    return $result;
}