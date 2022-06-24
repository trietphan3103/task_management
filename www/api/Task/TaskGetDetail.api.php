<?php
    require_once '../../utils.php';
    require_once './TaskGet.api.php';
    $conn = connection();

    if(!empty($_GET['task-id'])) {
        // check existed
        $stmt = $conn->prepare("SELECT * FROM `TASK` WHERE `task_id` = ?");
        $task_id = $_GET['task-id'];
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $check_exited_rs = $stmt->get_result();

        if (mysqli_num_rows($check_exited_rs) != 1) {
            http_response_code(404);
            echo "Không tồn tại task với id yêu cầu";
            return;
        }
        
        $result = _getTaskDetail($task_id);
        $result = $result->fetch_assoc();
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo "Vui lòng truyền id của task muốn lấy thông tin";
        return;
    }
?>
