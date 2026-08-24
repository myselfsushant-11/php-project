/* LUMIÈRE — shared front-end utilities */

function lmToast(message, type = 'success') {
  const container = document.getElementById('lmToastContainer');
  if (!container) { alert(message); return; }
  const bg = type === 'success' ? 'text-bg-success' : (type === 'error' ? 'text-bg-danger' : 'text-bg-warning');
  const el = document.createElement('div');
  el.className = `toast align-items-center ${bg} border-0`;
  el.setAttribute('role', 'alert');
  el.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>`;
  container.appendChild(el);
  const toast = new bootstrap.Toast(el, { delay: 3500 });
  toast.show();
  el.addEventListener('hidden.bs.toast', () => el.remove());
}

async function lmFetch(url, options = {}) {
  const res = await fetch(url, options);
  let data;
  try { data = await res.json(); } catch (e) { data = { success: false, message: 'Unexpected server response.' }; }
  return { status: res.status, data };
}

function lmSetLoading(btn, loading, loadingText = 'Please wait…') {
  if (!btn) return;
  if (loading) {
    btn.dataset.originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText}`;
  } else {
    btn.disabled = false;
    btn.innerHTML = btn.dataset.originalText || btn.innerHTML;
  }
}
