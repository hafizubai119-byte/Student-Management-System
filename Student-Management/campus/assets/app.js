// Mobile sidebar toggle
const btn = document.getElementById('menu-btn');
const side = document.getElementById('sidebar');
if (btn && side) {
  btn.addEventListener('click', () => {
    const open = side.classList.toggle('open');
    btn.setAttribute('aria-expanded', open);
  });
}
// Confirm before destructive forms
document.querySelectorAll('form[data-confirm]').forEach((f) => {
  f.addEventListener('submit', (e) => {
    if (!confirm(f.dataset.confirm)) e.preventDefault();
  });
});
// Auto-hide notices
const n = document.querySelector('.notice');
if (n) setTimeout(() => { n.style.opacity = '0'; }, 3500);
// Client-side validation hint: trim inputs on blur
document.querySelectorAll('.form input[type=text], .form input:not([type])').forEach((i) =>
  i.addEventListener('blur', () => { i.value = i.value.trim(); })
);
