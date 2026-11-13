  // Accordion behavior (smooth, accessible, optional single-open mode)
    const accordion = document.getElementById('accordion');
    const single = accordion?.dataset.accordion === 'single';

    accordion?.addEventListener('click', (e) => {
      const btn = e.target.closest('button[aria-controls]');
      if (!btn) return;

      const panelId = btn.getAttribute('aria-controls');
      const panel = document.getElementById(panelId);
      const icon = btn.querySelector('img');

      const isOpen = btn.getAttribute('aria-expanded') === 'true';

      // If single mode, close others first
      if (single) {
        accordion.querySelectorAll('button[aria-controls]').forEach(b => {
          if (b !== btn) {
            b.setAttribute('aria-expanded', 'false');
            const p = document.getElementById(b.getAttribute('aria-controls'));
            const i = b.querySelector('img');
            p.style.gridTemplateRows = '0fr';
            i?.classList.remove('rotate-180');
          }
        });
      }

      // Toggle this one
      btn.setAttribute('aria-expanded', String(!isOpen));
      panel.style.gridTemplateRows = isOpen ? '0fr' : '1fr';
      icon?.classList.toggle('rotate-180', !isOpen);
    });

    // Keyboard support for Space/Enter (buttons already handle this in most browsers,
    // but this ensures consistent behavior if default is prevented by something else)
    accordion?.querySelectorAll('button[aria-controls]').forEach(btn => {
      btn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          btn.click();
        }
      });
    });