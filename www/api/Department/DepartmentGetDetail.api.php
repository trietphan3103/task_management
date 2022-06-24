<?php
    require_once '../../utils.php';
    require_once './DepartmentGet.api.php';
    $conn = connection();

    if(!empty($_GET['phong-ban-id'])) {
        // check existed
        $stmt = $conn->prepare("SELECT * FROM `PHONG_BAN` WHERE phong_ban_id = ?");
        $phong_ban_id = $_GET['phong-ban-id'];
        $stmt->bind_param("i", $phong_ban_id);
        $stmt->execute();
        $check_exited_rs = $stmt->get_result();

        if (mysqli_num_rows($check_exited_rs) != 1) {
            http_response_code(404);
            echo "Không tồn tại phòng ban với id yêu cầu";
            return;
        }

        $result = _get_list_department($phong_ban_id);
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo "Không tồn tại phòng ban với id yêu cầu";
        return;
    }
?>