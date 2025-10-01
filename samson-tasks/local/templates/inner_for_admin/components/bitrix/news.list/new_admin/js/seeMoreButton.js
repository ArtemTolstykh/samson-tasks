document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-toggle-full');
    if (!btn) return;

    const id = btn.dataset.target;
    const teaser = document.getElementById('teaser-' + id);
    const full = document.getElementById('full-' + id);
    const expanded = btn.getAttribute('aria-expanded') === 'true';

    if (!teaser || !full) return;

    if (!expanded) {
        // раскрыть
        full.hidden = false;
        teaser.hidden = true;
        btn.setAttribute('aria-expanded', 'true');
        btn.textContent = btn.dataset.labelClose || 'скрыть все';
    } else {
        // свернуть
        full.hidden = true;
        teaser.hidden = false;
        btn.setAttribute('aria-expanded', 'false');
        btn.textContent = btn.dataset.labelOpen || 'показать все';
    }
});
