<?php
$pageTitle = 'Halls — CINEFILE Admin';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>
<div class="admin-main">
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-lumiere d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h5 class="mb-0 font-display">Halls</h5>
    </div>
    <button class="btn btn-lumiere btn-sm" data-bs-toggle="modal" data-bs-target="#hallModal" onclick="openAddHall()"><i class="bi bi-plus-lg me-1"></i>Add Hall</button>
  </div>

  <div class="admin-content">
    <div class="lm-surface p-3">
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Rows</th><th>Seats / Row</th><th>Total Seats</th><th>Actions</th></tr></thead>
          <tbody id="hallsTbody"><tr><td colspan="5" class="text-center text-muted py-4">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="hallModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content lm-surface border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="hallModalTitle">Add Hall</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="hallForm">
        <div class="modal-body">
          <input type="hidden" name="id" id="hallId">
          <div class="mb-3"><label class="lm-label">Hall Name</label><input type="text" name="name" id="hName" class="form-control lm-input" placeholder="Hall 1 — Cinefile" required></div>
          <div class="row g-3">
            <div class="col-6"><label class="lm-label">Rows</label><input type="number" name="rows_count" id="hRows" class="form-control lm-input" min="1" required></div>
            <div class="col-6"><label class="lm-label">Seats per Row</label><input type="number" name="seats_per_row" id="hCols" class="form-control lm-input" min="1" required></div>
          </div>
          <p class="small text-muted mt-2 mb-0">Seats are auto-generated per showtime based on this layout.</p>
          <div id="hallFormError" class="text-danger small mt-2" style="display:none;"></div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-lumiere" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-lumiere" id="hallSaveBtn">Save Hall</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let halls = [];

async function loadHalls() {
  const { data } = await lmFetch('ajax/halls.php?action=list');
  halls = data.halls || [];
  const tbody = document.getElementById('hallsTbody');
  if (!halls.length) { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No halls yet.</td></tr>'; return; }
  tbody.innerHTML = halls.map(h => `
    <tr>
      <td>${h.name}</td>
      <td>${h.rows_count}</td>
      <td>${h.seats_per_row}</td>
      <td>${h.total_seats}</td>
      <td>
        <button class="btn btn-sm btn-outline-lumiere" onclick="editHall(${h.id})"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteHall(${h.id})"><i class="bi bi-trash"></i></button>
      </td>
    </tr>`).join('');
}

function openAddHall() {
  document.getElementById('hallForm').reset();
  document.getElementById('hallId').value = '';
  document.getElementById('hallModalTitle').textContent = 'Add Hall';
}

function editHall(id) {
  const h = halls.find(x => x.id === id);
  if (!h) return;
  document.getElementById('hallModalTitle').textContent = 'Edit Hall';
  document.getElementById('hallId').value = h.id;
  document.getElementById('hName').value = h.name;
  document.getElementById('hRows').value = h.rows_count;
  document.getElementById('hCols').value = h.seats_per_row;
  new bootstrap.Modal(document.getElementById('hallModal')).show();
}

async function deleteHall(id) {
  if (!confirm('Delete this hall?')) return;
  const fd = new FormData(); fd.append('id', id);
  const { data } = await lmFetch('ajax/halls.php?action=delete', { method: 'POST', body: fd });
  lmToast(data.message, data.success ? 'success' : 'error');
  if (data.success) loadHalls();
}

document.getElementById('hallForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('hallSaveBtn');
  const errBox = document.getElementById('hallFormError');
  errBox.style.display = 'none';
  lmSetLoading(btn, true, 'Saving…');
  const { data } = await lmFetch('ajax/halls.php?action=save', { method: 'POST', body: new FormData(this) });
  lmSetLoading(btn, false);
  if (data.success) {
    lmToast(data.message, 'success');
    bootstrap.Modal.getInstance(document.getElementById('hallModal')).hide();
    loadHalls();
  } else {
    errBox.textContent = data.message;
    errBox.style.display = 'block';
  }
});

loadHalls();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
