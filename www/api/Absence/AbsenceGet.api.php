<?php
    function _getAbsenceDetail($id){
        $conn = connection();
        $stm = $conn->prepare("SELECT * from ABSENCE where absence_id = ?");

        // $id = $_GET['id'];

        $stm->bind_param('i', $id);
        $stm->execute();
        $result = $stm->get_result();
        
        return $result;
    }

    /* 
    * Get list absence
    * @return list absence detail
    */
    function _getListAbsence(){
        $conn = connection();
        $sql = "SELECT * from ABSENCE order by absence_id desc";
        $result = $conn->query($sql);

        return $result;
    }

    /*
    * Get list absence of current user
    * @return list absence of current user
    */
    function _get_list_absence_current_user() {
        $conn = connection();
        $stmt = $conn->prepare("SELECT `ABSENCE`.*, `USERS`.`ho_ten` from `ABSENCE`,`USERS` where `ABSENCE`.`nguoi_tao_id` = `USERS`.`user_id` and nguoi_tao_id = ? order by ngay_bat_dau desc");
        $stmt->bind_param("s", $current_user_id);
        $current_user_id = _get_current_user_id();

        $stmt->execute();
        $absences = $stmt->get_result();

        return $absences;
    }

    /*
    * Get list proccessed absence in future of current user
    * @return list proccessed absence in future of current user
    */
    function _get_list_proccessed_absence_future_current_user() {
        $conn = connection();
        $stmt = $conn->prepare("SELECT *, DATEDIFF(`ABSENCE`.`ngay_ket_thuc`,`ABSENCE`.`ngay_bat_dau`) + 1 as so_ngay_nghi  from `ABSENCE` where nguoi_tao_id = ? and ngay_bat_dau > NOW() and status = 1 order by ngay_bat_dau asc");
        $stmt->bind_param("s", $current_user_id);
        $current_user_id = _get_current_user_id();

        $stmt->execute();
        $absences = $stmt->get_result();

        return $absences;
    }

    /* 
    * Get list absence of department
    * @return list waiting absence of department
    */
    function _get_list_waiting_absence_of_deparment($p_department_id) {
        $conn = connection();
        $stmt = $conn->prepare("SELECT `ABSENCE`.*, `USERS`.`ho_ten` FROM `ABSENCE`,`USERS` WHERE `USERS`.`phong_ban_id` = ? and `ABSENCE`.`nguoi_tao_id` = `USERS`.`user_id`  and `ABSENCE`.`status` = 0 and `USERS`.`user_id` != ? order by `ABSENCE`.`created_on` desc");
        $stmt->bind_param("ss", $p_department_id, $current_user_id);
        $current_user_id = _get_current_user_id();

        $stmt->execute();
        $list_waiting_absences = $stmt->get_result();

        return $list_waiting_absences;
    }

    /* 
    * Get list absence of department
    * @return list proccessed absence of department
    */
    function _get_list_proccessed_absence_of_deparment($p_department_id) {
        $conn = connection();
        $stmt = $conn->prepare("SELECT `ABSENCE`.*, `USERS`.`ho_ten` FROM `ABSENCE`,`USERS` WHERE `USERS`.`phong_ban_id` = ?  and `ABSENCE`.`nguoi_tao_id` = `USERS`.`user_id`  and `ABSENCE`.`status` != 0 order by `ABSENCE`.`created_on` desc");
        $stmt->bind_param("s", $p_department_id);

        $stmt->execute();
        $list_waiting_absences = $stmt->get_result();

        return $list_waiting_absences;
    }

    /* 
    * Get list absence of all manager
    * @return list waiting absence of all manager
    */
    function _get_list_waiting_absence_of_manager() {
        $conn = connection();
        $stmt = $conn->prepare("SELECT `ABSENCE`.*, `USERS`.`ho_ten` FROM `ABSENCE`,`USERS` WHERE `USERS`.`status` = 1 and `ABSENCE`.`nguoi_tao_id` = `USERS`.`user_id`  and `ABSENCE`.`status` = 0 order by `ABSENCE`.`created_on` desc");
        $stmt->execute();
        $list_waiting_absences = $stmt->get_result();

        return $list_waiting_absences;
    }

    /* 
    * Get list absence of all manager
    * @return list proccessed absence of all manager
    */
    function _get_list_proccessed_absence_of_manager() {
        $conn = connection();
        $stmt = $conn->prepare("SELECT `ABSENCE`.*, `USERS`.`ho_ten` FROM `ABSENCE`,`USERS` WHERE `USERS`.`status` = 1 and `ABSENCE`.`nguoi_tao_id` = `USERS`.`user_id`  and `ABSENCE`.`status` != 0 order by `ABSENCE`.`created_on` desc");
        $stmt->execute();
        $list_proccessed_absences = $stmt->get_result();

        return $list_proccessed_absences;
    }

    /*
    * Get list today's absence 
    * @return list today's absence
    */
    function _get_list_today_absence() {
        $conn = connection();
        $stmt = $conn->prepare("SELECT `ABSENCE`.*, DATE_ADD(`ABSENCE`.`ngay_ket_thuc`, INTERVAL 1 DAY) as `ngay_quay_lai`,  `USERS`.*,`PHONG_BAN`.`ten_phong` from `ABSENCE`,`USERS`, `PHONG_BAN` where `ABSENCE`.`nguoi_tao_id` = `USERS`.`user_id` and `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` and `ABSENCE`.`ngay_bat_dau` <= curdate() and `ABSENCE`.`ngay_ket_thuc` >= curdate() order by ngay_bat_dau desc");
        $stmt->execute();
        $absences = $stmt->get_result();

        return $absences;
    }
?>
