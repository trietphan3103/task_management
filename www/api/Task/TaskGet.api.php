<?php
    /* 
    * Get Detail of one product base on specific id
    * @param id
    * @return product detail
    */
    function _getTaskDetail($id){
        $conn = connection();
        $stm = $conn->prepare("SELECT *,@diff:=ABS( UNIX_TIMESTAMP(`thoi_gian_deadline`) - UNIX_TIMESTAMP() - 60*60*7 ) , 
        CAST(@days := IF(@diff/86400 >= 1, floor(@diff / 86400 ),0) AS SIGNED) as days, 
        CAST(@hours := IF(@diff/3600 >= 1, floor((@diff:=@diff-@days*86400) / 3600),0) AS SIGNED) as hours, 
        CAST(@minutes := IF(@diff/60 >= 1, floor((@diff:=@diff-@hours*3600) / 60),0) AS SIGNED) as minutes,
        `TASK`.`mo_ta` as task_mo_ta
        FROM `TASK` 
        INNER JOIN `USERS` ON `TASK`.`nguoi_thuc_hien_id` = `USERS`.`user_id` 
        INNER JOIN `PHONG_BAN` ON `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id` 
        WHERE `TASK`.`task_id` = ?");


        $stm->bind_param('i', $id);
        $stm->execute();
        $result = $stm->get_result();
        
        return $result;
    }

    /* 
    * Get list product
    * @return list product detail
    */
    function _getListTask(){
        $conn = connection();
        $sql = "SELECT * from `TASK` order by `task_id` desc";
        $result = $conn->query($sql);

        return $result;
    }

    // Hàm lấy list task theo status
    function _getListTaskByStatus($status){
        $conn = connection();
        $stm = $conn->prepare("SELECT *,@diff:=ABS( UNIX_TIMESTAMP(`thoi_gian_deadline`) - UNIX_TIMESTAMP() - 60*60*7 ) , 
        CAST(@days := IF(@diff/86400 >= 1, floor(@diff / 86400 ),0) AS SIGNED) as days, 
        CAST(@hours := IF(@diff/3600 >= 1, floor((@diff:=@diff-@days*86400) / 3600),0) AS SIGNED) as hours, 
        CAST(@minutes := IF(@diff/60 >= 1, floor((@diff:=@diff-@hours*3600) / 60),0) AS SIGNED) as minutes,
        `TASK`.`mo_ta` as task_mo_ta
        FROM `TASK` 
        INNER JOIN `USERS` ON `TASK`.`nguoi_thuc_hien_id` = `USERS`.`user_id` 
        INNER JOIN `PHONG_BAN` ON `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id`
        where `PHONG_BAN`.`phong_ban_id` = (Select `phong_ban_id` from `USERS` u where u.`user_id` = ?) and `TASK`.`status`= ? order by `task_id` desc");
        
        $stm->bind_param('ss', $current_user_id ,$status);
        $current_user_id = _get_current_user_id();
        $stm->execute();
        $result = $stm->get_result();

        return $result;
    }

    // Hàm lấy tên phòng ban của task
    function _getTenPhongBan($id){
        $conn = connection();
        $stm = $conn->prepare("SELECT * from `TASK`, `USERS`, `PHONG_BAN` 
                                        where `TASK`.`nguoi_thuc_hien_id` = `USERS`.`user_id`
                                        and `USERS`.`phong_ban_id` = `PHONG_BAN`.`phong_ban_id`
                                        and  `TASK`.`task_id` = ?");       
        $stm->bind_param('i', $id);
        $stm->execute();
        $result = $stm->get_result();
        $result = $result->fetch_assoc();

        return $result['ten_phong'];
    }

    // Get task History
    function _get_task_history($id){
        $conn = connection();
        $stm = $conn->prepare("SELECT * from `HISTORY` where `task_id` = ? order by created_on desc");       
        $stm->bind_param('i', $id); 
        $stm->execute();
        $result = $stm->get_result();

        return $result;
    }

    // Get task History base on history_id
    function _get_task_history_detail($history_id){
        $conn = connection();
        $stm = $conn->prepare("SELECT * from `HISTORY` where `history_id` = ?");       
        $stm->bind_param('i', $history_id); 
        $stm->execute();
        $result = $stm->get_result();

        return $result;
    }
?>
