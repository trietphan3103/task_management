<?php
    session_start();
    error_reporting(0);
    require("../../utils.php");
    require("../../api/Task/TaskGet.api.php");
    _require_login();
    _require_manager();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="/style.css">
    <title>Quản lý công việc</title>
</head>

<body>
    <?php
		require("../Common/Header.php");
	?>
    <div class="body-layout">
        <div class="container-fluid mt-3 create-task-manage-btn">
            <div class="btn_contain" href="#" id="create-new-task__btn">
                <label>Thêm công việc mới</label>
                <i class="fas fa-plus-circle add_btn"></i>
            </div>
        </div>
    </div>
    
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
                                    <div class="cancel-task">
                                        <button class="btn btn-outline-danger" type="button" data-toggle="modal" data-target="#cancelTask" value="<?php echo $row['task_id'] ?>">Hủy</button>
                                    </div>
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
                                        <button class="btn" type="button" data-toggle="modal" data-target="#progress-task-modal" value="<?php echo $row['task_id'] ?>">Xem chi tiết</button>
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
                                            <div class="detail-btn">
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
                                                <button class="btn" type="button" data-toggle="modal" data-target="#completed-task-modal" value="<?php echo $row['task_id'] ?>"  onclick="updateRejectedHistory(<?php echo $row['task_id'] ?>)">Xem chi tiết</button>
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
                        <h5 class="modal-title" id="exampleModalLabel">Task's name</h5>
                        <div>
                            <label class="task-detail-phongban">Phòng ban: Marketing</label>
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
                <form id="new-task-manage-form">
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
                                        <div class="task-detail-remaining">
                                            Còn lại: 
                                            <span class="task-time-remaining">7d 4h 05m</span>
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
                                        <div class="task-member d-inline font-weight-bold"></div>
                                    </div>
                                </div>
            
                                <!-- Task description -->
                                <div class="task-detail-section">
                                    <i class="fas fa-tasks"></i>
                                    <h3>Mô tả công việc</h3>
                                    <div class="task-detail-input">
                                        <textarea rows = "5" cols = "55" placeholder="Thêm mô tả cho task..." class="task-desc-input" id="task-desc-input-update"></textarea>
                                    </div>
                                </div>
            
                                <!-- Task attachments -->
                                <div class="task-detail-section">
                                    <i class="far fa-file-alt"></i>
                                    <h3>File đính kèm</h3>
                                    <a href="#" id="task-detail-file" download></a>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="new_update_task">
                                            <label class="custom-file-label" for="task-detail-file">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                            </div>    
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" value id="update-task-btn">Lưu chỉnh sửa</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Task detail modal - IN PROGRESS -->
    <div class="modal fade" id="progress-task-modal">
        <div class="modal-dialog task-detail-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exampleModalLabel">Task's name</h5>
                        <div>
                            <label class="task-detail-phongban">Phòng ban: Marketing</label>
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
                                <h3>File mô tả</h3>
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <a href="#" download  id="task-detail-file"></a>
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
                            <label class="task-detail-phongban"></label>
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
                <form id="waiting-task-manage-form">
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
                        <div class="alert alert-danger col-12" id="message_waiting_task"></div>
                        <div class="modal-footer">
                            <div class="btn btn-outline-secondary reject-task-js" type="submit" id="reject-task">Không duyệt</div>
                            <div class="btn btn-success approve-task-js" type="submit" id="approve-task">Duyệt</div>
                        </div>
                    </div>
                </form>
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
                                                <a href="#" id="asignee-submit-task-file" download></a>
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
                                                <a href="#" id="history-submit-task-file" download></a>
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

    <!-- Create task modal -->
    <div id="create-task-overlay">
        <div class="task-detail-modal" id="create-task-modal">
            <div class="task-detail-wrapper mt-4">
                <!-- Close button -->
                <div class="task-detail-close create-task-close">
                    <i class="fas fa-times"></i>
                </div>

                <!-- Create task form -->
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <!-- Create task header -->
                            <div class="create-task-header">
                                <h4 class="task-detail-title">Thêm công việc mới</h4>
                            </div>

                            <!-- Create task form -->
                            <form id="create-task-form" class="create-task-form" action="#">
                                <div class="form-group mt-4">
                                    <i class="far fa-user"></i>
                                    <label for="select-nhanvien" class="create-task-label">Nhân viên thực hiện</label>
                                    <div class="select-nhanvien">
                                        <select class="custom-select" aria-placeholder="Chọn nhân viên" id="select-nhanvien">
                                            <option selected value="0">Chọn nhân viên</option>
                                            <?php
                                                $list_employee = _get_list_employee();

                                                if ($list_employee->num_rows > 0) {
                                                    while ($row =  $list_employee->fetch_assoc()) {
                                            ?>
                                                        <option value="<?php echo $row['user_id'] ?>"><?php echo $row['ho_ten'] ?></option>
                                            <?php
                                                    }
                                                };
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Create task's name -->
                                <div class="form-group mt-4">
                                    <div class="form-group-mb">
                                        <i class="far fa-edit"></i></i>
                                        <label for="create-task-desc" class="create-task-label">Tiêu đề</label>
                                    </div>
                                    <div class="task-detail-input">
                                        <textarea rows = "2" placeholder="Nhập tiêu đề task..." name="task-create-name" class="create-task-name" id="create-task-name"></textarea>
                                    </div>
                                </div>

                                <!-- Input task's description -->
                                <div class="form-group mt-4">
                                    <div class="form-group-mb">
                                        <i class="fas fa-tasks"></i>
                                        <label for="create-task-desc" class="create-task-label">Mô tả công việc</label>
                                    </div>
                                    <div class="task-detail-input">
                                        <textarea rows = "7" placeholder="Thêm mô tả cho task..." name="task-create-description" class="create-task-desc" id="create-task-desc"></textarea>
                                    </div>
                                </div>

                                <!-- Upload task'file -->
                                <div class="form-group mt-4">
                                    <div class="form-group-mb">
                                        <i class="far fa-file-alt"></i>
                                        <label class="create-task-label">File đính kèm</label>
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="task-create-file" id="task-create-file">
                                            <label class="custom-file-label" for="task-detail-file" aria-describedby="inputCreateTaskFile">Choose file</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Set task's deadline -->
                                <div class="form-group mt-4">
                                    <i class="far fa-clock"></i>
                                    <label for="task-deadline" class="create-task-label">Hạn nộp</label>
                                    <div class="deadline-picker">
                                        <input placeholder="Chọn ngày" type="datetime-local" class="form-control task-deadline-input" id="task-deadline">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="alert alert-danger col-12" id="message_create_task"></div>
                                    <!-- Create task footer -->
                                    <footer class="task-detail-footer">
                                        <button class="btn btn-outline-secondary close-create-btn">Đóng</button>
                                        <button class="btn btn-success" type="submit">Tạo</button>
                                    </footer>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thông báo xác nhận hủy task -->
    <div class="modal fade modal" id="cancelTask" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelTaskLabel">Xác nhận</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Bạn chắc chắn muốn hủy Task?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary cancel-modal-close__btn" data-dismiss="modal">Không</button>
                    <button type="button" class="btn btn-danger cancel-modal-accept__btn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm modal when task has been rejected -->
    <div id="task-rejected-overlay">
        <div id="task-rejected-modal" class="task-detail-modal">
            <div class="task-detail-wrapper mt-4">
                <!-- Close button -->
                <div class="task-rejected-close">
                    <i class="fas fa-times"></i>
                </div>

                <!-- Confirm task form -->
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <!-- History task wrapper -->
                            <div class="create-task-header">
                                <h4 class="task-detail-title">Từ chối</h4>
                            </div>
                            <form id="task-rejected-form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-danger" id="message_confirm_reject_task"></div>
                                        <!-- Upload task'file -->
                                        <div class="form-group mt-4">
                                            <div class="form-group-mb">
                                                <i class="far fa-file-alt"></i>
                                                <label class="create-task-label">File đính kèm</label>
                                            </div>
                                            <div class="input-group mb-3">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="task-reject-file" id="task-reject-file">
                                                    <label class="custom-file-label" for="task-detail-file">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Feedback của trưởng phòng -->
                                        <div class="task-detail-section">
                                            <i class="far fa-comment"></i>
                                            <h3>Feedback</h3>
                                            <div>
                                                <textarea rows = "10" cols = "55" placeholder="Feedback..." class="task-feedback-input" id="rejected-feedback-input"></textarea>
                                            </div>
                                        </div>

                                        <!-- Set task's deadline -->
                                        <div class="task-detail-section">
                                            <i class="far fa-clock"></i>
                                            <label for="task-deadline" class="create-task-label">Gia hạn</label>
                                            <div class="deadline-picker">
                                                <input placeholder="Chọn ngày" type="datetime-local" class="form-control task-deadline-input" id="task-deadline-extend">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <footer class="task-detail-footer-mb">
                                    <button class="btn btn-success" type="submit">Xác nhận</button>
                                </footer>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm modal when task has been completed -->
    <div id="task-success-overlay">
        <div id="task-success-modal" class="task-detail-modal">
            <div class="task-detail-wrapper mt-4">
                <!-- Close button -->
                <div class="task-success-close">
                    <i class="fas fa-times"></i>
                </div>

                <!-- Confirm task form -->
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <!-- History task wrapper -->
                            <div class="create-task-header">
                                <h4 class="task-detail-title">Đánh giá công việc</h4>
                            </div>
                            <form id="task-completed-form">
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Đánh giá của trưởng phòng -->
                                        <div class="task-detail-section">
                                            <i class="fas fa-star"></i>
                                            <h3>Đánh giá</h3>
                                            <div class="task-detail-rate">
                                                <select class="custom-select" id="task-detail-rank">
                                                    <option value="1">Bad</option>
                                                    <option value="2">OK</option>
                                                    <option selected value="3">Good</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Footer -->
                                <footer class="task-detail-footer-mb">
                                    <button class="btn btn-success" type="submit" id="approve-confirm-btn">Xác nhận</button>
                                </footer>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.16/moment-timezone-with-data.min.js"></script>
    <script src="/main.js"></script>
</body>

</html>