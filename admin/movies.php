<?php
$pageTitle = 'Movies — CINEFILE Admin';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>
<div class="admin-main">
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-sm btn-outline-lumiere d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
      <h5 class="mb-0 font-display">Movies</h5>
    </div>
    <button class="btn btn-lumiere btn-sm" data-bs-toggle="modal" data-bs-target="#movieModal" onclick="openAddMovie()"><i class="bi bi-plus-lg me-1"></i>Add Movie</button>
  </div>

  <div class="admin-content">
    <div class="lm-surface p-3 mb-3">
      <input type="text" id="movieSearch" class="form-control lm-input" placeholder="Search movies by title…">
    </div>

    <div class="lm-surface p-3">
      <div class="table-responsive">
        <table class="admin-table">
          <thead><tr><th>Poster</th><th>Title</th><th>Genre</th><th>Language</th><th>Duration</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="moviesTbody"><tr><td colspan="8" class="text-center text-muted py-4">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Movie Modal -->
<div class="modal fade" id="movieModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content lm-surface border-0">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="movieModalTitle">Add Movie</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="movieForm" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="id" id="movieId">
          <div class="row g-3">
            <div class="col-md-8"><label class="lm-label">Title</label><input type="text" name="title" id="mTitle" class="form-control lm-input" required></div>
            <div class="col-md-4"><label class="lm-label">Language</label><input type="text" name="language" id="mLanguage" class="form-control lm-input" required></div>
            <div class="col-md-6"><label class="lm-label">Genre</label><input type="text" name="genre" id="mGenre" class="form-control lm-input" placeholder="Action, Drama" required></div>
            <div class="col-md-3"><label class="lm-label">Duration (min)</label><input type="number" name="duration" id="mDuration" class="form-control lm-input" required></div>
            <div class="col-md-3"><label class="lm-label">Rating</label><input type="number" step="0.1" max="10" min="0" name="rating" id="mRating" class="form-control lm-input"></div>
            <div class="col-12"><label class="lm-label">Description</label><textarea name="description" id="mDescription" rows="3" class="form-control lm-input"></textarea></div>
            <div class="col-md-6"><label class="lm-label">Trailer URL (embed)</label><input type="text" name="trailer_url" id="mTrailer" class="form-control lm-input" placeholder="https://youtube.com/embed/..."></div>
            <div class="col-md-3"><label class="lm-label">Release Date</label><input type="date" name="release_date" id="mRelease" class="form-control lm-input"></div>
            <div class="col-md-3">
              <label class="lm-label">Status</label>
              <select name="status" id="mStatus" class="form-select lm-input">
                <option value="now_showing">Now Showing</option>
                <option value="coming_soon">Coming Soon</option>
              </select>
            </div>
            <div class="col-md-6"><label class="lm-label">Poster Image</label><input type="file" name="poster" class="form-control lm-input" accept=".jpg,.jpeg,.png,.webp"></div>
            <div class="col-md-6"><label class="lm-label">Backdrop Image</label><input type="file" name="backdrop" class="form-control lm-input" accept=".jpg,.jpeg,.png,.webp"></div>
          </div>
          <div id="movieFormError" class="text-danger small mt-3" style="display:none;"></div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-lumiere" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-lumiere" id="movieSaveBtn">Save Movie</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let movies = [];

async function loadMovies(q = '') {
  const { data } = await lmFetch('ajax/movies.php?action=list&q=' + encodeURIComponent(q));
  movies = data.movies || [];
  renderMovies();
}

function renderMovies() {
  const tbody = document.getElementById('moviesTbody');
  if (!movies.length) { tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No movies found.</td></tr>'; return; }
  tbody.innerHTML = movies.map(m => `
    <tr>
      <td><img src="../${m.poster || 'assets/images/movies/placeholder.jpg'}" style="width:40px;height:56px;object-fit:cover;border-radius:4px;"></td>
      <td>${m.title}</td>
      <td>${m.genre}</td>
      <td>${m.language}</td>
      <td>${m.duration} min</td>
      <td>${Number(m.rating).toFixed(1)}</td>
      <td><span class="badge ${m.status === 'now_showing' ? 'bg-success' : 'bg-secondary'}">${m.status.replace('_',' ')}</span></td>
      <td>
        <button class="btn btn-sm btn-outline-lumiere" onclick="editMovie(${m.id})"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-danger" onclick="deleteMovie(${m.id})"><i class="bi bi-trash"></i></button>
      </td>
    </tr>`).join('');
}

function openAddMovie() {
  document.getElementById('movieForm').reset();
  document.getElementById('movieId').value = '';
  document.getElementById('movieModalTitle').textContent = 'Add Movie';
}

function editMovie(id) {
  const m = movies.find(x => x.id === id);
  if (!m) return;
  document.getElementById('movieModalTitle').textContent = 'Edit Movie';
  document.getElementById('movieId').value = m.id;
  document.getElementById('mTitle').value = m.title;
  document.getElementById('mLanguage').value = m.language;
  document.getElementById('mGenre').value = m.genre;
  document.getElementById('mDuration').value = m.duration;
  document.getElementById('mRating').value = m.rating;
  document.getElementById('mDescription').value = m.description || '';
  document.getElementById('mTrailer').value = m.trailer_url || '';
  document.getElementById('mRelease').value = m.release_date || '';
  document.getElementById('mStatus').value = m.status;
  new bootstrap.Modal(document.getElementById('movieModal')).show();
}

async function deleteMovie(id) {
  if (!confirm('Delete this movie? This cannot be undone.')) return;
  const fd = new FormData(); fd.append('id', id);
  const { data } = await lmFetch('ajax/movies.php?action=delete', { method: 'POST', body: fd });
  lmToast(data.message, data.success ? 'success' : 'error');
  if (data.success) loadMovies();
}

document.getElementById('movieForm').addEventListener('submit', async function (e) {
  e.preventDefault();
  const btn = document.getElementById('movieSaveBtn');
  const errBox = document.getElementById('movieFormError');
  errBox.style.display = 'none';
  lmSetLoading(btn, true, 'Saving…');
  const { data } = await lmFetch('ajax/movies.php?action=save', { method: 'POST', body: new FormData(this) });
  lmSetLoading(btn, false);
  if (data.success) {
    lmToast(data.message, 'success');
    bootstrap.Modal.getInstance(document.getElementById('movieModal')).hide();
    loadMovies();
  } else {
    errBox.textContent = data.message;
    errBox.style.display = 'block';
  }
});

let searchTimer;
document.getElementById('movieSearch').addEventListener('input', function () {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => loadMovies(this.value), 300);
});

loadMovies();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
