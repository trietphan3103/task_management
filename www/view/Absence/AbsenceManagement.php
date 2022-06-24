<?php
    session_start();
    error_reporting(0);
    require("../../utils.php");
    _require_login();
    if (_check_normal_employee()){
        header('Location: /view/utilsView/Error403.html');
        exit;
    }

    require_once("../../api/User/UserGet.api.php");
    require_once("../../api/Absence/AbsenceGet.api.php");
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
    <title>Quản lý nghỉ phép</title>
</head>

<body>
    <?php
        require("../../view/Common/Header.php");
    ?>
    <div class="body-layout">
        <div class="container mb-5">
            <div class="row absence-request">
                <h2 class="absence-request-heading mb-3">Yêu cầu xin nghỉ phép</h2>
                <div class="table-wrapper table-no-scroll">
                    <table class="absence-list table table-hover table-striped">
                        <?php
                            if(isset($_GET["status"])){
                                if($_GET["status"] == "approved")
                                    echo '<div class="alert alert-info"> Duyệt đơn nghỉ phép thành công </div>';
                                if($_GET["status"] == "rejected")
                                    echo '<div class="alert alert-info"> Từ chối đơn nghỉ phép thành công </div>';
                            }
                        ?>
                        <div class="alert alert-info" id="message_absence_process_info"></div>
                        <div class="alert alert-danger" id="message_absence_process_danger"></div>
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="absence-number">Mã đơn</th>
                                <th scope="col" class="absence-creator">Người làm đơn</th>
                                <th scope="col" class="absence-date">Ngày bắt đầu</th>
                                <th scope="col" class="absence-date">Ngày kết thúc</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if(_check_giam_doc()){
                                    $waiting_absences = _get_list_waiting_absence_of_manager();
                                }else{
                                    $current_user_department_id = _get_current_user_department_id();
                                    $waiting_absences = _get_list_waiting_absence_of_deparment($current_user_department_id);
                                }

                                if ($waiting_absences->num_rows > 0) {
                                    while ($absence =  $waiting_absences->fetch_assoc()) {
                            ?>
                                <tr class="absence-item">
                                    <td><b><?php echo $absence['absence_id'] ?></b></td>
                                    <td class="absence-request-creator"><a href="/view/User/UserDetail.php?user_id=<?php echo $absence['nguoi_tao_id'] ?>"><?php echo $absence['ho_ten'] ?></a></td>
                                    <td class="date_format"><?php echo $absence['ngay_bat_dau'] ?></td>
                                    <td class="date_format"><?php echo $absence['ngay_ket_thuc'] ?></td>
                                    <td class="absence-request-action">
                                        <button type="button" class="btn btn-primary absence-view-detail-btn" data-toggle="modal" data-target="#absenceRequestDetailModal" id="view_detail_btn"
                                            asignee="<?php echo $absence['ho_ten'] ?>"
                                            start="<?php echo $absence['ngay_bat_dau'] ?>"
                                            end="<?php echo $absence['ngay_ket_thuc'] ?>"
                                            reason="<?php echo $absence['ly_do'] ?>"
                                            abs_id="<?php echo $absence['absence_id'] ?>"
                                            file="<?php echo $absence['file'] ?>" 
                                        >
                                            Xem chi tiết
                                        </button>
                                    </td>
                                </tr>
                            <?php 
                                    }
                                }else{
                                    echo '<tr><td colspan="5" rowspan="2" class="text-center py-5 table-alert"><div><i class="fas fa-exclamation"></i></div>Hiện tại không có đơn xin nghỉ phép nào cần duyệt</td></tr>';
                                }
                            ?>
                            
                        </tbody>
                    </table>
                </div>
                <!-- absence-request-detail-modal -->
                <div class="absence-request-detail-modal modal fade" id="absenceRequestDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <label class="col-6 col-sm-4 font-weight-bold" for="absenceCreator">Người xin nghỉ: </label>
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
                                    <div class="row absence-detail-footer modal-footer">
                                        <button type="button" class="btn btn-outline-success absence-approve-btn" id="btn_aprrove" abs_id=<?php echo $absence['absence_id'] ?>>Đồng ý</button>
                                        <button type="button" class="btn btn-outline-danger absence-refuse-btn" id="btn_reject" abs_id=<?php echo $absence['absence_id'] ?>>Từ chối</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row absence-history">
                <h2 class="absence-heading mb-3">Lịch sử nghỉ phép</h2>
                <div class="table-wrapper table-no-scroll">
                    <table class="absence-list table table-hover table-striped">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="absence-number">Mã đơn</th>
                                <th scope="col" class="absence-creator">Người làm đơn</th>
                                <th scope="col" class="absence-date">Ngày bắt đầu</th>
                                <th scope="col" class="absence-date">Ngày kết thúc</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                                if(_check_giam_doc()){
                                    $proccessed_absences = _get_list_proccessed_absence_of_manager();
                                }else{
                                    $current_user_department_id = _get_current_user_department_id();
                                    $proccessed_absences = _get_list_proccessed_absence_of_deparment($current_user_department_id);
                                }

                                if ($proccessed_absences->num_rows > 0) {
                                    while ($absence =  $proccessed_absences->fetch_assoc()) {
                            ?>
                                <tr class="absence-item">
                                    <td><b><?php echo $absence['absence_id'] ?></b></td>
                                    <td class="absence-request-creator"><a href="/view/User/UserDetail.php?user_id=<?php echo $absence['nguoi_tao_id'] ?>"><?php echo $absence['ho_ten'] ?></a></td>
                                    <td class="date_format"><?php echo $absence['ngay_bat_dau'] ?></td>
                                    <td class="date_format"><?php echo $absence['ngay_ket_thuc'] ?></td>
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
                                </tr>
                            <?php 
                                    }
                                }else{
                                    echo '<tr><td colspan=5 class="text-center table-alert py-5"><div><i class="fas fa-exclamation"></i></div>Chưa có đơn nào được duyệt </td></tr>';
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
                                        <label class="col-6 col-sm-4 font-weight-bold" for="absenceCreator">Người xin nghỉ: </label>
                                        <div class="absenceCreator text-success font-weight-bold col-6 col-sm-8" id="absenceCreator">Nguyễn Văn A
                                        </div>
                                        <label class="col-6 col-sm-4 font-weight-bold" for="absence-start-day">Ngày bắt đầu: </label>
                                        <div class="absence-start-day col-6 col-sm-8" id="absence-start-day">2/12/2021
                                        </div>
                                        <label class="col-6 col-sm-4 font-weight-bold" for="absence-end-day">Ngày kết thúc: </label>
                                        <div class="absence-end-day col-6 col-sm-8" id="absence-end-day">5/12/2021
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-12 font-weight-bold" for="absence-reason">Lý do: </label>
                                        <p class="absence-reason col-12" id="absence-reason">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Eveniet velit, hic inventore quae accusamus adipisci? Impedit facere labore dolores sint exercitationem asperiores cum natus, quasi cumque beatae, voluptatem voluptate neque.
                                        </p>
                                    </div>
                                </div>
                                </form>
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