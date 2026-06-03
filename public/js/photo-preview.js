document.addEventListener('DOMContentLoaded', () => {
  const input = document.querySelector('[data-photo-input]');
  const preview = document.querySelector('[data-photo-preview]');
  if (!input || !preview) return;
  input.addEventListener('change', () => {
    const file = input.files && input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => preview.src = e.target.result;
    reader.readAsDataURL(file);
  });
});
