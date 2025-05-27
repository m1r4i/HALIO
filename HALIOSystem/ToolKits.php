<?php
namespace ToolKits;

include __DIR__."/config.php";

function getColor($ratio){
    if($ratio < 1){
        return RATIO_COLOR_0;
    } else if($ratio < 31){
        return RATIO_COLOR_UNDER_31;
    } else if ($ratio < 61){
        return RATIO_COLOR_UNDER_61;
    } else if ($ratio < 81){
        return RATIO_COLOR_UNDER_81;
    } else {
        return RATIO_COLOR_OVER_81;
    }
}

// idに基づくコンテンツを取得する関数
function getContentById($id) {
    switch ($id) {
        case 0:
            return '退出';
        case 1:
            return '教務室';
        case 2:
            return '校内';
        case 3:
            return '校外';
        default:
            return '不明';
    }
}
