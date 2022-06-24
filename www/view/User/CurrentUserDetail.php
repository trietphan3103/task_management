<?php
    session_start();
    error_reporting(0);
    require("../../utils.php");
    _require_login();
    if(!_check_normal_employee() && !_check_manager()){
        header('Location: /view/utilsView/Error403.html');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@1,700&display=swap" rel="stylesheet">
    <title>Thông tin chi tiết người dùng</title>
</head>

<body>
    <?php
        require_once("../../api/User/UserGet.api.php");
        require_once("../../api/Department/DepartmentGet.api.php");
        $userInfo = _get_user_information(_get_current_user_id());
        $day_used = _get_day_off_used(_get_current_user_id());
    ?>

    <?php
        require("../../view/Common/Header.php");
    ?>
    <div class="modal fade" id="userResetModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel"><b>Thay đổi mật khẩu</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="message_update_user_danger"></div>
                    <div class="form-group col-md-12 px-4">
                        <input type="password" class="form-control" id="curr_password" placeholder="Mật khẩu hiện tại">
                    </div>
                    <div class="form-group col-md-12 px-4">
                        <input type="password" class="form-control" id="new_password" placeholder="Mật khẩu mới">
                    </div>
                    <div class="form-group col-md-12 px-4">
                        <input type="password" class="form-control" id="confirm_new_password" placeholder="Xác nhận mật khẩu mới">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary user-edit-btn" id="btn-update-user-pass">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>
    <div class="body-layout">
        <div class="container">
            <div class="row rounded bg-white mt-5 user-detail user-profile">
                <div class="col-md-4 border-right">
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                        <form class="profile-avatar" id="profile-avatar">
                            <label class="-label" for="profile-avatar-file">
                                <i class="fas fa-camera"></i>
                                <span>Change Avatar</span>
                            </label>
                            <input id="profile-avatar-file" type="file" name="profile-avatar-file" accept="image/*" />
                            <img src="<?php
                                            if (!empty($userInfo['anh_dai_dien'])) {
                                                echo ($userInfo['anh_dai_dien']);
                                            } 
                                            else {
                                                echo '/images/user_avt/default_avt.jpg';
                                            }
                                        ?>" id="profile-avatar-img" width="200" />
                        </form>
                        <!-- <img class="rounded-circle mt-5" src="https://i.imgur.com/0eg0aG0.jpg" width="90"> -->
                        <!-- <img class="rounded-circle mt-5" src="https://i.imgur.com/0eg0aG0.jpg" width="90"> -->
                        <span class="font-weight-bold mt-3"><?php echo $userInfo['ho_ten'] ?></span>
                        <span><?php echo $userInfo['ten_phong'] ?></span>
                        <span class="text-primary font-weight-bold"><?php if($userInfo['status'] == 0){echo "Nhân viên";}else{echo "Trưởng phòng";} ?></span>
                    </div>
                    <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                        <div class="progress mx-auto" data-value='<?php echo ($userInfo['so_absence_max'] - $day_used)/$userInfo['so_absence_max']*100; ?>'>
                            <span class="progress-left">
                                            <span class="progress-bar border-primary"></span>
                            </span>
                            <span class="progress-right">
                                            <span class="progress-bar border-primary"></span>
                            </span>
                            <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                                <div class="total-day"><?php echo $userInfo['so_absence_max'] - $day_used; ?><sup class="small"><i></i></sup></div>
                            </div>
                        </div>
                        <div class="document-block">
                            <div class="date-bank">
                                <span></span>
                                <span>Số ngày nghỉ cho phép: <span class="text-value"><?php echo $userInfo['so_absence_max'] - $day_used; ?> ngày</span></span>
                            </div>
                            <div class="date-used">
                                <span></span>
                                <span>Số ngày nghỉ đã dùng: <span class="text-value"><?php echo $day_used; ?> ngày</span></span>
                            </div>
                            <div class="date-remaining">
                                <span></span>
                                <span>Số ngày nghỉ còn lại: <span class="text-value"><?php echo $userInfo['so_absence_max'] - $day_used; ?> ngày</span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="p-3 py-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex flex-row align-items-center">
                                <div href="/view/User/UserDetail.php" class="p-2" id="prev-link">
                                    <i class="fas fa-caret-left mr-2"></i>
                                    <h6>Quay về</h6>
                                </div>
                            </div>
                            <h6 class="text-right primary-text user-edit-btn" id="reset-mode-btn" data-toggle="modal" data-target="#userResetModal">Đặt lại mật khẩu &nbsp; <i class="fas fa-key"></i></h6>
                        </div>
                        <form class="user-detail-form">
                            <div class="alert alert-info" id="message_update_user_infor"> </div>

                            <div class="form-row">
                                <div class="form-group col-md-6 px-4">
                                    <label for="user-name-info">Tên nhân viên</label>
                                    <div class="user-info" id="user-name-info"></div>
                                    <input type="text" class="form-control" id="ho_ten" value="<?php echo $userInfo['ho_ten'] ?>" default="<?php echo $userInfo['ho_ten'] ?>" name="ho_ten" readonly>
                                </div>
                                <div class="form-group col-md-6 px-4">
                                    <label for="user-username-info">Username</label>
                                    <input type="text" class="form-control" id="user_name" value="<?php echo $userInfo['user_name'] ?>" default="<?php echo $userInfo['user_name'] ?>" name="user_name" readonly>
                                </div>
                                <div class="form-group col-md-6 px-4">
                                    <label for="user-department-info">Phòng ban</label>
                                    <select class="custom-select" id="phong_ban_id" default="<?php echo $userInfo['phong_ban_id'] ?>" name="phong_ban_id" disabled>
                                        <?php
                                            $departments = _get_list_departments();
                                            if ($departments->num_rows > 0) {
                                                while ($department =  $departments->fetch_assoc()) {
                                        ?>
                                                <option <?php echo "value = '".$department['phong_ban_id']."'"; if($department['phong_ban_id'] == $userInfo['phong_ban_id']){ echo "selected";} ?> ><?php echo $department["ten_phong"] ?></option>
                                        <?php
                                                }
                                            }else{}
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 px-4">
                                    <label for="user-status">Ngày sinh</label>
                                    <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh" default="<?php echo $userInfo['ngay_sinh'] ?>" value="<?php echo $userInfo['ngay_sinh'] ?>" readonly>
                                </div>
                                <div class="form-group col-md-6 px-4">
                                    <label for="user-birthday-info">Chức vụ</label>
                                    <select class="custom-select" id="status" default="<?php echo $userInfo['status'] ?>" name="status" disabled>
                                        <option value="0" <?php if(0 == $userInfo['status']){ echo "selected";} ?> >Nhân viên</option>
                                        <option value="1" <?php if(1 == $userInfo['status']){ echo "selected";} ?> >Trưởng phòng</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 px-4">
                                    <label for="user-gender-info">Giới tính</label>
                                    <select class="custom-select" id="gioi_tinh" default="<?php echo $userInfo['gioi_tinh'] ?>" name="gioi_tinh" disabled>
                                        <option value="Nam" <?php if('Nam' == $userInfo['gioi_tinh']){ echo "selected";} ?>>Nam</option>
                                        <option value="Nữ" <?php if('Nữ' == $userInfo['gioi_tinh']){ echo "selected";} ?>>Nữ</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 px-4">
                                    <label for="user-number-info">Số điện thoại</label>
                                    <input type="text" class="form-control" id="sdt" name="sdt" default="<?php echo $userInfo['sdt'] ?>" value="<?php echo $userInfo['sdt'] ?>" readonly>
                                </div>
                            </div>

                            <button class="btn btn-mode btn-primary float-right">Cập nhật</button>
                            <button class="btn btn-mode btn-cancel float-right" id="btn-cancel">Hủy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="/main.js"></script>
</body>

</html>