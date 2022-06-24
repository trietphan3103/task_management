<?php
    require_once '../../utils.php';
    $conn = connection();

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
        "sdt length condition" => "Số điện thoại phải có 10 số",
        "Username condition" => "Tên tài khoản phải có ít nhất 5 chữ cái, nhiều nhất 20 chữ cái và không có khoảng trắng",
        "max_leader_condition" => "Vượt quá số lượng trưởng phòng, mỗi phòng tối đa 1 trưởng phòng",
        "user_role_condition" => "Người dùng không đủ quyền để thực hiện thao tác này",
    ];

    if(!_check_giam_doc()){
        http_response_code(403);
        echo $exceptionTranslation['user_role_condition'];
        return;
    }

    // Check existed user
    $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ?");
    $stmt->bind_param("s", $user_name);
    $user_name = $_POST['user_name'];
    $stmt->execute();
    $result = $stmt->get_result();

    if (mysqli_num_rows($result) > 0 ) {
        http_response_code(409);
        echo "Username has existed";
        return; 
    }

    // Check existed phone number
    $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE sdt = ?");
    $stmt->bind_param("s", $sdt);
    $sdt = $_POST['sdt'];
    $stmt->execute();
    $result = $stmt->get_result();

    if (mysqli_num_rows($result) > 0 ) {
        http_response_code(409);
        echo "Phone number has been used";
        return; 
    }

    // Check length phone
    if(strlen($_POST['sdt']) != 10){
        http_response_code(422);
        echo $exceptionTranslation["sdt length condition"];
        return;
    }

    // Check length username
    if(strlen($_POST['user_name']) < 5 || strlen($_POST['user_name']) > 20){
        http_response_code(422);
        echo $exceptionTranslation["Username condition"];
        return;
    }

    $attrArr = array();
    $valuesArr = array();
    $keyStr = "(";
    $createStr = " (";

    // Init default value
    $_POST['password'] = _encode_password($_POST['user_name']);
    $_POST["user_role"] = "Nhân viên";

    foreach ($_POST as $key => $value){
        if(!_validate_not_null($value)){
            http_response_code(422);
            echo $exceptionTranslation["Empty field"];
            return;
        }
        array_push($attrArr, $key);
        array_push($valuesArr, $value);
        $keyStr = $keyStr.$key.",";
        $createStr = $createStr." ?,";
    }

    $keyStr = substr($keyStr, 0, -1).")";
    $createStr = substr($createStr, 0, -1).")";

    $types = str_repeat('s', count($valuesArr));

    $queryStr = "INSERT INTO USERS".$keyStr." values".$createStr;

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