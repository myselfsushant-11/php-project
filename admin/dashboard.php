<?php
$pageTitle = 'Dashboard — CINEFILE Admin';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

$totalMovies   = (int) $pdo->query("SELECT COUNT(*) c FROM movies")->fetch()['c'];
$totalUsers    = (int) $pdo->query("SELECT COUNT(*) c FROM users")->fetch()['c'];
$totalBookings = (int) $pdo->query("SELECT COUNT(*) c FROM bookings WHERE status='confirmed'")->fetch()['c'];
$revenue       = (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) r FROM bookings WHERE status='confirmed'")->fetch()['r'];
$todayBookings = (int) $pdo->query("SELECT COUNT(*) c FROM bookings WHERE DATE(created_at) = CURDATE()")->fetch()['c'];

$upcomingShows = $pdo->query(
    "SELECT s.show_date, s.show_time, m.title, h.name AS hall_name
     FROM showtimes s JOIN movies m ON m.id=s.movie_id JOIN halls h ON h.id=s.hall_id
     WHERE CONCAT(s.show_date,' ',s.show_time) >= NOW()
     ORDER BY s.show_date, s.show_time LIMIT 6"
)->fetchAll();

$recentBookings = $pdo->query(
    "SELECT b.booking_code, b.customer_name, b.total_amount, b.created_at, m.title
     FROM bookings b JOIN showtimes s ON s.id=b.showtime_id JOIN movies m ON m.id=s.movie_id
     ORDER BY b.created_at DESC LIMIT 6"
)->fetchAll();

$popularMovies = $pdo->query(
    "SELECT m.title, COUNT(bs.id) AS tickets_sold
     FROM movies m
     JOIN showtimes s ON s.movie_id = m.id
     JOIN bookings b ON b.showtime_id = s.id AND b.status='confirmed'
     JOIN booking_seats bs ON bs.booking_id = b.id
     GROUP BY m.id ORDER BY tickets_sold DESC LIMIT 5"
)->fetchAll();
?>
<div class="admin-main">
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-lumiere d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h5 class="mb-0 font-display">Dashboard</h5>
    </div>
    <span class="text-muted small">Hi, <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
  </div>

  <div class="admin-content">
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="stat-card"><div class="stat-icon mb-3"><i class="bi bi-film"></i></div><div class="stat-value"><?= $totalMovies ?></div><div class="stat-label">Total Movies</div></div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card"><div class="stat-icon mb-3"><i class="bi bi-people"></i></div><div class="stat-value"><?= number_format($totalUsers) ?></div><div class="stat-label">Total Users</div></div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card"><div class="stat-icon mb-3"><i class="bi bi-ticket-perforated"></i></div><div class="stat-value"><?= number_format($totalBookings) ?></div><div class="stat-label">Total Bookings</div></div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card"><div class="stat-icon mb-3"><i class="bi bi-cash-stack"></i></div><div class="stat-value"><?= format_rupees($revenue) ?></div><div class="stat-label">Revenue</div></div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-4">
        <div class="lm-surface p-4 h-100">
          <h6 class="mb-3"><i class="bi bi-calendar-check me-2" style="color:var(--lm-accent)"></i>Today's Bookings</h6>
          <div class="display-6 fw-bold font-display"><?= $todayBookings ?></div>
          <div class="text-muted small">bookings made today</div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="lm-surface p-4 h-100">
          <h6 class="mb-3"><i class="bi bi-clock me-2" style="color:var(--lm-accent)"></i>Upcoming Shows</h6>
          <?php foreach ($upcomingShows as $s): ?>
          <div class="small mb-2 d-flex justify-content-between border-bottom pb-2" style="border-color:var(--lm-border)!important;">
            <span><?= htmlspecialchars($s['title']) ?> <span class="text-muted">• <?= htmlspecialchars($s['hall_name']) ?></span></span>
            <span class="text-muted"><?= format_date_pretty($s['show_date']) ?>, <?= format_time12($s['show_time']) ?></span>
          </div>
          <?php endforeach; ?>
          <?php if (!$upcomingShows): ?><p class="text-muted small mb-0">No upcoming shows.</p><?php endif; ?>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="lm-surface p-4 h-100">
          <h6 class="mb-3"><i class="bi bi-star me-2" style="color:var(--lm-accent)"></i>Popular Movies</h6>
          <?php foreach ($popularMovies as $p): ?>
          <div class="small mb-2 d-flex justify-content-between">
            <span><?= htmlspecialchars($p['title']) ?></span>
            <span class="text-muted"><?= (int)$p['tickets_sold'] ?> tickets</span>
          </div>
          <?php endforeach; ?>
          <?php if (!$popularMovies): ?><p class="text-muted small mb-0">No sales yet.</p><?php endif; ?>
        </div>
      </div>
    </div>

    <div class="lm-surface p-4 mt-4">
      <h6 class="mb-3">Recent Bookings</h6>
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Booking ID</th><th>Customer</th><th>Movie</th><th>Amount</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach ($recentBookings as $b): ?>
            <tr>
              <td><?= htmlspecialchars($b['booking_code']) ?></td>
              <td><?= htmlspecialchars($b['customer_name']) ?></td>
              <td><?= htmlspecialchars($b['title']) ?></td>
              <td><?= format_rupees($b['total_amount']) ?></td>
              <td><?= date('d M Y, g:i A', strtotime($b['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$recentBookings): ?><tr><td colspan="5" class="text-muted text-center">No bookings yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
