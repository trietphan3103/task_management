<?php
    require_once '../../utils.php';
    require_once './AbsenceGet.api.php';
    $conn = connection();

    if(!empty($_GET['absence-id'])) {
        // check existed
        $stmt = $conn->prepare("SELECT * FROM `ABSENCE` WHERE absence_id = ?");
        $absence_id = $_GET['absence-id'];
        $stmt->bind_param("i", $absence_id);
        $stmt->execute();
        $check_exited_rs = $stmt->get_result();

        if (mysqli_num_rows($check_exited_rs) != 1) {
            http_response_code(404);
            echo "Không tồn tại absence với id yêu cầu";
            return;
        }

        $result = _getAbsenceDetail($absence_id);
        $result = $result->fetch_assoc();
        echo json_encode($result);
    } else {
        http_response_code(404);
        echo "Không tồn tại absence với id yêu cầu";
        return;
    }
?>