<?php 
include 'views/templates/header.php'; ?>

<div class="fairy-container">
    <div class="card home-box">
        <div class="welcome-header">
            <h2>Welcome Home</h2>
            <p class="greeting-text">
                Greetings, <span class="user-name"><?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Traveler'); ?></span>. 
                
            </p>
        </div>

        <hr class="divider">

        <div class="magic-quote">
            <p><em>"Believe you can and you're halfway there." — Theodore Roosevelt</em></p>
        </div>



        <form method="POST" action="index.php?action=logout">
            <button type="submit" class="logout-btn">Log Out</button>
        </form>

        <p class="footer-note">Current local time: <?php echo date("H:i"); ?> ✨</p>
    </div>
</div>

<?php include 'views/templates/footer.php'; ?>