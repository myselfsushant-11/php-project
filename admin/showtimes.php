<?php
$pageTitle = 'Showtimes — CINEFILE Admin';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>
<div class="admin-main">
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-lumiere d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h5 class="mb-0 font-display">Showtimes</h5>
    </div>
    <button class="btn btn-lumiere btn-sm" data-bs-toggle="modal" data-bs-target="#showtimeModal" onclick="openAddShowtime()"><i class="bi bi-plus-lg me-1"></i>Add Showtime</button>
  </div>

  <div class="admin-content">
    <div class="lm-surface p-3">
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Movie</th><th>Hall</th><th>Date</th><th>Time</th><th>Price</th><th>Available</th><th>Booked</th><th>Actions</th></tr></thead>
          <tbody id="showtimesTbody"><tr><td colspan="8" class="text-center text-muted py-4">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="showtimeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content lm-surface border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="showtimeModalTitle">Add Showtime</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="showtimeForm">
        <div class="modal-body">
          <input type="hidden" name="id" id="stId">
          <div class="mb-3">
            <label class="lm-label">Movie</label>
            <select name="movie_id" id="stMovie" class="form-select lm-input" required></select>
          </div>
          <div class="mb-3">
            <label class="lm-label">Hall</label>
            <select name="hall_id" id="stHall" class="form-select lm-input" required></select>
          </div>
          <div class="row g-3">
            <div class="col-6"><label class="lm-label">Show Date</label><input type="date" name="show_date" id="stDate" class="form-control lm-input" required></div>
            <div class="col-6"><label class="lm-label">Show Time</label><input type="time" name="show_time" id="stTime" class="form-control lm-input" required></div>
          </div>
          <div class="mb-3 mt-3">
            <label class="lm-label">Ticket Price (Rs.)</label>
            <input type="number" step="0.01" min="1" name="ticket_price" id="stPrice" class="form-control lm-input" required>
          </div>
          <p class="small text-muted mb-0" id="stNote">Seats will be generated automatically based on the hall's layout.</p>
          <div id="showtimeFormError" class="text-danger small mt-2" style="display:none;"></div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-lumiere" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-lumiere" id="showtimeSaveBtn">Save Showtime</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let showtimes = [];

async function loadOptions() {
  const { data } = await lmFetch('ajax/showtimes.php?action=options');
  const movieSel = document.getElementById('stMovie');
  const hallSel = document.getElementById('stHall');
  movieSel.innerHTML = data.movies.map(m => `<option value="${m.id}">${m.title}</option>`).join('');
  hallSel.innerHTML = data.halls.map(h => `<option value="${h.id}">${h.name}</option>`).join('');
}

async function loadShowtimes() {
  const { data } = await lmFetch('ajax/showtimes.php?action=list');
  showtimes = data.showtimes || [];
  const tbody = document.getElementById('showtimesTbody');
  if (!showtimes.length) { tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No showtimes scheduled yet.</td></tr>'; return; }
  tbody.innerHTML = showtimes.map(s => `
    <tr>
      <td>${s.movie_title}</td>
      <td>${s.hall_name}</td>
      <td>${s.show_date}</td>
      <td>${s.show_time}</td>
      <td>Rs. ${Number(s.ticket_price).toLocaleString()}</td>
      <td>${s.total_seats - s.booked_seats}</td>
      <td>${s.booked_seats}</td>
      <td>
        <button class="btn btn-sm btn-outline-lumiere" onclick="editShowtime(${s.id})"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteShowtime(${s.id})"><i class="bi bi-trash"></i></button>
      </td>
    </tr>`).join('');
}

function openAddShowtime() {
  document.getElementById('showtimeForm').reset();
  document.getElementById('stId').value = '';
  document.getElementById('showtimeModalTitle').textContent = 'Add Showtime';
  document.getElementById('stNote').style.display = 'block';
}

function editShowtime(id) {
  const s = showtimes.find(x => x.id === id);
  if (!s) return;
  document.getElementById('showtimeModalTitle').textContent = 'Edit Showtime';
  document.getElementById('stId').value = s.id;
  document.getElementById('stMovie').value = s.movie_id;
  document.getElementById('stHall').value = s.hall_id;
  document.getElementById('stDate').value = s.show_date;
  document.getElementById('stTime').value = s.show_time;
  document.getElementById('stPrice').value = s.ticket_price;
  document.getElementById('stNote').style.display = 'none';
  new bootstrap.Modal(document.getElementById('showtimeModal')).show();
}

async function deleteShowtime(id) {
  if (!confirm('Delete this showtime?')) return;
  const fd = new FormData(); fd.append('id', id);
  const { data } = await lmFetch('ajax/showtimes.php?action=delete', { method: 'POST', body: fd });
  lmToast(data.message, data.success ? 'success' : 'error');
  if (data.success) loadShowtimes();
}

document.getElementById('showtimeForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('showtimeSaveBtn');
  const errBox = document.getElementById('showtimeFormError');
  errBox.style.display = 'none';
  lmSetLoading(btn, true, 'Saving…');
  const { data } = await lmFetch('ajax/showtimes.php?action=save', { method: 'POST', body: new FormData(this) });
  lmSetLoading(btn, false);
  if (data.success) {
    lmToast(data.message, 'success');
    bootstrap.Modal.getInstance(document.getElementById('showtimeModal')).hide();
    loadShowtimes();
  } else {
    errBox.textContent = data.message;
    errBox.style.display = 'block';
  }
});

loadOptions();
loadShowtimes();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
