<?php
    require_once '../../utils.php';
    $conn = connection();
    
    $exceptionTranslation = [
        "Data too long for column 'user_name' at row 1" => "Username maximum length is 20 letters",
        "Empty field" => "Username and password are mandatory",
        "Password condition" => "Password most contain at least 8 letters and not contain white space",
        "Username condition" => "Username most contain at least 6 letters and not contain white space",
        "Error" => "Some thing went wrong, please try again later",
    ];

    if(_validate_not_null($_POST['username']) && _validate_not_null($_POST['password'])){
        // Check length username and password
        if(strlen($_POST['username']) < 6){
            http_response_code(422);
            echo $exceptionTranslation["Username condition"];
            return;
        }

        if(strlen($_POST['password']) < 8){
            http_response_code(422);
            echo $exceptionTranslation["Password condition"];
            return;
        }

        // Check content username and password
        if(ctype_space($_POST['username'])){
            http_response_code(422);
            echo $exceptionTranslation["Username condition"];
            return;
        }

        if(ctype_space($_POST['password'])){
            http_response_code(422);
            echo $exceptionTranslation["Password condition"];
            return;
        }

        // Check existed user
        $stmt = $conn->prepare("SELECT * FROM `USERS` WHERE user_name = ?");
        $stmt->bind_param("s", $user_name);
        $user_name = $_POST['username'];
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) > 0 ) {
            http_response_code(409);
            echo "Username has existed";
            return; 
        }

        // Create new user account
        $stmt = $conn->prepare("INSERT INTO USERS(user_name,password) VALUES (?, ?)");
        $stmt->bind_param("ss", $user_name, $password);

        $user_name = $_POST['username'];
        $password = _encode_password($_POST['password']);
        $rs = $stmt->execute();
        
        if($rs){
            $stmt = $conn->prepare("INSERT INTO USER_ROLE(`user_id`, `role_id`) VALUES ((SELECT LAST_INSERT_ID()), 2)");
            $role_rs = $stmt->execute();
            if($role_rs){
                http_response_code(200);
                echo "Success";
                return;
            }else{
                http_response_code(500);
                echo $exceptionTranslation["Error"];
                return;
            }
        }else{  
            http_response_code(500);
            if(isset($exceptionTranslation[htmlspecialchars($stmt->error)])){
                echo $exceptionTranslation[htmlspecialchars($stmt->error)];
                return;
            }else{
                echo $exceptionTranslation["Error"];
                return;
            }
        }
        
        return;
    }else{
        http_response_code(422);
        echo $exceptionTranslation["Empty field"];
        return;
    }
?>