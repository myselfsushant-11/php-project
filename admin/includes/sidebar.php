<?php $current = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="lm-brand fs-5 mb-4 px-2">CINE<span style="color:var(--lm-accent)">FILE</span> <span class="small text-muted d-block" style="font-size:0.6rem; letter-spacing:2px;">STUDIO ADMIN</span></div>
  <nav class="nav flex-column">
    <a class="nav-link <?= $current === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a class="nav-link <?= $current === 'movies.php' ? 'active' : '' ?>" href="movies.php"><i class="bi bi-film"></i> Movies</a>
    <a class="nav-link <?= $current === 'showtimes.php' ? 'active' : '' ?>" href="showtimes.php"><i class="bi bi-clock-history"></i> Showtimes</a>
    <a class="nav-link <?= $current === 'halls.php' ? 'active' : '' ?>" href="halls.php"><i class="bi bi-building"></i> Halls</a>
    <a class="nav-link <?= $current === 'bookings.php' ? 'active' : '' ?>" href="bookings.php"><i class="bi bi-ticket-perforated"></i> Bookings</a>
    <a class="nav-link <?= $current === 'users.php' ? 'active' : '' ?>" href="users.php"><i class="bi bi-people"></i> Users</a>
    <hr style="border-color:var(--lm-border)">
    <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </nav>
</aside>
