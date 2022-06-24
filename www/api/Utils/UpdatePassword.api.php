<?php
    require_once '../../utils.php';
    $conn = connection();
    session_start();
    error_reporting(0);

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
        "invalid_password" => "Mật khẩu không hợp lệ",
        "password_lenght_condition" => "Mật khẩu ít nhất phải có 5 kí tự",
        "password_default_condiotion" => "Mật khẩu mới phải khác mật khẩu mặc định",
    ];
    
    if(isset($_POST['username']) && isset($_POST['new_password'])){
        // Check length new password
        if(strlen($_POST['new_password']) < 5){
            http_response_code(422);
            echo $exceptionTranslation["password_lenght_condition"];
            return;
        }

        // Check password default
        if($_POST['new_password'] == $_POST['username']){
            http_response_code(422);
            echo $exceptionTranslation['password_default_condiotion'];
            return;
        }

        // Update user password
        $stmt = $conn->prepare("UPDATE `USERS` SET password=? WHERE user_name = ?");
        $stmt->bind_param("ss", $new_password, $user_name);
        $new_password = _encode_password($_POST['new_password']);
        $user_name = $_POST['username'];
        $stmt->execute();
        $rs = $stmt->execute();

        if($rs){
            http_response_code(200);
            echo "Success";
            $_SESSION["username"] = $user_name;
            return;
        }else{
            http_response_code(500);
            echo $exceptionTranslation["Error"];
            return;
        }
    }else{
        http_response_code(422);
        echo $exceptionTranslation["Empty field"];
        return;
    }
?>