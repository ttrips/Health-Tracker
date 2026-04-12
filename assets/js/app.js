// Health Tracker — JS

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => el.style.opacity = '0', 3500);
    setTimeout(() => el.remove(), 4000);
});

// Mood selector
document.querySelectorAll('.mood-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.mood-btn').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        const input = document.getElementById('mood-input');
        if (input) input.value = btn.dataset.value;
    });
});

// Water cups — visual feedback
document.querySelectorAll('.water-cup').forEach((cup, i, all) => {
    cup.addEventListener('click', () => {
        all.forEach((c, j) => c.classList.toggle('filled', j <= i));
    });
});

// Progress bars — animate on load
window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.progress-bar-fill').forEach(bar => {
        const target = bar.dataset.width || '0';
        bar.style.width = '0%';
        setTimeout(() => bar.style.width = target + '%', 100);
    });
});
