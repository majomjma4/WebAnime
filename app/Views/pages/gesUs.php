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
<div class="flex items-center gap-3">
<div class="relative group">
<span class="absolute inset-y-0 left-3 flex items-center text-on-surface-variant">
<span class="material-symbols-outlined text-xl">search</span>
</span>
<input class="bg-surface-container-lowest border-none rounded-full py-2 pl-10 pr-4 text-sm w-56 focus:ring-1 ring-primary/20 transition-all placeholder:text-outline" placeholder="Buscar..." type="text" data-admin-users-search/>
</div>
<select class="bg-surface-container-lowest border-none rounded-full px-4 py-2 text-sm text-on-surface-variant focus:ring-1 ring-primary/20" data-admin-users-filter-role>
  <option value="">Rol</option>
  <option value="Premium">Premium</option>
  <option value="Registrado">Registrado</option>
</select>
<select class="bg-surface-container-lowest border-none rounded-full px-4 py-2 text-sm text-on-surface-variant focus:ring-1 ring-primary/20" data-admin-users-filter-status>
  <option value="">Estado</option>
  <option value="ACTIVO">Activo</option>
  <option value="BLOQUEADO">Bloqueado</option>
</select>
</div>
</header>
<section class="mt-20 px-10 py-12 space-y-10">
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
<div class="overflow-hidden">
<table class="w-full table-fixed text-left border-collapse">
<thead>
<tr class="bg-surface-container/50">
<th class="px-5 py-5 text-xs font-bold uppercase tracking-widest text-outline">ID</th>
<th class="px-5 py-5 text-xs font-bold uppercase tracking-widest text-outline">USUARIO</th>
<th class="px-5 py-5 text-center text-xs font-bold uppercase tracking-widest text-outline">ROL</th>
<th class="px-4 py-5 text-xs font-bold uppercase tracking-widest text-outline">CORREO ELECTRONICO</th>
<th class="px-[4.5rem] py-5 text-center text-xs font-bold uppercase tracking-widest text-outline whitespace-nowrap">FECHA DE UNIÓN</th>
<th class="px-4 py-5 text-center text-xs font-bold uppercase tracking-widest text-outline">ESTADO</th>
<th class="px-4 py-5 text-center text-xs font-bold uppercase tracking-widest text-outline">ACCIONES</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/5">
<!-- Template Row (Hidden) -->
<tr id="row-template" class="hidden group hover:bg-surface-container-high/50 transition-colors">
<td class="px-5 py-5 text-sm text-primary" data-id>#0</td>
<td class="px-5 py-5">
<p class="font-bold text-on-surface" data-name>Nombre</p>
</td>
<td class="px-5 py-5 text-center">
<span class="inline-flex items-center rounded-full border border-primary/25 bg-primary/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-primary" data-role>Rol</span>
</td>
<td class="px-4 py-5 pr-20 text-sm text-on-surface-variant" data-email>correo@ejemplo.com</td>
<td class="px-[4.5rem] py-5 text-center text-sm text-on-surface-variant whitespace-nowrap" data-date>Hoy</td>
<td class="px-4 py-5 text-center">
<span class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded-full border whitespace-nowrap" data-status>ACTIVO</span>
</td>
<td class="px-4 py-5 text-right">
<div class="flex justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 rounded-full text-on-surface-variant transition-all duration-200 hover:-translate-y-0.5 hover:bg-sky-400/10 hover:text-sky-200 hover:shadow-[0_0_14px_rgba(56,189,248,0.25)]" title="Ver" data-admin-user-view>
<span class="material-symbols-outlined text-lg">visibility</span>
</button>
<button class="p-2 rounded-full text-on-surface-variant transition-all duration-200 hover:-translate-y-0.5 hover:bg-amber-400/10 hover:text-amber-200 hover:shadow-[0_0_14px_rgba(251,191,36,0.22)]" title="Bloquear" data-admin-user-block>
<span class="material-symbols-outlined text-lg">block</span>
</button>
<button class="p-2 rounded-full text-on-surface-variant transition-all duration-200 hover:-translate-y-0.5 hover:bg-rose-400/10 hover:text-rose-300 hover:shadow-[0_0_14px_rgba(244,63,94,0.24)]" title="Eliminar" data-admin-user-delete>
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
<div class="flex items-center gap-2">
<button class="w-8 h-8 rounded-full border border-outline-variant/20 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors disabled:opacity-20" data-admin-users-prev>
<span class="material-symbols-outlined text-lg">chevron_left</span>
</button>
<button class="w-8 h-8 rounded-full border border-outline-variant/20 flex items-center justify-center text-on-surface hover:text-primary hover:border-primary transition-colors disabled:opacity-20" data-admin-users-next>
<span class="material-symbols-outlined text-lg">chevron_right</span>
</button>
</div>
</div>
</div>
</section>
</main>
<div class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/65 backdrop-blur-sm px-6" data-admin-users-modal>
  <div class="w-[min(620px,92vw)] overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-[#1a1630] via-[#171822] to-[#111318] shadow-[0_30px_80px_rgba(0,0,0,0.55)]">
    <div class="flex items-start justify-between border-b border-white/8 px-7 py-6">
      <div class="space-y-2">
        <span class="inline-flex items-center rounded-full border border-sky-300/20 bg-sky-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.22em] text-sky-200">Vista de usuario</span>
        <h3 class="text-3xl font-headline font-extrabold tracking-tight text-on-surface" data-admin-users-modal-name>Usuario</h3>
        <p class="text-sm text-on-surface-variant">Resumen rapido de la cuenta seleccionada.</p>
      </div>
      <button class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-on-surface-variant transition hover:bg-white/10 hover:text-on-surface" data-admin-users-modal-close>
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>
    <div class="grid grid-cols-1 gap-4 px-7 py-6 md:grid-cols-2">
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Correo</p>
        <p class="mt-2 text-sm font-medium text-on-surface break-all" data-admin-users-modal-email></p>
      </div>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Rol</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-modal-role></p>
      </div>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Estado</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-modal-status></p>
      </div>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Fecha de union</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-modal-date></p>
      </div>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4 md:col-span-2">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Motivo de bloqueo</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-modal-reason>Sin motivo registrado</p>
      </div>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4 md:col-span-2">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Penalizacion hasta</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-modal-penalty>Sin penalizacion activa</p>
      </div>
    </div>
  </div>
</div>
<div class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 backdrop-blur-sm px-6" data-admin-users-block-modal>
  <div class="w-[min(700px,94vw)] overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-[#181523] via-[#161821] to-[#101216] shadow-[0_30px_90px_rgba(0,0,0,0.6)]">
    <div class="flex items-start justify-between border-b border-white/8 px-7 py-6">
      <div class="space-y-2">
        <span class="inline-flex items-center rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.22em] text-amber-200">Bloquear usuario</span>
        <h3 class="text-3xl font-headline font-extrabold tracking-tight text-on-surface">Aplicar penalizacion</h3>
        <p class="text-sm text-on-surface-variant">Configura el motivo y el tiempo del bloqueo.</p>
      </div>
      <button class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-on-surface-variant transition hover:bg-white/10 hover:text-on-surface" data-admin-users-block-close>
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>
    <div class="grid grid-cols-1 gap-4 border-b border-white/8 px-7 py-6 md:grid-cols-3">
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Usuario</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-block-name></p>
      </div>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">ID</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-block-id></p>
      </div>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Rol</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-block-role></p>
      </div>
    </div>
    <div class="space-y-6 px-7 py-6">
      <div>
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Motivo</p>
        <div class="mt-3 flex flex-wrap gap-2" data-admin-users-block-reasons>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-amber-300/30 hover:bg-amber-400/10 hover:text-amber-200" data-reason-value="Spam o comportamiento abusivo">Spam o abuso</button>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-amber-300/30 hover:bg-amber-400/10 hover:text-amber-200" data-reason-value="Lenguaje ofensivo o acoso">Lenguaje ofensivo</button>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-amber-300/30 hover:bg-amber-400/10 hover:text-amber-200" data-reason-value="Contenido no permitido o reportes reiterados">Contenido no permitido</button>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-amber-300/30 hover:bg-amber-400/10 hover:text-amber-200" data-reason-value="Incumplimiento de normas de la comunidad">Incumplimiento de normas</button>
        </div>
      </div>
      <div>
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">Tiempo de penalizacion</p>
        <div class="mt-3 flex flex-wrap gap-2" data-admin-users-block-penalties>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-rose-300/30 hover:bg-rose-400/10 hover:text-rose-200" data-penalty-value="24h">24 horas</button>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-rose-300/30 hover:bg-rose-400/10 hover:text-rose-200" data-penalty-value="3d">3 dias</button>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-rose-300/30 hover:bg-rose-400/10 hover:text-rose-200" data-penalty-value="7d">7 dias</button>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-rose-300/30 hover:bg-rose-400/10 hover:text-rose-200" data-penalty-value="30d">30 dias</button>
          <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-4 py-2 text-xs font-semibold text-on-surface-variant transition hover:border-rose-300/30 hover:bg-rose-400/10 hover:text-rose-200" data-penalty-value="permanente">Permanente</button>
        </div>
      </div>
      <div>
        <label class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant" for="users-block-custom-reason">Motivo personalizado</label>
        <textarea id="users-block-custom-reason" class="mt-3 min-h-[110px] w-full rounded-2xl border border-white/10 bg-white/[0.03] px-4 py-3 text-sm text-on-surface placeholder:text-on-surface-variant/60 focus:border-amber-300/30 focus:ring-0 resize-none" placeholder="Escribe aqui otra razon si no aparece en las opciones..." data-admin-users-block-custom></textarea>
      </div>
    </div>
    <div class="flex items-center justify-end gap-3 border-t border-white/8 px-7 py-5">
      <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-5 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-on-surface-variant transition hover:text-on-surface" data-admin-users-block-close>Cancelar</button>
      <button type="button" class="rounded-full border border-rose-300/20 bg-gradient-to-r from-rose-400 to-amber-300 px-5 py-2 text-xs font-bold uppercase tracking-[0.2em] text-[#311018] transition hover:brightness-110" data-admin-users-block-confirm>Bloquear</button>
    </div>
  </div>
</div>
<div class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/70 backdrop-blur-sm px-6" data-admin-users-unblock-modal>
  <div class="w-[min(520px,92vw)] overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-[#171726] via-[#14161d] to-[#0f1116] shadow-[0_30px_80px_rgba(0,0,0,0.6)]">
    <div class="flex items-start justify-between border-b border-white/8 px-7 py-6">
      <div class="space-y-2">
        <span class="inline-flex items-center rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.22em] text-emerald-200">Quitar bloqueo</span>
        <h3 class="text-3xl font-headline font-extrabold tracking-tight text-on-surface">Confirmar acción</h3>
      </div>
      <button class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-on-surface-variant transition hover:bg-white/10 hover:text-on-surface" data-admin-users-unblock-close>
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>
    <div class="space-y-4 px-7 py-6">
      <p class="text-sm text-on-surface-variant">Â¿Deseas quitar el bloqueo de <span class="font-semibold text-on-surface" data-admin-users-unblock-name>este usuario</span>?</p>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">ID</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-unblock-id></p>
      </div>
    </div>
    <div class="flex items-center justify-end gap-3 border-t border-white/8 px-7 py-5">
      <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-5 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-on-surface-variant transition hover:text-on-surface" data-admin-users-unblock-close>Cancelar</button>
      <button type="button" class="rounded-full border border-emerald-300/20 bg-gradient-to-r from-emerald-300 to-sky-300 px-5 py-2 text-xs font-bold uppercase tracking-[0.2em] text-[#102022] transition hover:brightness-110" data-admin-users-unblock-confirm>Quitar bloqueo</button>
    </div>
  </div>
</div>
<div class="fixed inset-0 z-[75] hidden items-center justify-center bg-black/70 backdrop-blur-sm px-6" data-admin-users-delete-modal>
  <div class="w-[min(540px,92vw)] overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-[#201417] via-[#17161c] to-[#111217] shadow-[0_30px_80px_rgba(0,0,0,0.6)]">
    <div class="flex items-start justify-between border-b border-white/8 px-7 py-6">
      <div class="space-y-2">
        <span class="inline-flex items-center rounded-full border border-rose-300/20 bg-rose-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.22em] text-rose-200">Eliminar usuario</span>
        <h3 class="text-3xl font-headline font-extrabold tracking-tight text-on-surface">Confirmar eliminacion</h3>
      </div>
      <button class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 text-on-surface-variant transition hover:bg-white/10 hover:text-on-surface" data-admin-users-delete-close>
        <span class="material-symbols-outlined text-lg">close</span>
      </button>
    </div>
    <div class="space-y-4 px-7 py-6">
      <p class="text-sm leading-7 text-on-surface-variant">Â¿Deseas eliminar para siempre a <span class="font-semibold text-on-surface" data-admin-users-delete-name>este usuario</span>? Esta acción borrara toda su información, incluyendo sus registros en la base de datos.</p>
      <div class="rounded-2xl border border-white/8 bg-white/[0.03] px-5 py-4">
        <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-on-surface-variant">ID</p>
        <p class="mt-2 text-sm font-medium text-on-surface" data-admin-users-delete-id></p>
      </div>
    </div>
    <div class="flex items-center justify-end gap-3 border-t border-white/8 px-7 py-5">
      <button type="button" class="rounded-full border border-white/10 bg-white/[0.03] px-5 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-on-surface-variant transition hover:text-on-surface" data-admin-users-delete-close>Cancelar</button>
      <button type="button" class="rounded-full border border-rose-300/20 bg-gradient-to-r from-rose-400 to-orange-300 px-5 py-2 text-xs font-bold uppercase tracking-[0.2em] text-[#2e0f12] transition hover:brightness-110" data-admin-users-delete-confirm>Eliminar</button>
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
    const roleFilter = document.querySelector('[data-admin-users-filter-role]');
    const statusFilter = document.querySelector('[data-admin-users-filter-status]');
    const modal = document.querySelector('[data-admin-users-modal]');
    const blockModal = document.querySelector('[data-admin-users-block-modal]');
    const prevBtn = document.querySelector('[data-admin-users-prev]');
    const nextBtn = document.querySelector('[data-admin-users-next]');
    const unblockModal = document.querySelector('[data-admin-users-unblock-modal]');
    const deleteModal = document.querySelector('[data-admin-users-delete-modal]');
    const blockCloseEls = blockModal ? blockModal.querySelectorAll('[data-admin-users-block-close]') : [];
    const unblockCloseEls = unblockModal ? unblockModal.querySelectorAll('[data-admin-users-unblock-close]') : [];
    const deleteCloseEls = deleteModal ? deleteModal.querySelectorAll('[data-admin-users-delete-close]') : [];
    const blockConfirmBtn = blockModal ? blockModal.querySelector('[data-admin-users-block-confirm]') : null;
    const unblockConfirmBtn = unblockModal ? unblockModal.querySelector('[data-admin-users-unblock-confirm]') : null;
    const deleteConfirmBtn = deleteModal ? deleteModal.querySelector('[data-admin-users-delete-confirm]') : null;
    const blockReasonBtns = blockModal ? Array.from(blockModal.querySelectorAll('[data-reason-value]')) : [];
    const blockPenaltyBtns = blockModal ? Array.from(blockModal.querySelectorAll('[data-penalty-value]')) : [];
    const blockCustomInput = blockModal ? blockModal.querySelector('[data-admin-users-block-custom]') : null;
    const PAGE_SIZE = 50;
    let currentPage = 1;
    let blockState = { id: null, row: null, reason: '', penalty: '' };
    let unblockState = { id: null, row: null };
    let deleteState = { id: null, row: null };
    
    let stateRows = [];

    // Cargar Usuarios
    try {
        const res = await fetch('api/users.php?action=list');
        const data = await res.json();
        if (data.success) {
            renderUsers(data.data);
            updateStats(data.data, data.stats || {});
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
            row.dataset.publicId = u.public_id || `#${u.id}`;
            
            row.querySelector('[data-id]').textContent = u.public_id || `#${u.id}`;
            row.querySelector('[data-name]').textContent = u.name;
            const roleNode = row.querySelector('[data-role]');
            roleNode.textContent = u.role;
            row.querySelector('[data-email]').textContent = u.email;
            row.querySelector('[data-date]').textContent = u.date;
            row.dataset.name = (u.name || '').toLowerCase();
            row.dataset.role = (u.role || '').toLowerCase();
            row.dataset.status = (u.status || '').toLowerCase();
            row.dataset.date = (u.date || '').toLowerCase();
            row.dataset.publicId = (u.public_id || `#${u.id}`).toLowerCase();
            row.dataset.blockReason = u.block_reason || '';
            row.dataset.penaltyUntil = u.penalty_until || '';

            if (u.role === 'Premium') {
                roleNode.className = 'inline-flex items-center rounded-full border border-amber-300/30 bg-amber-400/12 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-amber-200';
            } else {
                roleNode.className = 'inline-flex items-center rounded-full border border-sky-300/25 bg-sky-400/10 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-sky-200';
            }
            
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
        currentPage = 1;
        applyPagination();
    }

    function getVisibleRows() {
        return stateRows.filter((row) => row.dataset.searchHidden !== '1');
    }

    function applyPagination() {
        const visibleRows = getVisibleRows();
        const total = visibleRows.length;
        const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * PAGE_SIZE;
        const end = start + PAGE_SIZE;

        stateRows.forEach((row) => {
            if (row.dataset.searchHidden === '1') {
                row.style.display = 'none';
                return;
            }
            const visibleIndex = visibleRows.indexOf(row);
            row.style.display = (visibleIndex >= start && visibleIndex < end) ? '' : 'none';
        });

        const shown = total === 0 ? 0 : Math.min(end, total);
        footer.textContent = `Mostrando ${shown} de ${total} usuarios`;
        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = currentPage >= totalPages || total === 0;
    }

    function updateStats(users, stats) {
        document.getElementById('stat-active').textContent = stats.active_now ?? 0;
        document.getElementById('stat-banned').textContent = stats.blocked_users ?? users.filter(u => u.status === 'BLOQUEADO').length;
        document.getElementById('stat-admin').textContent = stats.admin_users ?? 0;
        document.getElementById('stat-new').textContent = users.length;
    }

    function closeBlockModal() {
      if (!blockModal) return;
      blockModal.classList.add('hidden');
      blockModal.classList.remove('flex');
      if (blockCustomInput) blockCustomInput.value = '';
      blockState = { id: null, row: null, reason: '', penalty: '' };
      blockReasonBtns.forEach((btn) => btn.classList.remove('border-amber-300/40', 'bg-amber-400/15', 'text-amber-200'));
      blockPenaltyBtns.forEach((btn) => btn.classList.remove('border-rose-300/40', 'bg-rose-400/15', 'text-rose-200'));
    }

    function closeUnblockModal() {
      if (!unblockModal) return;
      unblockModal.classList.add('hidden');
      unblockModal.classList.remove('flex');
      unblockState = { id: null, row: null };
    }

    function openUnblockModal(row) {
      if (!unblockModal || !row) return;
      unblockState = { id: row.dataset.userId, row };
      unblockModal.querySelector('[data-admin-users-unblock-name]').textContent = row.querySelector('[data-name]').textContent;
      unblockModal.querySelector('[data-admin-users-unblock-id]').textContent = row.dataset.publicId || row.querySelector('[data-id]').textContent;
      unblockModal.classList.remove('hidden');
      unblockModal.classList.add('flex');
    }

    function closeDeleteModal() {
      if (!deleteModal) return;
      deleteModal.classList.add('hidden');
      deleteModal.classList.remove('flex');
      deleteState = { id: null, row: null };
    }

    function openDeleteModal(row) {
      if (!deleteModal || !row) return;
      deleteState = { id: row.dataset.userId, row };
      deleteModal.querySelector('[data-admin-users-delete-name]').textContent = row.querySelector('[data-name]').textContent;
      deleteModal.querySelector('[data-admin-users-delete-id]').textContent = row.dataset.publicId || row.querySelector('[data-id]').textContent;
      deleteModal.classList.remove('hidden');
      deleteModal.classList.add('flex');
    }

    function openBlockModal(row) {
      if (!blockModal || !row) return;
      blockState = {
        id: row.dataset.userId,
        row,
        reason: '',
        penalty: ''
      };
      blockModal.querySelector('[data-admin-users-block-name]').textContent = row.querySelector('[data-name]').textContent;
      blockModal.querySelector('[data-admin-users-block-id]').textContent = row.dataset.publicId || row.querySelector('[data-id]').textContent;
      blockModal.querySelector('[data-admin-users-block-role]').textContent = row.querySelector('[data-role]').textContent;
      if (blockCustomInput) blockCustomInput.value = '';
      blockModal.classList.remove('hidden');
      blockModal.classList.add('flex');
    }

    function applyFilters() {
      const term = (searchInput?.value || '').trim().toLowerCase();
      const role = (roleFilter?.value || '').trim().toLowerCase();
      const status = (statusFilter?.value || '').trim().toLowerCase();

      currentPage = 1;
      stateRows.forEach((row) => {
        const matchesTerm = !term || [
          row.dataset.publicId || '',
          row.dataset.name || '',
          row.dataset.role || '',
          row.dataset.status || '',
          row.dataset.date || ''
        ].some((value) => value.includes(term));

        const matchesRole = !role || (row.dataset.role || '') === role;
        const matchesStatus = !status || (row.dataset.status || '') === status;
        row.dataset.searchHidden = (matchesTerm && matchesRole && matchesStatus) ? '0' : '1';
      });
      applyPagination();
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (roleFilter) roleFilter.addEventListener('change', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);

    // Handlers
    document.addEventListener('click', async (e) => {
      const viewBtn = e.target.closest('[data-admin-user-view]');
      const blockBtn = e.target.closest('[data-admin-user-block]');
      const delBtn = e.target.closest('[data-admin-user-delete]');

      if (delBtn) {
        const row = delBtn.closest('tr');
        openDeleteModal(row);
        return;
      }

      if (blockBtn) {
        const row = blockBtn.closest('tr');
        const statusEl = row.querySelector('[data-status]');
        const isCurrentlyActive = statusEl.textContent === 'ACTIVO';

        if (isCurrentlyActive) {
          openBlockModal(row);
          return;
        }

        openUnblockModal(row);
        return;
      }

      if (viewBtn) {
        const row = viewBtn.closest('tr');
        modal.querySelector('[data-admin-users-modal-name]').textContent = row.querySelector('[data-name]').textContent;
        modal.querySelector('[data-admin-users-modal-email]').textContent = row.querySelector('[data-email]').textContent;
        modal.querySelector('[data-admin-users-modal-role]').textContent = row.querySelector('[data-role]').textContent;
        modal.querySelector('[data-admin-users-modal-status]').textContent = row.querySelector('[data-status]').textContent;
        modal.querySelector('[data-admin-users-modal-date]').textContent = row.querySelector('[data-date]').textContent;
        modal.querySelector('[data-admin-users-modal-reason]').textContent = row.dataset.blockReason || 'Sin motivo registrado';
        modal.querySelector('[data-admin-users-modal-penalty]').textContent = row.dataset.penaltyUntil || 'Sin penalizacion activa';
        modal.classList.remove('hidden');
      }
    });

    if (prevBtn) prevBtn.addEventListener('click', () => {
      if (currentPage <= 1) return;
      currentPage -= 1;
      applyPagination();
    });

    if (nextBtn) nextBtn.addEventListener('click', () => {
      currentPage += 1;
      applyPagination();
    });

    blockReasonBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        blockState.reason = btn.dataset.reasonValue || '';
        blockReasonBtns.forEach((item) => item.classList.remove('border-amber-300/40', 'bg-amber-400/15', 'text-amber-200'));
        btn.classList.add('border-amber-300/40', 'bg-amber-400/15', 'text-amber-200');
      });
    });

    blockPenaltyBtns.forEach((btn) => {
      btn.addEventListener('click', () => {
        blockState.penalty = btn.dataset.penaltyValue || '';
        blockPenaltyBtns.forEach((item) => item.classList.remove('border-rose-300/40', 'bg-rose-400/15', 'text-rose-200'));
        btn.classList.add('border-rose-300/40', 'bg-rose-400/15', 'text-rose-200');
      });
    });

    blockCloseEls.forEach((el) => el.addEventListener('click', closeBlockModal));
    unblockCloseEls.forEach((el) => el.addEventListener('click', closeUnblockModal));
    deleteCloseEls.forEach((el) => el.addEventListener('click', closeDeleteModal));
    if (blockModal) {
      blockModal.addEventListener('click', (e) => {
        if (e.target === blockModal) closeBlockModal();
      });
    }
    if (unblockModal) {
      unblockModal.addEventListener('click', (e) => {
        if (e.target === unblockModal) closeUnblockModal();
      });
    }
    if (deleteModal) {
      deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) closeDeleteModal();
      });
    }

    if (deleteConfirmBtn) {
      deleteConfirmBtn.addEventListener('click', async () => {
        if (!deleteState.id || !deleteState.row) return;

        try {
          const res = await fetch('api/users.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: deleteState.id })
          });
          const data = await res.json();
          if (!data.success) {
            alert(data.error || 'No se pudo eliminar el usuario');
            return;
          }
          deleteState.row.remove();
          stateRows = stateRows.filter((row) => row !== deleteState.row);
          closeDeleteModal();
          applyPagination();
        } catch (err) {
          alert('Error de conexion');
        }
      });
    }

    if (unblockConfirmBtn) {
      unblockConfirmBtn.addEventListener('click', async () => {
        if (!unblockState.id || !unblockState.row) return;

        try {
          const res = await fetch('api/users.php?action=toggle_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: unblockState.id, activo: 1 })
          });
          const data = await res.json();
          if (!data.success) {
            alert(data.error || 'No se pudo quitar el bloqueo');
            return;
          }

          const statusEl = unblockState.row.querySelector('[data-status]');
          statusEl.textContent = 'ACTIVO';
          statusEl.className = 'px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20 whitespace-nowrap';
          unblockState.row.classList.remove('bg-error/5');
          unblockState.row.dataset.blockReason = '';
          unblockState.row.dataset.penaltyUntil = '';
          closeUnblockModal();
        } catch (err) {
          alert('Error de conexion');
        }
      });
    }

    if (blockConfirmBtn) {
      blockConfirmBtn.addEventListener('click', async () => {
        const customReason = (blockCustomInput?.value || '').trim();
        if (!blockState.reason && !customReason) {
          alert('Selecciona o escribe un motivo');
          return;
        }
        if (!blockState.penalty) {
          alert('Selecciona un tiempo de penalizacion');
          return;
        }

        try {
          const res = await fetch('api/users.php?action=block', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              id: blockState.id,
              reason: blockState.reason,
              custom_reason: customReason,
              penalty: blockState.penalty
            })
          });
          const data = await res.json();
          if (!data.success) {
            alert(data.error || 'No se pudo bloquear al usuario');
            return;
          }

          if (blockState.row) {
            const statusEl = blockState.row.querySelector('[data-status]');
            statusEl.textContent = 'BLOQUEADO';
            statusEl.className = 'px-3 py-1 bg-error/10 text-error text-[10px] font-bold uppercase tracking-widest rounded-full border border-error/20 whitespace-nowrap';
            blockState.row.classList.add('bg-error/5');
          }
          closeBlockModal();
        } catch (err) {
          alert('Error de conexion');
        }
      });
    }

    const closeBtn = document.querySelector('[data-admin-users-modal-close]');
    if (closeBtn) closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
      });
    }

    applyPagination();

  })();
</script>
<script src="controllers/admin-layout.js?v=20260330a"></script>
</body></html>























