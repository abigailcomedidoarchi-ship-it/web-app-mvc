<?php include 'views/templates/header.php'; ?>

<div class="card">
    <h2>Login</h2>

    <?php if(!empty($error)) echo "<div class='message-error'>$error</div>"; ?>

    <form method="POST" action="index.php?action=login">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <p>No account? <a href="index.php?action=register">Register</a></p>
</div>

<?php include 'views/templates/footer.php'; ?>