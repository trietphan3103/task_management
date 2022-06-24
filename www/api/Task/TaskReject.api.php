<?php 
    require_once '../../utils.php';
    $conn = connection();
    $exceptionTranslation = [
        "Existed task condition" => "Không tồn tại task có ID cần nộp",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
        "deadline_condition" => "Deadline mới phải diễn ra sau deadline cũ",
    ];

    if(!_validate_not_null($_POST['task_id']) || !_validate_not_null($_POST['feedback']) || empty($_FILES['task-reject-file']['name']) ){
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

    // Check extend deadline condition
    $stmt = $conn->prepare("SELECT 1 FROM `TASK` WHERE `TASK`.`task_id` = ? and `TASK`.`thoi_gian_deadline` <= ?");
    $stmt->bind_param("ss", $task_id, $new_deadline);
    $new_deadline = $_POST['thoi_gian_deadline'];
    $stmt->execute();
    $check_deadline_rs = $stmt->get_result();

    if (mysqli_num_rows($check_deadline_rs) == 0) {
        http_response_code(409);
        echo $exceptionTranslation["deadline_condition"];
        return;
    };

    // Update extend deadline
    $stmt = $conn->prepare("UPDATE `TASK` set `thoi_gian_deadline` = ?  WHERE `task_id` = ?");
    $stmt->bind_param("ss", $new_deadline,  $task_id);
    $update_deadline_rs = $stmt->execute();

    if (!$update_deadline_rs) {
        http_response_code(500);
        // echo htmlspecialchars($stmt->error);
        if (isset($exceptionTranslation[htmlspecialchars($stmt->error)])) {
            echo $exceptionTranslation[htmlspecialchars($stmt->error)];
            return;
        } else {
            echo $exceptionTranslation["Error"];
            return;
        }
    };

    $current_task_detail = $check_exited_rs->fetch_assoc();

    $stmt = $conn->prepare("UPDATE `TASK` SET  `status` = 5 WHERE `task_id` = ?");
    $stmt->bind_param("s", $task_id);
    $rs = $stmt->execute();
    
    if ($rs) {
        $stmt = $conn->prepare("INSERT INTO `HISTORY`(`task_id`, `task_status`, `note`, `mo_ta_nop`, `file_history`,`file_task_nop`) values(?, -1, ?, ?, null, ?)");
        $stmt->bind_param("ssss", $task_id, $note_history, $mo_ta_nop_history, $file_task_nop);
        $mo_ta_nop_history = $current_task_detail['mo_ta_nop'];
        $file_task_nop = $current_task_detail['file_nop'];
        $note_history = $_POST['feedback'];
        $rs = $stmt->execute();

        if($rs){
            $last_id = $conn->insert_id;
            $file_path = '/files/history/'.$last_id."/".$_FILES['task-reject-file']['name'];
            //Upload file
            mkdir("../../files/history/".$last_id);
            move_uploaded_file($_FILES['task-reject-file']['tmp_name'], '../..'.$file_path);

            //store file path to db
            $stmt = $conn->prepare("UPDATE `HISTORY` SET `file_history` = ? WHERE `history_id` = ?");
            $stmt->bind_param("ss", $file_path, $last_id);
            $file_rs = $stmt->execute();
            
            if($file_rs){
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

            return;

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
