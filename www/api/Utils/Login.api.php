<?php
    require_once '../../utils.php';
    $conn = connection();
    session_start();
    error_reporting(0);
    
    if(isset($_POST['username']) && isset($_POST['password'])){
        // Check existed user
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ? and active = 1");
        $stmt->bind_param("s", $user_name);
        $user_name = $_POST['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) == 1 ) {
            $row = $result->fetch_assoc();
            
            if(password_verify($_POST['password'], $row['password'])){
                http_response_code(200);
                if($_POST['username'] == $_POST['password']){
                    echo "Password need update";
                }else{
                    echo "Sign in success";
                    $_SESSION["username"] = $user_name;
                }
                return; 
            }
        }

        http_response_code(403);
        echo "Invalid username or password";
        return;
    }else{
        http_response_code(422);
        echo "Please fill enought information";
        return;
    }
?>