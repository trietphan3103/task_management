<?php 
    require_once '../../utils.php';
    $conn = connection();
    $exceptionTranslation = [
        "Existed task condition" => "Không tồn tại task có ID cần nộp",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
    ];

    if(!_validate_not_null($_POST['task_id'])){
        http_response_code(422);
        echo $exceptionTranslation['Empty field'];
        return;
    }

    // Check existed task
    $task_id = $_POST['task_id'];
    $stmt = $conn->prepare("SELECT * FROM `TASK` WHERE `task_id` = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $check_exited_rs = $stmt->get_result();

    if (mysqli_num_rows($check_exited_rs) == 0) {
        http_response_code(409);
        echo $exceptionTranslation["Existed task condition"];
        return;
    };

    $current_task_detail = $check_exited_rs->fetch_assoc();

    $stmt = $conn->prepare("UPDATE `TASK` SET  `status` = 6, `muc_do_hoan_thanh` = ? WHERE `task_id` = ?");
    $stmt->bind_param("ss", $muc_do_hoan_thanh, $task_id);
    $muc_do_hoan_thanh = $_POST['muc_do_hoan_thanh'];
    $rs = $stmt->execute();
    
    if ($rs) {
        $stmt = $conn->prepare("INSERT INTO `HISTORY`(`task_id`, `task_status`, `note`, `mo_ta_nop`, `file_history`,`file_task_nop`) values(?, 1, null, ?, null, ?)");
        $stmt->bind_param("sss", $task_id, $mo_ta_nop_history, $file_task_nop);
        $mo_ta_nop_history = $current_task_detail['mo_ta_nop'];
        $file_task_nop = $current_task_detail['file_nop'];
        $rs = $stmt->execute();

        if($rs){
            http_response_code(200);
            echo "Success";
            return;
        }else{
            http_response_code(500);
            // echo htmlspecialchars($stmt->error);
            if (isset($exceptionTranslation[htmlspecialchars($stmt->error)])) {
                echo $exceptionTranslation[htmlspecialchars($stmt->error)];
                return;
            } else {
                echo $exceptionTranslation["Error"];
                return;
            }
        }
    } else {
        http_response_code(500);
        // echo htmlspecialchars($stmt->error);
        if (isset($exceptionTranslation[htmlspecialchars($stmt->error)])) {
            echo $exceptionTranslation[htmlspecialchars($stmt->error)];
            return;
        } else {
            echo $exceptionTranslation["Error"];
            return;
        }
    }
    return;
?>
