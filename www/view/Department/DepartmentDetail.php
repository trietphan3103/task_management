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
    <title>Thông tin chi tiết phòng ban</title>
</head>

<body>
    <?php
        require("../../view/Common/Header.php");
        require_once("../../api/User/UserGet.api.php");
        require_once("../../api/Department/DepartmentGet.api.php");
        $departmentInfo = _get_list_department($_GET['phong-ban-id']);
        $department = _get_department_detail($_GET['phong-ban-id']);
        $manager = _get_list_user_manager($_GET['phong-ban-id']);
        $countUser = _count_user_department($_GET['phong-ban-id']);
    ?>

    <div class="body-layout">
        <div class="container mb-3">
            <div class="row department-info mt-5">
                <div class="col-12">
                    <h2><?php echo $departmentInfo['ten_phong'] ?></h2>
                    <form id="edit-manager-form" class="mb-2">
                        <div class="edit-manager-wrapper">
                            <div class="manager-name-header">
                                <h5>Trưởng Phòng: </h5>
                            </div>
                            <div class="select-manager">
                                <select class="custom-select" id="user_id" default="<?php echo $manager['user_id'] ?>" disabled>
                                    <option selected value="<?php echo $manager['user_id'] ?>"><?php if($manager['status'] == 1){echo $manager['ho_ten'];} else{echo "Chưa có trưởng phòng";};?></option>
                                    <?php
                                        $userList = _get_list_user_status($_GET['phong-ban-id']);
                                        if ($userList->num_rows > 0) {
                                            while ($userSelect =  $userList->fetch_assoc()) {
                                    ?>
                                        <option class="available-employee" id="user_id" value="<?php echo $userSelect['user_id'] ?>"> <?php if($userSelect['status'] == 0){echo $userSelect['ho_ten'];} else{echo "Không thể cập nhật trưởng phòng";}; ?> </option>
                                    <?php
                                        }
                                    }  
                                    ?>
                                </select>
                            </div>
                            <?php
                                if(isset($_GET["status"])){
                                    if($_GET["status"] == "updated")
                                    echo '<div class="alert alert-info"> Cập nhật trưởng phòng mới thành công </div>';
                                }
                            ?>
                        </div>
                        <div class="edit-manager-action">
                            <button class="btn btn-outline-primary" id="edit-manager-btn">
                                <i class="far fa-edit"></i>
                                Cập nhật
                            </button>
                            <button class="btn btn-success" type="submit" id="save-manager-btn" value="<?php echo $department['user_id'] ?>">Lưu</button>
                        </div>
                        <div class="alert alert-danger" role="alert" id="message-edit-manager"></div>
                    </form>
                    <h5 class="mb-3">Số lượng nhân viên: <?php echo $countUser['count'] ?></h5>
                    <h5>Mô tả:</h5>
                    <p><?php echo $departmentInfo['mo_ta'] ?></p>
                </div>
            </div>
            <div class="row department-list-user">
                <div class="col">
                    <div class="row justify-content-between mb-3">
                        <h2 class="user-management-heading pl-2">Nhân viên</h2>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="user-list table table-hover table-striped">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="align-middle">ID </th>
                                <th scope="col" class="align-middle">Họ và tên</th>
                                <th scope="col" class="align-middle">Username</th>
                                <th scope="col" class="align-middle">Chức vụ</th>
                                <th scope="col" class="align-middle">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $userList = _get_list_user_department($_GET['phong-ban-id']);
                                if ($userList->num_rows > 0) {
                                    while($user =  $userList->fetch_assoc()) {
                            ?>
                                <tr class="user-item" id="user_<?php echo $user['user_id'] ?>">
                                    <th scope="row"><?php echo $user['user_id'] ?></th>
                                    <td><?php echo $user['ho_ten'] ?></td>
                                    <td><?php echo $user['user_name'] ?></td>
                                    <td><?php if($user['status'] == 0){echo "Nhân viên";}else{echo "Trưởng phòng";} ?></td>
                                    <td class="user-action">
                                        <a href="/view/User/UserDetail.php?user_id=<?php echo $user['user_id'] ?>">
                                            <button type="button" class="btn btn-primary user-view-detail-btn">Xem Chi Tiết</button>
                                        </a>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                                echo '<tr><td colspan="5" rowspan="2" class="text-center py-5 table-alert"><div><i class="fas fa-exclamation"></i></div>Hiện tại chưa có nhân viên nào</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
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