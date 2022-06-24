<?php
    require_once '../../utils.php';
    $conn = connection();

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
        "invalid_password" => "Mật khẩu không hợp lệ",
        "password_lenght_condition" => "Mật khẩu ít nhất phải có 5 kí tự",
    ];

    if(_validate_not_null($_SESSION['username']) && _validate_not_null($_POST['curr_password']) && _validate_not_null($_POST['new_password'])){
        // Check existed user
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ? and active = 1");
        $stmt->bind_param("s", $user_name);
        $user_name = $_SESSION['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) == 1 ) {
            $row = $result->fetch_assoc();
            
            if(!password_verify($_POST['curr_password'], $row['password'])){
                http_response_code(422);
                echo $exceptionTranslation["invalid_password"];
                return; 
            }
        }else{
            http_response_code(422);
            echo $exceptionTranslation["invalid_password"];
            return;
        }
    }else{
        http_response_code(422);
        echo $exceptionTranslation["Empty field"];
        return;
    }

    // Check length new password
    if(strlen($_POST['new_password']) < 5){
        http_response_code(422);
        echo $exceptionTranslation["password_lenght_condition"];
        return;
    }

    $stmt = $conn->prepare("UPDATE `USERS` SET password= ? WHERE user_name = ?");
    $stmt->bind_param("ss", $new_password, $user_name);
    $new_password = _encode_password($_POST['new_password']);
    $user_name = $_SESSION['username'];
    $stmt->execute();
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