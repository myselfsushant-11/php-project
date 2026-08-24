<?php
require_once __DIR__ . '/includes/auth.php';
require_login_redirect();
$assetBase = '';
$pageTitle = 'My Bookings — CINEPHILE';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<div class="container container-xl py-5">
  <h3 class="font-display mb-4">My Bookings</h3>

  <ul class="nav nav-pills mb-4" id="bookingTabs">
    <li class="nav-item"><button class="nav-link active btn btn-lumiere btn-sm me-2" data-type="upcoming">Upcoming</button></li>
    <li class="nav-item"><button class="nav-link btn btn-outline-lumiere btn-sm" data-type="past">Past Bookings</button></li>
  </ul>

  <div id="bookingsList" class="row g-4">
    <div class="text-center text-muted py-5"><span class="spinner-border spinner-border-sm me-2"></span>Loading bookings…</div>
  </div>
</div>

<script>
async function loadBookings(type) {
  const list = document.getElementById('bookingsList');
  list.innerHTML = '<div class="text-center text-muted py-5"><span class="spinner-border spinner-border-sm me-2"></span>Loading bookings…</div>';
  const { data } = await lmFetch('ajax/get_user_bookings.php?type=' + type);
  if (!data.success || data.bookings.length === 0) {
    list.innerHTML = `<div class="lm-surface p-5 text-center text-muted"><i class="bi bi-ticket-perforated fs-1 d-block mb-2"></i>No ${type} bookings found.</div>`;
    return;
  }
  list.innerHTML = data.bookings.map(b => `
    <div class="col-md-6 col-lg-4">
      <div class="lm-surface p-3 h-100 d-flex gap-3">
        <img src="${b.poster || 'assets/images/movies/placeholder.jpg'}" style="width:64px;height:90px;object-fit:cover;border-radius:8px;">
        <div class="flex-grow-1">
          <div class="fw-semibold font-display">${b.movie_title}</div>
          <div class="small text-muted mb-1">${b.hall_name} • ${b.date} • ${b.time}</div>
          <div class="small text-muted mb-1">Seats: ${b.seats} • ${b.amount}</div>
          <span class="badge badge-status-${b.status}">${b.status}</span>
          <div class="small text-muted mt-1">#${b.booking_code}</div>
          <a href="booking-success.php?id=${b.booking_code}" class="btn btn-outline-lumiere btn-sm mt-2 w-100">View Ticket</a>
        </div>
      </div>
    </div>
  `).join('');
}

document.querySelectorAll('#bookingTabs button').forEach(btn => {
  btn.addEventListener('click', function () {
    document.querySelectorAll('#bookingTabs button').forEach(b => {
      b.classList.remove('btn-lumiere'); b.classList.add('btn-outline-lumiere');
    });
    this.classList.remove('btn-outline-lumiere'); this.classList.add('btn-lumiere');
    loadBookings(this.dataset.type);
  });
});

loadBookings('upcoming');
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
