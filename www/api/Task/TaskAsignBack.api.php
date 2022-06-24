<?php
    require_once '../../utils.php';
    $conn = connection();
    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "task_not_found" => "Không tồn tại task có ID cần nhận",
        "task_status_condition" => "Đây không phải task đang bị từ chối",
        "id_missing" =>  "Vui lòng truyền id",
    ];

    if (
        isset($_POST['task_id'])
    ) {
        $task_id = $_POST['task_id'];

        // Check existed task
        $stmt = $conn->prepare("SELECT * FROM `TASK` WHERE `task_id` = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $check_exited_rs = $stmt->get_result();

        if (mysqli_num_rows($check_exited_rs) == 0) {
            http_response_code(409);
            echo $exceptionTranslation['task_not_found'];
            return;
        };

        // Check status
        $stmt = $conn->prepare("SELECT * FROM `TASK` WHERE `task_id` = ? and `status` = 5");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $check_exited_rs = $stmt->get_result();

        if (mysqli_num_rows($check_exited_rs) == 0) {
            http_response_code(409);
            echo $exceptionTranslation['task_status_condition'];
            return;
        };

        $stmt = $conn->prepare("UPDATE `TASK` SET `status` = 2 WHERE `TASK`.`task_id` = ?");
        $stmt->bind_param("i", $task_id);
        $rs = $stmt->execute();
        
        if ($rs) {
            http_response_code(200);
            echo "Success";
            return;
        } else {
            http_response_code(500);
            if (isset($exceptionTranslation[htmlspecialchars($stmt->error)])) {
                echo $exceptionTranslation[htmlspecialchars($stmt->error)];
                return;
            } else {
                echo $exceptionTranslation["Error"];
                return;
            }
        }
        return;
    } else {
        http_response_code(422);
        echo $exceptionTranslation['id_missing'];
        return;
    }
?>