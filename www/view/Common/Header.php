<?php
    session_start();
    // error_reporting(0);  
    // require_once("../../utils.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="fixed-top header-transparent">
        <div class="container d-flex align-items-center justify-content-between">

            <div class="logo">
                <h1 class="text-light"><a href="/"><span>TTH Company</span></a></h1>
            </div>

            <nav id="navbar" class="navbar">
                <ul>
                    <?php
                        if(!_check_giam_doc() && !_check_manager()){
                            echo  '<li><a class="nav-link scrollto" href="/view/Task/TaskIndex.php">Công việc</a></li>';
                        }
                    ?>
                    <?php
                        if(!_check_giam_doc()){
                            echo  '<li><a class="nav-link scrollto" href="/view/Absence/AbsenceIndex.php">Nghỉ phép</a></li>';
                        }
                    ?>
                    
                    <?php
                        if(_check_user_logged() && (_check_giam_doc() || _check_manager())){
                            echo '<li class="dropdown">
                                    <a href="#">Quản lý
                                        <i class="dropdown-icon fas fa-chevron-down"></i>
                                        <i class="dropdown-icon-mobile fas fa-chevron-right"></i>
                                    </a>
                                    <ul>';
                                        echo '<li><a href="/view/Absence/AbsenceManagement.php">Quản lý nghỉ phép</a></li>';
                                    if(_check_giam_doc()){
                                        echo '<li><a href="/view/User/UserManagement.php">Quản lý nhân viên</a></li>';
                                        echo '<li><a href="/view/Department/DepartmentManagement.php">Quản lý phòng ban</a></li>';
                                    }
                                    if(_check_manager()){
                                        echo '<li><a href="/view/Task/TaskManagement.php">Quản lý công việc</a></li>';
                                    }                                        
                            echo '  </ul>
                                </li>
                                ';
                        } 
                    ?>
                    <?php if(isset($_SESSION["username"])):?>
                        <li class="dropdown">
                            <a class="nav-link scrollto account" href="#"> <i class="fas fa-user-circle"></i> &nbsp &nbsp  <?php echo $_SESSION["username"]?></a>
                            <ul class="account-dropdown">
                                <?php
                                    if(!_check_giam_doc()){
                                        echo  '<li><a href="/view/User/CurrentUserDetail.php">Thông tin người dùng</a></li>';
                                    }
                                ?>
                                <li class="logout-btn"><a href="/view/utilsView/Logout.php">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a class="nav-link scrollto login-button" href="/view/utilsView/Login.php">Đăng nhập</a></li>
                    <?php endif; ?>
                </ul>
                <i class="fas fa-bars mobile-nav-toggle"></i>
            </nav>
            <!-- .navbar -->

        </div>
    </header>
    <!-- End Header -->
</body>

</html>