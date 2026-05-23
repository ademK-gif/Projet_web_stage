// js/main.js

// Animate elements on scroll
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-up').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(30px)';
  el.style.transition = 'all 0.6s ease';
  observer.observe(el);
});

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(alert => {
  setTimeout(() => {
    alert.style.transition = 'opacity 0.5s ease';
    alert.style.opacity = '0';
    setTimeout(() => alert.remove(), 500);
  }, 5000);
});

// Mobile nav toggle
const navLinks = document.querySelector('.nav-links');
const hamburger = document.getElementById('hamburger');
if (hamburger) {
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('open');
  });
}

// Search box placeholder rotation
const placeholders = [
  'Développeur Web, Tunis...',
  'Marketing Digital, Sfax...',
  'Stage Finance, Sousse...',
  'Designer UX/UI...',
  'Ingénieur Informatique...'
];
const searchInput = document.querySelector('.search-box input[type="text"]');
if (searchInput) {
  let i = 0;
  setInterval(() => {
    searchInput.setAttribute('placeholder', placeholders[i % placeholders.length]);
    i++;
  }, 3000);
}

// Confirm delete actions
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', (e) => {
    if (!confirm(el.dataset.confirm)) e.preventDefault();
  });
});

// Active nav link highlight
const currentPath = window.location.pathname;
document.querySelectorAll('.nav-links a').forEach(link => {
  if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href').split('/').pop())) {
    link.style.color = 'var(--primary)';
    link.style.fontWeight = '600';
  }
});

// Character counter for textareas
document.querySelectorAll('textarea[maxlength]').forEach(ta => {
  const counter = document.createElement('div');
  counter.style.cssText = 'font-size:.75rem;color:var(--muted);text-align:right;margin-top:.25rem';
  ta.parentNode.appendChild(counter);
  const update = () => counter.textContent = `${ta.value.length}/${ta.getAttribute('maxlength')} caractères`;
  ta.addEventListener('input', update);
  update();
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
  });
});

console.log('%cJobLink 🚀', 'color:#4f46e5;font-size:2rem;font-weight:800');
console.log('%cPlateforme Emploi & Stage', 'color:#64748b;font-size:1rem');
