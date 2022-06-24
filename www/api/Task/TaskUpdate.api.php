<?php
    require_once '../../utils.php';
    $conn = connection();

    $exceptionTranslation = [
        "Ten task length condition" => "Độ dài tối đa của tên task là 50 kí tự",
        "Mo ta length condition" => "Độ dài tối đa của mô tả là 2550 kí tự",
        "Error" => "Something went wrong, please try again later",
        "Deadline condition" => "Hạn nộp phải sau hôm nay",
        "user_role_condition" => "Người dùng không đủ quyền để thực hiện thao tác này",
    ];

    if(!_check_manager()){
        http_response_code(403);
        echo $exceptionTranslation['user_role_condition'];
        return;
    }

    $removedAttr = [
        "task_id" => "remove",
        "status" => "remove",
        "nguoi_thuc_hien_id" => "remove",
        "nguoi_tao_id" => "remove",
    ];
    
    if(!empty($_FILES['task-create-file']['name'])){
        // check file error
        if ($_FILES['task-create-file']['error'] > 0) {
            http_response_code(422);
            echo 'File bị lỗi, vui lòng chọn file khác';
            return;
        };
    }

    //check deadline
    $date_now = date('Y-m-d');
    if (date($_POST['thoi_gian_deadline']) <= $date_now) {
        echo $exceptionTranslation["Deadline condition"];
        return;
    };

    $valuesArr = array();
    $updateStr = " SET";

    foreach ($_POST as $key => $value){
        if(!_validate_not_null($value)){
            http_response_code(422);
            echo $exceptionTranslation["Empty field"];
            return;
        }

        if(!isset($removedAttr[$key])){
            $updateStr = $updateStr." ".$key."= ?,";
            array_push($valuesArr, $value);
        }
    }

    array_push($valuesArr, $_POST["task_id"]);
    $updateStr = substr($updateStr, 0, -1);

    $types = str_repeat('s', count($valuesArr));

    $queryStr = "UPDATE `TASK` ".$updateStr." WHERE task_id = ?";

    $stmt = $conn->prepare($queryStr);
    $stmt->bind_param($types, ...$valuesArr);
    $rs = $stmt->execute();
    
    if($rs){
        http_response_code(200);
        echo "Success";
        return;
    }else{
        http_response_code(500);
        // echo $exceptionTranslation["Error"];
        // echo htmlspecialchars($stmt->error);
        if(isset($exceptionTranslation[htmlspecialchars($stmt->error)])){
            echo $exceptionTranslation[htmlspecialchars($stmt->error)];
        }else{
            echo $exceptionTranslation["Error"];
        }
        return;
    }
?>