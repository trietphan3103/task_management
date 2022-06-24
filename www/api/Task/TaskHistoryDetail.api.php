<?php
    require_once '../../utils.php';
    require_once './TaskGet.api.php';

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
    ];

    if(isset($_GET['history_id'])){
        $conn = connection();
        $list_history = _get_task_history_detail($_GET['history_id']);
        $arr_rs = [];
        while ($history =  $list_history->fetch_assoc()) {
            $arr_rs[] = $history;
        }
        echo json_encode($arr_rs);
        return;
    } else {
        http_response_code(422);
        echo $exceptionTranslation['Empty field'];
        return;
    }
?>
