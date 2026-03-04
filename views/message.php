<!DOCTYPE html>
<html>
<head>
    <title>Message</title>
    <link rel="stylesheet" href="public/styles.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>Notification</h2>
        
    <?php if(!empty($message)): ?>
        <div class="message-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if(!empty($error)): ?>
        <div class="message-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <p><a href="index.php?action=login">Go to Login</a></p>
</div>
</body>
</html>