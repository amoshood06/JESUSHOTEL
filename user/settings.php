<?php
include_once 'user-header.php';
?>

<div class="container">
    <h2>Settings</h2>
    <p>Manage your account settings here.</p>
    <!-- Settings form or options will go here -->
    <form action="" method="POST">
        <div class="form-group">
            <label for="password">Change Password:</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update Settings</button>
    </form>
</div>

<?php
include_once 'user-footer.php';
?>