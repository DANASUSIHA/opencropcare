
<!-- Mobile Bottom Navigation Bar (Visible on phones & small tablets) -->
<?php if (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') !== 'admin'): ?>
    <div class="mobile-bottom-nav d-md-none">
        <a href="index.php?route=dashboard" class="mobile-nav-btn <?= is_active_route('dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="index.php?route=history" class="mobile-nav-btn <?= is_active_route('history') ? 'active' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>History</span>
        </a>
        <a href="index.php?route=identify" class="mobile-nav-btn-highlight" title="Scan Leaf">
            <i class="fa-solid fa-camera"></i>
        </a>
        <a href="index.php?route=diseases" class="mobile-nav-btn <?= is_active_route('diseases') ? 'active' : '' ?>">
            <i class="fa-solid fa-book-medical"></i>
            <span>Library</span>
        </a>
        <a href="index.php?route=help" class="mobile-nav-btn <?= is_active_route('help') ? 'active' : '' ?>">
            <i class="fa-solid fa-circle-question"></i>
            <span>Guide</span>
        </a>
    </div>
<?php endif; ?>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Main App JS -->
<script src="js/main.js"></script>
</body>
</html>
