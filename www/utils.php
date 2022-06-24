<?php
    session_start();
    error_reporting(0);
    // Hàm tạo connection tới DB
    function connection(){
        $host = 'mysql-server'; // tên mysql server
        $user = 'root';
        $pass = 'root';
        $db = 'QUANLYPHONGBAN'; // tên databse

        $conn = new mysqli($host, $user, $pass, $db);
        $conn->set_charset("utf8");
        if ($conn->connect_error) {
            die('Không thể kết nối database: ' . $conn->connect_error);
        }

       return $conn;
    }

    // Hàm lấy url hiện tại
    function current_url(){
        $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $validURL = str_replace("&", "&amp;", $url);
        return $validURL;
    }

    // Function return encoded password
    function _encode_password($password){
        $options = [
            'cost' => 12,
        ];

        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    // Function get current user name
    function _get_current_user(){
        return $_SESSION["username"];
    }

    // Function get current user id
    function _get_current_user_id(){
        $conn = connection();
        $stmt = $conn->prepare("SELECT `USERS`.`user_id`FROM `USERS`WHERE `USERS`.`user_name` = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_SESSION["username"];
        $stmt->execute();
        $result = $stmt->get_result();

        $user_info = $result->fetch_assoc();

        return $user_info['user_id'];
    }

    // Function get current user department id
    function _get_current_user_department_id(){
        $conn = connection();
        $stmt = $conn->prepare("SELECT `USERS`.`phong_ban_id`FROM `USERS`WHERE `USERS`.`user_name` = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_SESSION["username"];
        $stmt->execute();
        $result = $stmt->get_result();

        $user_info = $result->fetch_assoc();

        return $user_info['phong_ban_id'];
    }

    // Function to valide params type string not null
    function _validate_not_null($str){
        return isset($str) && strlen($str) > 0;
    }

    // Function to validate user is login or not
    function _check_user_logged(){
        return _validate_not_null($_SESSION["username"]);
    }

    // Function require user logged
    function _require_login(){
        if(!_check_user_logged()){
            header('Location: /view/utilsView/Login.php');
        }
    }

     // Function require user not logged
     function _require_not_logged(){
        if(_check_user_logged()){
            header('Location: /');
        }
    }

    // Function require user_role is "giám đốc"
    function _require_giam_doc(){
        $conn = connection();
        
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_SESSION['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        $user_info = $result->fetch_assoc();
        if($user_info['user_role'] != 'Giám đốc'){
            header('Location: /view/utilsView/Error403.html');
            exit;
        }
    }

    // Function check user_role is "giám đốc"
    function _check_giam_doc(){
        $conn = connection();
        
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_SESSION['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        $user_info = $result->fetch_assoc();
        return $user_info['user_role'] == 'Giám đốc';
    }

    // Function check user status is "trưởng phòng" (=1)
    function _check_manager(){
        $conn = connection();
        
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_SESSION['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        $user_info = $result->fetch_assoc();
        return $user_info['status'] == 1;
    }

    // Function check user is normal user
    function _check_normal_employee(){
        $conn = connection();
        
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_SESSION['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        $user_info = $result->fetch_assoc();
        return $user_info['status'] == 0 && $user_info['user_role'] != 'Giám đốc';
    }

     // Function require user is manager
     function _require_manager(){
        if(!_check_manager()){
            header('Location: /view/utilsView/Error403.html');
            exit;
        }
    }

    // Hàm chuyển second sang ?d?h?m
    function _convert_sec_to_day_left($n) {
        $day = floor($n / (24 * 3600));
    
        $n = $n % (24 * 3600);
        $hour = floor($n / 3600);
    
        $n %= 3600;
        $minutes = floor($n / 60);
        
        return $day."d ".$hour."h ".$minutes."m";
    }

    // Hàm lấy danh sách nhân viên của trưởng phòng
    function _get_list_employee() {
        $conn = connection();
        
        $stmt = $conn->prepare("SELECT * FROM `USERS` 
                                        WHERE `phong_ban_id` = (
                                            SELECT `phong_ban_id` 
                                            FROM `USERS`
                                            WHERE `user_name` = ?)
                                        AND `user_name` != ?");
        $stmt->bind_param("ss", $user_name, $user_name);
        $user_name = $_SESSION['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    // Hàm lấy id dựa trên user_name
    function _get_current_id() {
        $conn = connection();
        
        $stmt = $conn->prepare("SELECT DISTINCT * FROM `USERS` WHERE `user_name` = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_SESSION['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        $user_info = $result->fetch_assoc();

        return $user_info['user_id'];
    }
?>
