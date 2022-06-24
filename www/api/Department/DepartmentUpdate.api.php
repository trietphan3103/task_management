<?php 
    require_once '../../utils.php';
    $conn = connection();

    $exceptionTranslation = [
        "Ten phong length condition" => "Độ dài tối đa của tên phòng ban là 30 kí tự",
        "Mo ta length condition" => "Độ dài tối đa của mô tả là 2550 kí tự",
        "Phong ban existed" => "Phòng ban này đã được tạo trước đó",
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "user_role_condition" => "Người dùng không đủ quyền để thực hiện thao tác này",
    ];

    if(!_check_giam_doc()){
        http_response_code(403);
        echo $exceptionTranslation['user_role_condition'];
        return;
    }

    if (!empty($_POST['phong_ban_id']) && !empty($_POST['ten_phong']) && !empty($_POST['mo_ta'])) {
        // Check length
        if (strlen($_POST['ten_phong']) > 30) {
            http_response_code(422);
            echo $exceptionTranslation["Ten phong length condition"];
            return;
        }

        if (strlen($_POST['mo_ta']) > 2550) {
            http_response_code(422);
            echo $exceptionTranslation["Mo ta length condition"];
            return;
        }

        // check existed phong ban
        $stmt = $conn->prepare("SELECT * FROM `PHONG_BAN` WHERE `ten_phong` = ? and `phong_ban_id` != ?;");
        $ten_phong = $_POST['ten_phong'];
        $phong_ban_id = $_POST['phong_ban_id'];
        $stmt->bind_param("si", $ten_phong, $phong_ban_id);
        $stmt->execute();
        $check_exited_rs = $stmt->get_result();

        if (mysqli_num_rows($check_exited_rs) > 0) {
            http_response_code(409);
            echo $exceptionTranslation["Phong ban existed"];
            return;
        }

        // Update new phong ban
        $ten_phong = $_POST['ten_phong'];
        $mo_ta = $_POST['mo_ta'];

        $stmt = $conn->prepare("UPDATE `PHONG_BAN` SET `ten_phong` = ?, `mo_ta` = ? WHERE `phong_ban_id` = ?;");
        $stmt->bind_param("ssi",$ten_phong, $mo_ta, $phong_ban_id);
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
        echo "Vui lòng điền đầy đủ thông tin";
        return;
    };
?>
