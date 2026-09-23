function toggleModal(id) {
  const modal = document.getElementById(id);
  if (!modal) return;

  // If modal is already active → close it
  if (modal.classList.contains('active')) {
    modal.classList.remove('active');

    // Wait for fade-out animation, then hide it
    setTimeout(() => {
      modal.style.display = 'none';
    }, 300); // match transition duration in CSS
  } else {
    // Show modal, then trigger animation
    modal.style.display = 'flex';
    setTimeout(() => {
      modal.classList.add('active');
    }, 10); // small delay ensures transition triggers
  }
}
