<?php
    require_once '../../utils.php';
    require_once("../../api/User/UserGet.api.php");

    $conn = connection();

    $exceptionTranslation = [
        "Error" => "Có lỗi đã xảy ra, vui lòng thử lại lần nữa",
        "Empty field" => "Vui lòng điền đầy đủ thông tin",
        "absence_create_conditon" => "Không thể tạo đơn xin nghỉ phép mới do bạn đang có đơn nghỉ phép khác chờ duyệt hoặc bạn có đơn nghỉ phép đã duyệt trong vòng 7 ngày gần đây",
        "absence_date_condiotion" => "Ngày kết thúc phải lớn hơn ngày bắt đầu và ngày bắt đầu phải lớn hơn ngày hiện tại",
        "absence_exceed_max_conditon" => "Số ngày nghỉ vượt quá số lượng cho phép",
        "absence_role_condition" => "Giám đốc không cần tạo đơn nghỉ phép",
        "File error condition" => "File bị lỗi, vui lòng chọn file khác",
        "File extension condition" => "File sai định dạng",
        "File size condition" => "Vui lòng chọn file nhỏ hơn 5M"
    ];

    if(_check_giam_doc()){
        http_response_code(422);
        echo $exceptionTranslation['absence_role_condition'];
        return;
    }

    $attrArr = array();
    $valuesArr = array();
    $keyStr = "(";
    $createStr = " (";

    // Init default value
    $_POST["nguoi_tao_id"] = _get_current_user_id();

    foreach ($_POST as $key => $value){
        if(!_validate_not_null($value)){
            http_response_code(422);
            echo $exceptionTranslation["Empty field"];
            return;
        }
        if ($key != 'absence-file') {
            array_push($attrArr, $key);
            array_push($valuesArr, $value);
            $keyStr = $keyStr.$key.",";
            $createStr = $createStr." ?,";
        }
    }

    $keyStr = substr($keyStr, 0, -1).")";
    $createStr = substr($createStr, 0, -1).")";

    $types = str_repeat('s', count($valuesArr));

    $queryStr = "INSERT INTO `ABSENCE`".$keyStr." values".$createStr;

    $stmt = $conn->prepare($queryStr);
    $stmt->bind_param($types, ...$valuesArr);
    $rs = $stmt->execute();
    
    if($rs){
        if (!empty($_FILES['absence-file']['name'])) {
            // check file error
            if ($_FILES['absence-file']['error'] > 0) {
                http_response_code(422);
                echo $exceptionTranslation["File error condition"];
                return;
            };

            // check file size
            $file_size = $_FILES['absence-file']['size'];
            if($file_size > 5000000) {
                http_response_code(422);
                echo $exceptionTranslation["File size condition"];
                return;
            };

            // check file extension
            $file_name = $_FILES['absence-file']['name'];
            $file_name_extn = substr($file_name, strrpos($file_name, '.')+1);
            if($file_name_extn == 'exe' || $file_name_extn == 'sh') {
                http_response_code(422);
                echo $exceptionTranslation["File extension condition"];
                return;
            };


            $last_id = $conn->insert_id;
            $file_path = '/files/absence/'.$last_id."/".$_FILES['absence-file']['name'];
            //Upload file
            mkdir("../../files/absence/".$last_id);
            move_uploaded_file($_FILES['absence-file']['tmp_name'], '../..'.$file_path);

            // //store file path to db
            $stmt = $conn->prepare("UPDATE `ABSENCE` SET `file` = ? WHERE `absence_id` = ?");
            $stmt->bind_param("si", $file_path, $last_id);
            $file_rs = $stmt->execute();
            
            if($file_rs){
                http_response_code(200);
                echo "Success";
                return;
            }else{
                http_response_code(500);
                echo $exceptionTranslation["Error"];
                return;
            }
        } else {
            http_response_code(200);
            echo "Success";
            return;
        }
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