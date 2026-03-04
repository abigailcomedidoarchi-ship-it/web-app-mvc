<?php include 'views/templates/header.php'; ?>

<div class="card">
    <h2>Email Verification</h2>

    <?php if (!empty($message)) echo "<div class='message-success'>$message</div>"; ?>
    <?php if (!empty($error)) echo "<div class='message-error'>$error</div>"; ?>

    <p><a href="index.php?action=login">Go to Login</a></p>
</div>

<?php include 'views/templates/footer.php'; ?>