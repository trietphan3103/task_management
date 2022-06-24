<?php
    session_start();
    error_reporting(0);
    require("../../utils.php");
    _require_login();
    _require_giam_doc();
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
    <title>Quản lý user</title>
</head>

<body>
    <?php
        require("../../view/Common/Header.php");
        require_once("../../api/Department/DepartmentGet.api.php");
        require_once("../../api/User/UserGet.api.php");
    ?>
    <div class="body-layout">
        <div class="container mb-5">
            <div class="row user-management">
                <div class="col">
                    <div class="row justify-content-between mb-3">
                        <h2 class="user-management-heading">Quản lý nhân viên</h2>
                        <div class="user-create">
                            <div class="btn_contain" href="#" data-toggle="modal" data-target="#userCreateModal">
                                <label>Thêm nhân viên mới</label>
                                <i class="fas fa-plus-circle add_btn"></i>
                            </div>
                            <!--user Create Modal -->
                            <div class="user-create-modal modal fade" id="userCreateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle"><b>Thêm nhân viên</b></h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="user-create-form" id="user_create_form">
                                                <div class="alert alert-danger" id="message_create_user"></div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="user-name-input">Tên nhân viên</label>
                                                        <input type="text" class="form-control" id="ho_ten" placeholder="Nhập tên nhân viên" name="ho_ten">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="user-username-input">Username</label>
                                                        <input type="text" class="form-control" id="user_name" placeholder="Nhập username nhân viên" name="user_name">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="user-birthday-input">Ngày sinh</label>
                                                        <input type="date" class="form-control" id="ngay_sinh" name="ngay_sinh">
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="user-gender-input">Giới tính</label>
                                                        <select class="custom-select" id="gioi_tinh" name="gioi_tinh">
                                                            <option value="Nam" selected>Nam</option>
                                                            <option value="Nữ">Nữ</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="user-gender-input">Phòng ban</label>
                                                        <select class="custom-select" id="phong_ban_id" name="phong_ban_id">
                                                            <?php
                                                                $departments = _get_list_departments();
                                                                if ($departments->num_rows > 0) {
                                                                    while ($department =  $departments->fetch_assoc()) {
                                                            ?>
                                                                    <option <?php echo "value = '".$department['phong_ban_id']."'" ?>><?php echo $department["ten_phong"] ?></option>
                                                            <?php
                                                                    }
                                                                }else{}
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="user-gender-input">Chức vụ</label>
                                                        <select class="custom-select" id="status" name="status">
                                                            <option value="0">Nhân viên</option>
                                                            <option value="1">Trưởng phòng</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="user-number-input">Số điện thoại</label>
                                                        <input type="text" class="form-control" id="sdt" name="sdt">
                                                    </div>
                                                </div>
                                                <div class="form-footer mt-2">
                                                    <button type="reset" class="btn btn-outline-primary">Clear</button>
                                                    <button type="submit" class="btn btn-success">Lưu</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-wrapper table-no-scroll">
                    <?php
                        if(isset($_GET["status"])){
                            if($_GET["status"] == "created")
                                echo '<div class="alert alert-info"> Tạo mới tài khoản nhân viên thành công </div>';
                            if($_GET["status"] == "deleted")
                                echo '<div class="alert alert-info"> Xóa nhân viên thành công </div>';
                        }
                    ?>
                    
                    <table class="user-list table table-hover table-striped">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="align-middle employee-id">Username</th>
                                <th scope="col" class="align-middle employee-name">Họ và tên</th>
                                <th scope="col" class="align-middle employee-department">Phòng ban</th>
                                <th scope="col" class="align-middle employee-department">Chức vụ</th>
                                <th scope="col" class="align-middle">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $users = _get_list_users();
                                if ($users->num_rows > 0) {
                                    while ($user =  $users->fetch_assoc()) {
                            ?>
                                <tr class="user-item" id="user_<?php echo $user['user_id'] ?>">                                 
                                    <td> <a href="/view/User/UserDetail.php?user_id=<?php echo $user['user_id'] ?>"> <b><?php echo $user['user_name'] ?></b></a></td>
                                    <td class="font-weight-bold"><?php echo $user['ho_ten'] ?></td>   
                                    <td><?php echo $user['ten_phong'] ?></td>
                                    <td><?php if($user['status'] == 0){echo "Nhân viên";}else{echo "Trưởng phòng";} ?></td>
                                    <td class="user-action">
                                        <button type="button" class="btn user-delete-btn" data-toggle="modal" data-target="#userDeleteModal" onClick="_setSelectedUser('<?php echo $user['user_name'] ?>')">Vô hiệu hóa</button>
                                    </td>
                                </tr>
                            <?php
                                    }
                                }else{
                                    echo '<tr> <td colspan=5>Chưa có user nào</td> </tr>';
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- user-delete-modal -->
                <div class="modal fade" id="userDeleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">Xác nhận vô hiệu hóa nhân viên</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Bạn có chắc là muốn xóa <b>username</b> chứ?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-danger" onclick="_deleteUser()">Xác nhận</button>
                            </div>
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