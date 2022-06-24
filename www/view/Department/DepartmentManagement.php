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
    <title>Quản lý phòng ban</title>
</head>

<body>
    <?php
        require("../../view/Common/Header.php");
        require_once("../../api/Department/DepartmentGet.api.php");
    ?>
    <div class="body-layout">
        <div class="container">
            <div class="row department-management">
                <div class="col">
                    <div class="row justify-content-between mb-3">
                        <h2 class="department-management-heading">Quản lý phòng ban</h2>
                        <div class="department-create">
                            <div>
                                <div class="btn_contain" href="#" id="create-department-btn" data-toggle="modal" data-target="#departmentCreateModal">
                                    <label>Thêm phòng ban</label>
                                    <i class="fas fa-plus-circle add_btn"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Department table -->
                <div class="table-wrapper">
                    <?php
                        if(isset($_GET["status"])){
                            if($_GET["status"] == "created")
                                echo '<div class="alert alert-info"> Thêm phòng ban mới thành công </div>';
                            if($_GET["status"] == "deleted")
                                echo '<div class="alert alert-info"> Xóa phòng ban thành công </div>';
                        }
                    ?>
                    <table class="department-list table table-hover table-striped">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="align-middle text-center department-number">Mã phòng</th>
                                <th scope="col" class="align-middle department-name">Tên phòng ban</th>
                                <th scope="col" class="align-middle department-manager">Trưởng phòng</th>
                                <th scope="col" class="align-middle department-description">Mô tả</th>
                                <th scope="col" class="align-middle">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $departmentList = _get_list_departments();
                                if ($departmentList->num_rows > 0) {
                                    while($department =  $departmentList->fetch_assoc()) {
                            ?>
                                <tr class="department-item">
                                    <th scope="row" class="text-center"><?php echo $department['phong_ban_id']; ?></th>
                                    <td class="department-request-creator"><?php echo $department['ten_phong']; ?></td>
                                    <td><?php 
                                        $managerName = _get_list_user_manager($department['phong_ban_id']);
                                        if($managerName['status'] == 1){echo $managerName['ho_ten'];} else{echo "Chưa có trưởng phòng";}?>
                                    </td>
                                    <td class="department-desc"><?php echo $department['mo_ta']; ?></td>
                                    <td class="department-action">
                                        <a href="/view/Department/DepartmentDetail.php?phong-ban-id=<?php echo $department['phong_ban_id'] ?>">
                                            <button type="button" class="btn btn-primary department-view-detail-btn">Xem Chi Tiết</button>
                                        </a>            
                                        <button type="button" class="btn btn-success department-modify-btn" data-toggle="modal" data-target="#departmentUpdateModal" value="<?php echo $department['phong_ban_id'] ?>">Chỉnh sửa</button>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="5" rowspan="2" class="text-center py-5 table-alert"><div><i class="fas fa-exclamation"></i></div>Hiện tại chưa có phòng ban nào</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                
    <!--Department Create Modal -->
    <div class="department-create-modal modal fade" id="departmentCreateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm phòng ban</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="department-create-form">
                        <div class="form-group department-create-name">
                            <label for="department-name-input">Tên phòng ban</label>
                            <input type="text" class="form-control" id="department-name-input" placeholder="Nhập tên phòng ban">
                        </div>
                        <div class="form-group">
                            <label for="department-desc-input">Mô tả</label>
                            <textarea rows="4" type="text" class="form-control w-100" id="department-desc-input"></textarea>
                        </div>
                        <div class="alert alert-danger" role="alert" id="message_create_department"></div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-outline-primary">Clear</button>
                            <button type="submit" class="btn btn-success">Tạo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Department Update Modal -->
    <div class="department-create-modal modal fade" id="departmentUpdateModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa phòng ban</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="department-update-form">
                        <div class="form-group department-create-name">
                            <label for="department-name-update">Tên phòng ban</label>
                            <input type="text" class="form-control" id="department-name-update" placeholder="Nhập tên phòng ban">
                        </div>
                        <div class="form-group">
                            <label for="department-desc-update">Mô tả</label>
                            <textarea rows="4" type="text" class="form-control w-100" id="department-desc-update"></textarea>
                        </div>
                        <div class="alert alert-danger" role="alert" id="message_update_department"></div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-outline-primary">Clear</button>
                            <button type="submit" class="btn btn-success">Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Delete Modal -->
    <div class="modal fade" id="departmentDeleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Xác nhận xóa phòng ban</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Bạn có chắc là muốn xóa <b>phòng ban A</b> chứ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-danger">Xác nhận</button>
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