<!DOCTYPE html>

<html class="dark" lang="en"><head>
<link rel="icon" href="img/icon3.png" />
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>NekoraList - Gestión de Usuarios</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500&amp;display=swap" rel="stylesheet"/>
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
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="flex min-h-screen" data-admin-page="users">
<!-- SideNavBar Component -->
<div data-admin-sidebar></div>
<main class="ml-64 flex-1 flex flex-col">
<!-- TopAppBar Anchor -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] z-40 bg-[#0e0e0e]/70 backdrop-blur-xl flex justify-between items-center h-20 px-12">
<link rel="icon" href="img/icon3.png" />
<div class="flex items-center gap-4">
<h2 class="font-['Manrope'] font-semibold text-lg text-on-surface">Gestionar Usuarios</h2>
<span class="px-3 py-1 bg-surface-container-high text-primary text-xs font-bold rounded-full" data-admin-users-total>10 en Total</span>
</div>
<div class="flex items-center gap-6">
<div class="relative group">
<span class="absolute inset-y-0 left-3 flex items-center text-on-surface-variant">
<span class="material-symbols-outlined text-xl" data-icon="search">search</span>
</span>
<input class="bg-surface-container-lowest border-none rounded-full py-2 pl-10 pr-4 text-sm w-64 focus:ring-1 ring-primary/20 transition-all placeholder:text-outline" placeholder="Buscar usuarios..." type="text" data-admin-users-search/>
</div>
</div>
</header>
<!-- Content Canvas -->
<section class="mt-20 p-12 space-y-10">
<!-- Summary Bento -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
<div class="bg-surface-container-low p-6 rounded-lg border-none shadow-sm hover:bg-surface-container-high transition-colors group">
<div class="flex justify-between items-start mb-4">
<div class="p-3 rounded-2xl bg-primary/10 text-primary">
<span class="material-symbols-outlined" data-icon="person_add">person_add</span>
</div>
<span class="text-xs font-bold text-emerald-400">+12%</span>
</div>
<h3 class="text-on-surface-variant text-sm font-medium">Nuevos Registros</h3>
<p class="text-2xl font-bold font-headline mt-1">2</p>
<p class="text-xs text-outline mt-2 italic">Ultimos 7 dias</p>
</div>
<div class="bg-surface-container-low p-6 rounded-lg border-none shadow-sm hover:bg-surface-container-high transition-colors group">
<div class="flex justify-between items-start mb-4">
<div class="p-3 rounded-2xl bg-tertiary-container/10 text-tertiary-container">
<span class="material-symbols-outlined" data-icon="verified_user">verified_user</span>
</div>
<span class="text-xs font-bold text-on-surface-variant">88%</span>
</div>
<h3 class="text-on-surface-variant text-sm font-medium">Activos Ahora</h3>
<p class="text-2xl font-bold font-headline mt-1">7</p>
<p class="text-xs text-outline mt-2 italic">Actividad en vivo</p>
</div>
<div class="bg-surface-container-low p-6 rounded-lg border-none shadow-sm hover:bg-surface-container-high transition-colors group">
<div class="flex justify-between items-start mb-4">
<div class="p-3 rounded-2xl bg-error-container/10 text-error">
<span class="material-symbols-outlined" data-icon="block">block</span>
</div>
<span class="text-xs font-bold text-error-dim">-2%</span>
</div>
<h3 class="text-on-surface-variant text-sm font-medium">Restringidos</h3>
<p class="text-2xl font-bold font-headline mt-1">1</p>
<p class="text-xs text-outline mt-2 italic">Violaciones de politica</p>
</div>
<div class="bg-surface-container-low p-6 rounded-lg border-none shadow-sm hover:bg-surface-container-high transition-colors group">
<div class="flex justify-between items-start mb-4">
<div class="p-3 rounded-2xl bg-primary-container/10 text-on-primary-container">
<span class="material-symbols-outlined" data-icon="star">star</span>
</div>
<span class="text-xs font-bold text-primary">Premium</span>
</div>
<h3 class="text-on-surface-variant text-sm font-medium">Miembros Premium</h3>
<p class="text-2xl font-bold font-headline mt-1">3</p>
<p class="text-xs text-outline mt-2 italic">Suscripciones activas</p>
</div>
</div>
<!-- Main Table Section -->
<div class="bg-surface-container-low rounded-lg overflow-hidden border-none shadow-2xl">
<div class="p-8 border-b border-outline-variant/10 flex justify-between items-center">
<div>
<h3 class="text-xl font-headline font-bold">Informacin de Usuarios</h3>
<p class="text-sm text-on-surface-variant mt-1">Administra los usuarios registrados, revisa su actividad y gestiona sus permisos.</p>
</div>
<div class="flex gap-3">
<button class="px-4 py-2 bg-surface-container-highest text-on-surface text-sm font-bold rounded-full hover:bg-surface-bright transition-colors flex items-center gap-2" data-admin-users-filter-toggle><span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span> Filtros</button>
</div>
</div>
<div class="hidden px-8 pb-6" data-admin-users-filter-panel>
  <div class="rounded-2xl border border-outline-variant/10 bg-surface-container-high/40 p-4 flex flex-wrap items-center gap-3">
    <span class="text-[10px] uppercase tracking-[0.3em] text-on-surface-variant">Filtro rapido</span>
    <button class="px-3 py-1 rounded-full border border-outline-variant/20 text-xs text-on-surface-variant" data-admin-users-filter="ALL">Todos</button>
    <button class="px-3 py-1 rounded-full border border-outline-variant/20 text-xs text-on-surface-variant" data-admin-users-filter="ACTIVO">Activos</button>
    <button class="px-3 py-1 rounded-full border border-outline-variant/20 text-xs text-on-surface-variant" data-admin-users-filter="BANNED">Bloqueados</button>
    <button class="ml-auto px-3 py-1 rounded-full border border-outline-variant/20 text-xs text-on-surface-variant" data-admin-users-filter-clear>Limpiar</button>
  </div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container/50">
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline">USUARIO</th>
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline">CORREO ELECTRNICO</th>
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline">FECHA DE UNIN</th>
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline">ESTADO</th>
<th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-outline text-right">ACCIONES</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/5">
<!-- Row 1 -->
<tr class="group hover:bg-surface-container-high/50 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="h-10 w-10 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold overflow-hidden">
<img class="w-full h-full object-cover" data-alt="professional headshot of a young man with glasses and creative aesthetic in high key lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD6PEzMdoNwt0vrEUzXTHl3P96lrDMYotUA1jfTnoLim-S1Cr69UIR7DAKeayOQqBqB5_hwPPr8uoq_GYV_02a0kiTfherpFPS300FmZP6L4XRyZkoZSQhL1MdZC9VlphOfK2GUSpX4TUP3_MqHmOyILFzGVJCBu6Jo5jUBlE-FQUYhLT2VEHHfgUHfM5Xl9WKrFF5JA9KTuIvyW3DgUg2L7YBOUBJrHVuH98hbWF78leLpEA-y3xjQAnvkf5m8F5HXb8-ELNDESIq7"/>
</div>
<div>
<p class="font-bold text-on-surface">SpikeSpiegel</p>
<p class="text-xs text-outline italic">Curator</p>
</div>
</div>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">spike@bebop.com</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">Oct 24, 2023</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">ACTIVO</span>
</td>
<td class="px-8 py-5 text-right">
<div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant hover:text-primary transition-colors" title="View Profile" data-admin-user-view>
<span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
</button>
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant hover:text-primary transition-colors" title="Block User" data-admin-user-block>
<span class="material-symbols-outlined text-lg" data-icon="block">block</span>
</button>
<button class="p-2 hover:bg-error/10 rounded-full text-on-surface-variant hover:text-error transition-colors" title="Delete User" data-admin-user-delete>
<span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
</button>
</div>
</td>
</tr>
<!-- Row 2 -->
<tr class="group hover:bg-surface-container-high/50 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="h-10 w-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary font-bold overflow-hidden">
<img class="w-full h-full object-cover" data-alt="close-up portrait of a cheerful young woman with bright smile and outdoor natural sunlight" src="https://lh3.googleusercontent.com/aida-public/AB6AXuABHUW-DcoCGoQUU_RwVGUhDaSs32tmlyKEhX5arnAZ-0yRIBzRpdXRjI_Fh0SwnLiq60crjfuYGQR-QhpxDnT3vv4zsdeZAq5ikgBxutR91kl7ae63WwmjJVFcgEqUuu-g-P7roNKvkPoOMo1mZ7GDJ6I0TsBC9tuR1SkuP-wj1VQjNp98fLfQINAc_RYpYbyO7tS6herXNuLSTj8xpzqPlb6KhmLyi3dzSn0nla65Vn2O1axSicVy-cRbTGWt8TFRokTQAdVhT5id"/>
</div>
<div>
<p class="font-bold text-on-surface">Motoko_K</p>
<p class="text-xs text-outline italic">Regular</p>
</div>
</div>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">major@section9.jp</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">Nov 12, 2023</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">ACTIVO</span>
</td>
<td class="px-8 py-5 text-right">
<div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant hover:text-primary transition-colors" title="View Profile" data-admin-user-view>
<span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
</button>
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant hover:text-primary transition-colors" title="Block User" data-admin-user-block>
<span class="material-symbols-outlined text-lg" data-icon="block">block</span>
</button>
<button class="p-2 hover:bg-error/10 rounded-full text-on-surface-variant hover:text-error transition-colors" title="Delete User" data-admin-user-delete>
<span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
</button>
</div>
</td>
</tr>
<!-- Row 3 (Blocked State) -->
<tr class="group bg-error/5 hover:bg-error/10 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="h-10 w-10 rounded-full bg-error-container/20 flex items-center justify-center text-error overflow-hidden">
<img class="w-full h-full object-cover grayscale" data-alt="portrait of a man with serious expression in moody dark studio lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQaOoflmags06nL8OPxeeEvGDxdB1xlDZ6CZGbTp74JmoeBjXtegDsZtyTMJHogPmFc_7PZxHvJUY8ItwnsglsAVScLcdpocVr--EGrYKU1fpyfVUZCB7d-_78yyE3ZP-PR-_oFbKadkCQKiT_r6BSiFEYTwOEsnlAPVl1DFJ6zDZZXRZbX3WyJ9TQUYD1KiKqpjqVhL4SfSL648NqeK2a_OEdgZIk8Uxyn4DcfZ5NpXsrgvMdJeyfGLZPHAQGL13dWLQr-RazkxvO"/>
</div>
<div>
<p class="font-bold text-on-surface">Genz0_W</p>
<p class="text-xs text-error-dim italic">Banned</p>
</div>
</div>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">g.walker@spam.net</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">Dec 01, 2023</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-error/10 text-error text-[10px] font-bold uppercase tracking-widest rounded-full border border-error/20">BLOQUEADO</span>
</td>
<td class="px-8 py-5 text-right">
<div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant hover:text-primary transition-colors" title="View Profile" data-admin-user-view>
<span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
</button>
<button class="p-2 hover:bg-emerald-500/10 rounded-full text-emerald-400 transition-colors" title="Unblock User" data-admin-user-block>
<span class="material-symbols-outlined text-lg" data-icon="lock_open">lock_open</span>
</button>
<button class="p-2 hover:bg-error/10 rounded-full text-error transition-colors" title="Delete User">
<span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
</button>
</div>
</td>
</tr>
<!-- Row 4 -->
<tr class="group hover:bg-surface-container-high/50 transition-colors">
<td class="px-8 py-5">
<div class="flex items-center gap-3">
<div class="h-10 w-10 rounded-full bg-tertiary/20 flex items-center justify-center text-tertiary font-bold overflow-hidden">
<img class="w-full h-full object-cover" data-alt="close-up portrait of a woman with artistic lighting and soft focus background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBWXfD2hwx7g9Hmi4uHyTcIuZ9Z-gx8pp3CeCWB60SR4zTQMH7c7eHnmByndRXPGFU5OJ2XUKnWc6ZWSuWEeHuhuS0zGFP76sMzGc-7NV6LryG82tzXUbV1wcLC7Pujit7LZ3r2RXR9pjvGFPK6siGxf6EWy3oonMAokpm-8bqx3YzpD4bMP7Us8WZfy0JzDYebOS39nixDf4_xfwjZzBwqaZcqC2cVukaSce-_D7BoOeOYZ8RgbLEtQfu8ft-_uD1woGXkLuKbTVXb"/>
</div>
<div>
<p class="font-bold text-on-surface">Lain_Wire</p>
<p class="text-xs text-outline italic">Regular</p>
</div>
</div>
</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">lain@wired.com</td>
<td class="px-8 py-5 text-sm text-on-surface-variant">Jan 05, 2024</td>
<td class="px-8 py-5">
<span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">ACTIVO</span>
</td>
<td class="px-8 py-5 text-right">
<div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant hover:text-primary transition-colors" title="View Profile">
<span class="material-symbols-outlined text-lg" data-icon="visibility">visibility</span>
</button>
<button class="p-2 hover:bg-surface-container-highest rounded-full text-on-surface-variant hover:text-primary transition-colors" title="Block User" data-admin-user-block>
<span class="material-symbols-outlined text-lg" data-icon="block">block</span>
</button>
<button class="p-2 hover:bg-error/10 rounded-full text-on-surface-variant hover:text-error transition-colors" title="Delete User" data-admin-user-delete>
<span class="material-symbols-outlined text-lg" data-icon="delete">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-8 border-t border-outline-variant/10 flex items-center justify-between">
<p class="text-xs text-outline" data-admin-users-footer>Mostrando <span class="text-on-surface font-bold">4</span> de <span class="text-on-surface font-bold">10</span> usuarios</p>
<div class="flex gap-2" data-admin-users-pagination>
<button class="h-10 w-10 flex items-center justify-center rounded-full bg-surface-container-highest text-on-surface-variant" data-admin-users-prev>
<span class="material-symbols-outlined">chevron_left</span>
</button>
<button class="h-10 w-10 flex items-center justify-center rounded-full bg-primary text-on-primary font-bold" data-admin-users-page="1">1</button>
<button class="h-10 w-10 flex items-center justify-center rounded-full bg-surface-container-highest text-on-surface-variant hover:text-on-surface transition-colors" data-admin-users-page="2">2</button>
<button class="h-10 w-10 flex items-center justify-center rounded-full bg-surface-container-highest text-on-surface-variant hover:text-on-surface transition-colors" data-admin-users-page="3">3</button>
<button class="h-10 w-10 flex items-center justify-center rounded-full bg-surface-container-highest text-on-surface-variant hover:text-on-surface transition-colors" data-admin-users-next>
<span class="material-symbols-outlined">chevron_right</span>
</button>
</div>
</div>
</div>
<!-- Activity Heatmap Mockup (Editorial touch) -->
<div class="bg-surface-container-low p-8 rounded-lg border-none">
<div class="flex justify-between items-center mb-8">
<h3 class="text-xl font-headline font-bold">Tendencias de Registro</h3>
<div class="flex gap-4">
<div class="flex items-center gap-2">
<div class="w-3 h-3 rounded-full bg-primary/20"></div>
<span class="text-xs text-outline">Bajo</span>
</div>
<div class="flex items-center gap-2">
<div class="w-3 h-3 rounded-full bg-primary"></div>
<span class="text-xs text-outline">Alto</span>
</div>
</div>
</div>
<div class="flex gap-1 h-32 items-end">
<div class="flex-1 bg-primary/10 rounded-t-sm" style="height: 40%"></div>
<div class="flex-1 bg-primary/20 rounded-t-sm" style="height: 60%"></div>
<div class="flex-1 bg-primary/40 rounded-t-sm" style="height: 30%"></div>
<div class="flex-1 bg-primary/10 rounded-t-sm" style="height: 80%"></div>
<div class="flex-1 bg-primary/60 rounded-t-sm" style="height: 90%"></div>
<div class="flex-1 bg-primary/30 rounded-t-sm" style="height: 50%"></div>
<div class="flex-1 bg-primary/80 rounded-t-sm" style="height: 100%"></div>
<div class="flex-1 bg-primary/40 rounded-t-sm" style="height: 65%"></div>
<div class="flex-1 bg-primary/20 rounded-t-sm" style="height: 45%"></div>
<div class="flex-1 bg-primary/50 rounded-t-sm" style="height: 75%"></div>
<div class="flex-1 bg-primary/90 rounded-t-sm" style="height: 85%"></div>
<div class="flex-1 bg-primary/10 rounded-t-sm" style="height: 20%"></div>
<div class="flex-1 bg-primary/30 rounded-t-sm" style="height: 55%"></div>
<div class="flex-1 bg-primary/100 rounded-t-sm" style="height: 95%"></div>
</div>
<div class="flex justify-between mt-4 text-[10px] text-outline uppercase tracking-widest font-bold">
<span>Lunes</span>
<span>Mircoles</span>
<span>Viernes</span>
<span>Domingo</span>
</div>
</div>
</section>
</main>
<div class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm" data-admin-users-modal>
  <div class="w-[min(560px,92vw)] rounded-2xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-2xl">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-[10px] uppercase tracking-[0.3em] text-on-surface-variant">Perfil</p>
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
      <div><span class="text-on-surface">Fecha de union:</span> <span data-admin-users-modal-date></span></div>
    </div>
  </div>
</div>
<script>
  (function () {
    const searchInput = document.querySelector('[data-admin-users-search]');
    const rows = Array.from(document.querySelectorAll('tbody tr'));
    const desiredTotal = 10;
    if (rows.length < desiredTotal) {
      const template = rows[0]?.cloneNode(true);
      for (let i = rows.length; i < desiredTotal && template; i += 1) {
        const clone = template.cloneNode(true);
        clone.className = template.className.replace('bg-error/5', '');
        rows[0].parentElement.appendChild(clone);
        rows.push(clone);
      }
    }

    const users = [
      { name: 'SpikeSpiegel', role: 'Curator', email: 'spike@bebop.com', date: 'Oct 24, 2023', status: 'ACTIVO' },
      { name: 'Motoko_K', role: 'Regular', email: 'major@section9.jp', date: 'Nov 12, 2023', status: 'ACTIVO' },
      { name: 'Genz0_W', role: 'Banned', email: 'g.walker@spam.net', date: 'Dec 01, 2023', status: 'BLOQUEADO' },
      { name: 'Lain_Wire', role: 'Regular', email: 'lain@wired.com', date: 'Jan 05, 2024', status: 'ACTIVO' },
      { name: 'Yoru_Ai', role: 'Premium', email: 'yoru@nekora.io', date: 'Jan 18, 2024', status: 'ACTIVO' },
      { name: 'Haru_88', role: 'Regular', email: 'haru@nekora.io', date: 'Feb 01, 2024', status: 'ACTIVO' },
      { name: 'Kira_L', role: 'Curator', email: 'kira@nekora.io', date: 'Feb 14, 2024', status: 'ACTIVO' },
      { name: 'Sora_N', role: 'Regular', email: 'sora@nekora.io', date: 'Mar 03, 2024', status: 'ACTIVO' },
      { name: 'Aki_77', role: 'Premium', email: 'aki@nekora.io', date: 'Mar 09, 2024', status: 'ACTIVO' },
      { name: 'Ren_Z', role: 'Regular', email: 'ren@nekora.io', date: 'Mar 20, 2024', status: 'ACTIVO' }
    ];

    rows.slice(0, desiredTotal).forEach((row, idx) => {
      const u = users[idx % users.length];
      const nameEl = row.querySelector('td:nth-child(1) p.font-bold');
      const roleEl = row.querySelector('td:nth-child(1) p.text-xs');
      const emailEl = row.querySelector('td:nth-child(2)');
      const dateEl = row.querySelector('td:nth-child(3)');
      const statusEl = row.querySelector('td:nth-child(4) span');
      if (nameEl) nameEl.textContent = u.name;
      if (roleEl) roleEl.textContent = u.role;
      if (emailEl) emailEl.textContent = u.email;
      if (dateEl) dateEl.textContent = u.date;
      if (statusEl) {
        if (u.status === 'BLOQUEADO') {
          statusEl.textContent = 'BLOQUEADO';
          statusEl.className = 'px-3 py-1 bg-error/10 text-error text-[10px] font-bold uppercase tracking-widest rounded-full border border-error/20';
          row.classList.add('bg-error/5');
        } else {
          statusEl.textContent = 'ACTIVO';
          statusEl.className = 'px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20';
          row.classList.remove('bg-error/5');
        }
      }
    });

    const totalBadge = document.querySelector('[data-admin-users-total]');
    if (totalBadge) totalBadge.textContent = `${desiredTotal} en Total`;
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const term = searchInput.value.trim().toLowerCase();
        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          row.style.display = !term || text.includes(term) ? '' : 'none';
        });
      });
    }

    const filterToggle = document.querySelector('[data-admin-users-filter-toggle]');
    const filterPanel = document.querySelector('[data-admin-users-filter-panel]');
    if (filterToggle && filterPanel) {
      filterToggle.addEventListener('click', () => {
        filterPanel.classList.toggle('hidden');
      });
      filterPanel.querySelectorAll('[data-admin-users-filter]').forEach((btn) => {
        btn.addEventListener('click', () => {
          const val = btn.getAttribute('data-admin-users-filter');
          rows.forEach((row) => {
            const statusEl = row.querySelector('td:nth-child(4) span');
            const status = statusEl ? statusEl.textContent.trim().toUpperCase() : '';
            if (val === 'ALL') {
              row.style.display = '';
            } else if (val === 'ACTIVO') {
              row.style.display = status === 'ACTIVO' ? '' : 'none';
            } else {
              row.style.display = (status === 'BANNED' || status === 'BLOQUEADO') ? '' : 'none';
            }
          });
        });
      });
      const clearBtn = filterPanel.querySelector('[data-admin-users-filter-clear]');
      if (clearBtn) {
        clearBtn.addEventListener('click', () => rows.forEach((row) => { row.style.display = ''; }));
      }
    }

    const footer = document.querySelector('[data-admin-users-footer]');
    const prevBtn = document.querySelector('[data-admin-users-prev]');
    const nextBtn = document.querySelector('[data-admin-users-next]');
    const pageBtns = Array.from(document.querySelectorAll('[data-admin-users-page]'));
    const pageSize = 4;
    let currentPage = 1;
    const maxPage = Math.max(1, Math.ceil(desiredTotal / pageSize));

    const updatePagination = () => {
      const start = (currentPage - 1) * pageSize;
      const end = start + pageSize;
      rows.forEach((row, idx) => {
        row.style.display = idx >= start && idx < end ? '' : 'none';
      });
      if (footer) footer.innerHTML = `Mostrando <span class="text-on-surface font-bold">${start + 1}-${Math.min(end, desiredTotal)}</span> de <span class="text-on-surface font-bold">${desiredTotal}</span> usuarios`;
      if (prevBtn) prevBtn.disabled = currentPage <= 1;
      if (nextBtn) nextBtn.disabled = currentPage >= maxPage;
      pageBtns.forEach((btn) => {
        const isActive = Number(btn.getAttribute('data-admin-users-page')) === currentPage;
        btn.className = isActive
          ? 'h-10 w-10 flex items-center justify-center rounded-full bg-primary text-on-primary font-bold'
          : 'h-10 w-10 flex items-center justify-center rounded-full bg-surface-container-highest text-on-surface-variant hover:text-on-surface transition-colors';
      });
    };

    if (prevBtn) prevBtn.addEventListener('click', () => { if (currentPage > 1) { currentPage -= 1; updatePagination(); } });
    if (nextBtn) nextBtn.addEventListener('click', () => { if (currentPage < maxPage) { currentPage += 1; updatePagination(); } });
    pageBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        const page = Number(btn.getAttribute('data-admin-users-page'));
        if (!Number.isNaN(page)) { currentPage = page; updatePagination(); }
      });
    });
    updatePagination();

    const modal = document.querySelector('[data-admin-users-modal]');
    const closeModal = () => modal && modal.classList.add('hidden');
    if (modal) {
      modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
      const closeBtn = modal.querySelector('[data-admin-users-modal-close]');
      if (closeBtn) closeBtn.addEventListener('click', closeModal);
    }

    document.addEventListener('click', (e) => {
      const viewBtn = e.target.closest('[data-admin-user-view]');
      const blockBtn = e.target.closest('[data-admin-user-block]');
      const delBtn = e.target.closest('[data-admin-user-delete]');

      if (delBtn) {
        const row = delBtn.closest('tr');
        if (row) row.remove();
        return;
      }
      if (blockBtn) {
        const row = blockBtn.closest('tr');
        if (!row) return;
        const statusEl = row.querySelector('td:nth-child(4) span');
        const roleEl = row.querySelector('td:nth-child(1) p.text-xs');
        if (statusEl) {
          const currentStatus = statusEl.textContent.trim().toUpperCase();
          const isActive = currentStatus === 'ACTIVO';
          if (isActive) {
            statusEl.textContent = 'BLOQUEADO';
            statusEl.className = 'px-3 py-1 bg-error/10 text-error text-[10px] font-bold uppercase tracking-widest rounded-full border border-error/20';
            row.classList.add('bg-error/5');
            if (roleEl) roleEl.textContent = 'Banned';
          } else {
            statusEl.textContent = 'ACTIVO';
            statusEl.className = 'px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20';
            row.classList.remove('bg-error/5');
            if (roleEl) roleEl.textContent = 'Regular';
          }
        }
        return;
      }
      if (viewBtn) {
        const row = viewBtn.closest('tr');
        if (!row || !modal) return;
        const name = row.querySelector('td:nth-child(1) p.font-bold')?.textContent.trim() || 'Usuario';
        const role = row.querySelector('td:nth-child(1) p.text-xs')?.textContent.trim() || '-';
        const email = row.querySelector('td:nth-child(2)')?.textContent.trim() || '-';
        const date = row.querySelector('td:nth-child(3)')?.textContent.trim() || '-';
        const status = row.querySelector('td:nth-child(4) span')?.textContent.trim() || '-';
        modal.querySelector('[data-admin-users-modal-name]').textContent = name;
        modal.querySelector('[data-admin-users-modal-email]').textContent = email;
        modal.querySelector('[data-admin-users-modal-role]').textContent = role;
        modal.querySelector('[data-admin-users-modal-status]').textContent = status;
        modal.querySelector('[data-admin-users-modal-date]').textContent = date;
        modal.classList.remove('hidden');
      }
    });

    const exportBtn = document.querySelector('[data-admin-users-export]');
    if (exportBtn) {
      exportBtn.addEventListener('click', () => {
        const visibleRows = rows.filter((r) => r.style.display !== 'none');
        const lines = ['Usuario,Correo,Fecha,Estado'];
        visibleRows.forEach((row) => {
          const name = row.querySelector('td:nth-child(1) p.font-bold')?.textContent.trim() || '';
          const email = row.querySelector('td:nth-child(2)')?.textContent.trim() || '';
          const date = row.querySelector('td:nth-child(3)')?.textContent.trim() || '';
          const status = row.querySelector('td:nth-child(4) span')?.textContent.trim() || '';
          lines.push([name,email,date,status].map((v) => `"${v.replace(/"/g,'""')}"`).join(','));
        });
        const blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'usuarios.csv';
        a.click();
        URL.revokeObjectURL(url);
      });
    }
  })();
</script>
<script src="controllers/admin-layout.js"></script>
</body></html>

