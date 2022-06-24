<?php
    session_start();
    unset($_SESSION['username']);
    header( 'Location: /view/utilsView/Login.php' );
?>