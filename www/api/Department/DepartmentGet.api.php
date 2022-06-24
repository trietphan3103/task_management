<?php

    /* 
    * Get list department
    * @return list department
    */
    function _get_list_departments() {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM `PHONG_BAN` WHERE `ten_phong` != N'Phòng giám đốc' ORDER BY `PHONG_BAN`.`phong_ban_id` asc");
                                    
        $stmt->execute();
        $departments = $stmt->get_result();

        return $departments;
    }

    /* 
    * Get department information by phong_ban_id
    * @return department information
    */
    function _get_list_department($id) {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM `PHONG_BAN` WHERE `phong_ban_id` = ?;");
        $stmt->bind_param("i", $phong_ban_id);
        $phong_ban_id = $id;
        $stmt->execute();
        $result = $stmt->get_result();

        $rs = $result->fetch_assoc();

        return $rs;
    }

    /* 
    * Get department detail by phong_ban_id
    * @return department detail
    */
    function _get_department_detail($id) {
        $conn = connection();
        $stm = $conn->prepare("SELECT * FROM `PHONG_BAN`, `USERS` WHERE `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` AND `PHONG_BAN`.`phong_ban_id` = ?;");
        $stm->bind_param('i', $phong_ban_id);
        $phong_ban_id = $id;
        $stm->execute();
        $result = $stm->get_result();
        
        $rs = $result->fetch_assoc();

        return $rs;
    }

    /* 
    * Get user information by user_id
    * @return user information
    */
    function _get_list_user_department($id) {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM `USERS`, `PHONG_BAN` WHERE `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` AND `PHONG_BAN`.`phong_ban_id` = ?;");
        $stmt->bind_param("i", $phong_ban_id);
        $phong_ban_id = $id;
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    /* 
    * Get user information by user_id where status = 0
    * @return user information
    */
    function _get_list_user_status($id) {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM `USERS`, `PHONG_BAN` WHERE `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` AND `PHONG_BAN`.`phong_ban_id` = ? AND `status` = 0;");
        $stmt->bind_param("i", $phong_ban_id);
        $phong_ban_id = $id;
        $stmt->execute();
        $result = $stmt->get_result();

        return $result;
    }

    /* 
    * Get user information by user_id where status = 1
    * @return user information
    */
    function _get_list_user_manager($id) {
        $conn = connection();
        $stmt = $conn->prepare("SELECT * FROM `USERS`, `PHONG_BAN` WHERE `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` AND `PHONG_BAN`.`phong_ban_id` = ? AND `status` = 1;");
        $stmt->bind_param("i", $phong_ban_id);
        $phong_ban_id = $id;
        $stmt->execute();
        $result = $stmt->get_result();

        $rs = $result->fetch_assoc();

        return $rs;
    }

    /* 
    * Count users group by phong_ban_id
    * @return quantity of users
    */
    function _count_user_department($pb_id) {
        $conn = connection();
        $stmt = $conn->prepare("SELECT COUNT(*) as `count` FROM `USERS`, `PHONG_BAN` WHERE `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` AND `PHONG_BAN`.`phong_ban_id` = ?");
        $stmt->bind_param("i", $phong_ban_id);
        $phong_ban_id = $pb_id;
        $stmt->execute();
        $result = $stmt->get_result();
 
        $rs = $result->fetch_assoc();

        return $rs;
    }
?>
