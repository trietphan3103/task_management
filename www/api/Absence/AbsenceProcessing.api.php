<?php
    require_once '../../utils.php';
    $conn = connection();

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "absence_process_role_condition" => "Người dùng không đủ quyền để thực hiện thao tác này",
        "absence_process_date_condition" => "Ngày duyệt phải sớm hơn ngày bắt đầu và sớm hơn hoặc bằng ngày tạo",
    ];

    if(!_check_giam_doc() && !_check_manager()){
        http_response_code(422);
        echo $exceptionTranslation['absence_process_role_condition'];
        return;
    }

    $nguoi_duyet = _get_current_user_id();

    $queryStr = "UPDATE `ABSENCE` SET status = ?, nguoi_duyet_id = ?, ngay_duyet = NOW() WHERE absence_id = ?";

    $stmt = $conn->prepare($queryStr);
    $stmt->bind_param("sss", $_POST['status'], $nguoi_duyet , $_POST['absence_id']);
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