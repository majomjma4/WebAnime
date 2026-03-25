<!DOCTYPE html>

<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link rel="icon" href="img/icon3.png" />
<title>NekoraList - Añadir Nuevo Anime</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&amp;family=Inter:wght@400;500;600&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        body {
            background-color: #0e0e0e;
            color: #e7e5e4;
            font-family: 'Inter', sans-serif;
        }
        .font-headline { font-family: 'Manrope', sans-serif; }
        .glass-panel {
            background: rgba(19, 19, 19, 0.6);
            backdrop-filter: blur(20px);
        }
    </style>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "surface-variant": "#252626",
              "surface-container-highest": "#252626",
              "secondary-fixed": "#e6e1e6",
              "on-secondary-container": "#c2bec3",
              "tertiary": "#edecff",
              "surface": "#0e0e0e",
              "surface-dim": "#0e0e0e",
              "primary-fixed": "#e8deff",
              "on-primary-fixed": "#4700bd",
              "inverse-primary": "#6834eb",
              "on-surface-variant": "#acabaa",
              "primary-dim": "#c0adff",
              "on-secondary": "#211f23",
              "tertiary-container": "#ddddfe",
              "on-tertiary-container": "#4c4e68",
              "tertiary-fixed": "#ddddfe",
              "secondary-dim": "#a09da1",
              "on-tertiary-fixed": "#3a3c55",
              "background": "#0e0e0e",
              "surface-container-lowest": "#000000",
              "secondary": "#a09da1",
              "secondary-fixed-dim": "#d8d3d8",
              "inverse-surface": "#fcf8f8",
              "on-error": "#490013",
              "on-secondary-fixed": "#403e42",
              "on-primary-fixed-variant": "#652fe7",
              "secondary-container": "#3c3b3e",
              "tertiary-fixed-dim": "#cecfef",
              "primary-fixed-dim": "#dacdff",
              "on-error-container": "#ff97a3",
              "outline": "#767575",
              "on-background": "#e7e5e4",
              "tertiary-dim": "#cecfef",
              "surface-container-low": "#131313",
              "primary-container": "#4f00d0",
              "inverse-on-surface": "#565554",
              "surface-bright": "#2c2c2c",
              "error-dim": "#b95463",
              "error": "#ec7c8a",
              "surface-tint": "#cdbdff",
              "surface-container-high": "#1f2020",
              "on-primary-container": "#d6c9ff",
              "on-primary": "#4800bf",
              "on-secondary-fixed-variant": "#5d5b5f",
              "error-container": "#7f2737",
              "on-tertiary-fixed-variant": "#565873",
              "surface-container": "#191a1a",
              "primary": "#cdbdff",
              "on-tertiary": "#555671",
              "outline-variant": "#484848",
              "on-surface": "#e7e5e4"
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
</head>
<body class="flex min-h-screen" data-admin-page="add">
<!-- SideNavBar Component -->
<div data-admin-sidebar></div>
<main class="ml-64 flex-1 relative min-h-screen">
<!-- TopNavBar (JSON Derived) -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 flex justify-between items-center px-8 h-20 bg-[#0e0e0e]/60 backdrop-blur-xl shadow-[0px_20px_40px_rgba(0,0,0,0.4)]">
<div class="flex items-center gap-4 bg-surface-container-lowest px-4 py-2 rounded-full w-96 focus-within:ring-2 focus-within:ring-[#7c4dff]/20 transition-all">
<span class="material-symbols-outlined text-on-surface-variant" data-icon="search">search</span>
<input class="bg-transparent border-none focus:ring-0 text-sm w-full text-on-surface" placeholder="Search catalog..." type="text"/>
</div>
</header>
<!-- Form Content -->
<section class="pt-32 pb-20 px-12 max-w-6xl mx-auto">
<div class="flex flex-col gap-2 mb-12">
<h1 class="text-5xl font-headline font-extrabold tracking-tight text-on-surface">Añadir Nuevo Anime</h1>
<p class="text-on-surface-variant text-lg">Completa los detalles para catalogar una nueva obra maestra.</p>
</div>
<form class="grid grid-cols-12 gap-8">
<!-- Left Column: Core Data & Imagery -->
<div class="col-span-12 lg:col-span-8 space-y-8">
<!-- Basic Info Section -->
<div class="bg-surface-container-low p-8 rounded-lg shadow-sm space-y-6">
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Ttulo del Anime</label>
<input class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 transition-all text-on-surface placeholder:text-on-surface-variant/40" placeholder="Ej: Neon Genesis Evangelion" type="text"/>
</div>
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Sinopsis / Descripcin</label>
<textarea class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 transition-all text-on-surface placeholder:text-on-surface-variant/40 resize-none" placeholder="Escribe una descripcin detallada que cautive a la audiencia..." rows="6"></textarea>
</div>
</div>
<!-- Media Section -->
<div class="bg-surface-container-low p-8 rounded-lg shadow-sm">
<label class="text-sm font-bold uppercase tracking-wider text-primary mb-6 block">Material Visual</label>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- Hero Banner Upload -->
<div class="space-y-4">
<p class="text-sm font-medium text-on-surface-variant">Hero Banner (Landscape)</p>
<div class="relative group aspect-video rounded-lg overflow-hidden bg-surface-container-lowest border-2 border-dashed border-outline-variant/30 flex flex-col items-center justify-center cursor-pointer hover:border-primary/50 transition-all">
<div class="text-center z-10 p-6">
<span class="material-symbols-outlined text-4xl text-primary mb-2" data-icon="landscape">landscape</span>
<p class="text-xs font-bold uppercase tracking-tighter">Click para subir banner</p>
<p class="text-[10px] text-on-surface-variant mt-1">Recomendado: 1920x1080px</p>
</div>
<div class="absolute inset-0 opacity-20 group-hover:opacity-40 transition-opacity">
<img alt="Hero Placeholder" class="w-full h-full object-cover" data-alt="Abstract cinematic landscape with deep purple and indigo clouds and misty horizon" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCPJf4oPZ3nuDEyzU3pKoVmK4cFondkJuweY1F-1Ex9VopZeDVn84YUXf4suA7-juu8szXKTLAAxwXvyX7Eg-33N3v_VvzKpfYjsTRk1kMP6zSJdVVMwWgZhvHkcYto14c-DnKEtX4R2FbOVegj7OONs9xkxParIlV1Q_Gf0jFntylNcCfwANBOMwfFFmyC2u7QxR3uPWrUamCKBi4ZsOrp6FEtYgiyFDOHWuVPaW50H3QQp2zY23J4TSCPst36qkwOusbGpUMZhvyO"/>
</div>
</div>
</div>
<!-- Poster Upload -->
<div class="space-y-4">
<p class="text-sm font-medium text-on-surface-variant">Poster (Portrait)</p>
<div class="relative group aspect-[2/3] rounded-lg overflow-hidden bg-surface-container-lowest border-2 border-dashed border-outline-variant/30 flex flex-col items-center justify-center cursor-pointer hover:border-primary/50 transition-all">
<div class="text-center z-10 p-6">
<span class="material-symbols-outlined text-4xl text-primary mb-2" data-icon="portrait">portrait</span>
<p class="text-xs font-bold uppercase tracking-tighter">Click para subir poster</p>
<p class="text-[10px] text-on-surface-variant mt-1">Recomendado: 800x1200px</p>
</div>
<div class="absolute inset-0 opacity-20 group-hover:opacity-40 transition-opacity">
<img alt="Poster Placeholder" class="w-full h-full object-cover" data-alt="Futuristic neon city poster background with vertical lines and vibrant cyan and magenta lights" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQwqxXQobjAMYVXhopPu_olwb7heHFkuZulMsD4vtm8FofDrKGBJxx3_ja6vHaoMg-rrA0ca00nbpuPhF-SB4QPrDMlm_Go2WbYhKyDdDGNMaUnRanMSMl3qMDtALrnzDhFqUxItZJ30CTKL7g6yPtPze14u8BIh3eAkaYAM3vYSUJ6_9FLJvlbFdzbwm57-hWGD4rLiwZo0hhJZ7nxy7QZcNLD6dr6pkBUPekJmYamS7AwSZMaAdqJaJpsOq2V9Zc6jJiWxMczy8j"/>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Right Column: Metadata & Categorization -->
<div class="col-span-12 lg:col-span-4 space-y-8">
<!-- Technical Specs Section -->
<div class="bg-surface-container-low p-8 rounded-lg shadow-sm space-y-6">
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Estado</label>
<select class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface appearance-none cursor-pointer">
<option>Airing</option>
<option>Finished</option>
<option>To be announced</option>
</select>
</div>
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Estudio</label>
<select class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface appearance-none cursor-pointer">
<option>MAPPA</option>
<option>Ufotable</option>
<option>Wit Studio</option>
<option>CloverWorks</option>
<option>Madhouse</option>
</select>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Temporada</label>
<select class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface appearance-none cursor-pointer">
<option>Invierno</option>
<option>Primavera</option>
<option>Verano</option>
<option>Otoo</option>
</select>
</div>
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Ao</label>
<input class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface" type="number" value="2024"/>
</div>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Episodios</label>
<div class="relative">
<input class="w-full bg-surface-container-lowest border-none rounded-lg p-4 pl-12 focus:ring-2 focus:ring-primary/40 text-on-surface" placeholder="0" type="number"/>
<span class="material-symbols-outlined absolute left-4 top-4 text-on-surface-variant text-xl" data-icon="format_list_numbered">format_list_numbered</span>
</div>
</div>
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Duracin (min)</label>
<div class="relative">
<input class="w-full bg-surface-container-lowest border-none rounded-lg p-4 pl-12 focus:ring-2 focus:ring-primary/40 text-on-surface" placeholder="24" type="number"/>
<span class="material-symbols-outlined absolute left-4 top-4 text-on-surface-variant text-xl" data-icon="timer">timer</span>
</div>
</div>
</div>
</div>
<!-- Genres Section -->
<div class="bg-surface-container-low p-8 rounded-lg shadow-sm">
<label class="text-sm font-bold uppercase tracking-wider text-primary mb-4 block">Gneros</label>
<div class="flex flex-wrap gap-2">
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer hover:border-primary/50 hover:bg-surface-container-high transition-all">
<input class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant group-hover:text-on-surface">Accin</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer hover:border-primary/50 hover:bg-surface-container-high transition-all">
<input class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant group-hover:text-on-surface">Aventura</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer hover:border-primary/50 hover:bg-surface-container-high transition-all">
<input class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant group-hover:text-on-surface">Romance</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer hover:border-primary/50 hover:bg-surface-container-high transition-all">
<input class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant group-hover:text-on-surface">Sci-Fi</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer hover:border-primary/50 hover:bg-surface-container-high transition-all">
<input class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant group-hover:text-on-surface">Drama</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer hover:border-primary/50 hover:bg-surface-container-high transition-all">
<input class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant group-hover:text-on-surface">Fantasa</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer hover:border-primary/50 hover:bg-surface-container-high transition-all">
<input class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant group-hover:text-on-surface">Seinen</span>
</label>
</div>
</div>
<!-- Actions -->
<div class="bg-surface-container-high p-4 rounded-lg flex flex-col gap-3 mt-4">
<button class="w-full py-4 bg-gradient-to-br from-[#cdbdff] to-[#4f00d0] text-white rounded-full font-headline font-extrabold text-sm uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/20" type="submit" data-admin-save>
                            Subir Anime
                        </button>
</div>
</div>
</form>
</section>
</main>
<div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm" data-admin-exit-modal>
  <div class="w-[min(520px,92vw)] rounded-2xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl text-center">
    <h3 class="text-xl font-headline font-bold text-on-surface">¿Esta seguro de que desea salir?</h3>
    <p class="text-sm text-on-surface-variant mt-2">Los cambios no guardados se perdern.</p>
    <div class="mt-6 flex items-center justify-center gap-3">
      <button class="px-5 py-2 rounded-full border border-outline-variant/30 text-on-surface-variant text-xs uppercase tracking-widest" data-admin-exit-cancel>Cancelar</button>
      <button class="px-5 py-2 rounded-full bg-primary text-on-primary text-xs uppercase tracking-widest font-bold" data-admin-exit-confirm>Salir</button>
    </div>
  </div>
</div>
<div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" data-admin-save-toast>
  <div class="rounded-2xl bg-surface-container-high px-6 py-3 text-sm font-bold text-on-surface shadow-xl border border-primary/20">
    Anime subido exitosamente
  </div>
</div>
<script>
  (function () {
    const form = document.querySelector('form');
    const toast = document.querySelector('[data-admin-save-toast]');
    if (!form || !toast) return;
    let isDirty = false;
    let pendingHref = null;
    const exitModal = document.querySelector('[data-admin-exit-modal]');
    const exitCancel = document.querySelector('[data-admin-exit-cancel]');
    const exitConfirm = document.querySelector('[data-admin-exit-confirm]');

    form.addEventListener('input', () => { isDirty = true; }, true);
    form.addEventListener('change', () => { isDirty = true; }, true);

    document.addEventListener('click', (e) => {
      const link = e.target.closest('a[href]');
      if (!link) return;
      const href = link.getAttribute('href');
      if (!href) return;
      if (href === '#' || href.startsWith('#')) return;
      if (!isDirty) return;
      e.preventDefault();
      pendingHref = href;
      if (exitModal) {
        exitModal.classList.remove('hidden');
        exitModal.classList.add('flex');
      }
    }, true);

    if (exitCancel) {
      exitCancel.addEventListener('click', () => {
        if (exitModal) {
          exitModal.classList.add('hidden');
          exitModal.classList.remove('flex');
        }
        pendingHref = null;
      });
    }
    if (exitConfirm) {
      exitConfirm.addEventListener('click', () => {
        const go = pendingHref;
        pendingHref = null;
        if (exitModal) {
          exitModal.classList.add('hidden');
          exitModal.classList.remove('flex');
        }
        if (go) window.location.href = go;
      });
    }

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      isDirty = false;
      toast.classList.remove('hidden');
      toast.classList.add('flex');
      setTimeout(() => {
        toast.classList.add('hidden');
        toast.classList.remove('flex');
      }, 2200);
    });
  })();
</script>
<script src="controllers/admin-layout.js"></script>
</body></html>




