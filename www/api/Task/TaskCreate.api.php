<?php 
    require_once '../../utils.php';
    $conn = connection();
    $exceptionTranslation = [
        "Ten task length condition" => "Độ dài tối đa của tên task là 50 kí tự",
        "Mo ta length condition" => "Độ dài tối đa của mô tả là 2550 kí tự",
        "Error" => "Something went wrong, please try again later",
        "Deadline condition" => "Hạn nộp phải sau hôm nay",
        "user_role_condition" => "Người dùng không đủ quyền để thực hiện thao tác này",
        "File error condition" => "File bị lỗi, vui lòng chọn file khác",
        "File extension condition" => "File sai định dạng",
        "File size condition" => "Vui lòng chọn file nhỏ hơn 5M"
    ];

    if(!_check_manager()){
        http_response_code(403);
        echo $exceptionTranslation['user_role_condition'];
        return;
    }

    if (!empty($_POST['ten_task']) && !empty($_POST['thoi_gian_deadline'])
        && !empty($_POST['mo_ta']) && !empty($_POST['nguoi_thuc_hien_id'])
    ){
        if (!empty($_FILES['task-create-file']['name'])) {
            // check file error
            if ($_FILES['task-create-file']['error'] > 0) {
                http_response_code(422);
                echo $exceptionTranslation["File error condition"];
                return;
            };

            // check file size
            $file_size = $_FILES['task-create-file']['size'];
            if($file_size > 5000000) {
                http_response_code(422);
                echo $exceptionTranslation["File size condition"];
                return;
            };

            // check file extension
            $file_name = $_FILES['task-create-file']['name'];
            $file_name_extn = substr($file_name, strrpos($file_name, '.')+1);
            if($file_name_extn == 'exe' || $file_name_extn == 'sh') {
                http_response_code(422);
                echo $exceptionTranslation["File extension condition"];
                return;
            };
        }

        // Check length
        if (strlen($_POST['ten_task']) > 50) {
            http_response_code(422);
            echo $exceptionTranslation["Ten task length condition"];
            return;
        };

        if (strlen($_POST['mo_ta']) > 2550) {
            http_response_code(422);
            echo $exceptionTranslation["Mo ta length condition"];
            return;
        };

        //check deadline
        $date_now = date('Y-m-d');
        if (date($_POST['thoi_gian_deadline']) <= $date_now) {
            echo $exceptionTranslation["Deadline condition"];
            return;
        };

        // Create new task
        $nguoi_tao_id = _get_current_id();
        
        $ten_task = $_POST['ten_task'];
        $mo_ta = $_POST['mo_ta'];
        $thoi_gian_deadline = $_POST['thoi_gian_deadline'];
        $nguoi_thuc_hien_id = $_POST['nguoi_thuc_hien_id'];
        
        $stmt = $conn->prepare("INSERT INTO `TASK` (`task_id`, `nguoi_tao_id`, `nguoi_thuc_hien_id`, `ten_task`, `mo_ta`, `status`, `muc_do_hoan_thanh`, `file`, `thoi_gian_deadline`, `note`) 
                                        VALUES (NULL, ?, ?, ?, ?, 1, 1, NULL, ?, NULL)");
        $stmt->bind_param("iisss", $nguoi_tao_id, $nguoi_thuc_hien_id, $ten_task, $mo_ta, $thoi_gian_deadline);
        $rs = $stmt->execute();
            
        if ($rs) {
            if (!empty($_FILES['task-create-file']['name'])) {
                $last_id = $conn->insert_id;
                $file_path = '/files/task/task_giao/'.$last_id."/".$_FILES['task-create-file']['name'];
                //Upload file
                mkdir("../../files/task/task_giao/".$last_id);
                move_uploaded_file($_FILES['task-create-file']['tmp_name'], '../..'.$file_path);

                // //store file path to db
                $stmt = $conn->prepare("UPDATE `TASK` SET `file` = ? WHERE `task_id` = ?");
                $stmt->bind_param("si", $file_path, $last_id);
                $file_rs = $stmt->execute();
                
                if($file_rs){
                    http_response_code(200);
                    echo "Success";
                    return;
                }else{
                    http_response_code(500);
                    echo $exceptionTranslation["Error"];
                    return;
                }
            } else {
                http_response_code(200);
                echo "Success";
                return;
            }
            return;
        } else {
            http_response_code(422);
            echo "Please fill enought information";
            return;
        };
    }
?>