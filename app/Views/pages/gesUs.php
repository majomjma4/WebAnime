<!DOCTYPE html>
<html class="dark" lang="es"><head>
<link rel="icon" href="img/icon3.png" />
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>NekoraList - Gestión de Usuarios</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "secondary-container": "#3c3b3e",
              "on-tertiary": "#555671",
              "error-dim": "#b95463",
              "on-surface-variant": "#acabaa",
              "tertiary-fixed": "#ddddfe",
              "surface-container-lowest": "#000000",
              "on-primary-fixed": "#4700bd",
              "outline": "#767575",
              "on-primary-container": "#d6c9ff",
              "on-background": "#e7e5e4",
              "tertiary-dim": "#cecfef",
              "primary": "#cdbdff",
              "primary-fixed": "#e8deff",
              "secondary-fixed-dim": "#d8d3d8",
              "secondary-dim": "#a09da1",
              "surface-container-high": "#1f2020",
              "error-container": "#7f2737",
              "surface-container-low": "#131313",
              "tertiary-container": "#ddddfe",
              "on-primary": "#4800bf",
              "surface-container": "#191a1a",
              "error": "#ec7c8a",
              "surface-variant": "#252626",
              "surface": "#0e0e0e",
              "on-error": "#490013",
              "secondary": "#a09da1",
              "on-surface": "#e7e5e4"
            },
            fontFamily: {
              "headline": ["Manrope"],
              "body": ["Inter"]
            },
            borderRadius: {"DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { background-color: #0e0e0e; color: #e7e5e4; font-family: 'Inter', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="flex min-h-screen" data-admin-page="users">
<div data-admin-sidebar></div>
<main class="ml-64 flex-1 flex flex-col">
<!-- TopAppBar -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 bg-[#0e0e0e]/70 backdrop-blur-xl flex justify-between items-center h-20 px-12">
<div class="flex items-center gap-4">
<h2 class="font-['Manrope'] font-semibold text-lg text-on-surface">Gestionar Usuarios</h2>
<span class="px-3 py-1 bg-surface-container-high text-primary text-xs font-bold rounded-full" data-admin-users-total>Cargando...</span>
</div>
<div class="flex items-center gap-6">
<div class="relative group">
<span class="absolute inset-y-0 left-3 flex items-center text-on-surface-variant">
<span class="material-symbols-outlined text-xl">search</span>
</span>
<input class="bg-surface-container-lowest border-none rounded-full py-2 pl-10 pr-4 text-sm w-64 focus:ring-1 ring-primary/20 transition-all placeholder:text-outline" placeholder="Buscar usuarios..." type="text" data-admin-users-search/>
</div>
</div>
</header>
<section class="mt-20 p-12 space-y-10">
<!-- Summary Bento -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
<div class="bg-surface-container-low p-6 rounded-lg border-none shadow-sm hover:bg-surface-container-high transition-colors group">
<h3 class="text-on-surface-variant text-sm font-medium">Nuevos Registros</h3>
<p class="text-2xl font-bold font-headline mt-1" id="stat-new">0</p>
<p class="text-xs text-outline mt-2 italic">Ultimos 7 dias</p>
</div>
<div class="bg-surface-container-low p-6 rounded-lg border-none shadow-sm hover:bg-surface-container-high transition-colors group">
<h3 class="text-on-surface-variant text-sm font-medium">Activos Ahora</h3>
<p class="text-2xl font-bold font-headline mt-1" id="stat-active">0</p>
<p class="text-xs text-outline mt-2 italic">Tasa de actividad</p>
</div>
<div class="bg-surface-container-low p-6 rounded-lg border-none shadow-sm hover:bg-surface-container-high transition-colors group">
<h3 class="text-on-surface-variant text-sm font-medium">Restringidos</h3>
<p class="text-2xl font-bold font-headline mt-1" id="stat-banned">0</p>
<p class="text-xs text-outline mt-2 italic">Cuentas bloqueadas</p>
</div>
<div class="bg-surface-container-low p-6 rounded-lg border-none shadow-sm hover:bg-surface-container-high transition-colors group">
<h3 class="text-on-surface-variant text-sm font-medium">Administradores</h3>
<p class="text-2xl font-bold font-headline mt-1" id="stat-admin">0</p>
<p class="text-xs text-outline mt-2 italic">Personal del sitio</p>
</div>
</div>
<!-- Main Table Section -->
<div class="bg-surface-container-low rounded-lg overflow-hidden border-none shadow-2xl">
<div class="p-8 border-b border-outline-variant/10 flex justify-between items-center">
<div>
<h3 class="text-xl font-headline font-bold">Información de Usuarios</h3>
<p class="text-sm text-on-surface-variant mt-1">Administra los usuarios registrados en el sistema.</p>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container/50">
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline">USUARIO</th>
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline">CORREO ELECTRÓNICO</th>
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline">FECHA DE UNIÓN</th>
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline">ESTADO</th>
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline text-right">ACCIONES</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/5">
<!-- Template Row (Hidden) -->
<tr id="row-template" class="hidden group hover:bg-surface-container-high/50 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold overflow-hidden" data-avatar>?</div>
<div>
<p class="font-bold text-on-surface" data-name>Nombre</p>
<p class="text-xs text-outline italic" data-role>Rol</p>
</div>
</div>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant" data-email>correo@ejemplo.com</td>
<td class="px-8 py-5 text-sm text-on-surface-variant" data-date>Hoy</td>
<td class="px-8 py-5">
<span class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded-full border" data-status>ACTIVO</span>
</td>
<td class="px-8 py-5 text-right">
<div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant transition-colors" title="Ver" data-admin-user-view>
<span class="material-symbols-outlined text-lg">visibility</span>
</button>
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant transition-colors" title="Bloquear" data-admin-user-block>
<span class="material-symbols-outlined text-lg">block</span>
</button>
<button class="p-2 hover:bg-error/10 rounded-full text-on-surface-variant hover:text-error transition-colors" title="Eliminar" data-admin-user-delete>
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-8 border-t border-outline-variant/10 flex items-center justify-between">
<p class="text-xs text-outline" data-admin-users-footer>Cargando usuarios...</p>
</div>
</div>
</section>
</main>
<div class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm" data-admin-users-modal>
  <div class="w-[min(560px,92vw)] rounded-2xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-2xl font-headline font-bold text-on-surface" data-admin-users-modal-name>Usuario</h3>
      </div>
      <button class="w-9 h-9 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant hover:text-on-surface" data-admin-users-modal-close>
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>
    <div class="mt-6 grid grid-cols-1 gap-3 text-sm text-on-surface-variant">
      <div><span class="text-on-surface">Correo:</span> <span data-admin-users-modal-email></span></div>
      <div><span class="text-on-surface">Rol:</span> <span data-admin-users-modal-role></span></div>
      <div><span class="text-on-surface">Estado:</span> <span data-admin-users-modal-status></span></div>
      <div><span class="text-on-surface">Fecha de unión:</span> <span data-admin-users-modal-date></span></div>
    </div>
  </div>
</div>
<script>
  (async function () {
    const tbody = document.querySelector('tbody');
    const template = document.getElementById('row-template');
    const searchInput = document.querySelector('[data-admin-users-search]');
    const totalBadge = document.querySelector('[data-admin-users-total]');
    const footer = document.querySelector('[data-admin-users-footer]');
    const modal = document.querySelector('[data-admin-users-modal]');
    
    let stateRows = [];

    // Cargar Usuarios
    try {
        const res = await fetch('api/users.php?action=list');
        const data = await res.json();
        if (data.success) {
            renderUsers(data.data);
            updateStats(data.data);
        } else {
            console.error("Error cargando usuarios:", data.error);
            totalBadge.textContent = "Error";
        }
    } catch (e) {
        console.error("Fallo de red", e);
    }

    function renderUsers(users) {
        tbody.innerHTML = '';
        tbody.appendChild(template); // keep template
        stateRows = [];
        
        users.forEach(u => {
            const row = template.cloneNode(true);
            row.id = '';
            row.classList.remove('hidden');
            row.dataset.userId = u.id;
            
            row.querySelector('[data-name]').textContent = u.name;
            row.querySelector('[data-role]').textContent = u.role;
            row.querySelector('[data-email]').textContent = u.email;
            row.querySelector('[data-date]').textContent = u.date;
            row.querySelector('[data-avatar]').textContent = u.name.charAt(0).toUpperCase();
            
            const stNode = row.querySelector('[data-status]');
            
            if (u.status === 'BLOQUEADO') {
                stNode.textContent = 'BLOQUEADO';
                stNode.className = 'px-3 py-1 bg-error/10 text-error text-[10px] font-bold uppercase tracking-widest rounded-full border border-error/20';
                row.classList.add('bg-error/5');
            } else {
                stNode.textContent = 'ACTIVO';
                stNode.className = 'px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20';
            }
            
            tbody.appendChild(row);
            stateRows.push(row);
        });

        totalBadge.textContent = `${users.length} en Total`;
        footer.textContent = `Mostrando ${users.length} usuarios`;
    }

    function updateStats(users) {
        document.getElementById('stat-active').textContent = users.filter(u => u.status === 'ACTIVO').length;
        document.getElementById('stat-banned').textContent = users.filter(u => u.status === 'BLOQUEADO').length;
        document.getElementById('stat-admin').textContent = users.filter(u => u.role === 'Admin').length;
        document.getElementById('stat-new').textContent = users.length; // Simplified
    }

    // Buscador
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const term = searchInput.value.trim().toLowerCase();
        stateRows.forEach((row) => {
          row.style.display = !term || row.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
      });
    }

    // Handlers
    document.addEventListener('click', async (e) => {
      const viewBtn = e.target.closest('[data-admin-user-view]');
      const blockBtn = e.target.closest('[data-admin-user-block]');
      const delBtn = e.target.closest('[data-admin-user-delete]');

      if (delBtn) {
        const row = delBtn.closest('tr');
        const id = row.dataset.userId;
        if (!confirm('¿Estás seguro de eliminar este usuario de la base de datos?')) return;
        
        try {
            const res = await fetch('api/users.php?action=delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const data = await res.json();
            if (data.success) { row.remove(); } else { alert(data.error); }
        } catch (err) { alert('Error de conexión'); }
        return;
      }

      if (blockBtn) {
        const row = blockBtn.closest('tr');
        const id = row.dataset.userId;
        const statusEl = row.querySelector('[data-status]');
        const isCurrentlyActive = statusEl.textContent === 'ACTIVO';
        const newStatus = isCurrentlyActive ? 0 : 1;
        
        try {
            const res = await fetch('api/users.php?action=toggle_status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, activo: newStatus })
            });
            const data = await res.json();
            if (data.success) {
                if (!isCurrentlyActive) {
                    statusEl.textContent = 'ACTIVO';
                    statusEl.className = 'px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20';
                    row.classList.remove('bg-error/5');
                } else {
                    statusEl.textContent = 'BLOQUEADO';
                    statusEl.className = 'px-3 py-1 bg-error/10 text-error text-[10px] font-bold uppercase tracking-widest rounded-full border border-error/20';
                    row.classList.add('bg-error/5');
                }
            } else { alert(data.error); }
        } catch (err) { alert('Error de conexión'); }
        return;
      }

      if (viewBtn) {
        const row = viewBtn.closest('tr');
        modal.querySelector('[data-admin-users-modal-name]').textContent = row.querySelector('[data-name]').textContent;
        modal.querySelector('[data-admin-users-modal-email]').textContent = row.querySelector('[data-email]').textContent;
        modal.querySelector('[data-admin-users-modal-role]').textContent = row.querySelector('[data-role]').textContent;
        modal.querySelector('[data-admin-users-modal-status]').textContent = row.querySelector('[data-status]').textContent;
        modal.querySelector('[data-admin-users-modal-date]').textContent = row.querySelector('[data-date]').textContent;
        modal.classList.remove('hidden');
      }
    });

    const closeBtn = document.querySelector('[data-admin-users-modal-close]');
    if (closeBtn) closeBtn.addEventListener('click', () => modal.classList.add('hidden'));

  })();
</script>
<script src="controllers/admin-layout.js"></script>
</body></html>
