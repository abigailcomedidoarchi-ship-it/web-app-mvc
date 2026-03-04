<?php include 'views/templates/header.php'; ?>

<div class="card">
    <h2>Register</h2>

    <?php if(!empty($error)) echo "<div class='message-error'>$error</div>"; ?>

    <form method="POST" action="index.php?action=register">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="index.php?action=login">Login</a></p>
</div>

<?php include 'views/templates/footer.php'; ?>