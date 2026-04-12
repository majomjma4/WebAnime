(() => {
  const buildAppUrl = window.AniDexShared?.buildAppUrl || ((path = "") => String(path || ""));

  /**
   * Inicializa el sidebar que ya ha sido incluido vía PHP
   */
  async function loadAdminSidebar() {
    // Buscamos el aside que ya debe estar en el DOM gracias al include de PHP
    const aside = document.querySelector('aside.fixed.left-0');
    if (!aside) return;

    // Aplicar estilo de item activo
    const page = document.body.dataset.adminPage || '';
    const active = aside.querySelector(`[data-admin-item="${page}"]`);
    if (active) {
      active.className = 'flex items-center gap-4 text-[#cdbdff] bg-[#1f2020] rounded-r-full border-l-4 border-[#7c4dff] py-4 px-6 mb-2 transition-all duration-300 font-headline font-bold tracking-tight text-sm uppercase';
      const icon = active.querySelector('.material-symbols-outlined');
      if (icon) icon.style.fontVariationSettings = "'FILL' 1";
    }

    // Listener para el botón de logout
    const logout = aside.querySelector('[data-admin-logout]');
    if (logout) {
      logout.addEventListener('click', async (event) => {
        event.preventDefault();
        // Sincronización de estado local (opcional)
        try {
          const res = await fetch(buildAppUrl('api/auth?action=check'), { cache: 'no-store', credentials: 'same-origin' });
          if (res.ok) {
            const auth = await res.json();
            if (auth && auth.logged) {
              localStorage.setItem('nekora_logged_in', 'true');
              if (auth.username) localStorage.setItem('nekora_user', auth.username);
              if (auth.isAdmin) localStorage.setItem('nekora_admin', 'true');
              else localStorage.removeItem('nekora_admin');
            }
          }
        } catch (e) {}
        window.location.href = buildAppUrl('index');
      });
    }

    // Interactividad adicional si se requiere (fallback para páginas sin script propio)
    if (document.body.dataset.adminPage) {
      initAdminButtons();
    }
  }

  /**
   * Interactividad de botones genéricos del dashboard
   */
  function initAdminButtons() {
    // Si la página tiene su propia lógica de paginación (como admin o gestion), salimos
    if (document.querySelector('[data-admin-pagination-script]')) return;

    // Lógica para botones de link directo (si existen)
    document.addEventListener('click', (event) => {
      const button = event.target.closest('button');
      if (!button) return;
      if (!document.body.dataset.adminPage) return;
      if (button.closest('form') && button.type === 'submit') return;
      
      const link = button.getAttribute('data-admin-link');
      if (link) {
        window.location.href = link;
      }
    });

    const searchInput = document.querySelector('[data-admin-search]');
    if (searchInput) {
      // Mock de búsqueda para páginas estáticas
      searchInput.addEventListener('input', () => {
        const term = searchInput.value.trim().toLowerCase();
        const rows = Array.from(document.querySelectorAll('[data-admin-request-row]'));
        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          row.style.display = (!term || text.includes(term)) ? '' : 'none';
        });
      });
    }
  }

  // Inicialización inteligente
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadAdminSidebar);
  } else {
    loadAdminSidebar();
  }
})();
