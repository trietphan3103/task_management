<?php
    require_once '../../utils.php';
    $conn = connection();

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
        "sdt length condition" => "Số điện thoại phải có 10 số",
        "Username condition" => "Tên tài khoản phải có ít nhất 5 chữ cái, nhiều nhất 20 chữ cái và không có khoảng trắng",
        "max_leader_condition" => "Vượt quá số lượng trưởng phòng, mỗi phòng tối đa 1 trưởng phòng",
    ];


    $removedAttr = [
        "phong_ban_id" => "remove",
        "user_name" => "remove",
        "user_role" => "remove",
        "user_id" => "remove",
    ];
    
    if(isset($_POST['user_name'])){
        // Check existed user
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_POST['user_name'];
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) == 1 ) {
            http_response_code(409);
            echo "Tên tài khoản này đã được sử dụng";
            return; 
        }

        // Check length username
        if(strlen($_POST['user_name']) < 5 || strlen($_POST['user_name']) > 20){
            http_response_code(422);
            echo $exceptionTranslation["Username condition"];
            return;
        }
    }

    // Check length phone
    if(isset($_POST['sdt'])){
        if(strlen($_POST['sdt']) != 10){
            http_response_code(422);
            echo $exceptionTranslation["sdt length condition"];
            return;
        }

        // Check existed phone number
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE sdt = ? and user_id != ?");
        $stmt->bind_param("ss", $sdt, $user_id);
        $sdt = $_POST['sdt'];
        $user_id = $_POST['user_id'];
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) > 0 ) {
            http_response_code(409);
            echo "Số điện thoại đã được sử dụng";
            return; 
        }
    }

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

    array_push($valuesArr, $_POST["user_id"]);
    $updateStr = substr($updateStr, 0, -1);

    $types = str_repeat('s', count($valuesArr));

    $queryStr = "UPDATE `USERS` ".$updateStr." WHERE user_id = ?";

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