<?php
    require_once '../../utils.php';
    $conn = connection();

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
        "default_pass_condtion" => "Mật khẩu phải khác với tên đăng nhập",
        "user_role_condition" => "Người dùng không đủ quyền để thực hiện thao tác này",
    ];

    if(!_check_giam_doc()){
        http_response_code(403);
        echo $exceptionTranslation['user_role_condition'];
        return;
    }

    if(isset($_POST['user_name'])){
        // Check existed user
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_POST['user_name'];
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) == 0 ) {
            http_response_code(409);
            echo "Tên tài khoản không tồn tại";
            return; 
        }
    }

    if($_POST['password'] == $_POST['user_name']){
        http_response_code(422);
        echo $exceptionTranslation['default_pass_condtion'];
        return;
    }

    $_POST['password'] = _encode_password($_POST['user_name']);

    $queryStr = "UPDATE `USERS` set `password` = ? WHERE user_name = ?";

    $stmt = $conn->prepare($queryStr);
    $stmt->bind_param("ss", $_POST['password'], $_POST['user_name']);
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