function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('main-content');
  sidebar.classList.toggle('open');
  mainContent.classList.toggle('ml-64');
}

// filter sidebar //
function toggleFilterSidebar() {
  document.getElementById('sidebar-filter').classList.toggle('translate-x-full');
  document.getElementById('sidebar-overlay').classList.toggle('hidden');
  document.body.classList.toggle('overflow-hidden');
}

function closeFilterSidebar() {
  document.getElementById('sidebar-filter').classList.add('translate-x-full');
  document.getElementById('sidebar-overlay').classList.add('hidden');
  document.body.classList.remove('overflow-hidden');
}

