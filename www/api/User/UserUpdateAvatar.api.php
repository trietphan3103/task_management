<?php 
    require_once '../../utils.php';
    $conn = connection();
    $exceptionTranslation = [
        "File error condition" => "File bị lỗi, vui lòng chọn file khác",
        "File extension condition" => "File sai định dạng"
    ];

    if (!empty($_FILES['profile-avatar-file']['name'])) {
        // check file error
        if ($_FILES['profile-avatar-file']['error'] > 0) {
            http_response_code(422);
            echo $exceptionTranslation["File error condition"];
            return;
        };

        // check file extension
        $file_name = $_FILES['profile-avatar-file']['name'];
        $file_name_extn = substr($file_name, strrpos($file_name, '.')+1);
        if($file_name_extn == 'exe' || $file_name_extn == 'sh') {
            http_response_code(422);
            echo $exceptionTranslation["File extension condition"];
            return;
        }

        $user_id = _get_current_user_id();

        $file_path = '/images/user_avt/'.$user_id."/".$_FILES['profile-avatar-file']['name'];
    } else {
        $user_id = _get_current_user_id();

        $file_path = '';
    };
    //Upload file
    mkdir("../../images/user_avt/".$user_id);
    move_uploaded_file($_FILES['profile-avatar-file']['tmp_name'], '../..'.$file_path);

    $stmt = $conn->prepare("UPDATE `USERS` SET `anh_dai_dien` = ? WHERE `user_id` = ?");
    $stmt->bind_param("ss", $file_path, $user_id);
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
?>
