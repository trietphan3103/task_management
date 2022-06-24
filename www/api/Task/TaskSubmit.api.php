<?php 
    require_once '../../utils.php';
    $conn = connection();
    $exceptionTranslation = [
        "Mo ta nop length condition" => "Độ dài tối đa của mô tả là 2550 kí tự",
        "Error" => "Something went wrong, please try again later",
        "User condition" => "Không được submit task của người khác",
        "File error condition" => "File bị lỗi, vui lòng chọn file khác",
        "File extension condition" => "File sai định dạng",
        "File size condition" => "Vui lòng chọn file nhỏ hơn 5M",
        "Existed task condition" => "Không tồn tại task có ID cần nộp"
    ];

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

    $nguoi_thuc_hien_id = $check_exited_rs->fetch_assoc()['nguoi_thuc_hien_id'];

    if ($nguoi_thuc_hien_id != _get_current_user_id()) {  
        http_response_code(422);
        echo $exceptionTranslation["User condition"];
        return;
    }; 
    
    if ( !empty($_POST['mo_ta_nop'])) {
        if (!empty($_FILES['task-submit-file']['name'])) {
            // check file error
            if ($_FILES['task-submit-file']['error'] > 0) {
                http_response_code(422);
                echo $exceptionTranslation["File error condition"];
                return;
            };
            
            // check file size
            $file_size = $_FILES['task-submit-file']['size'];
            if($file_size > 5000000) {
                http_response_code(422);
                echo $exceptionTranslation["File size condition"];
                return;
            };

            // check file extension
            $file_name = $_FILES['task-submit-file']['name'];
            $file_name_extn = substr($file_name, strrpos($file_name, '.')+1);
            if($file_name_extn == 'exe' || $file_name_extn == 'sh') {
                http_response_code(422);
                echo $exceptionTranslation["File extension condition"];
                return;
            };

        }

        // Check length

        if (strlen($_POST['mo_ta_nop']) > 2550) {
            http_response_code(422);
            echo $exceptionTranslation["Mo ta nop length condition"];
            return;
        };

        // Update task
        $mo_ta_nop = $_POST['mo_ta_nop'];
        $file_path = '/files/task/task_nop/'.$task_id."/".$_FILES['task-submit-file']['name'];

        //Upload file
        mkdir("../../files/task/task_nop/".$task_id);
        move_uploaded_file($_FILES['task-submit-file']['tmp_name'], '../..'.$file_path);

        $stmt = $conn->prepare("UPDATE `TASK` SET `mo_ta_nop` = ?, `file_nop` = ?, `thoi_gian_hoan_thanh` = DATE_ADD(NOW(), INTERVAL 7 HOUR), `status` = 4 WHERE `task_id` = ?");
        $stmt->bind_param("sss", $mo_ta_nop, $file_path, $task_id);
        $rs = $stmt->execute();
        
        if ($rs) {
            http_response_code(200);
                echo "Success";
                return;
        } else {
            http_response_code(500);
            echo htmlspecialchars($stmt->error);
            // if (isset($exceptionTranslation[htmlspecialchars($stmt->error)])) {
            //     echo $exceptionTranslation[htmlspecialchars($stmt->error)];
            //     return;
            // } else {
            //     echo $exceptionTranslation["Error"];
            //     return;
            // }
        }
        return;
    } else {
        http_response_code(422);
        echo "Vui lòng điền đầy đủ thông tin";
        return;
    };
?>
