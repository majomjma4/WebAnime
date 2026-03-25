(() => {
  async function loadAdminSidebar() {
    const host = document.querySelector('[data-admin-sidebar]');
    if (!host) return;
    try {
      const res = await fetch('partials/admin-layout.html', { cache: 'no-store' });
      if (!res.ok) return;
      const html = await res.text();
      const container = document.createElement('div');
      container.innerHTML = html;
      const tpl = container.querySelector('#admin-sidebar');
      if (!tpl) return;
      host.innerHTML = tpl.innerHTML.trim();

      const page = document.body.dataset.adminPage || '';
      const active = host.querySelector(`[data-admin-item="${page}"]`);
      if (active) {
        active.className = 'flex items-center gap-4 text-[#cdbdff] bg-[#1f2020] rounded-r-full border-l-4 border-[#7c4dff] py-4 px-6 mb-2 transition-all duration-300 font-headline font-bold tracking-tight text-sm uppercase';
        const icon = active.querySelector('.material-symbols-outlined');
        if (icon) icon.style.fontVariationSettings = "'FILL' 1";
      }

      const logout = host.querySelector('[data-admin-logout]');
      if (logout) {
        logout.addEventListener('click', (event) => {
          event.preventDefault();
          window.location.href = 'index.html';
        });
      }

      if (document.body.dataset.adminPage) {
        initAdminButtons();
      }
    } catch (e) {}
  }

  function initAdminButtons() {
    if (document.getElementById('admin-toast')) return;

    const toast = document.createElement('div');
    toast.id = 'admin-toast';
    toast.style.cssText = [
      'position:fixed',
      'left:50%',
      'top:24px',
      'transform:translateX(-50%)',
      'background:rgba(17,24,39,0.95)',
      'color:#e7e5e4',
      'padding:10px 16px',
      'border-radius:999px',
      'font-size:12px',
      'letter-spacing:0.08em',
      'text-transform:uppercase',
      'border:1px solid rgba(124,77,255,0.4)',
      'box-shadow:0 12px 30px rgba(0,0,0,0.35)',
      'opacity:0',
      'pointer-events:none',
      'transition:opacity 0.2s ease'
    ].join(';');
    toast.textContent = 'Función en desarrollo';
    document.body.appendChild(toast);

    let toastTimer = null;
    const showToast = () => {
      toast.style.opacity = '1';
      clearTimeout(toastTimer);
      toastTimer = setTimeout(() => {
        toast.style.opacity = '0';
      }, 1400);
    };

    document.addEventListener('click', (event) => {
      const button = event.target.closest('button');
      if (!button) return;
      if (!document.body.dataset.adminPage) return;
      if (button.closest('form') && button.type === 'submit') return;
      const link = button.getAttribute('data-admin-link');
      if (link) {
        window.location.href = link;
        return;
      }
      showToast();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAdminSidebar);
  } else {
    loadAdminSidebar();
  }
})();
