<?php
$pageTitle = 'Bookings — CINÈFILÈ Admin';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>
<div class="admin-main">
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-lumiere d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h5 class="mb-0 font-display">Bookings</h5>
    </div>
    <span class="text-muted small" id="bookingCount"></span>
  </div>

  <div class="admin-content">
    <div class="lm-surface p-3 mb-3">
      <div class="row g-2">
        <div class="col-md-3">
          <select id="fMovie" class="form-select lm-input form-control-sm">
            <option value="">All Movies</option>
          </select>
        </div>
        <div class="col-md-2"><input type="text" id="fCode" class="form-control lm-input form-control-sm" placeholder="Booking ID"></div>
        <div class="col-md-2"><input type="text" id="fHall" class="form-control lm-input form-control-sm" placeholder="Hall"></div>
        <div class="col-md-2"><input type="date" id="fDate" class="form-control lm-input form-control-sm"></div>
        <div class="col-md-2"><input type="text" id="fUser" class="form-control lm-input form-control-sm" placeholder="Customer"></div>
        <div class="col-md-1">
          <select id="fStatus" class="form-select lm-input form-control-sm">
            <option value="">Status</option>
            <option value="confirmed">Confirmed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-3" id="summaryRow">
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon mb-2"><i class="bi bi-ticket-perforated"></i></div>
          <div class="stat-value" id="sumBookings">0</div>
          <div class="stat-label">Bookings</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon mb-2"><i class="bi bi-person-check"></i></div>
          <div class="stat-value" id="sumSeats">0</div>
          <div class="stat-label">Tickets Sold</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon mb-2"><i class="bi bi-cash-stack"></i></div>
          <div class="stat-value" id="sumRevenue">Rs. 0</div>
          <div class="stat-label">Revenue</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <div class="stat-icon mb-2"><i class="bi bi-x-circle"></i></div>
          <div class="stat-value" id="sumCancelled">0</div>
          <div class="stat-label">Cancelled</div>
        </div>
      </div>
    </div>

    <div class="lm-surface p-3">
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Booking ID</th><th>Customer</th><th>Movie</th><th>Hall</th><th>Date</th><th>Time</th><th>Seats</th><th>Amount</th><th>Status</th><th>Booked On</th></tr></thead>
          <tbody id="bookingsTbody"><tr><td colspan="10" class="text-center text-muted py-4">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
async function loadMovieOptions() {
  const { data } = await lmFetch('ajax/bookings.php?action=movie_options');
  const sel = document.getElementById('fMovie');
  (data.movies || []).forEach(m => {
    const opt = document.createElement('option');
    opt.value = m.id;
    opt.textContent = m.title;
    sel.appendChild(opt);
  });
}

async function loadBookings() {
  const params = new URLSearchParams({
    movie_id: document.getElementById('fMovie').value,
    code: document.getElementById('fCode').value,
    hall: document.getElementById('fHall').value,
    date: document.getElementById('fDate').value,
    user: document.getElementById('fUser').value,
    status: document.getElementById('fStatus').value,
  });
  const { data } = await lmFetch('ajax/bookings.php?' + params.toString());
  const bookings = data.bookings || [];

  document.getElementById('bookingCount').textContent = bookings.length + ' result(s)';

  // Summary for the currently selected movie/filters
  const confirmedBookings = bookings.filter(b => b.status === 'confirmed');
  const totalSeats = confirmedBookings.reduce((sum, b) => sum + Number(b.total_seats), 0);
  const totalRevenue = confirmedBookings.reduce((sum, b) => sum + Number(b.total_amount), 0);
  const cancelledCount = bookings.filter(b => b.status === 'cancelled').length;

  document.getElementById('sumBookings').textContent = confirmedBookings.length;
  document.getElementById('sumSeats').textContent = totalSeats;
  document.getElementById('sumRevenue').textContent = 'Rs. ' + totalRevenue.toLocaleString();
  document.getElementById('sumCancelled').textContent = cancelledCount;

  const tbody = document.getElementById('bookingsTbody');
  if (!bookings.length) { tbody.innerHTML = '<tr><td colspan="10" class="text-center text-muted py-4">No bookings found.</td></tr>'; return; }
  tbody.innerHTML = bookings.map(b => `
    <tr>
      <td>${b.booking_code}</td>
      <td>${b.customer_name}</td>
      <td>${b.movie_title}</td>
      <td>${b.hall_name}</td>
      <td>${b.show_date}</td>
      <td>${b.show_time}</td>
      <td>${b.total_seats}</td>
      <td>Rs. ${Number(b.total_amount).toLocaleString()}</td>
      <td><span class="badge badge-status-${b.status}">${b.status}</span></td>
      <td>${new Date(b.created_at).toLocaleString()}</td>
    </tr>`).join('');
}

['fMovie','fCode','fHall','fDate','fUser','fStatus'].forEach(id => {
  document.getElementById(id).addEventListener('input', debounceLoad);
  document.getElementById(id).addEventListener('change', debounceLoad);
});
let debTimer;
function debounceLoad() { clearTimeout(debTimer); debTimer = setTimeout(loadBookings, 300); }

loadMovieOptions();
loadBookings();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>