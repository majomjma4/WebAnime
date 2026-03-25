<!DOCTYPE html>

<html class="dark" lang="en"><head>
<link rel="icon" href="../img/icon3.png" />
<title>NekoraList - Gestion de Animes</title>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Manrope:wght@600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="flex min-h-screen" data-admin-page="manage">
<!-- SideNavBar Component -->
<div data-admin-sidebar></div>
<main class="flex-1 ml-64 min-h-screen relative overflow-x-hidden">
<!-- TopAppBar Anchor -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 bg-[#0e0e0e]/70 backdrop-blur-xl flex justify-between items-center h-20 px-12">
<link rel="icon" href="../img/icon3.png" />
<title>NekoraList</title>
<div class="flex items-center">
<h2 class="font-['Manrope'] font-semibold text-lg text-on-surface">Gestionar Catlogo</h2>
</div>
<div class="flex items-center gap-8">
<div class="relative group">
<span class="absolute left-4 top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant text-sm" data-icon="search">search</span>
<input class="bg-surface-container-lowest border-none rounded-full py-2 pl-12 pr-6 text-sm w-64 focus:ring-1 ring-primary/20 transition-all text-on-surface" placeholder="Buscar serie, estudio o ID..." type="text" data-admin-manage-search/>
</div>
</div>
</header>
<!-- Page Content -->
<div class="pt-32 px-12 pb-20">
<!-- Hero Header Section -->
<div class="flex justify-between items-end mb-12">
<div>
<span class="text-primary font-bold uppercase tracking-[0.2em] text-xs mb-2 block">VISTA GENERAL DEL CATLOGO</span>
<h3 class="text-5xl font-['Manrope'] font-extrabold tracking-tighter text-on-surface">Administrar Anime</h3>
</div>
<div class="flex gap-4">
<button class="px-8 py-3 bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-semibold rounded-full border border-outline-variant/10 transition-all flex items-center gap-2" data-admin-filter-toggle><span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span>
                        Filtrar</button>
<button class="px-8 py-3 bg-gradient-to-r from-primary to-primary-container text-on-primary font-bold rounded-full shadow-xl shadow-primary/20 hover:scale-105 transition-transform flex items-center gap-2" data-admin-link="aÃ±adir\.php"><span class="material-symbols-outlined" data-icon="add">add</span>
                        Aadir Nuevo Anime</button>
</div>
</div>
<div class="hidden mb-8 rounded-2xl border border-outline-variant/10 bg-surface-container-low p-4" data-admin-filter-panel>
  <div class="flex flex-wrap items-center gap-3">
    <div class="text-[10px] uppercase tracking-[0.3em] text-on-surface-variant">Filtro rapido</div>
    <div class="flex flex-wrap gap-2">
      <button class="px-3 py-1 rounded-full border border-outline-variant/20 text-xs text-on-surface-variant" data-admin-filter-status="ALL">Todos</button>
      <button class="px-3 py-1 rounded-full border border-outline-variant/20 text-xs text-on-surface-variant" data-admin-filter-status="EN EMISION">En emision</button>
      <button class="px-3 py-1 rounded-full border border-outline-variant/20 text-xs text-on-surface-variant" data-admin-filter-status="FINALIZADO">Finalizado</button>
    </div>
    <div class="ml-auto flex items-center gap-2">
      <button class="px-3 py-1 rounded-full border border-outline-variant/20 text-xs text-on-surface-variant" data-admin-filter-clear>Limpiar</button>
    </div>
  </div>
</div>
<!-- Table Section (No-Line Editorial Grid) -->
<div class="bg-surface-container-low rounded-lg overflow-hidden shadow-2xl">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-high/50 text-on-surface-variant uppercase text-[10px] font-bold tracking-[0.2em]">
<th class="py-6 px-8">ID</th>
<th class="py-6 px-8">SERIE</th>
<th class="py-6 px-8">ESTUDIO</th>
<th class="py-6 px-8">FECHA DE ESTRENO</th>
<th class="py-6 px-8">ESTADO</th>
<th class="py-6 px-8 text-right">ACCIONES</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/10">
<!-- Row 1 -->
<tr class="hover:bg-surface-container-high/30 transition-colors group">
<td class="py-6 px-8 font-mono text-xs text-primary/60">#AE-0842</td>
<td class="py-6 px-8">
<div class="flex items-center gap-4">
<div class="w-12 h-16 rounded-md overflow-hidden flex-shrink-0 bg-surface-container-highest">
<img alt="Anime Art" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="vibrant neon anime character illustration with futuristic city backdrop and dramatic purple lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC3rcj7hRUrshz8lpDUVsTJ4swB3bFgjQThTXJ3MZlq17_W0uSf6IHPXD2Y7eWEJS3z_kyZLSLM5I_zWLB4wf0VdvHJAq_1w9edVn3NNp6pCMh6wtwxMgCRpvguPbjzYkwde3JVSjPn6ACZjMprmae8X9HTO_AzKMsr2xd2jWV62RWNq4G5cs0Wptx-kWN67x4onAdY7TvR-VXRRNvy4QcpFGzg09v7fQQn2vkH8LBv2PQ8kf7GTDFpRfX84uMjE3UcOrxNg8Lk3RII"/>
</div>
<div>
<p class="font-bold text-on-surface">Neon Genesis: Reborn</p>
<p class="text-xs text-on-surface-variant">Sci-Fi, Mecha</p>
</div>
</div>
</td>
<td class="py-6 px-8">
<span class="text-on-surface-variant text-sm">Studio Mappa</span>
</td>
<td class="py-6 px-8">
<span class="text-on-surface-variant text-sm">Oct 12, 2023</span>
</td>
<td class="py-6 px-8">
<span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded-full">EN EMISIN</span>
</td>
<td class="py-6 px-8 text-right">
<div class="flex justify-end gap-3">
<button class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-primary/20 hover:text-primary transition-all" data-admin-edit>
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-error/20 hover:text-error transition-all" data-admin-delete>
<span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
</button>
</div>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-surface-container-high/30 transition-colors group">
<td class="py-6 px-8 font-mono text-xs text-primary/60">#AE-0911</td>
<td class="py-6 px-8">
<div class="flex items-center gap-4">
<div class="w-12 h-16 rounded-md overflow-hidden flex-shrink-0 bg-surface-container-highest">
<img alt="Anime Art" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="cinematic anime landscape with a floating castle in a twilight sky with stars and soft purple clouds" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAuN5ApG35ffBZFHNkOkk-EBexHSLBiHL7rueCUh7-S8vWZtRhG6V2vrMBWSHYZWgYJQBydCq5taeTAefkcD6r0X47Yam9EE_CG4EED4l1bvtyjIARAWfrGDxx4CWvJxbGoC4HgK9hsJFLcR7ZEWeDlNXiaeflIkJX58Q0MMoni4my1MfEsuzGjKlhnuUIZSW4GCVPDOctc-yqARXMvntc3UO2UKdf9lsE-U8_Yu3NB8vwfTNZ4v98NPpw7JL4_73zSWOwY8klporsS"/>
</div>
<div>
<p class="font-bold text-on-surface">Celestial Voyage</p>
<p class="text-xs text-on-surface-variant">Fantasy, Adventure</p>
</div>
</div>
</td>
<td class="py-6 px-8">
<span class="text-on-surface-variant text-sm">Ufotable</span>
</td>
<td class="py-6 px-8">
<span class="text-on-surface-variant text-sm">Mar 05, 2023</span>
</td>
<td class="py-6 px-8">
<span class="px-3 py-1 bg-on-surface-variant/10 text-on-surface-variant text-[10px] font-bold uppercase rounded-full">FINALIZADO</span>
</td>
<td class="py-6 px-8 text-right">
<div class="flex justify-end gap-3">
<button class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-primary/20 hover:text-primary transition-all" data-admin-edit>
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-error/20 hover:text-error transition-all" data-admin-delete>
<span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
</button>
</div>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-surface-container-high/30 transition-colors group">
<td class="py-6 px-8 font-mono text-xs text-primary/60">#AE-1025</td>
<td class="py-6 px-8">
<div class="flex items-center gap-4">
<div class="w-12 h-16 rounded-md overflow-hidden flex-shrink-0 bg-surface-container-highest">
<img alt="Anime Art" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="close-up of a stylish anime character wearing tech-wear with glowing purple accents in a rainy cyberpunk alley" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD0KbCjdsW5uqCdj5nguWeWeaoDchYZYUpKOu-Knd745uH9WbHlWkMfgZCGFBlqCPk_J4SRCvzUQezXc1Uzm4lyqjjw9ucGHouoAQiFeY45hmGSCdwPApXgBiYQqjV53i7VM5HKprNL8XhjI1uWlGxkXZBdpGn7VNdbZ6y5avDx7FtMoubdDL_ZanVoqU2gYumzSjQs1xxdDrUh7oM_e5aysyfxex6QVq316mgC7aFhtpYqNobzPgZ0VHYyRhlxKl55SCxSHdGQm2iH"/>
</div>
<div>
<p class="font-bold text-on-surface">Midnight Protocol</p>
<p class="text-xs text-on-surface-variant">Cyberpunk, Thriller</p>
</div>
</div>
</td>
<td class="py-6 px-8">
<span class="text-on-surface-variant text-sm">Wit Studio</span>
</td>
<td class="py-6 px-8">
<span class="text-on-surface-variant text-sm">Jan 18, 2024</span>
</td>
<td class="py-6 px-8">
<span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded-full">EN EMISIN</span>
</td>
<td class="py-6 px-8 text-right">
<div class="flex justify-end gap-3">
<button class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-primary/20 hover:text-primary transition-all" data-admin-edit>
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-error/20 hover:text-error transition-all" data-admin-delete>
<span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
</button>
</div>
</td>
</tr>
<!-- Row 4 -->
<tr class="hover:bg-surface-container-high/30 transition-colors group">
<td class="py-6 px-8 font-mono text-xs text-primary/60">#AE-0754</td>
<td class="py-6 px-8">
<div class="flex items-center gap-4">
<div class="w-12 h-16 rounded-md overflow-hidden flex-shrink-0 bg-surface-container-highest">
<img alt="Anime Art" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="ethereal anime art featuring a girl standing in a field of glowing purple lilies under a massive full moon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCw5sS0sRxhh77PxyLTXAOMvk8ncKN6UKwMNfYO35vwu2jIwkllrJBczmwtEYFjU8pLTMAeeyQ6YaleUhFZLxuqCVOpSVdnLKBrNOARxYwFkxg02YNS5VftqTW57hW8nB0fIB0hgYHjY_1Wh4BKzRtkqXllduq0MMy8IxiCjKJWXwlKrfLm220OPDpEUVszrb9h_F_ceclyI8H5TJlkCA4xC_RdNsBLhvBYRcOARMrLgyBw9x8X-D2rZ8Y1YP2IFw7yZEm_Ktz-U6t9"/>
</div>
<div>
<p class="font-bold text-on-surface">Shadow of Lilies</p>
<p class="text-xs text-on-surface-variant">Drama, Supernatural</p>
</div>
</div>
</td>
<td class="py-6 px-8">
<span class="text-on-surface-variant text-sm">Bones</span>
</td>
<td class="py-6 px-8">
<span class="text-on-surface-variant text-sm">Nov 30, 2022</span>
</td>
<td class="py-6 px-8">
<span class="px-3 py-1 bg-on-surface-variant/10 text-on-surface-variant text-[10px] font-bold uppercase rounded-full">FINALIZADO</span>
</td>
<td class="py-6 px-8 text-right">
<div class="flex justify-end gap-3">
<button class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-primary/20 hover:text-primary transition-all" data-admin-edit>
<span class="material-symbols-outlined text-lg" data-icon="edit">edit</span>
</button>
<button class="w-9 h-9 flex items-center justify-center rounded-full bg-surface-container-high hover:bg-error/20 hover:text-error transition-all" data-admin-delete>
<span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination/Footer -->
<div class="py-6 px-8 bg-surface-container-high/20 flex justify-between items-center border-t border-outline-variant/10">
<p class="text-xs text-on-surface-variant">Mostrando <span class="text-on-surface font-bold">4</span> de <span class="text-on-surface font-bold">4</span> ttulos</p>
<div class="flex gap-2">
<button class="w-10 h-10 flex items-center justify-center rounded-full bg-primary text-on-primary font-bold text-xs">1</button>
</div>
</div>
</div>
<!-- Bento Stats Section -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-12">
<div class="col-span-1 p-6 bg-surface-container-low rounded-lg border border-outline-variant/5">
<p class="text-on-surface-variant text-[10px] uppercase font-bold tracking-widest mb-1">TTULOS TOTALES</p>
<h4 class="text-3xl font-extrabold text-on-surface font-['Manrope']">4</h4>
<div class="mt-4 flex items-center gap-2 text-primary text-xs font-bold"><span class="material-symbols-outlined text-sm" data-icon="trending_up">trending_up</span>
                        +12% desde el mes pasado</div>
</div>
<div class="col-span-1 p-6 bg-surface-container-low rounded-lg border border-outline-variant/5">
<p class="text-on-surface-variant text-[10px] uppercase font-bold tracking-widest mb-1">EMITINDOSE AHORA</p>
<h4 class="text-3xl font-extrabold text-on-surface font-['Manrope']">0</h4>
</div>
<div class="col-span-2 p-6 bg-gradient-to-br from-primary/10 to-transparent rounded-lg border border-primary/10 relative overflow-hidden">
<div class="relative z-10">
<p class="text-primary text-[10px] uppercase font-bold tracking-widest mb-1">ESTADO DEL SISTEMA</p>
<h4 class="text-xl font-bold text-on-surface font-['Manrope'] mb-2">Sincronizacin de Metadatos Activa</h4>
<p class="text-sm text-on-surface-variant max-w-[240px]">La sincronizacin del catlogo global funciona con un 100% de eficiencia. Todos los nodos API operativos.</p>
</div>
<span class="material-symbols-outlined absolute right-[-20px] bottom-[-20px] text-[120px] text-primary/5 rotate-12" data-icon="sync">sync</span>
</div>
</div>
</div>
</main>
<div class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm" data-admin-edit-modal>
  <div class="w-[min(720px,92vw)] rounded-2xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-[10px] uppercase tracking-[0.3em] text-on-surface-variant">Editar titulo</p>
        <h3 class="text-2xl font-headline font-bold text-on-surface">Detalles del anime</h3>
      </div>
      <button class="w-9 h-9 rounded-full bg-surface-container-high flex items-center justify-center text-on-surface-variant hover:text-on-surface" data-admin-edit-close>
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <label class="text-xs text-on-surface-variant">ID
        <input class="mt-2 w-full rounded-xl bg-surface-container-highest border border-outline-variant/20 px-4 py-2 text-sm text-on-surface" data-admin-field="id"/>
      </label>
      <label class="text-xs text-on-surface-variant">Titulo
        <input class="mt-2 w-full rounded-xl bg-surface-container-highest border border-outline-variant/20 px-4 py-2 text-sm text-on-surface" data-admin-field="title"/>
      </label>
      <label class="text-xs text-on-surface-variant">Generos
        <input class="mt-2 w-full rounded-xl bg-surface-container-highest border border-outline-variant/20 px-4 py-2 text-sm text-on-surface" data-admin-field="genres"/>
      </label>
      <label class="text-xs text-on-surface-variant">Estudio
        <input class="mt-2 w-full rounded-xl bg-surface-container-highest border border-outline-variant/20 px-4 py-2 text-sm text-on-surface" data-admin-field="studio"/>
      </label>
      <label class="text-xs text-on-surface-variant">Fecha de estreno
        <input class="mt-2 w-full rounded-xl bg-surface-container-highest border border-outline-variant/20 px-4 py-2 text-sm text-on-surface" data-admin-field="date"/>
      </label>
      <label class="text-xs text-on-surface-variant">Estado
        <select class="mt-2 w-full rounded-xl bg-surface-container-highest border border-outline-variant/20 px-4 py-2 text-sm text-on-surface" data-admin-field="status">
          <option>EN EMISION</option>
          <option>FINALIZADO</option>
          <option>PAUSADO</option>
        </select>
      </label>
      <label class="text-xs text-on-surface-variant">Episodios
        <input class="mt-2 w-full rounded-xl bg-surface-container-highest border border-outline-variant/20 px-4 py-2 text-sm text-on-surface" data-admin-field="episodes"/>
      </label>
      <label class="text-xs text-on-surface-variant">Portada (URL)
        <input class="mt-2 w-full rounded-xl bg-surface-container-highest border border-outline-variant/20 px-4 py-2 text-sm text-on-surface" data-admin-field="cover"/>
      </label>
    </div>
    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
      <button class="px-4 py-2 rounded-full border border-error/40 text-error text-xs uppercase tracking-widest" data-admin-edit-delete>Eliminar titulo</button>
      <div class="flex gap-2">
        <button class="px-4 py-2 rounded-full border border-outline-variant/30 text-on-surface-variant text-xs uppercase tracking-widest" data-admin-edit-cancel>Cancelar</button>
        <button class="px-5 py-2 rounded-full bg-primary text-on-primary text-xs uppercase tracking-widest font-bold" data-admin-edit-save>Guardar cambios</button>
      </div>
    </div>
  </div>
</div>
<script>
  (function () {
    const input = document.querySelector('[data-admin-manage-search]');
    const rows = Array.from(document.querySelectorAll('tbody tr'));
    if (!input || rows.length === 0) return;
    input.addEventListener('input', () => {
      const term = input.value.trim().toLowerCase();
      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = !term || text.includes(term) ? '' : 'none';
      });
    });

    const filterToggle = document.querySelector('[data-admin-filter-toggle]');
    const filterPanel = document.querySelector('[data-admin-filter-panel]');
    const setStatus = (value) => {
      rows.forEach((row) => {
        const statusEl = row.querySelector('td:nth-child(5) span');
        const status = statusEl ? statusEl.textContent.trim().toUpperCase() : '';
        const match = value === 'ALL' || status === value;
        row.style.display = match ? '' : 'none';
      });
    };
    if (filterToggle && filterPanel) {
      filterToggle.addEventListener('click', () => {
        filterPanel.classList.toggle('hidden');
      });
      filterPanel.querySelectorAll('[data-admin-filter-status]').forEach((btn) => {
        btn.addEventListener('click', () => {
          const value = btn.getAttribute('data-admin-filter-status');
          setStatus(value);
        });
      });
      const clearBtn = filterPanel.querySelector('[data-admin-filter-clear]');
      if (clearBtn) {
        clearBtn.addEventListener('click', () => {
          rows.forEach((row) => { row.style.display = ''; });
        });
      }
    }

    const modal = document.querySelector('[data-admin-edit-modal]');
    const closeModal = () => modal && modal.classList.add('hidden');
    const openModal = () => modal && modal.classList.remove('hidden');
    const field = (name) => modal.querySelector(`[data-admin-field="${name}"]`);
    let currentRow = null;

    document.addEventListener('click', (e) => {
      const editBtn = e.target.closest('[data-admin-edit]');
      const deleteBtn = e.target.closest('[data-admin-delete]');
      if (editBtn && editBtn.disabled) return;
      if (deleteBtn && deleteBtn.disabled) return;
      if (deleteBtn) {
        const row = deleteBtn.closest('tr');
        if (row) row.remove();
        return;
      }
      if (!editBtn) return;
      const row = editBtn.closest('tr');
      if (!row) return;
      currentRow = row;
      const cells = row.querySelectorAll('td');
      if (cells[0]) field('id').value = cells[0].textContent.trim();
      if (cells[1]) {
        const title = cells[1].querySelector('p.font-bold');
        const genres = cells[1].querySelector('p.text-xs');
        if (title) field('title').value = title.textContent.trim();
        if (genres) field('genres').value = genres.textContent.trim();
        const img = cells[1].querySelector('img');
        if (img) field('cover').value = img.getAttribute('src') || '';
      }
      if (cells[2]) field('studio').value = cells[2].textContent.trim();
      if (cells[3]) field('date').value = cells[3].textContent.trim();
      if (cells[4]) {
        const status = cells[4].textContent.trim();
        field('status').value = status || 'EN EMISION';
      }
      openModal();
    });

    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });
      const closeBtn = modal.querySelector('[data-admin-edit-close]');
      const cancelBtn = modal.querySelector('[data-admin-edit-cancel]');
      if (closeBtn) closeBtn.addEventListener('click', closeModal);
      if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

      const saveBtn = modal.querySelector('[data-admin-edit-save]');
      if (saveBtn) {
        saveBtn.addEventListener('click', () => {
          if (!currentRow) return;
          const cells = currentRow.querySelectorAll('td');
          if (cells[0]) cells[0].textContent = field('id').value.trim();
          if (cells[1]) {
            const title = cells[1].querySelector('p.font-bold');
            const genres = cells[1].querySelector('p.text-xs');
            if (title) title.textContent = field('title').value.trim();
            if (genres) genres.textContent = field('genres').value.trim();
            const img = cells[1].querySelector('img');
            if (img && field('cover').value.trim()) img.setAttribute('src', field('cover').value.trim());
          }
          if (cells[2]) cells[2].textContent = field('studio').value.trim();
          if (cells[3]) cells[3].textContent = field('date').value.trim();
          if (cells[4]) {
            const statusText = field('status').value.trim();
            cells[4].innerHTML = `<span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded-full">${statusText}</span>`;
          }
          closeModal();
        });
      }

      const deleteInModal = modal.querySelector('[data-admin-edit-delete]');
      if (deleteInModal) {
        deleteInModal.addEventListener('click', () => {
          if (currentRow) currentRow.remove();
          closeModal();
        });
      }
    }
  })();
</script>
<script src="../controllers/admin-layout.js"></script>
</body></html>



