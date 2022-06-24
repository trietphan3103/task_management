<?php
    session_start();
    error_reporting(0);
    require("../../utils.php");
    require("../../api/Task/TaskGet.api.php");
    _require_login();

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
    <title>INDEX</title>
</head>

<body>
    <?php
        require("../../view/Common/Header.php");
    ?>

    <div class="body-layout">
        <div class="container-fluid task-manager-container">
            <!-- New task column -->
            <div class="content-column new-column">
                <div class="content">
                    <div class="content-head-ng">
                        <div class="content content-head"> New</div>
                    </div>
                    <?php
                    $result = _getListTaskByStatus(1);

                    if ($result->num_rows > 0) {
                        while ($row =  $result->fetch_assoc()) {
                    ?>
                        <div class="content-card new-task">
                            <div class="content-card-body">                            
                                <div class="col-12">
                                    <div class="task-id"><a href="#"><?php echo $row['ten_task'] ?></a></div>
                                    <div class="department">
                                        <?php echo $row['ten_phong'] ?>
                                    </div>
                                    <div class="descrip"><?php echo $row['task_mo_ta'] ?></div>        
                                </div>
                            </div>
                            <div class="content-card-footer">
                                <div class="footer-card-section">
                                    <div class="detail-btn">
                                        <button class="btn" type="button" data-toggle="modal" data-target="#new-task-modal" value="<?php echo $row['task_id'] ?>">Xem chi tiết</button>
                                    </div>
                                    <?php 
                                        if(_get_current_user_id() == $row['nguoi_thuc_hien_id']){
                                    ?>
                                        <div class="start-task">
                                            <button class="btn" type="button" value="<?php echo $row['task_id'] ?>">Bắt đầu</button>
                                        </div>
                                    <?php
                                        }         
                                    ?>
                                    
                                </div>
                                <div class="footer-card-section footer-card-line">
                                    <div class="time-left">
                                        <i>
                                            <?php 
                                                if ((time() + 60*60*7)> strtotime($row['thoi_gian_deadline'])) {
                                                    echo "Quá hạn";
                                                }
                                                else {
                                                    $timeleft = $row['days']."d ".$row['hours']."h ".$row['minutes']."m";
                                                    echo $timeleft . '<span class="font-weight-normal text-black-50"> left</span>';
                                                }
                                            ?>
                                        </i>
                                    </div>
                                    <a href="#" class="img-task-assignee">
                                        <img class="rounded-circle" src="<?php
                                            if (!empty($row['anh_dai_dien'])) {
                                                echo ($row['anh_dai_dien']);
                                            } 
                                            else {
                                                echo '/images/user_avt/default_avt.jpg';
                                            }
                                        ?>" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    } else { ?>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <!-- In Progress task column -->
            <div class="content-column in-progress-column">
                <div class="content">
                    <div class="content-head-ng">
                        <div class="content content-head">In Progress</div>
                    </div>
                    <?php
                        $result = _getListTaskByStatus(2);

                        if ($result->num_rows > 0) {
                            while ($row =  $result->fetch_assoc()) {
                    ?>
                            <div class="content-card ">
                                <div class="content-card-body">
                                    <div class="col-12">
                                        <div class="task-id"><a href="#"><?php echo $row['ten_task'] ?></a></div>
                                        <div class="department">
                                            <?php echo $row['ten_phong'] ?>
                                        </div>
                                        <div class="descrip"><?php echo $row['task_mo_ta'] ?></div>    
                                    </div>
                                </div>
                                <div class="content-card-footer">
                                    <div class="footer-card-section">
                                        <div class="detail-btn inprogress-task-manage-js">
                                            <button class="btn" type="button" data-toggle="modal" data-target="#progress-task-modal" value="<?php echo $row['task_id'] ?>" onclick="updateRejectedHistory(<?php echo $row['task_id'] ?>)">Xem chi tiết</button>
                                        </div>
                                        <?php 
                                            if(_get_current_user_id() == $row['nguoi_thuc_hien_id']){
                                        ?>
                                            <div class="submit-task">
                                                <button class="btn" type="button" data-toggle="modal" data-target="#submit-task-main-modal" value="<?php echo $row['task_id'] ?>">Nộp</button>
                                            </div>
                                        <?php
                                            }         
                                        ?>
                                    </div>
                                    <div class="footer-card-section footer-card-line">
                                        <div class="time-left"><i>
                                            <?php 
                                                if ((time() + 60*60*7)> strtotime($row['thoi_gian_deadline'])) {
                                                    echo "Quá hạn";
                                                }
                                                else {
                                                    $timeleft = $row['days']."d ".$row['hours']."h ".$row['minutes']."m";
                                                    echo $timeleft . '<span class="font-weight-normal text-black-50"> left</span>';
                                                }
                                            ?></i>
                                        </div>
                                        <a href="#" class="img-task-assignee">
                                            <img class="rounded-circle" src="<?php
                                                if (!empty($row['anh_dai_dien'])) {
                                                    echo ($row['anh_dai_dien']);
                                                } 
                                                else {
                                                    echo '/images/user_avt/default_avt.jpg';
                                                }
                                            ?>" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                    <?php
                            }
                        } else { ?>
                    <?php
                        } 
                    ?>
                </div>
            </div>

            <!-- Canceled task column -->
            <div class="content-column canceled-column">
                <div class="content">
                    <div class="content-head-ng">
                        <div class="content content-head">Canceled</div>
                    </div>
                    <?php
                        $result = _getListTaskByStatus(3);

                        if ($result->num_rows > 0) {
                            while ($row =  $result->fetch_assoc()) {
                    ?>
                                <div class="content-card">
                                    <div class="content-card-body">
                                        <div class="col-12">
                                            <div class="task-id"><a href="#"><?php echo $row['ten_task'] ?></a></div>
                                            <div class="department">
                                                <?php echo $row['ten_phong'] ?>
                                            </div>
                                            <div class="descrip"><?php echo $row['task_mo_ta'] ?></div>
                                        </div>
                                    </div>
                                    <div class="content-card-footer">
                                        <div class="footer-card-section">
                                            <div class="detail-btn">
                                                <button class="btn" type="button" data-toggle="modal" data-target="#canceled-task-modal" value="<?php echo $row['task_id'] ?>">Xem chi tiết</button>
                                            </div>
                                            <a href="#" class="img-task-assignee">
                                                <img class="rounded-circle" src="<?php
                                                    if (!empty($row['anh_dai_dien'])) {
                                                        echo ($row['anh_dai_dien']);
                                                    } 
                                                    else {
                                                        echo '/images/user_avt/default_avt.jpg';
                                                    }
                                                ?>" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                    <?php
                            }
                        } else { ?>
                    <?php
                        } 
                    ?>
                </div>
            </div>

            <!-- Waiting task column -->
            <div class="content-column waiting-column">
                <div class="content">
                    <div class="content-head-ng">
                        <div class="content content-head">Waiting</div>
                    </div>
                    <?php
                        $result = _getListTaskByStatus(4);

                        if ($result->num_rows > 0) {
                            while ($row =  $result->fetch_assoc()) {
                    ?>
                                <div class="content-card">
                                    <div class="content-card-body">
                                        <div class="col-12">
                                            <div class="task-id"><a href="#"><?php echo $row['ten_task'] ?></a></div>
                                            <div class="department">
                                                <?php echo $row['ten_phong'] ?>
                                            </div>
                                            <div class="descrip"><?php echo $row['task_mo_ta'] ?></div>
                                        </div>
                                    </div>
                                    <div class="content-card-footer">
                                        <div class="footer-card-section">
                                            <div class="detail-btn canceled-task-manage-js">
                                                <button class="btn" type="button" data-toggle="modal" data-target="#waiting-task-modal" value="<?php echo $row['task_id'] ?>" onclick="updateRejectedHistory(<?php echo $row['task_id'] ?>)">Xem chi tiết</button>
                                            </div>
                                        </div>
                                        <div class="footer-card-section footer-card-line">
                                            <div class="time-left"><i>
                                                <?php 
                                                    if ((time() + 60*60*7)> strtotime($row['thoi_gian_deadline'])) {
                                                        echo "Quá hạn";
                                                    }
                                                    else {
                                                        $timeleft = $row['days']."d ".$row['hours']."h ".$row['minutes']."m";
                                                        echo $timeleft . '<span class="font-weight-normal text-black-50"> left</span>';
                                                    }
                                                ?></i>
                                            </div>
                                            <a href="#" class="img-task-assignee">
                                                <img class="rounded-circle" src="<?php
                                                    if (!empty($row['anh_dai_dien'])) {
                                                        echo ($row['anh_dai_dien']);
                                                    } 
                                                    else {
                                                        echo '/images/user_avt/default_avt.jpg';
                                                    }
                                                ?>" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                    <?php
                            }
                        } else { ?>
                    <?php
                        } 
                    ?>
                </div>
            </div>

            <!-- Rejected task column -->
            <div class="content-column rejected-column">
                <div class="content">
                    <div class="content-head-ng">
                        <div class="content content-head">Rejected</div>
                    </div>
                    <?php
                        $result = _getListTaskByStatus(5);

                        if ($result->num_rows > 0) {
                            while ($row =  $result->fetch_assoc()) {
                    ?>
                                <div class="content-card">
                                    <div class="content-card-body">
                                        <div class="col-12">
                                            <div class="task-id"><a href="#"><?php echo $row['ten_task'] ?></a></div>
                                            <div class="department">
                                                <?php echo $row['ten_phong'] ?>
                                            </div>
                                            <div class="descrip"><?php echo $row['task_mo_ta'] ?></div>
                                        </div>
                                    </div>
                                    <div class="content-card-footer">
                                        <div class="footer-card-section">
                                            <div class="detail-btn canceled-task-manage-js">
                                                <button class="btn" type="button" data-toggle="modal" data-target="#rejected-task-index-modal" value="<?php echo $row['task_id'] ?>" onclick="updateRejectedHistory(<?php echo $row['task_id'] ?>)">Xem chi tiết</button>
                                            </div>
                                            <?php 
                                                if(_get_current_user_id() == $row['nguoi_thuc_hien_id']){
                                            ?>
                                                <div class="submit-task">
                                                    <button class="btn asign-again-btn" type="button" data-toggle="modal" value="<?php echo $row['task_id'] ?>" id="asign-again-btn">Nhận lại</button>
                                                </div>
                                            <?php
                                                }         
                                            ?>
                                        </div>
                                        <div class="footer-card-section footer-card-line">
                                            <div class="time-left"><i>
                                                <?php 
                                                    if ((time() + 60*60*7)> strtotime($row['thoi_gian_deadline'])) {
                                                        echo "Quá hạn";
                                                    }
                                                    else {
                                                        $timeleft = $row['days']."d ".$row['hours']."h ".$row['minutes']."m";
                                                        echo $timeleft . '<span class="font-weight-normal text-black-50"> left</span>';
                                                    }
                                                ?></i>
                                            </div>
                                            <a href="#" class="img-task-assignee">
                                                <img class="rounded-circle" src="<?php
                                                    if (!empty($row['anh_dai_dien'])) {
                                                        echo ($row['anh_dai_dien']);
                                                    } 
                                                    else {
                                                        echo '/images/user_avt/default_avt.jpg';
                                                    }
                                                ?>" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                    <?php
                            }
                        } else { ?>
                    <?php
                        } 
                    ?>
                </div>
            </div>

            <!-- Completed task column -->
            <div class="content-column completed-column">
                <div class="content">
                    <div class="content-head-ng">
                        <div class="content content-head">Completed</div>
                    </div>
                    <?php
                        $result = _getListTaskByStatus(6);

                        if ($result->num_rows > 0) {
                            while ($row =  $result->fetch_assoc()) {
                    ?>
                                <div class="content-card">
                                    <div class="content-card-body">
                                        <div class="col-12">
                                            <div class="task-id"><a href="#"><?php echo $row['ten_task'] ?></a></div>
                                            <div class="department">
                                                <?php echo $row['ten_phong'] ?>
                                            </div>
                                            <div class="descrip"><?php echo $row['task_mo_ta'] ?></div>
                                        </div>
                                    </div>
                                    <div class="content-card-footer">
                                        <div class="footer-card-section">
                                            <div class="detail-btn canceled-task-manage-js">
                                                <button class="btn" type="button" data-toggle="modal" data-target="#completed-task-modal" value="<?php echo $row['task_id'] ?>" onclick="updateRejectedHistory(<?php echo $row['task_id'] ?>)">Xem chi tiết</button>
                                            </div>
                                            <a href="#" class="img-task-assignee">
                                                <img class="rounded-circle" src="<?php
                                                    if (!empty($row['anh_dai_dien'])) {
                                                        echo ($row['anh_dai_dien']);
                                                    } 
                                                    else {
                                                        echo '/images/user_avt/default_avt.jpg';
                                                    }
                                                ?>" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                    <?php
                            }
                        } else { ?>
                    <?php
                        } 
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Task detail modal - NEW -->
    <div class="modal fade" id="new-task-modal">
        <div class="modal-dialog task-detail-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <div>
                            <label class="task-detail-phongban"></label>
                        </div>
                        <div>
                            <div class="status-dot status-dot-new"></div>
                            <div class="status-content status-content-new">New</div>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row task-detail-container">
                        <!-- Task sidebar -->
                        <div class="task-right">
                            <div class="task-detail-sidebar col-xs-12 mt-3">
                                <div class="task-detail-estimate">
                                    <div>
                                        <i class="far fa-clock"></i>
                                        <h3>Hạn nộp</h3>
                                    </div>
                                    <div class="task-detail-date"></div>
                                    <div class="task-detail-time"></div>
                                    <div class="task-detail-remaining">
                                        Còn lại: 
                                        <span class="task-time-remaining"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Task detail container -->
                        <div class="task-detail-content col-xs-12">
                            <!-- Nguoi thuc hien task -->
                            <div>
                                <div class="task-detail-section">
                                    <i class="far fa-user"></i>
                                    <h3>Người thực hiện: </h3>
                                    <div class="task-member d-inline font-weight-bold">
                                    </div>
                                </div>
                            </div>
        
                            <!-- Task description -->
                            <div class="task-detail-section">
                                <i class="fas fa-tasks"></i>
                                <h3>Mô tả công việc</h3>
                                <div class="task-detail-input">
                                    <textarea rows = "5" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="task-desc-input" readonly></textarea>
                                </div>
                            </div>
        
                            <!-- Task attachments -->
                            <div class="task-detail-section">
                                <i class="far fa-file-alt"></i>
                                <h3>File đính kèm</h3>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <a href="#" download id="task-detail-file"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task detail modal - IN PROGRESS -->
    <div class="modal fade" id="progress-task-modal">
        <div class="modal-dialog task-detail-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <div>
                            <label class="task-detail-phongban"></label>
                        </div>
                        <div>
                            <div class="status-dot status-dot-in-progress"></div>
                            <div class="status-content status-content-in-progress">In Progress</div>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row task-detail-container">
                        <!-- Task sidebar -->
                        <div class="task-right">
                            <div class="task-detail-sidebar col-xs-12 mt-3">
                                <div class="task-detail-estimate">
                                    <div>
                                        <i class="far fa-clock"></i>
                                        <h3>Hạn nộp</h3>
                                    </div>
                                    <div class="task-detail-date"></div>
                                    <div class="task-detail-time"></div>
                                    <div class="task-detail-remaining">
                                        Còn lại: 
                                        <span class="task-time-remaining"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="task-detail-sidebar col-xs-12 mt-3">
                                    <div class="task-detail-history">
                                        <div>
                                            <i class="fas fa-history"></i>
                                            <h3>Lịch sử công việc</h3>
                                        </div>
                                        <div class="history-task-bar" id="rejected_modal_history">
                                        </div>
                                    </div>
                                </div>
                        </div>
                    
                        <!-- Task detail container -->
                        <div class="task-detail-content col-xs-12">
                            <!-- Nguoi thuc hien task -->
                            <div>
                                <div class="task-detail-section">
                                    <i class="far fa-user"></i>
                                    <h3>Người thực hiện: </h3>
                                    <div class="task-member d-inline font-weight-bold">
                                    </div>
                                </div>
                            </div>
        
                            <!-- Task description -->
                            <div class="task-detail-section">
                                <i class="fas fa-tasks"></i>
                                <h3>Mô tả công việc</h3>
                                <div class="task-detail-input">
                                    <textarea rows = "5" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="task-desc-input" disabled></textarea>
                                </div>
                            </div>
        
                            <!-- Task attachments -->
                            <div class="task-detail-section">
                                <i class="far fa-file-alt"></i>
                                <h3>File đính kèm</h3>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <a href="#" download id="task-detail-file"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task detail modal - CANCELED -->
    <div class="modal fade" id="canceled-task-modal">
        <div class="modal-dialog task-detail-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exampleModalLabel">Task's name</h5>
                        <div>
                            <label class="task-detail-phongban">Phòng ban: Marketing</label>
                        </div>
                        <div>
                            <div class="status-dot status-dot-canceled"></div>
                            <div class="status-content status-content-canceled">Canceled</div>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row task-detail-container">
                        <!-- Task sidebar -->
                        <div class="task-right">
                            <div class="task-detail-sidebar col-xs-12 mt-3">
                                <div class="task-detail-estimate">
                                    <div>
                                        <i class="far fa-clock"></i>
                                        <h3>Hạn nộp</h3>
                                    </div>
                                    <div class="task-detail-date">14/12/2021</div>
                                    <div class="task-detail-time">15:00</div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Task detail container -->
                        <div class="task-detail-content col-xs-12">
                            <!-- Nguoi thuc hien task -->
                            <div>
                                <div class="task-detail-section">
                                    <i class="far fa-user"></i>
                                    <h3>Người thực hiện: </h3>
                                    <div class="task-member d-inline font-weight-bold">
                                        Nguyễn Văn A
                                    </div>
                                </div>
                            </div>
        
                            <!-- Task description -->
                            <div class="task-detail-section">
                                <i class="fas fa-tasks"></i>
                                <h3>Mô tả công việc</h3>
                                <div class="task-detail-input">
                                    <textarea rows = "5" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="task-desc-input" disabled></textarea>
                                </div>
                            </div>
        
                            <!-- Task attachments -->
                            <div class="task-detail-section">
                                <i class="far fa-file-alt"></i>
                                <h3>File đính kèm</h3>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <a href="#" download id="task-detail-file"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task detail modal - WAITING -->
    <div class="modal fade" id="waiting-task-modal">
        <div class="modal-dialog task-detail-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <div>
                            <label class="task-detail-phongban">Phòng ban: Marketing</label>
                        </div>
                        <div>
                            <div class="status-dot status-dot-waiting"></div>
                            <div class="status-content status-content-waiting">Waiting</div>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row task-detail-container">
                        <!-- Task sidebar -->
                            <div class="task-right">
                                <div class="task-detail-sidebar col-xs-12 mt-3">
                                    <div class="task-detail-estimate">
                                        <div class="mb-3">
                                            <i class="fas fa-calendar"></i>
                                            <h3>Thời gian nộp</h3>
                                            <div class="time-submit-manage"></div>
                                        </div>
                                        <div>
                                            <i class="far fa-clock"></i>
                                            <h3>Hạn nộp</h3>
                                        </div>
                                        <div class="task-detail-date"></div>
                                        <div class="task-detail-time"></div>
                                        <div class="task-detail-remaining">
                                            Còn lại: 
                                            <span class="task-time-remaining"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="task-detail-sidebar col-xs-12 mt-3">
                                    <div class="task-detail-history">
                                        <div>
                                            <i class="fas fa-history"></i>
                                            <h3>Lịch sử công việc</h3>
                                        </div>
                                        <div class="history-task-bar">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- Task detail container -->
                        <div class="task-detail-content col-xs-12">
                            <!-- Nguoi thuc hien task -->
                            <div>
                                <div class="task-detail-section">
                                    <i class="far fa-user"></i>
                                    <h3>Người thực hiện: </h3>
                                    <div class="task-member d-inline font-weight-bold">
                                    </div>
                                </div>
                            </div>
        
                            <!-- Task description -->
                            <div class="task-detail-section">
                                <i class="fas fa-tasks"></i>
                                <h3>Mô tả công việc</h3>
                                <div class="task-detail-input">
                                    <textarea rows = "5" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="waiting-desc-manage-input" disabled></textarea>
                                </div>
                            </div>
        
                            <!-- Task attachments -->
                            <div class="task-detail-section task-manage-line">
                                <i class="far fa-file-alt"></i>
                                <h3>File đính kèm</h3>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <a href="#" id="waiting-task-detail-file" download></a>
                                    </div>
                                </div>
                            </div>

                            <!-- Mô tả được submit cho trưởng phòng -->
                            <div class="task-detail-section">
                                <i class="fas fa-tasks"></i>
                                <h3>Mô tả báo cáo của nhân viên</h3>
                                <div class="task-detail-input">
                                    <textarea rows = "3" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="waiting-submit-task-input" disabled></textarea>
                                </div>
                            </div>

                            <!-- File được submit cho trưởng phòng -->
                            <div class="task-detail-section">
                                <i class="far fa-file-alt"></i>
                                <h3>File nộp của nhân viên</h3>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <a href="#" id="waiting-submit-task-file" download></a>
                                    </div>
                                </div>
                            </div>        
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task detail modal - REJECTED -->
    <div class="modal fade" id="rejected-task-index-modal">
        <div class="modal-dialog task-detail-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <div>
                            <label class="task-detail-phongban"></label>
                        </div>
                        <div>
                            <div class="status-dot status-dot-rejected"></div>
                            <div class="status-content status-content-rejected">Rejected</div>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="new-task-detail-form">
                    <div class="modal-body">
                        <div class="row task-detail-container">
                            <!-- Task sidebar -->
                            <div class="task-right">
                                <div class="task-detail-sidebar col-xs-12 mt-3">
                                    <div class="task-detail-estimate">
                                        <div class="mb-3">
                                            <i class="fas fa-calendar"></i>
                                            <h3>Thời gian nộp</h3>
                                            <div class="rejected-time-submit-manage"></div>
                                        </div>
                                        <div>
                                            <i class="far fa-clock"></i>
                                            <h3>Hạn nộp</h3>
                                        </div>
                                        <div class="task-detail-date"></div>
                                        <div class="task-detail-time"></div>
                                        <div class="task-detail-remaining">
                                            Còn lại: 
                                            <span class="task-time-remaining"></span>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="task-detail-sidebar col-xs-12 mt-3">
                                    <div class="task-detail-history">
                                        <div>
                                            <i class="fas fa-history"></i>
                                            <h3>Lịch sử công việc</h3>
                                        </div>
                                        <div class="history-task-bar">
                                            <div class="history-section">
                                                <h3>13:00, 15/12/2021</h3>
                                                <div class="history-desc">Message</div>
                                            </div>
                                            <div class="history-section">
                                                <h3>12:00, 14/12/2021</h3>
                                                <div class="history-desc">Message</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Task detail container -->
                            <div class="task-detail-content col-xs-12">
                                <!-- Nguoi thuc hien task -->
                                <div>
                                    <div class="task-detail-section">
                                        <i class="far fa-user"></i>
                                        <h3>Người thực hiện: </h3>
                                        <div class="task-member d-inline font-weight-bold">
                                        </div>
                                    </div>
                                </div>
            
                                <!-- Task description -->
                                <div class="task-detail-section">
                                    <i class="fas fa-tasks"></i>
                                    <h3>Mô tả công việc</h3>
                                    <div class="task-detail-input">
                                        <textarea rows = "5" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="rejected-desc-manage-input" disabled></textarea>
                                    </div>
                                </div>
            
                                <!-- Task attachments -->
                                <div class="task-detail-section task-manage-line">
                                    <i class="far fa-file-alt"></i>
                                    <h3>File đính kèm</h3>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <a href="#" download id="rejected-task-detail-file"></a>                                            
                                        </div>
                                    </div>
                                </div>
    
                                <!-- Mô tả được submit cho trưởng phòng -->
                                <div class="task-detail-section">
                                    <i class="fas fa-tasks"></i>
                                    <h3>Mô tả báo cáo của nhân viên</h3>
                                    <div class="task-detail-input">
                                        <textarea rows = "3" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="rejected-submit-task-input" disabled></textarea>
                                    </div>
                                </div>
    
                                <!-- File được submit cho trưởng phòng -->
                                <div class="task-detail-section">
                                    <i class="far fa-file-alt"></i>
                                    <h3>File nộp của nhân viên</h3>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <a href="#" download id="rejected-submit-task-file"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
 
    <!-- Task detail modal - COMPLETED -->
    <div class="modal fade" id="completed-task-modal">
        <div class="modal-dialog task-detail-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exampleModalLabel">Task's name</h5>
                        <div>
                            <label class="task-detail-phongban">Phòng ban: Marketing</label>
                        </div>
                        <div>
                            <div class="status-dot status-dot-completed"></div>
                            <div class="status-content status-content-completed">Completed</div>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="new-task-detail-form">
                    <div class="modal-body">
                        <div class="row task-detail-container">
                            <!-- Task sidebar -->
                            <div class="task-right">
                                <div class="task-detail-sidebar col-xs-12 mt-3">
                                    <div class="task-detail-estimate">
                                        <div class="mb-3">
                                            <i class="fas fa-calendar"></i>
                                            <h3>Thời gian nộp</h3>
                                            <div class="completed-time-submit-manage"></div>
                                        </div>
                                        <div>
                                            <i class="far fa-clock"></i>
                                            <h3>Hạn nộp</h3>
                                        </div>
                                        <div class="task-detail-date">14/12/2021</div>
                                        <div class="task-detail-time">15:00</div>
                                    </div>
                                </div>
        
                                <div class="task-detail-sidebar col-xs-12 mt-3">
                                    <div class="task-detail-history">
                                        <div>
                                            <i class="fas fa-history"></i>
                                            <h3>Lịch sử công việc</h3>
                                        </div>
                                        <div class="history-task-bar">
                                            <div class="history-section">
                                                <h3>13:00, 15/12/2021</h3>
                                                <div class="history-desc">Message</div>
                                            </div>
                                            <div class="history-section">
                                                <h3>12:00, 14/12/2021</h3>
                                                <div class="history-desc">Message</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Task detail container -->
                            <div class="task-detail-content col-xs-12">
                                <!-- Nguoi thuc hien task -->
                                <div class="">
                                    <div class="task-detail-section">
                                        <i class="far fa-user"></i>
                                        <h3>Người thực hiện: </h3>
                                        <div class="task-member d-inline font-weight-bold">
                                            Nguyễn Văn A
                                        </div>
                                    </div>
                                </div>
            
                                <!-- Task description -->
                                <div class="task-detail-section">
                                    <i class="fas fa-tasks"></i>
                                    <h3>Mô tả công việc</h3>
                                    <div class="task-detail-input">
                                        <textarea rows = "5" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="completed-desc-manage-input" disabled></textarea>
                                    </div>
                                </div>
            
                                <!-- Task attachments -->
                                <div class="task-detail-section task-manage-line">
                                    <i class="far fa-file-alt"></i>
                                    <h3>File đính kèm</h3>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <a href="#" download id="completed-task-detail-file"></a>
                                        </div>
                                    </div>
                                </div>
    
                                <!-- Mô tả được submit cho trưởng phòng -->
                                <div class="task-detail-section">
                                    <i class="fas fa-tasks"></i>
                                    <h3>Mô tả báo cáo của nhân viên</h3>
                                    <div class="task-detail-input">
                                        <textarea rows = "3" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="completed-submit-task-input" disabled></textarea>
                                    </div>
                                </div>
    
                                <!-- File được submit cho trưởng phòng -->
                                <div class="task-detail-section task-manage-line">
                                    <i class="far fa-file-alt"></i>
                                    <h3>File nộp của nhân viên</h3>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <a href="#" download id="completed-submit-task-file"></a>
                                        </div>
                                    </div>
                                </div>
            
                                <!-- Đánh giá của trưởng phòng -->
                                <div class="task-detail-section">
                                    <i class="fas fa-star"></i>
                                    <h3>Đánh giá</h3>
                                    <div id="completed-ranking"></div>
                                </div>                      
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- History task modal -->
    <div id="task-history-overlay">
        <div id="task-history-modal" class="task-detail-modal">
            <div class="task-detail-wrapper mt-4">
                <!-- Close button -->
                <div class="history-task-close">
                    <i class="fas fa-times"></i>
                </div>

                <!-- History task detail -->
                <div class="container">
                    <div class="row">
                        <div class="col-12 history-task-container">
                            <!-- History task wrapper -->
                            <div class="create-task-header">
                                <h4 class="task-detail-title">Lịch sử task</h4>
                            </div>
                            <div class="row history-task-wrapper">
                                <div class="col-12">
                                    <div class="task-detail-section">
                                        <h3>Mô tả báo cáo của nhân viên</h3>
                                        <div>
                                            <textarea rows = "3" placeholder="Mô tả task" class="task-feedback-input" id="history-desc-text" disabled></textarea>
                                        </div>
                                    </div>
                                    <div class="task-detail-section">
                                        <h3>File nộp của nhân viên</h3>
                                        <div class="input-group history-file">
                                            <div class="custom-file">
                                                <a href="#" download id="asignee-submit-task-file"></a>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="task-detail-section reject-part-section">
                                        <h3>Feedback của trưởng phòng</h3>
                                        <div>
                                            <textarea rows = "2" placeholder="Feedback..." class="task-feedback-input" id="history-feedback-text" disabled></textarea>
                                        </div>
                                    </div>
                                    <div class="task-detail-section reject-part-section">
                                        <h3>File đính kèm</h3>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <a href="#" download id="history-submit-task-file"></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <footer class="task-detail-footer">
                            <button class="btn btn-outline-secondary close-history-btn" type="submit">Đóng</button>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit task modal -->
    <div class="modal fade" id="submit-task-main-modal">
        <div class="modal-dialog task-detail-modal" id="submit-task-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Nộp báo cáo</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="submit-task-main-form">
                    <div class="modal-body">
                        <div class="row submit-task-container">
                            <!-- Submit task wrapper -->
                            <div class="col-12">
                                <div class="task-detail-section">
                                    <h3>Mô tả báo cáo được nộp</h3>
                                    <div>
                                        <textarea rows = "5" placeholder="Mô tả task" class="task-feedback-input" id="task-submit-desc"></textarea>
                                    </div>
                                </div>
                                <div class="task-detail-section">
                                    <h3>File đính kèm</h3>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="task-submit-file" id="task-submit-file">
                                            <label class="custom-file-label" for="history-submit-file">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="modal-footer">
                        <div class="alert alert-danger col-12" id="message_submit_task"></div>
                        <button type="submit" class="btn btn-success">Nộp</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.16/moment-timezone-with-data.min.js"></script>
    <script src="/main.js"></script>
</body>

</html>