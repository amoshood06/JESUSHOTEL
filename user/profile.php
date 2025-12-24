<?php
include_once 'user-header.php';
?>

<div class="container">
    <h2>My Profile</h2>
    <?php if ($currentUser): ?>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($currentUser['email']); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($currentUser['role']); ?></p>
        <?php if (!empty($currentUser['phone'])): ?>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($currentUser['phone']); ?></p>
        <?php endif; ?>
        <?php if (!empty($currentUser['address'])): ?>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($currentUser['address']); ?></p>
        <?php endif; ?>
        <?php if (!empty($currentUser['city'])): ?>
            <p><strong>City:</strong> <?php echo htmlspecialchars($currentUser['city']); ?></p>
        <?php endif; ?>
        <?php if (!empty($currentUser['state'])): ?>
            <p><strong>State:</strong> <?php echo htmlspecialchars($currentUser['state']); ?></p>
        <?php endif; ?>
        <?php if (!empty($currentUser['country'])): ?>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($currentUser['country']); ?></p>
        <?php endif; ?>
        <?php if (!empty($currentUser['postal_code'])): ?>
            <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($currentUser['postal_code']); ?></p>
        <?php endif; ?>
        <!-- Add more profile details as needed -->
    <?php else: ?>
        <p>User not found or not logged in.</p>
    <?php endif; ?>
</div>

<?php
include_once 'user-footer.php';
?>