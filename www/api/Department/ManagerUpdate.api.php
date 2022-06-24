<?php
    require_once '../../utils.php';
    $conn = connection();

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
        "user_role_condition" => "Người dùng không đủ quyền để thực hiện thao tác này",
    ];
    
    if(!_check_giam_doc()){
        http_response_code(403);
        echo $exceptionTranslation['user_role_condition'];
        return;
    }
    
    $user_id = $_POST['user_id'];
    $phong_ban_id = $_POST['phong_ban_id'];

    if (!empty($_POST['user_id']) && !empty($_POST['phong_ban_id'])) {
        if(isset($_POST['user_id'])){
            // Check existed user
            $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE `user_id` = ?");
            $stmt->bind_param("i", $user_id);
            $user_id = $_POST['user_id'];
            $stmt->execute();
            $result = $stmt->get_result();
    
            if (mysqli_num_rows($result) == 0 ) {
                http_response_code(409);
                echo "Không tồn tại ID người dùng";
                return; 
            }
        }

        $remove_old_manager_stmt = $conn->prepare("UPDATE `USERS` SET `status` = 0 WHERE `phong_ban_id` = ? and `status` = 1;");
        $add_new_manager_stmt = $conn->prepare("UPDATE `USERS` SET `status` = 1 WHERE `user_id` = ?;");     

        $remove_old_manager_stmt->bind_param("s", $phong_ban_id);
        $add_new_manager_stmt->bind_param("s", $user_id);

        $check_update_old_rs = $remove_old_manager_stmt->execute();
        $check_update_new_rs = $add_new_manager_stmt->execute();

        if ($check_update_old_rs && $check_update_new_rs) {
            http_response_code(200);
            echo "Success";
            return;
        } else {
            http_response_code(500);
            echo $exceptionTranslation["Error"];
            echo htmlspecialchars($stmt->error);
        }
    
    } else {
        http_response_code(422);
        echo $exceptionTranslation["Empty field"];
        return;
    };
?>