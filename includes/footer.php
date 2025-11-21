<?php if ($current_page !== 'signin.php' && $current_page !== 'register.php'): ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const hamburger = document.getElementById("hamburger");
    const sidebar = document.getElementById("sidebar");

    if (hamburger && sidebar) {
        hamburger.addEventListener("click", function() {
            sidebar.classList.toggle("open");
        });
    }
});
</script>
</div>
<?php endif; ?>
</body>
</html>
