<?php
    session_start();
    error_reporting(0);
    require("../../utils.php");
    _require_login();
    require_once("../../api/User/UserGet.api.php");
    $current_user_id = _get_current_user_id();
    $userInfo = _get_user_information($current_user_id);
    $day_used = _get_day_off_used($userInfo['user_id']);

    if  ($userInfo['user_role'] == 'Giám đốc'){
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
    <title>Thông tin nghỉ phép</title>
</head>

<body>
    <?php
        require("../../view/Common/Header.php");
    ?>
    <div class="body-layout">
        <div class="container mb-5">
            <div class="row">
                <div class="col-md-3 col-sm-12">
                    <div class="row absence-box-index">
                        <div class="col-12 absence-days">
                            <div class="d-flex flex-column align-items-center text-center py-3 px-1">
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
                                        <span>Số ngày nghỉ cho phép: <span class="text-value"><?php echo $userInfo['so_absence_max']; ?> ngày</span></span>
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
                                
                        <!-- Absence create Modal -->
                        <div class="absence-create-modal modal fade" id="absenceCreateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Đơn xin nghỉ phép</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="absence-create-form" id="form_create_absence">
                                            <div class="alert alert-danger" id="message_create_absence"></div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="absence-start-day-input">Ngày bắt đầu</label>
                                                    <input type="date" name="ngay_bat_dau" class="form-control" id="ngay_bat_dau">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="absence-end-day-input">Ngày kết thúc</label>
                                                    <input type="date" name="ngay_ket_thuc" class="form-control" id="ngay_ket_thuc">
                                                </div>
                                            </div>
                                            <!-- Task attachments -->
                                            <div class="task-detail-section">
                                                <i class="far fa-file-alt"></i>
                                                <h3>File đính kèm</h3>
                                                <div class="input-group mb-3">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input absence-file" name="absence-file" id="absence-file">
                                                        <label class="custom-file-label" for="absence-file">Choose file</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="absence-reason-input">Lý do</label>
                                                <textarea rows="4" name="ly_do" type="text" class="form-control" id="ly_do"></textarea>
                                            </div>
                                            <div class="form-footer">
                                                <button type="reset" class="btn btn-outline-primary">Clear</button>
                                                <button type="submit" class="btn btn-success">Nộp đơn</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              
                <div class="col-md-9 col-sm-12 absence-history-wrapper">
                    <div class="row absence-history">
                        <div class="col my-3">                            
                            <div class="row justify-content-between">
                                <h2 class="absence-heading">Lịch sử nghỉ phép</h2>
                                <div class="btn_contain" data-toggle="modal" data-target="#absenceCreateModal">
                                    <label>Tạo đơn xin nghỉ phép</label>
                                    <i class="fas fa-plus-circle add_btn"></i>
                                </div>
                            </div>
                        </div>
                        <div class="table-wrapper">
                            <table class="absence-list table table-hover table-striped text-center">
                            <?php
                                if(isset($_GET["status"])){
                                    if($_GET["status"] == "created")
                                        echo '<div class="alert alert-info"> Tạo đơn xin nghỉ phép thành công </div>';
                                }
                            ?>
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col">Mã đơn</th>
                                        <th scope="col">Ngày bắt đầu</th>
                                        <th scope="col">Ngày kết thúc</th>
                                        <th scope="col">State</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        require_once("../../api/Absence/AbsenceGet.api.php");
                                        $absences = _get_list_absence_current_user();
                                        if ($absences->num_rows > 0) {
                                            while ($absence =  $absences->fetch_assoc()) {
                                    ?>
                                    <tr class="absence-item">
                                        <th scope="row"><?php echo $absence['absence_id'] ?></th>
                                        <td><?php echo $absence['ngay_bat_dau'] ?></td>
                                        <td><?php echo $absence['ngay_ket_thuc'] ?></td>
                                        
                                        <?php
                                            if($absence['status'] == 0){
                                                echo'<td class="absence-state absence-waiting">Chờ duyệt</td>';
                                            }
                                            if($absence['status'] == -1){
                                                echo'<td class="absence-state absence-refused">Bị từ chối</td>';
                                            }
                                            if($absence['status'] == 1){
                                                echo'<td class="absence-state absence-approved">Đã duyệt</td>';
                                            }
                                        ?>
                                        
                                        <td>
                                            <button type="button" class="btn btn-primary absence-view-detail-btn" data-toggle="modal" data-target="#absenceDetailModal"
                                                asignee="<?php echo $absence['ho_ten'] ?>"
                                                start="<?php echo $absence['ngay_bat_dau'] ?>"
                                                end="<?php echo $absence['ngay_ket_thuc'] ?>"
                                                reason="<?php echo $absence['ly_do'] ?>"
                                                file="<?php echo $absence['file'] ?>"
                                                id="absence_view_detail_btn"
                                            >Xem chi tiết</button>
                                        </td>
                                    </tr>
                                    <?php 
                                            }
                                        }else{
                                            echo '<tr><td colspan=5 class="table-alert py-5"><div><i class="fas fa-exclamation"></i></div>Hiện tại bạn không có đơn xin nghỉ phép nào </td></tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Modal -->
                        <div class="absence-detail-modal modal fade" id="absenceDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Đơn xin nghỉ phép</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="absence-detail">
                                            <div class="row">
                                                <label class="col-6 col-sm-4 font-weight-bold" for="absence-creator">Người xin nghỉ: </label>
                                                <div class="absenceCreator text-success font-weight-bold col-6 col-sm-8" id="absence-creator">
                                                </div>
                                                <label class="col-6 col-sm-4 font-weight-bold" for="absence-start-day">Ngày bắt đầu: </label>
                                                <div class="absence-start-day col-6 col-sm-8" id="absence-start-day">
                                                </div>
                                                <label class="col-6 col-sm-4 font-weight-bold" for="absence-end-day">Ngày kết thúc: </label>
                                                <div class="absence-end-day col-6 col-sm-8" id="absence-end-day">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-12 font-weight-bold" for="absence-reason">Lý do: </label>
                                                <p class="absence-reason col-12" id="absence-reason"></p>
                                            </div>
                                            <!-- File attachments -->
                                            <div class="task-detail-section">
                                                <i class="far fa-file-alt"></i>
                                                <h3>File đính kèm</h3>
                                                <div class="input-group mb-3">
                                                    <div class="custom-file">
                                                        <a href="#" id="absence-detail-file" download></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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