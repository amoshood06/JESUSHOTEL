<?php
require_once 'config/database.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/admin-dashboard.php');
    } else {
        redirect('user/user-dashboard.php');
    }
} else {
    redirect('login.php?redirect=account.php');
}
