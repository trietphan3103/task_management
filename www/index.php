<?php
    session_start();
    error_reporting(0);
    require("./utils.php");
    _require_login();
    require_once("./api/Absence/AbsenceGet.api.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="/style.css"> <!-- Sử dụng link tuyệt đối tính từ root, vì vậy có dấu / đầu tiên -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@1,700&display=swap" rel="stylesheet">
	<title>Trang chủ</title>
</head>

<body>
	<?php
		require("view/Common/Header.php");
        require_once("./api/User/UserGet.api.php");
        $userInfo = _get_user_information(_get_current_user_id());
        $day_used = _get_day_off_used(_get_current_user_id());
	?>
    <?php 
        if(!_check_giam_doc()){
    ?>
        <div id="absence-index" class="row absence-box-index">
            <div class="absence-days flex-column col-12 col-md-8 col-lg-4">
                <div class="d-flex flex-column align-items-center text-center p-3">
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
            <div class="flex-column col-12 col-md-12 col-lg-8" id="upcoming-absence-tbl">
                <div class="row rounded bg-white absence-index-wrapper">
                    <h3 class="title">Ngày nghỉ sắp tới</h3>
                    <div class="table-wrapper">
                        <table class="absence-list table table-hover table-striped">
                            <thead class="table-header">
                                <tr>
                                    <th scope="col">Ngày bắt đầu</th>
                                    <th scope="col">Ngày kết thúc</th>
                                    <th scope="col">Lý do</th>
                                    <th scope="col" class="text-center">Số ngày nghỉ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $absences = _get_list_proccessed_absence_future_current_user();
                                    if ($absences->num_rows > 0) {
                                        while ($absence =  $absences->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><?php echo $absence['ngay_bat_dau'] ?></td>
                                        <td><?php echo $absence['ngay_ket_thuc'] ?></td>
                                        <td><?php echo $absence['ly_do'] ?></td>
                                        <td class="text-center"><?php echo $absence['so_ngay_nghi'] ?></td>
                                    </tr>
                                <?php 
                                        }
                                    }else{
                                        echo '<tr><td colspan="5" rowspan="2" class="text-center py-5 table-alert"><div><i class="fas fa-exclamation"></i></div>Bạn không có đơn nghỉ phép nào được duyệt trong tương lai</td></tr>';                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>

    <div id="absence-index-list" class="row">
            <div class="flex-column col-12">
                <div class="row rounded bg-white absence-index-wrapper">
                    <div class="col-12 p-0">
                        <h3 class="title">Thành viên vắng hôm nay</h3>
                    </div>
                    <?php 
                        $absences = _get_list_today_absence();
                        if ($absences->num_rows > 0) {
                            while ($absence =  $absences->fetch_assoc()) {
                    ?>
                        <div class="absence-index-card rounded col-md-3 col-sm-6">
                        <div class="d-flex flex-column align-items-center p-3">
                            <img class="rounded-circle" src="<?php
                                                            if (!empty($absence['anh_dai_dien'])) {
                                                                echo ($absence['anh_dai_dien']);
                                                            } 
                                                            else {
                                                                echo '/images/user_avt/default_avt.jpg';
                                                            }
                                                        ?>" 
                            width="90">
                        </div>
                        <div class="absence-card-label">
                            <div>
                                <strong>Tên: </strong>
                                <span class="absence-card-name"><?php echo $absence['ho_ten'] ?></span>
                            </div>
                            <div>
                                <strong>Phòng: </strong>
                                <span><?php echo $absence['ten_phong'] ?></span>
                            </div>
                            <div>
                                <strong>Ngày quay lại: </strong>
                                <span><?php echo $absence['ngay_quay_lai'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php 
                            }
                        }else{
                            echo '
                                <div class="col-12 align-items-center text-center none-absence">
                                    <img src="/images/absence-list.png" alt="no-one-absence" width="90">
                                    <div>Hurray! Không nhân viên nào vắng mặt hôm nay!</div>
                                </div>';                                    
                        }
                    ?>
                </div>
            </div>
        </div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="/main.js"></script> <!-- Sử dụng link tuyệt đối tính từ root, vì vậy có dấu / đầu tiên -->
</body>

</html>