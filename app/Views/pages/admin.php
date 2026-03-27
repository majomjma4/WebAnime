<!DOCTYPE html>

<html class="dark" lang="en"><head>
<link rel="icon" href="img/icon3.png" />
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>NekoraList - Solicitudes</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
              "tertiary-fixed-dim": "#cecfef",
              "error-container": "#7f2737",
              "on-tertiary-fixed-variant": "#565873",
              "surface-container-low": "#131313",
              "tertiary-container": "#ddddfe",
              "on-primary": "#4800bf",
              "surface-container": "#191a1a",
              "inverse-primary": "#6834eb",
              "on-error-container": "#ff97a3",
              "on-secondary-fixed": "#403e42",
              "on-secondary": "#211f23",
              "background": "#0e0e0e",
              "primary-container": "#4f00d0",
              "outline-variant": "#484848",
              "tertiary": "#edecff",
              "on-primary-fixed-variant": "#652fe7",
              "on-secondary-fixed-variant": "#5d5b5f",
              "surface-tint": "#cdbdff",
              "on-tertiary-container": "#4c4e68",
              "inverse-on-surface": "#565554",
              "surface-bright": "#2c2c2c",
              "error": "#ec7c8a",
              "secondary-fixed": "#e6e1e6",
              "primary-fixed-dim": "#dacdff",
              "surface-variant": "#252626",
              "surface": "#0e0e0e",
              "on-error": "#490013",
              "surface-container-highest": "#252626",
              "primary-dim": "#c0adff",
              "on-tertiary-fixed": "#3a3c55",
              "secondary": "#a09da1",
              "inverse-surface": "#fcf8f8",
              "on-surface": "#e7e5e4",
              "surface-dim": "#0e0e0e",
              "on-secondary-container": "#c2bec3"
            },
            fontFamily: {
              "headline": ["Manrope"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
  body {
    background-color: #0e0e0e;
    color: #e7e5e4;
    font-family: 'Inter', sans-serif;
  }
  .editorial-shadow {
    box-shadow: 0px 20px 40px rgba(0, 0, 0, 0.4);
  }
</style>


</head>
<body class="overflow-x-hidden" data-admin-page="requests">
<!-- SideNavBar Component -->
<div data-admin-sidebar></div>
<main class="ml-64 min-h-screen">
<!-- TopAppBar Component -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 bg-[#0e0e0e]/70 backdrop-blur-xl flex justify-between items-center h-20 px-12">
<link rel="icon" href="img/icon3.png" />
<h1 class="font-headline font-semibold text-lg text-on-surface">Solicitudes de Anime</h1>
<div class="flex items-center gap-8">
<!-- Search Bar -->
<div class="relative group">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm" data-icon="search">search</span>
<input class="bg-surface-container-lowest text-on-surface text-sm rounded-full py-2.5 pl-12 pr-6 w-64 border-none focus:ring-1 ring-primary/20 transition-all placeholder:text-on-surface-variant/50" placeholder="Buscar solicitudes..." data-admin-search type="text"/>
</div>
</div>
<!-- Trailing Actions -->
</header>
<!-- Dashboard Canvas -->
<div class="pt-28 px-12 pb-12">
<!-- Welcome/Stats Bento -->
<section class="grid grid-cols-12 gap-6 mb-12">
<div class="col-span-8 bg-surface-container-low p-8 rounded-lg flex justify-between items-end relative overflow-hidden group" data-admin-queue>
<div class="relative z-10">
<h2 class="font-headline text-4xl font-extrabold mb-2 text-on-surface tracking-tighter">Resumen de la Cola</h2>
<p class="text-on-surface-variant text-lg font-body max-w-md">Tienes <span class="text-primary font-bold" data-admin-pending-text>24 solicitudes pendientes</span> esperando revisión hoy. Mantén el catálogo actualizado.</p>
</div>
<div class="relative z-10 text-right">
<span class="block text-5xl font-extrabold font-headline text-primary-container/40" data-admin-pending-count>24</span>
<span class="text-[10px] uppercase tracking-[0.2em] font-bold text-on-surface-variant">PENDIENTES TOTAL</span>
</div>
<!-- Decorative Bleed Image -->
<div class="absolute -right-10 -bottom-10 w-64 h-64 opacity-10 group-hover:opacity-20 transition-opacity">
<img alt="Decorative Cinema" class="w-full h-full object-contain" data-alt="Abstract cinematic film reel with light leaks and purple neon glow in a dark studio setting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAE4jby1eBcNnMnbOqDXjXXE5c4ZNhhUlyI9J1pzEy3d_EDkNdqv_hB-LpDCk-LhHWfrFVl-CoVKsNy-dy_2NrgYrfZzZmy3jyXJBoFNUFOpSP134uB53-AA9tH5p45TuM_P2lXruPSTxcKkcOvgXTlic1NBHDzoxjV_qw_S7OpGoP8sz04h5TuxAMWLy8hpxf8P3e5x7ZDW61Xd_NuCcfBo-1LmypNMLbMfLveDtD4qZX5kFSZBU63sxbSVemj99wX8zgZZkTqccjh"/>
</div>
</div>
<div class="col-span-4 bg-primary-container p-8 rounded-lg flex flex-col justify-between shadow-xl shadow-primary-container/10 cursor-pointer" data-admin-quick-action>
<span class="material-symbols-outlined text-on-primary-container text-4xl" data-icon="auto_awesome">auto_awesome</span>
<div>
<h3 class="font-headline font-bold text-on-primary-container text-xl uppercase tracking-tight">ACCIÓN RÁPIDA</h3>
<p class="text-on-primary-container/70 text-sm mt-1">Aprobar automáticamente solicitudes de Curadores de Confianza.</p>
</div>
</div>
</section>
<!-- Requests Table Container -->
<section class="bg-surface-container-low rounded-lg p-1">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="text-on-surface-variant text-[10px] uppercase tracking-[0.2em] font-bold border-b border-outline-variant/10">
<th class="px-8 py-6">USUARIO</th>
<th class="px-8 py-6">TÍTULO SOLICITADO</th>
<th class="px-8 py-6">FECHA DE ENVÍO</th>
<th class="px-8 py-6 text-right">ACCIONES</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/5"></tbody>
</table>
</div>
<!-- Table Footer / Pagination -->
<div class="px-8 py-6 flex justify-between items-center bg-surface-container-low border-t border-outline-variant/10">
<p class="text-xs text-on-surface-variant font-body" data-admin-pending-footer>Mostrando 4 de 24 solicitudes pendientes</p>
<div class="flex items-center gap-2">
<button class="w-8 h-8 rounded-full border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors disabled:opacity-20" data-admin-page-prev>
<span class="material-symbols-outlined text-sm" data-icon="chevron_left">chevron_left</span>
</button>
<button class="w-8 h-8 rounded-full border border-outline-variant/20 flex items-center justify-center text-on-surface hover:text-primary hover:border-primary transition-colors" data-admin-page-next>
<span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
</button>
</div>
</div>
</section>
<!-- Bottom Note -->

</div>
</main>
<script src="controllers/admin-layout.js"></script>
<script data-admin-pagination-script>
(function(){
  function initAdmin(){
    const tbody = document.querySelector('tbody');
    if (!tbody) return;
    const total = 6;
    const data = Array.from({length: total}, (_, i) => ({
      name: `Usuario ${i+1}`,
      role: `Curador Nivel ${((i)%5)+1}`,
      title: `Titulo Solicitud ${i+1}`,
      source: `Fuente ${((i)%6)+1}`,
      date: `Oct ${String(24 - (i%24)).padStart(2,'0')}, 2023  -  ${(8 + (i%10))}:${String((i*7)%60).padStart(2,'0')} ${i%2? 'PM':'AM'}`
    }));

    let page = 1;
    const size = 4;

    const emptyText = 'No hay solicitudes por revisar.';
    const upToDateText = 'Estas al dia. No hay peticiones.';

    function render(){
      const start = (page-1)*size;
      const end = start + size;
      const slice = data.slice(start, end);
      let html = '';
      slice.forEach((item, idx) => {
        const n = start + idx + 1;
        html += `
<tr class="group hover:bg-surface-container-high transition-colors" data-admin-request-row data-index="${start+idx}">
  <td class="px-8 py-6">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center text-[10px] font-bold text-primary">${String(n).padStart(2,'0')}</div>
      <div>
        <p class="text-sm font-semibold text-on-surface">${item.name}</p>
        <p class="text-[10px] text-on-surface-variant">${item.role}</p>
      </div>
    </div>
  </td>
  <td class="px-8 py-6">
    <p class="text-base font-bold font-headline text-on-surface tracking-tight">${item.title}</p>
    <p class="text-xs text-on-surface-variant italic">Source: ${item.source}</p>
  </td>
  <td class="px-8 py-6">
    <p class="text-xs text-on-surface-variant font-body">${item.date}</p>
  </td>
  <td class="px-8 py-6">
    <div class="flex justify-end items-center gap-3">
      <button class="w-10 h-10 rounded-full flex items-center justify-center bg-surface-container-highest text-on-surface-variant hover:bg-surface-container-lowest hover:text-error transition-all group/btn" data-admin-reject>
        <span class="material-symbols-outlined text-sm">close</span>
      </button>
      <button class="px-5 py-2 rounded-full bg-surface-container-highest text-on-surface font-headline font-bold text-xs uppercase tracking-tight hover:bg-primary hover:text-on-primary transition-all flex items-center gap-2" data-admin-approve>
        <span class="material-symbols-outlined text-[18px]">check</span>
        APROBAR
      </button>
    </div>
  </td>
</tr>`;
      });
      tbody.innerHTML = html || `<tr><td colspan="4" class="px-8 py-10 text-center text-on-surface-variant" data-admin-empty>${emptyText}</td></tr>`;

      const countEl = document.querySelector('[data-admin-pending-count]');
      const textEl = document.querySelector('[data-admin-pending-text]');
      const footerEl = document.querySelector('[data-admin-pending-footer]');
      if (countEl) countEl.textContent = String(data.length);
      if (textEl) {
        textEl.textContent = data.length
          ? `${data.length} solicitudes pendientes`
          : '0 solicitudes pendientes';
      }
      if (footerEl) {
        footerEl.textContent = data.length
          ? `Mostrando ${slice.length} de ${data.length} solicitudes pendientes`
          : 'Mostrando 0 de 0 solicitudes pendientes';
      }

      const prev = document.querySelector('[data-admin-page-prev]');
      const next = document.querySelector('[data-admin-page-next]');
      const maxPage = Math.max(1, Math.ceil(data.length / size));
      if (prev) prev.disabled = page <= 1;
      if (next) next.disabled = page >= maxPage;

      const queueText = document.querySelector('[data-admin-queue] p');
      if (queueText) {
        queueText.innerHTML = `Tienes <span class="text-primary font-bold" data-admin-pending-text>${data.length} solicitudes pendientes</span> esperando revision hoy. Manten el catalogo actualizado.`;
      }
    }

    render();
    const prev = document.querySelector('[data-admin-page-prev]');
    const next = document.querySelector('[data-admin-page-next]');
    if (prev) prev.addEventListener('click', e => { e.preventDefault(); if (page>1){ page--; render(); } });
    if (next) next.addEventListener('click', e => { e.preventDefault(); const maxPage = Math.max(1, Math.ceil(data.length/size)); if (page<maxPage){ page++; render(); } });

    tbody.addEventListener('click', (e) => {
      const approveBtn = e.target.closest('[data-admin-approve]');
      const rejectBtn = e.target.closest('[data-admin-reject]');
      if (!approveBtn && !rejectBtn) return;
      const row = e.target.closest('[data-admin-request-row]');
      if (!row) return;
      const index = Number(row.getAttribute('data-index'));
      if (Number.isNaN(index)) return;
      data.splice(index, 1);
      const maxPage = Math.max(1, Math.ceil(data.length / size));
      if (page > maxPage) page = maxPage;
      render();
    });

    const quick = document.querySelector('[data-admin-quick-action]');
    if (quick) {
      quick.addEventListener('click', () => {
        data.splice(0, data.length);
        page = 1;
        render();
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAdmin);
  } else {
    initAdmin();
  }
})();
</script>
</body></html>







