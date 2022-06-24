<?php
    // require_once '../../utils.php';

    /* 
    * Get list user
    * @return list department
    */
    function _get_list_users() {
        $conn = connection();
        $stmt = $conn->prepare("SELECT `USERS`.*, `PHONG_BAN`.`ten_phong` FROM `USERS`, `PHONG_BAN` WHERE `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` and `USERS`.`user_role` != N'Giám đốc' and `USERS`.`active` = 1 order by `USERS`.`user_id`");
        $stmt->execute();
        $results = $stmt->get_result();

        return $results;
    }

    /* 
    * Get user information by user_id
    * @return user information
    */
    function _get_user_information($p_user_id){
        $conn = connection();
        $stmt = $conn->prepare("SELECT `USERS`.*, `PHONG_BAN`.* FROM `USERS`, `PHONG_BAN` WHERE `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` and `USERS`.`user_id` = ?");
        $stmt->bind_param("s", $user_id);
        $user_id = $p_user_id;
        $stmt->execute();
        $result = $stmt->get_result();

        $user_info = $result->fetch_assoc();

        return $user_info;
    }

    /* 
    * Get user information by user_id
    * @return user information
    */

    function _get_day_off_used($p_user_id){
        $conn = connection();
        $stmt = $conn->prepare("CALL _get_current_day_off_used(?)");
        $stmt->bind_param("s", $user_id);
        $user_id = $p_user_id;
        $stmt->execute();
        $result = $stmt->get_result();

        $day_used = $result->fetch_assoc();

        return $day_used['day_used'];
    }
?>
