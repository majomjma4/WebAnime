<!DOCTYPE html>
<html class="dark" lang="es">
  <head>
    <script data-ui-preload>document.documentElement.classList.add("preload-ui");</script>
    <style>

    .badge-shimmer-gold {
      background: linear-gradient(120deg, rgba(251,191,36,0.30) 0%, rgba(252,211,77,0.5) 35%, rgba(245,158,11,0.45) 60%, rgba(251,191,36,0.30) 100%);
      background-size: 200% 200%;
      animation: badgeGoldShimmer 4.5s ease-in-out infinite;
    }
    @keyframes badgeGoldShimmer {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .badge-shimmer {
      background: linear-gradient(120deg, rgba(56,189,248,0.75) 0%, rgba(167,139,250,0.9) 35%, rgba(59,130,246,0.85) 60%, rgba(56,189,248,0.75) 100%);
      background-size: 200% 200%;
      animation: badgeShimmer 4.5s ease-in-out infinite;
    }
    @keyframes badgeShimmer {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
  .preload-ui body { opacity: 0; }
  .user-carousel-wrap {
      position: relative;
  }
  .user-carousel-nav-zone {
      position: absolute;
      top: 0;
      width: 64px;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 70;
      pointer-events: auto;
      background: rgba(0, 0, 0, 0);
  }
  .user-carousel-nav-zone.is-disabled {
      pointer-events: none;
  }
  .user-carousel-nav-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      opacity: 1;
      pointer-events: auto;
      transition: opacity 0.25s ease, transform 0.25s ease;
      z-index: 71;
  }
  .user-carousel-nav-btn.is-disabled {
      opacity: 0.35 !important;
      pointer-events: none;
  }
  .is-disabled {
      opacity: 0.45 !important;
      pointer-events: none !important;
      filter: grayscale(1);
  }
  .user-carousel-nav-left { left: -24px; }
  .user-carousel-nav-right { right: -24px; }
  @media (max-width: 768px) {
      .user-carousel-nav-left { left: -12px; }
      .user-carousel-nav-right { right: -12px; }
  }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NekoraList - Usuario</title>
    <link rel="icon" href="img/icon3.png" />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "primary-dim": "#c0adff",
            "primary-fixed-dim": "#dacdff",
            "background": "#0e0e0e",
            "on-background": "#e7e5e4",
            "surface-bright": "#2c2c2c",
            "on-surface-variant": "#acabaa",
            "tertiary-dim": "#cecfef",
            "on-tertiary-container": "#4c4e68",
            "on-secondary-fixed-variant": "#5d5b5f",
            "on-tertiary-fixed-variant": "#565873",
            "inverse-surface": "#fcf8f8",
            "error-container": "#7f2737",
            "on-primary": "#4800bf",
            "on-secondary-fixed": "#403e42",
            "on-error-container": "#ff97a3",
            "surface-container-highest": "#252626",
            "on-surface": "#e7e5e4",
            "outline": "#767575",
            "on-error": "#490013",
            "tertiary-fixed": "#ddddfe",
            "surface-container-lowest": "#000000",
            "surface-tint": "#cdbdff",
            "surface": "#0e0e0e",
            "tertiary-container": "#ddddfe",
            "surface-variant": "#252626",
            "inverse-on-surface": "#565554",
            "inverse-primary": "#6834eb",
            "surface-container-high": "#1f2020",
            "primary-container": "#4f00d0",
            "primary": "#cdbdff",
            "secondary-fixed": "#e6e1e6",
            "on-primary-fixed-variant": "#652fe7",
            "outline-variant": "#484848",
            "on-secondary-container": "#c2bec3",
            "on-tertiary-fixed": "#3a3c55",
            "secondary-dim": "#a09da1",
            "secondary-container": "#3c3b3e",
            "error": "#ec7c8a",
            "on-primary-container": "#d6c9ff",
            "tertiary": "#edecff",
            "surface-container-low": "#131313",
            "surface-container": "#191a1a",
            "secondary-fixed-dim": "#d8d3d8",
            "surface-dim": "#0e0e0e",
            "on-tertiary": "#555671",
            "on-secondary": "#211f23",
            "tertiary-fixed-dim": "#cecfef",
            "error-dim": "#b95463",
            "secondary": "#a09da1",
            "primary-fixed": "#e8deff",
            "on-primary-fixed": "#4700bd"
          },
          fontFamily: {
            "headline": ["Manrope"],
            "body": ["Inter"],
            "label": ["Inter"]
          },
          borderRadius: { "DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px" }
        }
      }
    }
    </script>
    <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .glass-effect {
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
    }
    .glass-panel {
      background: linear-gradient(135deg, rgba(19, 19, 19, 0.92), rgba(36, 32, 46, 0.9));
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
    }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .transparent-scrollbar {
      scrollbar-width: thin;
      scrollbar-color: rgba(255,255,255,0.2) transparent;
    }
    .transparent-scrollbar::-webkit-scrollbar {
      width: 8px;
    }
    .transparent-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }
    .transparent-scrollbar::-webkit-scrollbar-thumb {
      background: rgba(255,255,255,0.2);
      border-radius: 999px;
    }
    .modal-shell {
      background: linear-gradient(135deg, #1c1a2a 0%, #202236 45%, #24192d 100%);
      min-height: 98vh;
      height: 98vh;
      display: flex;
      flex-direction: column;
    }
    .modal-grid {
      grid-auto-rows: 1fr;
    }
    .modal-accent {
      height: 4px;
      width: 100%;
      border-radius: 999px;
      margin-bottom: 1rem;
    }
    .soft-accent {
      border: 1px solid rgba(139, 92, 246, 0.22);
      background: rgba(255, 255, 255, 0.03);
      box-shadow: 0 0 12px rgba(139, 92, 246, 0.12);
    }
    .soft-button {
      border: 1px solid rgba(139, 92, 246, 0.25);
      background: rgba(139, 92, 246, 0.08);
      box-shadow: 0 8px 20px rgba(139, 92, 246, 0.12);
    }
    #lists-modal.theme-mylist .modal-shell {
      border-color: rgba(139, 92, 246, 0.55);
      box-shadow: 0 20px 50px rgba(139, 92, 246, 0.22);
    }
    #lists-modal.theme-mylist .modal-accent {
      background: linear-gradient(90deg, rgba(139,92,246,0.9), rgba(79,70,229,0.65));
    }
    #lists-modal.theme-favorites .modal-shell {
      border-color: rgba(236, 72, 153, 0.55);
      box-shadow: 0 20px 50px rgba(236, 72, 153, 0.22);
    }
    #lists-modal.theme-favorites .modal-accent {
      background: linear-gradient(90deg, rgba(236,72,153,0.9), rgba(168,85,247,0.6));
    }
    #status-modal.theme-pending .modal-shell {
      border-color: rgba(251, 191, 36, 0.6);
      box-shadow: 0 20px 50px rgba(251, 191, 36, 0.2);
    }
    #status-modal.theme-pending .modal-accent {
      background: linear-gradient(90deg, rgba(251,191,36,0.95), rgba(245,158,11,0.7));
    }
    #status-modal.theme-completed .modal-shell {
      border-color: rgba(52, 211, 153, 0.6);
      box-shadow: 0 20px 50px rgba(52, 211, 153, 0.2);
    }
    #status-modal.theme-completed .modal-accent {
      background: linear-gradient(90deg, rgba(52,211,153,0.95), rgba(16,185,129,0.7));
    }
    </style>
    <link rel="icon" href="img/icon3.png" />


  </head>
  <body class="bg-background text-on-background font-body selection:bg-primary-container selection:text-on-primary-container">
    <!-- Navbar Component -->
    <div data-layout="header"></div>

    <!-- VIEW: User Profile Layout -->
    <main class="relative pt-32 pb-20 px-6 max-w-7xl mx-auto overflow-visible">
      <div class="pointer-events-none absolute -top-40 right-[-10%] h-72 w-72 rounded-full bg-violet-500/15 blur-3xl"></div>
      <div class="pointer-events-none absolute top-16 left-[-10%] h-72 w-72 rounded-full bg-sky-500/15 blur-3xl"></div>
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start overflow-visible">
      <section class="relative group lg:col-span-6">
        <div id="profile-card" class="glass-panel w-full rounded-3xl overflow-hidden border border-white/10 shadow-[0_30px_80px_rgba(0,0,0,0.45)] relative overflow-visible">
          <div class="h-1 w-full bg-gradient-to-r from-sky-400 via-violet-400 to-primary"></div>
            <div class="p-8 md:p-10 pb-0 space-y-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div>
                <h2 class="font-headline text-2xl md:text-3xl font-black italic tracking-tight text-primary uppercase">IDENTIDAD NEKORA</h2>
                <p class="text-on-surface-variant font-label text-[10px] tracking-widest" data-profile-id-display>ID: ...</p>
              </div>
              <div class="flex items-center gap-2 -mr-4">
                <span class="bg-emerald-500/15 text-emerald-300 px-3 py-1 rounded-full font-label text-[10px] font-bold tracking-widest uppercase inline-flex items-center gap-1.5" data-identity-badge>
                  <span class="material-symbols-outlined text-[14px] leading-none" data-identity-icon>nightlight</span>
                  <span data-identity-text>Modo Dream</span>
                </span>
                <button class="rounded-full bg-surface-container-low/70 border border-white/10 w-8 h-8 flex items-center justify-center text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-colors -mt-1 -ml-1" type="button" id="profile-settings-btn" aria-label="Ajustes de perfil">
                  <span class="material-symbols-outlined text-[18px]">settings</span>
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[auto,1fr] gap-8 items-start">
              <div class="flex flex-col items-center lg:items-start gap-4">
                <div class="relative w-36 h-36 rounded-2xl p-1 bg-gradient-to-br from-violet-500 via-fuchsia-500 to-sky-500 shadow-[0_0_30px_rgba(139,92,246,0.45)] flex-shrink-0">
                  <div class="w-full h-full rounded-2xl bg-surface-container-lowest p-1">
                    <img alt="Foto de perfil" class="w-full h-full object-cover rounded-2xl" data-profile-avatar src="https://upload.wikimedia.org/wikipedia/en/b/bd/Doraemon_character.png"/>
                  </div>
                </div>
              </div>
              <div class="space-y-5 text-center lg:text-left">
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3">
                  <h1 class="font-headline text-4xl md:text-5xl font-extrabold tracking-tight text-on-surface">Cargando...</h1>
                </div>
                <p class="text-on-surface-variant font-medium whitespace-pre-line">...</p>
              </div>
                <div class="lg:col-span-2 space-y-3">
                  <div class="grid w-full grid-cols-3 gap-2 text-left">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-1.5 w-full">
                      <div class="text-[10px] uppercase tracking-widest text-on-surface-variant">Miembro desde</div>
                    <div class="mt-1 text-sm font-semibold text-on-surface" data-profile-member>2018</div>
                  </div>
                  <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-1.5 w-full">
                    <div class="text-[10px] uppercase tracking-widest text-on-surface-variant">&Uacute;ltimo acceso</div>
                    <div class="mt-1 text-sm font-semibold text-on-surface" data-profile-last>Hoy</div>
                  </div>
                  <div class="rounded-2xl border border-white/10 bg-white/5 px-3 py-1.5 w-full">
                    <div class="text-[10px] uppercase tracking-widest text-on-surface-variant">Horas vistas</div>
                    <div class="mt-1 text-sm font-semibold text-on-surface" data-profile-hours>0 h</div>
                  </div>
                </div>
                  <div class="rounded-2xl border border-white/10 bg-white/5 p-4 overflow-hidden">
                    <div class="text-[10px] uppercase tracking-widest text-on-surface-variant mb-2">Preferencias</div>
                    <div class="flex flex-nowrap gap-2 overflow-x-auto no-scrollbar pb-1 h-8 items-center" data-pref-display>
                      <span class="rounded-full border border-white/10 bg-white/10 inline-flex w-fit items-center justify-center px-4 py-1 text-xs text-center text-on-surface-variant h-7 truncate shrink-0">Sin preferencias</span>
                    </div>
                  </div>
                  <div class="mt-3 flex w-full flex-wrap items-center justify-center gap-x-3 gap-y-2 translate-y-2" id="profile-actions">
                <button id="request-title-btn" type="button" aria-label="Solicitar título" class="hidden rounded-full border border-white/10 bg-gradient-to-r from-sky-400 to-violet-400 px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-900 transition-all duration-200 hover:border-sky-200/60 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(56,189,248,0.25)]">
                      Solicitar título
                    </button>
                <button id="logout-btn" type="button" aria-label="Cerrar sesión" class="rounded-full border border-white/10 bg-gradient-to-r from-rose-400 to-orange-300 px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-slate-900 transition-all duration-200 hover:border-rose-200/60 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(248,113,113,0.25)] flex items-center justify-center group/logout">
                  Cerrar sesión
                </button>
                  </div>
                </div>
              </div>
            </div>
        </div>
      </section>

        <section class="lg:col-span-6 lg:h-full grid grid-cols-1 sm:grid-cols-2 gap-5 mt-0 overflow-visible">
        <button type="button" data-open-list="my-list" class="group rounded-3xl border border-white/10 bg-surface-container-low/70 p-6 text-left shadow-[0_16px_40px_rgba(0,0,0,0.35)] transition-all hover:-translate-y-1 hover:border-violet-400/60 hover:shadow-[0_24px_60px_rgba(139,92,246,0.35)]">
          <div class="flex items-center justify-between">
            <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Mi lista</div>
            <span class="material-symbols-outlined text-violet-400">playlist_add</span>
          </div>
          <div class="mt-4 text-4xl font-bold text-on-surface" data-count-mylist>0</div>
          <p class="mt-2 text-sm text-on-surface-variant">T&iacute;tulos guardados para ver despu&eacute;s.</p>
        </button>
        <button type="button" data-open-list="favorites" class="group rounded-3xl border border-white/10 bg-surface-container-low/70 p-6 text-left shadow-[0_16px_40px_rgba(0,0,0,0.35)] transition-all hover:-translate-y-1 hover:border-fuchsia-400/60 hover:shadow-[0_24px_60px_rgba(236,72,153,0.35)]">
          <div class="flex items-center justify-between">
            <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Favoritos</div>
            <span class="material-symbols-outlined text-fuchsia-400">favorite</span>
          </div>
          <div class="mt-4 text-4xl font-bold text-on-surface" data-count-favorites>0</div>
          <p class="mt-2 text-sm text-on-surface-variant">Tus historias imprescindibles.</p>
        </button>
        <button type="button" data-open-status="pending" class="group rounded-3xl border border-white/10 bg-surface-container-low/70 p-6 text-left shadow-[0_16px_40px_rgba(0,0,0,0.35)] transition-all hover:-translate-y-1 hover:border-amber-400/60 hover:shadow-[0_24px_60px_rgba(251,191,36,0.35)]">
          <div class="flex items-center justify-between">
            <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Pendientes</div>
            <span class="material-symbols-outlined text-amber-300">schedule</span>
          </div>
          <div class="mt-4 text-4xl font-bold text-on-surface" data-count-pending>0</div>
          <p class="mt-2 text-sm text-on-surface-variant">Lo pr&oacute;ximo en tu marat&oacute;n.</p>
        </button>
        <button type="button" data-open-status="completed" class="group rounded-3xl border border-white/10 bg-surface-container-low/70 p-6 text-left shadow-[0_16px_40px_rgba(0,0,0,0.35)] transition-all hover:-translate-y-1 hover:border-emerald-400/60 hover:shadow-[0_24px_60px_rgba(52,211,153,0.35)]">
          <div class="flex items-center justify-between">
            <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Completados</div>
            <span class="material-symbols-outlined text-emerald-400">done_all</span>
          </div>
          <div class="mt-4 text-4xl font-bold text-on-surface" data-count-completed>0</div>
          <p class="mt-2 text-sm text-on-surface-variant">Historias finalizadas con &eacute;xito.</p>
        </button>
          <section id="continue-section" class="sm:col-span-2 rounded-3xl border border-white/5 bg-surface-container-low/60 p-5 shadow-[0_18px_40px_rgba(0,0,0,0.35)] overflow-visible min-h-[260px]">
          <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
              <h2 class="font-headline text-2xl font-bold">Continuar viendo</h2>
            </div>
          </div>
          <div class="mt-3 flex min-h-[180px] items-center justify-center overflow-visible">
            <div id="continue-empty" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-8 text-center text-sm text-on-surface-variant">
              Aún no tienes títulos en progreso.
            </div>
            <div id="continue-grid" class="hidden w-full grid grid-cols-4 gap-4 overflow-visible pb-2 pt-3 px-4"></div>
          </div>
        </section>
        <div class="sm:col-span-2 flex min-h-[120px] items-center justify-center py-0">
          <a id="premium-access-btn" href="pago.php" class="rounded-full bg-gradient-to-br from-sky-500 to-violet-500 px-9 py-4 text-base font-bold tracking-wide text-white shadow-lg transition-transform hover:scale-105 active:scale-95 hover:ring-2 hover:ring-white/40 hover:shadow-[0_0_18px_rgba(129,140,248,0.5)] inline-flex items-center justify-center">
            Acceso Premium
          </a>
        </div>
      </section>
    </div>

      <div id="profile-lists" class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-0">
        <div class="lg:col-span-8 space-y-8">
          <section class="space-y-6 rounded-3xl border border-white/5 bg-surface-container-low/60 p-6 shadow-[0_18px_40px_rgba(0,0,0,0.35)]">
            <div class="flex flex-wrap justify-between items-end gap-4">
              <div>
                <h2 class="font-headline text-2xl font-bold">Mi Lista</h2>
                <p class="text-sm text-on-surface-variant">Tus animes listos para continuar.</p>
              </div>
              <div class="flex items-center gap-2">
                <button class="text-primary text-sm font-bold underline-offset-4 hover:underline px-3 py-1.5 rounded-full border border-primary/30 bg-primary/10 hover:bg-primary/20 transition inline-flex items-center gap-1.5" type="button" data-open-list="my-list" aria-disabled="false">
                  <span>Ver todo</span>
                  <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                </button>
              </div>
            </div>
            <div class="user-carousel-wrap">
              <div class="user-carousel-nav-zone user-carousel-nav-left">
                <button class="user-carousel-nav-btn rounded-full bg-surface-container-high/90 px-4 py-3 text-2xl font-bold text-violet-200 hover:text-white hover:bg-surface-container-high transition-colors backdrop-blur" type="button" data-scroll-left="my-list-grid" aria-label="Desplazar a la izquierda">&lsaquo;</button>
              </div>
              <div class="user-carousel-nav-zone user-carousel-nav-right">
                <button class="user-carousel-nav-btn rounded-full bg-surface-container-high/90 px-4 py-3 text-2xl font-bold text-violet-200 hover:text-white hover:bg-surface-container-high transition-colors backdrop-blur" type="button" data-scroll-right="my-list-grid" aria-label="Desplazar a la derecha">&rsaquo;</button>
              </div>
              <div id="my-list-grid" class="flex gap-4 overflow-x-auto no-scrollbar overflow-visible py-6 scroll-smooth snap-x snap-mandatory"></div>
            </div>
          </section>

          <section class="space-y-6 rounded-3xl border border-white/5 bg-surface-container-low/60 p-6 shadow-[0_18px_40px_rgba(0,0,0,0.35)]">
            <div class="flex flex-wrap justify-between items-end gap-4">
              <div>
                <h2 class="font-headline text-2xl font-bold">Favoritos</h2>
                <p class="text-sm text-on-surface-variant">Las historias que te marcaron.</p>
              </div>
              <div class="flex items-center gap-2">
                <button class="text-primary text-sm font-bold underline-offset-4 hover:underline px-3 py-1.5 rounded-full border border-primary/30 bg-primary/10 hover:bg-primary/20 transition inline-flex items-center gap-1.5" type="button" data-open-list="favorites" aria-disabled="false">
                  <span>Ver todo</span>
                  <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                </button>
              </div>
            </div>
            <div class="user-carousel-wrap">
              <div class="user-carousel-nav-zone user-carousel-nav-left">
                <button class="user-carousel-nav-btn rounded-full bg-surface-container-high/90 px-4 py-3 text-2xl font-bold text-violet-200 hover:text-white hover:bg-surface-container-high transition-colors backdrop-blur" type="button" data-scroll-left="favorites-grid" aria-label="Desplazar a la izquierda">&lsaquo;</button>
              </div>
              <div class="user-carousel-nav-zone user-carousel-nav-right">
                <button class="user-carousel-nav-btn rounded-full bg-surface-container-high/90 px-4 py-3 text-2xl font-bold text-violet-200 hover:text-white hover:bg-surface-container-high transition-colors backdrop-blur" type="button" data-scroll-right="favorites-grid" aria-label="Desplazar a la derecha">&rsaquo;</button>
              </div>
              <div id="favorites-grid" class="flex gap-4 overflow-x-auto no-scrollbar overflow-visible py-6 scroll-smooth snap-x snap-mandatory"></div>
            </div>
          </section>
        </div>

        <aside class="lg:col-span-4 space-y-6">
          <div class="space-y-4 bg-gradient-to-b from-surface-container-low to-surface-container-high/70 rounded-3xl p-6 border border-white/5 shadow-[0_20px_50px_rgba(0,0,0,0.4)]">
            <div class="flex items-center justify-between">
              <h2 class="font-headline text-xl font-bold">Recomendados</h2>
            </div>
            <div class="space-y-3">
              <button class="w-full bg-surface-container-low/80 hover:bg-surface-container-high rounded-2xl p-3 flex items-center gap-3 border border-white/5 hover:border-violet-400/40 hover:shadow-[0_0_20px_rgba(139,92,246,0.35)] hover:scale-[1.02] transition-all cursor-pointer relative" data-mal-id="5114" data-title="Fullmetal Alchemist: Brotherhood" onclick="window.location.href='detail.php?mal_id=5114&q=Fullmetal%20Alchemist%3A%20Brotherhood'">
                <span class="absolute top-2 right-2 rounded-full bg-violet-500/90 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-widest text-white">Anime</span>
                <img src="https://cdn.myanimelist.net/images/anime/1223/96541.jpg" alt="Fullmetal Alchemist: Brotherhood" class="w-12 h-16 object-cover rounded-md" />
                <div class="text-left">
                  <p class="text-sm font-bold">Fullmetal Alchemist: Brotherhood</p>
                  <p class="text-xs text-on-surface-variant">Acci&oacute;n - Aventura</p>
                </div>
              </button>
              <button class="w-full bg-surface-container-low/80 hover:bg-surface-container-high rounded-2xl p-3 flex items-center gap-3 border border-white/5 hover:border-violet-400/40 hover:shadow-[0_0_20px_rgba(139,92,246,0.35)] hover:scale-[1.02] transition-all cursor-pointer relative" data-mal-id="9253" data-title="Steins;Gate" onclick="window.location.href='detail.php?mal_id=9253&q=Steins%3BGate'">
                <span class="absolute top-2 right-2 rounded-full bg-violet-500/90 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-widest text-white">Anime</span>
                <img src="https://cdn.myanimelist.net/images/anime/5/73199.jpg" alt="Steins;Gate" class="w-12 h-16 object-cover rounded-md" />
                <div class="text-left">
                  <p class="text-sm font-bold">Steins;Gate</p>
                  <p class="text-xs text-on-surface-variant">Sci-Fi - Suspenso</p>
                </div>
              </button>
              <button class="w-full bg-surface-container-low/80 hover:bg-surface-container-high rounded-2xl p-3 flex items-center gap-3 border border-white/5 hover:border-violet-400/40 hover:shadow-[0_0_20px_rgba(139,92,246,0.35)] hover:scale-[1.02] transition-all cursor-pointer relative" data-mal-id="32281" data-title="Kimi no Na wa." onclick="window.location.href='detail.php?mal_id=32281&q=Kimi%20no%20Na%20wa.'">
                <span class="absolute top-2 right-2 rounded-full bg-fuchsia-500/90 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-widest text-white">Pel&iacute;cula</span>
                <img src="https://cdn.myanimelist.net/images/anime/5/87048.jpg" alt="Kimi no Na wa." class="w-12 h-16 object-cover rounded-md" />
                <div class="text-left">
                  <p class="text-sm font-bold">Kimi no Na wa.</p>
                  <p class="text-xs text-on-surface-variant">Romance - Fantas&iacute;a</p>
                </div>
              </button>
              <button class="w-full bg-surface-container-low/80 hover:bg-surface-container-high rounded-2xl p-3 flex items-center gap-3 border border-white/5 hover:border-violet-400/40 hover:shadow-[0_0_20px_rgba(139,92,246,0.35)] hover:scale-[1.02] transition-all cursor-pointer relative" data-mal-id="16498" data-title="Shingeki no Kyojin" onclick="window.location.href='detail.php?mal_id=16498&q=Shingeki%20no%20Kyojin'">
                <span class="absolute top-2 right-2 rounded-full bg-violet-500/90 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-widest text-white">Anime</span>
                <img src="https://cdn.myanimelist.net/images/anime/10/47347.jpg" alt="Shingeki no Kyojin" class="w-12 h-16 object-cover rounded-md" />
                <div class="text-left">
                  <p class="text-sm font-bold">Attack on Titan</p>
                  <p class="text-xs text-on-surface-variant">Acci&oacute;n - Drama</p>
                </div>
              </button>
              <button class="w-full bg-surface-container-low/80 hover:bg-surface-container-high rounded-2xl p-3 flex items-center gap-3 border border-white/5 hover:border-violet-400/40 hover:shadow-[0_0_20px_rgba(139,92,246,0.35)] hover:scale-[1.02] transition-all cursor-pointer relative" data-mal-id="12355" data-title="Ookami Kodomo no Ame to Yuki" onclick="window.location.href='detail.php?mal_id=12355&q=Ookami%20Kodomo%20no%20Ame%20to%20Yuki'">
                <span class="absolute top-2 right-2 rounded-full bg-fuchsia-500/90 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-widest text-white">Pel&iacute;cula</span>
                <img src="https://cdn.myanimelist.net/images/anime/9/35721.jpg" alt="Wolf Children" class="w-12 h-16 object-cover rounded-md" />
                <div class="text-left">
                  <p class="text-sm font-bold">Wolf Children</p>
                  <p class="text-xs text-on-surface-variant">Drama - Fantas&iacute;a</p>
                </div>
              </button>
              <button class="w-full bg-surface-container-low/80 hover:bg-surface-container-high rounded-2xl p-3 flex items-center gap-3 border border-white/5 hover:border-violet-400/40 hover:shadow-[0_0_20px_rgba(139,92,246,0.35)] hover:scale-[1.02] transition-all cursor-pointer relative" data-mal-id="40591" data-title="Kaguya-sama: Love is War" onclick="window.location.href='detail.php?mal_id=40591&q=Kaguya-sama%3A%20Love%20is%20War'">
                <span class="absolute top-2 right-2 rounded-full bg-violet-500/90 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-widest text-white">Anime</span>
                <img src="https://cdn.myanimelist.net/images/anime/1295/106551.jpg" alt="Kaguya-sama: Love is War" class="w-12 h-16 object-cover rounded-md" />
                <div class="text-left">
                  <p class="text-sm font-bold">Kaguya-sama: Love is War</p>
                  <p class="text-xs text-on-surface-variant">Romance - Comedia</p>
                </div>
              </button>
            </div>
            <a class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-gradient-to-br from-violet-500 to-fuchsia-600 px-4 py-2 text-xs font-bold uppercase tracking-widest text-white shadow-lg hover:scale-105 active:scale-95 transition-transform" href="destacados.php">Ver m&aacute;s</a>
          </div>
        </aside>
      </div>
    </main>

    <!-- Avatar Picker Modal -->
    <div id="avatar-modal" class="fixed inset-0 z-[80] hidden">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-avatar-close></div>
      <div class="relative mx-auto mt-28 w-[86%] max-w-[580px] rounded-lg bg-surface-container-high p-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-headline text-xl font-bold">Elige tu nuevo avatar</h3>
          <button class="rounded-full bg-surface-container-low w-8 h-8 flex items-center justify-center text-xs font-bold text-on-surface-variant hover:text-on-surface" type="button" data-avatar-close aria-label="Cerrar">x</button>
        </div>
        <p class="text-sm text-on-surface-variant mb-4">Selecciona una imagen para tu perfil.</p>
        <div class="max-h-[420px] overflow-y-auto pr-2 transparent-scrollbar">
          <div id="avatar-grid" class="grid grid-cols-5 gap-3"></div>
        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="profile-modal" class="fixed inset-0 z-[60] hidden">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-profile-close></div>
      <div class="relative mx-auto mt-16 w-[78%] max-w-md rounded-2xl border border-white/10 bg-surface-container-high p-5 shadow-[0_24px_60px_rgba(0,0,0,0.5)]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-headline text-xl font-bold">Editar perfil</h3>
          <button class="rounded-full bg-surface-container-low w-8 h-8 flex items-center justify-center text-xs font-bold text-on-surface-variant hover:text-on-surface" type="button" data-profile-close aria-label="Cerrar">x</button>
        </div>
          <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-2 transparent-scrollbar">
          <div class="flex items-center gap-4 rounded-2xl p-4 soft-accent">
            <img data-profile-avatar-preview src="https://upload.wikimedia.org/wikipedia/en/b/bd/Doraemon_character.png" alt="Avatar actual" class="w-14 h-14 rounded-xl object-cover" />
            <div class="flex-1">
              <p class="text-xs uppercase tracking-widest text-on-surface-variant">Foto de perfil</p>
              <button id="profile-avatar-open" class="mt-2 inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:text-on-surface soft-button" type="button">
                <span class="material-symbols-outlined text-[16px]">image</span>
                Cambiar avatar
              </button>
            </div>
          </div>
          <div class="space-y-2 soft-accent p-4 rounded-2xl">
            <label class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold">Nombre de usuario</label>
            <input id="profile-name-input" class="w-full rounded-xl bg-surface-container-low border border-outline-variant/60 px-4 py-3 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/40" type="text" placeholder="Tu nombre" />
          </div>
          <div class="space-y-2 soft-accent p-4 rounded-2xl">
            <label class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold">Descripción</label>
            <textarea id="profile-desc-input" maxlength="200" class="w-full rounded-xl bg-surface-container-low border border-outline-variant/60 px-4 py-3 text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/40 min-h-[120px]" placeholder="Escribe una breve Descripción (máx. 200 caracteres)"></textarea>
            <div class="text-[11px] text-on-surface-variant text-right" id="profile-desc-count">0/200</div>
          </div>
          <div class="space-y-3 soft-accent p-4 rounded-2xl">
            <label class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold">Preferencias</label>
            <div id="preferences-picker" class="space-y-3"></div>
            <p class="text-[11px] text-on-surface-variant">Selecciona varias opciones y se verán reflejadas en tu perfil.</p>
          </div>
          <div class="space-y-2">
            <label class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold">Color del recuadro</label>
            <div id="profile-color-options" class="flex flex-wrap gap-3">
              <button type="button" data-color-class="bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800" class="w-9 h-9 rounded-full bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 border border-zinc-700 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(255,255,255,0.18)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-violet-900/50 via-indigo-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-900/50 via-indigo-900/40 to-slate-900/30 border border-violet-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(139,92,246,0.35)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-fuchsia-900/50 via-rose-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-fuchsia-900/50 via-rose-900/40 to-slate-900/30 border border-rose-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(244,114,182,0.35)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-emerald-900/50 via-teal-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-900/50 via-teal-900/40 to-slate-900/30 border border-emerald-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(52,211,153,0.35)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-sky-900/50 via-cyan-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-900/50 via-cyan-900/40 to-slate-900/30 border border-sky-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(56,189,248,0.35)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-amber-900/50 via-orange-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-900/50 via-orange-900/40 to-slate-900/30 border border-amber-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(251,191,36,0.35)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-lime-900/50 via-green-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-lime-900/50 via-green-900/40 to-slate-900/30 border border-lime-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(163,230,53,0.35)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-red-900/50 via-orange-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-red-900/50 via-orange-900/40 to-slate-900/30 border border-red-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(248,113,113,0.35)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-blue-900/50 via-indigo-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-900/50 via-indigo-900/40 to-slate-900/30 border border-blue-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(96,165,250,0.35)]"></button>
              <button type="button" data-color-class="bg-gradient-to-br from-purple-900/50 via-pink-900/40 to-slate-900/30" class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-900/50 via-pink-900/40 to-slate-900/30 border border-pink-400/40 transition-transform duration-300 hover:scale-110 hover:shadow-[0_0_18px_rgba(236,72,153,0.35)]"></button>
            </div>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button class="rounded-full border border-white/10 bg-surface-container-low px-4 py-2 text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:text-on-surface" type="button" data-profile-close>Cancelar</button>
            <button id="profile-save-btn" class="rounded-full bg-primary/90 text-on-primary px-6 py-2 text-xs font-bold uppercase tracking-widest shadow-[0_10px_24px_rgba(139,92,246,0.3)] hover:scale-105 active:scale-95 transition-transform" type="button">Guardar</button>
          </div>
        </div>


      </div>
    </div>

    <!-- Request Title Modal -->
    <div id="request-title-modal" class="fixed inset-0 z-[70] hidden">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-request-close></div>
      <div class="relative mx-auto mt-24 w-[84%] max-w-sm rounded-2xl border border-white/10 bg-surface-container-high p-5 shadow-[0_20px_50px_rgba(0,0,0,0.45)]">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-headline text-lg font-bold">Solicitar título</h3>
          <button class="rounded-full bg-surface-container-low w-8 h-8 flex items-center justify-center text-xs font-bold text-on-surface-variant hover:text-on-surface" type="button" data-request-close aria-label="Cerrar">x</button>
        </div>
        <form id="request-title-form" class="space-y-4">
          <div class="space-y-2">
            <label class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold">Nombre del anime o película</label>
            <input id="request-title-input" type="text" class="w-full rounded-xl border border-white/10 bg-surface-container-low/70 px-4 py-2 text-sm text-on-surface outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/20" placeholder="Ej: Fullmetal Alchemist: Brotherhood" />
          </div>
          <div class="space-y-2">
            <label class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold">Tipo</label>
            <select id="request-title-type" class="w-full rounded-xl border border-white/10 bg-surface-container-low/70 px-4 py-2 text-sm text-on-surface outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/20">
              <option value="Anime">Anime</option>
              <option value="Película">Película</option>
            </select>
          </div>
          <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" data-request-close class="rounded-full border border-white/10 bg-surface-container-low/70 px-4 py-2 text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:text-on-surface transition-colors">Cancelar</button>
            <button type="submit" class="rounded-full border border-white/10 bg-gradient-to-r from-sky-400 to-violet-400 px-4 py-2 text-xs font-bold uppercase tracking-widest text-slate-900 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(56,189,248,0.25)]">Solicitar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Request Toast -->
    <div id="request-toast" class="fixed inset-0 z-[90] hidden items-center justify-center">
      <div class="rounded-full border border-white/10 bg-surface-container-high/90 px-6 py-3 text-sm font-semibold text-on-surface shadow-[0_18px_40px_rgba(0,0,0,0.45)]">
        Título solicitado
      </div>
    </div>

    <!-- Logout Confirm Modal -->
    <div id="logout-confirm" class="fixed inset-0 z-[90] hidden items-center justify-center">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-logout-close></div>
      <div class="relative mx-auto w-[86%] max-w-xs rounded-2xl border border-white/10 bg-surface-container-high p-5 text-center shadow-[0_20px_50px_rgba(0,0,0,0.45)]">
        <h3 class="font-headline text-lg font-bold">¿Seguro que quieres cerrar sesión?</h3>
        <p class="mt-2 text-sm text-on-surface-variant">Se perderán tus cambios locales.</p>
        <div class="mt-4 flex items-center justify-center gap-3">
          <button type="button" data-logout-cancel class="rounded-full border border-white/10 bg-surface-container-low/70 px-4 py-2 text-xs font-bold uppercase tracking-widest text-on-surface-variant hover:text-on-surface transition-colors">
            cancelar
          </button>
          <button type="button" data-logout-confirm class="rounded-full border border-white/10 bg-gradient-to-r from-rose-400 to-orange-300 px-4 py-2 text-xs font-bold uppercase tracking-widest text-slate-900 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_10px_24px_rgba(248,113,113,0.25)]">
            cerrar
          </button>
        </div>
      </div>
    </div>

    <!-- Full Lists Modal -->
    <div id="lists-modal" class="fixed inset-0 z-[70] hidden theme-mylist flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-lists-close></div>
      <div class="relative w-[62%] max-w-lg rounded-3xl border border-white/10 p-4 md:p-4 modal-shell">
        <div class="modal-accent"></div>
        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
          <div>
            <h3 class="font-headline text-2xl font-bold" id="lists-modal-title">Mi Lista</h3>
            <p class="text-sm text-on-surface-variant">Explora todos tus t&iacute;tulos guardados.</p>
          </div>
          <div class="flex items-center gap-2">
            <span id="lists-count-mylist" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-on-surface-variant">Mi lista: <span data-count-mylist>0</span></span>
            <span id="lists-count-favorites" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-on-surface-variant">Favoritos: <span data-count-favorites>0</span></span>
            <button class="rounded-full bg-surface-container-low w-9 h-9 flex items-center justify-center text-xs font-bold text-on-surface-variant hover:text-on-surface" type="button" data-lists-close aria-label="Cerrar">
              <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
          </div>
        </div>
        <div id="lists-modal-body" class="flex-1 overflow-y-auto overflow-x-hidden pr-1 pb-2 transparent-scrollbar">
          <div id="my-list-modal" class="space-y-4 hidden">
            <div id="my-list-grid-full" data-show-all="1" class="grid grid-cols-5 gap-2 modal-grid"></div>
          </div>
          <div id="favorites-modal" class="space-y-4 hidden">
            <div id="favorites-grid-full" data-show-all="1" class="grid grid-cols-5 gap-2 modal-grid"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Modal -->
    <div id="status-modal" class="fixed inset-0 z-[70] hidden theme-completed flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-status-close></div>
      <div class="relative w-[62%] max-w-lg rounded-3xl border border-white/10 p-4 md:p-4 modal-shell">
        <div class="modal-accent"></div>
        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
          <div>
            <h3 class="font-headline text-2xl font-bold" id="status-modal-title">Completados</h3>
            <p class="text-sm text-on-surface-variant">Tus avances organizados por estado.</p>
          </div>
          <div class="flex items-center gap-2">
            <span id="status-count-completed" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-on-surface-variant">Completados: <span data-count-completed>0</span></span>
            <span id="status-count-pending" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-on-surface-variant">Pendientes: <span data-count-pending>0</span></span>
            <button class="rounded-full bg-surface-container-low w-9 h-9 flex items-center justify-center text-xs font-bold text-on-surface-variant hover:text-on-surface" type="button" data-status-close aria-label="Cerrar">
              <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
          </div>
        </div>
        <div id="status-modal-body" class="flex-1 overflow-y-auto overflow-x-hidden pr-1 pb-2 transparent-scrollbar">
          <div id="completed-section" class="space-y-4 hidden">
            <div id="completed-grid" class="grid grid-cols-5 gap-2 modal-grid"></div>
          </div>
          <div id="pending-section" class="space-y-4 hidden">
            <div id="pending-grid" class="grid grid-cols-5 gap-2 modal-grid"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Component -->
    <div data-layout="footer"></div>
    <script src="controllers/layout.js?v=final14"></script>
    <script src="controllers/i18n.js"></script>
    <script src="controllers/title-images.js?v=3"></script>
    <script src="controllers/search.js"></script>
    <script src="controllers/favorites.js"></script>
    <script src="controllers/detail-links.js"></script>
    <script>
  document.addEventListener("DOMContentLoaded", () => {
    if (window.AniDexI18n) window.AniDexI18n.init();
    if (window.AniDexTitleImages) window.AniDexTitleImages.init();
    if (window.AniDexSearch) window.AniDexSearch.init();
    if (window.AniDexDetailLinks) window.AniDexDetailLinks.init();
    if (window.AniDexFavorites) window.AniDexFavorites.init();
  });
    </script>
    <script>
  /* MVC: User Profile (Model / View / Controller) */
  (function () {
    // ========================
    // MODEL: Data & Storage
    // ========================
    const editBtn = document.getElementById("profile-avatar-edit");
    const openBtn = document.getElementById("profile-avatar-open");
    const modal = document.getElementById("avatar-modal");
    const profileModal = document.getElementById("profile-modal");
    const grid = document.getElementById("avatar-grid");
    const avatarImg = document.querySelector("img[alt='Foto de perfil']");
    const previewImg = document.querySelector("[data-profile-avatar-preview]");

    if (!modal || !grid || !avatarImg) return;

    const avatars = [
      "https://upload.wikimedia.org/wikipedia/en/b/bd/Doraemon_character.png"
    ];

    let returnToProfile = false;
    const openModal = () => {
      returnToProfile = !!(profileModal && !profileModal.classList.contains("hidden"));
      if (returnToProfile) profileModal.classList.add("hidden");
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    };
    const closeModal = () => {
      modal.classList.add("hidden");
      if (returnToProfile && profileModal) {
        profileModal.classList.remove("hidden");
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "";
      }
      returnToProfile = false;
    };

    const renderAvatars = () => {
      if (!avatars.length) {
        grid.innerHTML = Array.from({ length: 12 }).map(() => `
          <div class="aspect-square bg-surface-container-low/80 animate-pulse"></div>
        `).join("");
        return;
      }
      grid.innerHTML = avatars.map((src) => `
        <button type="button" class="group relative aspect-square overflow-hidden bg-surface-container-low hover:bg-surface-container-high transition-colors" data-avatar-src="${src}">
          <img src="${src}" alt="Avatar" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" decoding="async" />
        </button>
      `).join("");
    };
    renderAvatars();

    // Cargar avatares desde Jikan (personajes populares)
    Promise.all([
      fetch("https://api.jikan.moe/v4/characters?order_by=favorites&sort=desc&limit=24&page=1", { cache: "no-store" }),
      fetch("https://api.jikan.moe/v4/characters?order_by=favorites&sort=desc&limit=8&page=2", { cache: "no-store" })
    ])
      .then(async ([res1, res2]) => {
        const json1 = res1 && res1.ok ? await res1.json() : null;
        const json2 = res2 && res2.ok ? await res2.json() : null;
        const list = [
          ...(json1 && json1.data ? json1.data : []),
          ...(json2 && json2.data ? json2.data : [])
        ];
        list.forEach((it) => {
          const img = it?.images?.webp?.image_url || it?.images?.jpg?.image_url;
          if (img) avatars.push(img);
        });
        renderAvatars();
      })
      .catch(() => {
        grid.innerHTML = '<div class="col-span-full text-center text-xs text-on-surface-variant">No se pudieron cargar avatares.</div>';
      });

    const syncAvatars = (src) => {
      if (!src) return;
      avatarImg.src = src;
      document.querySelectorAll("img[data-profile-avatar]").forEach((img) => {
        img.src = src;
      });
      try { 
          localStorage.setItem("anidex_profile_avatar", src); 
          saveProfileToDB();
      } catch (e) {}
    };

    const saved = (() => { try { return localStorage.getItem("anidex_profile_avatar"); } catch (e) { return null; } })();
    if (saved) syncAvatars(saved);

    grid.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-avatar-src]");
      if (!btn) return;
      const src = btn.getAttribute("data-avatar-src");
      if (src) {
        if (returnToProfile) {
          if (previewImg) previewImg.src = src;
          window.__profilePendingAvatar = src;
        } else {
          syncAvatars(src);
        }
      }
      closeModal();
    });

    if (editBtn) editBtn.addEventListener("click", openModal);
    if (openBtn) openBtn.addEventListener("click", openModal);
    modal.querySelectorAll("[data-avatar-close]").forEach((el) => el.addEventListener("click", closeModal));
  })();
    </script>
    <script>
  /* MVC: Session & Access (Model / View / Controller) */
  (function () {
    // MODEL: session flags from localStorage
    const modal = document.getElementById("lists-modal");
    const title = document.getElementById("lists-modal-title");
    const myWrap = document.getElementById("my-list-modal");
    const favWrap = document.getElementById("favorites-modal");
    const countMy = document.getElementById("lists-count-mylist");
    const countFav = document.getElementById("lists-count-favorites");
    if (!modal || !title || !myWrap || !favWrap) return;

    const openModal = (which) => {
      modal.classList.remove("theme-mylist", "theme-favorites");
      modal.classList.add(which === "my-list" ? "theme-mylist" : "theme-favorites");
      if (which === "my-list") {
        title.textContent = "Mi Lista";
        myWrap.classList.remove("hidden");
        favWrap.classList.add("hidden");
        if (countMy) countMy.classList.remove("hidden");
        if (countFav) countFav.classList.add("hidden");
      } else {
        title.textContent = "Favoritos";
        favWrap.classList.remove("hidden");
        myWrap.classList.add("hidden");
        if (countFav) countFav.classList.remove("hidden");
        if (countMy) countMy.classList.add("hidden");
      }
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    };
    const closeModal = () => {
      modal.classList.add("hidden");
      document.body.style.overflow = "";
    };

    document.querySelectorAll("[data-open-list]").forEach((btn) => {
      btn.addEventListener("click", () => {
        if (btn.disabled) return;
        openModal(btn.getAttribute("data-open-list"));
      });
    });
    modal.querySelectorAll("[data-lists-close]").forEach((el) => el.addEventListener("click", closeModal));
  })();
    </script>
    <script>
  (function () {
    const modal = document.getElementById("status-modal");
    const title = document.getElementById("status-modal-title");
    const completed = document.getElementById("completed-section");
    const pending = document.getElementById("pending-section");
    const countCompleted = document.getElementById("status-count-completed");
    const countPending = document.getElementById("status-count-pending");
    if (!modal || !title || !completed || !pending) return;

    const openModal = (which) => {
      modal.classList.remove("theme-completed", "theme-pending");
      modal.classList.add(which === "completed" ? "theme-completed" : "theme-pending");
      if (which === "completed") {
        title.textContent = "Completados";
        completed.classList.remove("hidden");
        pending.classList.add("hidden");
        if (countCompleted) countCompleted.classList.remove("hidden");
        if (countPending) countPending.classList.add("hidden");
      } else {
        title.textContent = "Pendientes";
        pending.classList.remove("hidden");
        completed.classList.add("hidden");
        if (countPending) countPending.classList.remove("hidden");
        if (countCompleted) countCompleted.classList.add("hidden");
      }
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    };
    const closeModal = () => {
      modal.classList.add("hidden");
      document.body.style.overflow = "";
    };

    document.querySelectorAll("[data-open-status]").forEach((btn) => {
      btn.addEventListener("click", () => {
        if (btn.disabled) return;
        openModal(btn.getAttribute("data-open-status"));
      });
    });
    modal.querySelectorAll("[data-status-close]").forEach((el) => el.addEventListener("click", closeModal));
  })();
    </script>
    <script>
  (function () {
    const bindCarousel = (rowId) => {
      const row = document.getElementById(rowId);
      const prev = document.querySelector(`[data-scroll-left="${rowId}"]`);
      const next = document.querySelector(`[data-scroll-right="${rowId}"]`);
      if (!row || !prev || !next) return null;

      const step = () => {
        const card = row.querySelector(":scope > *");
        if (!card) return Math.max(260, Math.round(row.clientWidth * 0.8));
        const styles = getComputedStyle(row);
        const gap = parseFloat(styles.columnGap || styles.gap || "0") || 0;
        const cardWidth = card.getBoundingClientRect().width;
        return Math.round(cardWidth + gap);
      };
      const setDisabled = (btn, disabled) => {
        btn.disabled = disabled;
        btn.classList.toggle("is-disabled", disabled);
        const zone = btn.closest(".user-carousel-nav-zone");
        if (zone) zone.classList.toggle("is-disabled", disabled);
      };
      const updateState = () => {
        const max = row.scrollWidth - row.clientWidth;
        if (max <= 2) {
          setDisabled(prev, true);
          setDisabled(next, true);
          return;
        }
        setDisabled(prev, row.scrollLeft <= 2);
        setDisabled(next, row.scrollLeft >= max - 2);
      };
      prev.addEventListener("click", () => row.scrollBy({ left: -step(), behavior: "smooth" }));
      next.addEventListener("click", () => row.scrollBy({ left: step(), behavior: "smooth" }));
      row.addEventListener("scroll", () => requestAnimationFrame(updateState));
      window.addEventListener("resize", updateState);
      setTimeout(updateState, 0);
      return updateState;
    };

    const updates = [
      bindCarousel("my-list-grid"),
      bindCarousel("favorites-grid")
    ].filter(Boolean);

    window.refreshUserCarousels = () => {
      updates.forEach((fn) => fn());
    };
  })();
    </script>
    <script>
  (function () {
    // DOM Elements
    const settingsBtn = document.getElementById("profile-settings-btn");
    const editBtn = document.getElementById("edit-profile-btn");
    const modal = document.getElementById("profile-modal");
    const closeEls = modal ? modal.querySelectorAll("[data-profile-close]") : [];
    
    const requestBtn = document.getElementById("request-title-btn");
    const requestModal = document.getElementById("request-title-modal");
    const requestForm = document.getElementById("request-title-form");
    const requestInput = document.getElementById("request-title-input");
    const requestToast = document.getElementById("request-toast");
    const requestCloseEls = requestModal ? requestModal.querySelectorAll("[data-request-close]") : [];
    
    const idDisplay = document.querySelector("[data-profile-id-display]");
    console.log("AniDex DEBUG: idDisplay =", idDisplay);
    const avatarImg = document.querySelector("img[alt='Foto de perfil']");
    const nameInput = document.getElementById("profile-name-input");
    const descInput = document.getElementById("profile-desc-input");
    const saveBtn = document.getElementById("profile-save-btn");
    const descCount = document.getElementById("profile-desc-count");
    
    const nameEl = document.querySelector("h1.font-headline");
    const descEl = document.querySelector("p.text-on-surface-variant.font-medium");
    const cardEl = document.getElementById("profile-card");
    const memberEl = document.querySelector("[data-profile-member]");
    const lastEl = document.querySelector("[data-profile-last]");
    const hoursEl = document.querySelector("[data-profile-hours]");
    
    const colorOptions = document.getElementById("profile-color-options");
    const prefsWrap = document.getElementById("preferences-picker");
    const prefsDisplay = document.querySelector("[data-pref-display]");

    // Constants
    const PREF_KEY = "anidex_profile_prefs";
    const PREF_GROUPS = {
      "Idioma": ["Sub. español", "Doblaje español", "Japonés", "Inglés"],
      "Género": ["Acción", "Fantasía", "Drama", "Comedia", "Romance", "Terror", "Sci-Fi"]
    };
    const PREF_DEFAULTS = { "Idioma": [], "Género": [] };
    const PREF_GROUP_STYLES = {
      "Idioma": { baseBorder: "border-blue-400/70", baseBg: "bg-blue-500/45", activeBorder: "border-blue-300/90", activeBg: "bg-blue-500/70", hoverRing: "hover:ring-0", glow: "" },
      "Género": { baseBorder: "border-violet-400/70", baseBg: "bg-violet-500/45", activeBorder: "border-violet-300/90", activeBg: "bg-violet-500/70", hoverRing: "hover:ring-0", glow: "" }
    };
    const colorClasses = [
      "bg-gradient-to-br", "from-zinc-950", "from-zinc-900", "via-zinc-900", "to-zinc-800",
      "from-violet-900/50", "via-indigo-900/40", "to-slate-900/30",
      "from-fuchsia-900/50", "via-rose-900/40", "from-emerald-900/50", "via-teal-900/40",
      "from-sky-900/50", "via-cyan-900/40", "from-amber-900/50", "via-orange-900/40",
      "from-lime-900/50", "via-green-900/40", "from-red-900/50", "from-blue-900/50",
      "from-purple-900/50", "via-pink-900/40"
    ];

    let pendingPrefs = null;
    const isLogged = (window.AniDexLayout && typeof window.AniDexLayout.isLoggedIn === 'function') 
      ? window.AniDexLayout.isLoggedIn() 
      : (localStorage.getItem("nekora_logged_in") === "true");

    // Functions
    const prefStyleFor = (group, isActive) => {
      const gKey = group === "Género" ? "G\u00e9nero" : group; 
      const palette = PREF_GROUP_STYLES[group] || PREF_GROUP_STYLES[gKey] || {};
      const base = `${palette.baseBg || "bg-white/10"} ${palette.baseBorder || "border-white/20"} text-white ${palette.glow || ""} ${palette.hoverRing || "hover:ring-2 hover:ring-white/20"}`;
      return isActive ? `bg-slate-400/70 border-slate-500/80 text-white ${palette.glow || ""} ring-2 ring-white/35` : `${base} opacity-85 hover:opacity-100`;
    };

    const normalizePrefs = (prefs) => {
      const out = {};
      Object.keys(PREF_GROUPS).forEach((k) => { out[k] = prefs && Array.isArray(prefs[k]) ? prefs[k] : []; });
      return out;
    };

    const loadPrefs = () => {
      try {
        const raw = localStorage.getItem(PREF_KEY);
        if (raw) return normalizePrefs(JSON.parse(raw));
      } catch (e) {}
      return normalizePrefs(PREF_DEFAULTS);
    };

    const renderPrefDisplay = (prefs) => {
      if (!prefsDisplay) return;
      const chips = [];
      Object.keys(PREF_GROUPS).forEach((group) => {
        (prefs[group] || []).forEach((value) => {
          chips.push({ value, style: prefStyleFor(group, false) });
        });
      });
      prefsDisplay.innerHTML = chips.length
        ? chips.map(c => `<span class="rounded-full border ${c.style} inline-flex items-center justify-center px-1 py-1 text-[10px] text-center h-7 truncate shrink-0 w-[110px]">${c.value}</span>`).join("")
        : '<span class="rounded-full border border-white/10 bg-white/10 inline-flex items-center justify-center px-1 py-1 text-[10px] text-center text-on-surface-variant h-7 truncate shrink-0 w-[110px]">Sin preferencias</span>';
    };

    const renderPrefPicker = (prefs) => {
      if (!prefsWrap) return;
      prefsWrap.innerHTML = Object.entries(PREF_GROUPS).map(([group, values]) => `
        <div class="space-y-4">
          <h4 class="text-xs uppercase tracking-[0.2em] text-on-surface-variant font-bold">${group}</h4>
            <div class="grid grid-cols-2 gap-2">
              ${values.map(val => {
                const active = (prefs[group] || []).includes(val);
                return `<button type="button" data-pref-cat="${group}" data-pref-val="${val}" class="transition-all duration-300 ${prefStyleFor(group, active)}">${val}</button>`;
              }).join("")}
            </div>
        </div>
      `).join("");
    };

    const loadProfile = (skipRestore = false) => {
      try {
        const userSuffix = (window.AniDexProfile && window.AniDexProfile.getOrCreateUserSuffix) ? AniDexProfile.getOrCreateUserSuffix() : (localStorage.getItem("anidex_user_suffix") || "22");
        const defaultName = `NekoraUser_${userSuffix}`;
        const userId = (window.AniDexProfile && window.AniDexProfile.getOrCreateUserId) ? AniDexProfile.getOrCreateUserId() : (localStorage.getItem("anidex_user_id") || "NK-000000");
        const savedName = localStorage.getItem("anidex_profile_name");
        const savedDesc = localStorage.getItem("anidex_profile_desc");
        const savedColor = localStorage.getItem("anidex_profile_color");
        const savedMember = localStorage.getItem("anidex_profile_member_since");
        const savedHours = localStorage.getItem("anidex_profile_hours");
        
        if (idDisplay) idDisplay.textContent = `ID: ${userId}`;
        if (nameEl) nameEl.textContent = (savedName || defaultName).trim();
        if (descEl) descEl.textContent = (savedDesc || "Explorador de animes en NekoraList").trim();

        if (memberEl) {
          const yearNow = new Date().getFullYear();
          memberEl.textContent = savedMember || String(yearNow);
        }

        if (hoursEl) {
          const hoursVal = (savedHours && savedHours.trim() ? savedHours.trim() : "0");
          hoursEl.textContent = (window.AniDexProfile && window.AniDexProfile.formatHours) 
            ? window.AniDexProfile.formatHours(hoursVal) 
            : `${hoursVal} h`;
        }

        if (savedColor && cardEl) {
          colorClasses.forEach((c) => cardEl.classList.remove(c));
          savedColor.split(" ").forEach((c) => cardEl.classList.add(c));
        }

        const savedAvatar = localStorage.getItem("anidex_profile_avatar");
        if (savedAvatar && avatarImg) avatarImg.src = savedAvatar;
        
        const prefs = loadPrefs();
        pendingPrefs = prefs;
        renderPrefPicker(prefs);
        renderPrefDisplay(prefs);
        
        if (isLogged && !skipRestore && !window.__restoreAttempted) {
          window.__restoreAttempted = true;
          if (window.AniDexProfile && typeof window.AniDexProfile.restoreFromDB === 'function') {
              window.AniDexProfile.restoreFromDB();
          }
        }
      } catch (e) { console.error("loadProfile error:", e); }
    };

    const openModal = () => {
      if (!modal) return;
      nameInput.value = nameEl.textContent.trim();
      descInput.value = descEl.textContent.trim();
      if (descCount) descCount.textContent = `${descInput.value.length}/200`;
      pendingPrefs = loadPrefs();
      renderPrefPicker(pendingPrefs);
      modal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    };

    const closeModal = () => {
      if (modal) modal.classList.add("hidden");
      document.body.style.overflow = "";
    };

    const openRequestModal = () => {
      if (requestModal) {
        requestModal.classList.remove("hidden");
        requestModal.classList.add("flex");
        document.body.style.overflow = "hidden";
      }
    };

    const closeRequestModal = () => {
      if (requestModal) {
        requestModal.classList.add("hidden");
        requestModal.classList.remove("flex");
        document.body.style.overflow = "";
      }
    };

    const saveProfileToDB = async () => {
      if (window.__anidex_restoring) return;
      if (window.AniDexProfile && typeof window.AniDexProfile.saveToDB === "function") {
        await window.AniDexProfile.saveToDB();
      }
    };

    // Listeners
    if (editBtn) editBtn.addEventListener("click", openModal);
    if (settingsBtn) settingsBtn.addEventListener("click", openModal);
    closeEls.forEach(el => el.addEventListener("click", closeModal));
    if (requestBtn) requestBtn.addEventListener("click", openRequestModal);
    requestCloseEls.forEach(el => el.addEventListener("click", closeRequestModal));

    if (descInput && descCount) {
      descInput.addEventListener("input", () => {
        if (descInput.value.length > 200) descInput.value = descInput.value.slice(0, 200);
        descCount.textContent = `${descInput.value.length}/200`;
      });
    }

    if (saveBtn) {
      saveBtn.addEventListener("click", async () => {
        const newName = nameInput.value.trim() || nameEl.textContent;
        const newDesc = descInput.value.trim() || descEl.textContent;
        nameEl.textContent = newName;
        descEl.textContent = newDesc;
        localStorage.setItem("anidex_profile_name", newName);
        localStorage.setItem("anidex_profile_desc", newDesc);
        if (pendingPrefs) {
          localStorage.setItem(PREF_KEY, JSON.stringify(pendingPrefs));
          renderPrefDisplay(pendingPrefs);
        }
        await saveProfileToDB();
        closeModal();
      });
    }

    if (prefsWrap) {
      prefsWrap.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-pref-cat]");
        if (!btn) return;
        const cat = btn.getAttribute("data-pref-cat");
        const val = btn.getAttribute("data-pref-val");
        const prefs = pendingPrefs || loadPrefs();
        const list = new Set(prefs[cat] || []);
        if (list.has(val)) list.delete(val); else list.add(val);
        prefs[cat] = Array.from(list);
        pendingPrefs = prefs;
        renderPrefPicker(prefs);
      });
    }

    if (colorOptions && cardEl) {
      colorOptions.addEventListener("click", (e) => {
        const btn = e.target.closest("[data-color-class]");
        if (!btn) return;
        const colorClass = btn.getAttribute("data-color-class");
        colorClasses.forEach(c => cardEl.classList.remove(c));
        colorClass.split(" ").forEach(c => cardEl.classList.add(c));
        localStorage.setItem("anidex_profile_color", colorClass); 
        saveProfileToDB();
      });
    }

    if (requestForm) {
      requestForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const name = (requestInput?.value || "").trim();
        if (!name) return;
        closeRequestModal();
        if (requestToast) {
          requestToast.classList.remove("hidden");
          requestToast.classList.add("flex");
          setTimeout(() => { requestToast.classList.add("hidden"); }, 2000);
        }
        if (requestInput) requestInput.value = "";
      });
    }

    // Initial load
    loadProfile();

    // Export refresh (Registry pattern)
    if (window.AniDexLayout && typeof window.AniDexLayout.registerRefresh === "function") {
      window.AniDexLayout.registerRefresh(() => loadProfile(true));
    }
  })();
    </script>
    <script>
  (function () {
    const createBtn = document.getElementById("auth-create-btn");
    const loginBtn = document.getElementById("auth-login-btn");
    const logoutBtn = document.getElementById("logout-btn");
    const profileActions = document.getElementById("profile-actions");
    const profileLists = document.getElementById("profile-lists");
    const requestTitleBtn = document.getElementById("request-title-btn");
    const continueSection = document.getElementById("continue-section");
    const logoutModal = document.getElementById("logout-confirm");
    const logoutClose = logoutModal ? logoutModal.querySelectorAll("[data-logout-close]") : [];
    const logoutCancel = logoutModal ? logoutModal.querySelector("[data-logout-cancel]") : null;
    const logoutConfirm = logoutModal ? logoutModal.querySelector("[data-logout-confirm]") : null;
    const premiumAccessBtn = document.getElementById("premium-access-btn");
    const premiumCancelBtn = document.getElementById("premium-cancel-btn");
    const applyState = () => {
      const isLogged = window.AniDexLayout ? window.AniDexLayout.isLoggedIn() : (localStorage.getItem("nekora_logged_in") === "true");
      if (createBtn) createBtn.classList.toggle("hidden", isLogged);
      if (loginBtn) loginBtn.classList.toggle("hidden", isLogged);
      if (logoutBtn) logoutBtn.classList.toggle("hidden", !isLogged);
      const isPremium =
        isLogged &&
        (localStorage.getItem("nekora_premium") === "true" ||
          localStorage.getItem("nekora_admin") === "true");
      if (premiumAccessBtn) premiumAccessBtn.classList.toggle("hidden", isPremium);
      if (premiumCancelBtn) premiumCancelBtn.classList.toggle("hidden", !isPremium);
      if (continueSection) continueSection.classList.toggle("hidden", !isPremium);
      const isAdmin = localStorage.getItem("nekora_admin") === "true";
      if (requestTitleBtn) requestTitleBtn.classList.toggle("hidden", !isPremium || isAdmin);
      if (profileLists) {
        profileLists.style.marginTop = isPremium ? "-6rem" : "1.5rem";
      }
      if (profileActions) {
        const isAdmin = localStorage.getItem("nekora_admin") === "true";
        const alignLeft = (isLogged && !isPremium) || isAdmin;
        profileActions.classList.toggle("justify-start", alignLeft);
        profileActions.classList.toggle("pl-2", alignLeft);
        profileActions.classList.toggle("justify-center", !alignLeft);
        profileActions.classList.toggle("pl-0", !alignLeft);
      }
      const badge = document.querySelector("[data-identity-badge]");
      const badgeIcon = document.querySelector("[data-identity-icon]");
      const badgeText = document.querySelector("[data-identity-text]");
      if (badge && badgeIcon && badgeText) {
        if (isPremium) {
          if (isAdmin) {
            badge.className = "badge-shimmer text-white px-3 py-1 rounded-full font-label text-[10px] font-bold tracking-widest uppercase inline-flex items-center gap-1.5 shadow-[0_0_16px_rgba(56,189,248,0.9)] border border-sky-300/90";
            badgeIcon.textContent = "crown";
            badgeIcon.className = "material-symbols-outlined text-[14px] leading-none text-white";
            badgeText.textContent = "Modo super blue";
          } else {
            badge.className = "badge-shimmer-gold text-white px-3 py-1 rounded-full font-label text-[10px] font-bold tracking-widest uppercase inline-flex items-center gap-1.5 shadow-[0_0_12px_rgba(251,191,36,0.30)] border border-amber-300/50";
            badgeIcon.textContent = "bolt";
            badgeIcon.className = "material-symbols-outlined text-[14px] leading-none text-white";
            badgeText.textContent = "Modo limit break";
          }
        } else {

          badge.className = "bg-emerald-500/15 text-emerald-300 px-3 py-1 rounded-full font-label text-[10px] font-bold tracking-widest uppercase inline-flex items-center gap-1.5";
          badgeIcon.textContent = "nightlight";
          badgeIcon.className = "material-symbols-outlined text-[14px] leading-none";
          badgeText.textContent = "Modo Dream";
        }
      }
    };
    const doLogout = async () => {
      try {
        await fetch("api/auth.php?action=logout", { credentials: "same-origin" });
      } catch (e) { console.error("Logout API failed", e); }
      
      localStorage.removeItem("nekora_logged_in");
      localStorage.removeItem("nekora_user");
      localStorage.removeItem("nekora_premium");
      [
        "anidex_my_list_v1",
        "anidex_favorites_v1",
        "anidex_status_v1",
        "nekora_admin",
        "anidex_profile_prefs",
        "anidex_profile_desc",
        "anidex_profile_color",
        "anidex_profile_avatar",
        "anidex_profile_member_since",
        "anidex_profile_hours",
        "anidex_profile_name",
        "anidex_user_id",
        "anidex_user_suffix"
      ].forEach((key) => localStorage.removeItem(key));
      window.location.href = "index.php";
    };
    const openLogoutModal = () => {
      if (!logoutModal) return;
      logoutModal.classList.remove("hidden");
      logoutModal.classList.add("flex");
      document.body.style.overflow = "hidden";
    };
    const closeLogoutModal = () => {
      if (!logoutModal) return;
      logoutModal.classList.add("hidden");
      logoutModal.classList.remove("flex");
      document.body.style.overflow = "";
    };
    if (logoutBtn) {
      logoutBtn.addEventListener("click", () => {
        openLogoutModal();
      });
    }
    logoutClose.forEach((el) => el.addEventListener("click", closeLogoutModal));
    if (logoutCancel) logoutCancel.addEventListener("click", closeLogoutModal);
    if (logoutConfirm) logoutConfirm.addEventListener("click", async () => {
      closeLogoutModal();
      await doLogout();
    });
    if (premiumCancelBtn) {
      premiumCancelBtn.addEventListener("click", () => {
        localStorage.removeItem("nekora_premium");
        applyState();
      });
    }
    if (window.AniDexLayout && typeof window.AniDexLayout.onReady === "function") {
      window.AniDexLayout.onReady(applyState);
    } else {
      applyState();
    }
  })();
    </script>
    <script>
  /* MVC: Continue Watching (Model / View / Controller) */
  (async function () {
    // MODEL: continue items stored in localStorage
    const grid = document.getElementById("continue-grid");
    const empty = document.getElementById("continue-empty");
    if (!grid || !empty) return;
    let items = [];
    try {
      items = JSON.parse(localStorage.getItem("anidex_continue_v1") || "[]");
    } catch {
      items = [];
    }
    const norm = (v) =>
      (v || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^\w\s]/g, " ")
        .replace(/\s+/g, " ")
        .trim();
    let titleMap = {};
    try {
      titleMap = JSON.parse(localStorage.getItem("anidex_title_id_map_v1") || "{}");
    } catch {
      titleMap = {};
    }
    items = (items || [])
      .filter((it) => it && (it.title || it.query))
      .sort((a, b) => (b.lastSeen || 0) - (a.lastSeen || 0))
      .slice(0, 4);
    const resolveMissingIds = async () => {
      const needs = items.filter((it) => !it.malId);
      if (!needs.length) return;
      for (const item of needs) {
        const title = item.title || item.query || "";
        if (!title) continue;
        try {
          const res = await fetch(
            `https://api.jikan.moe/v4/anime?q=${encodeURIComponent(title)}&limit=1&order_by=popularity&sort=asc`
          );
          if (!res.ok) continue;
          const data = await res.json();
          const found = data?.data?.[0];
          if (found?.mal_id) {
            item.malId = found.mal_id;
            titleMap[norm(title)] = found.mal_id;
            item.detailUrl = `detail.php?mal_id=${encodeURIComponent(found.mal_id)}`;
          }
        } catch {}
      }
      try {
        localStorage.setItem("anidex_title_id_map_v1", JSON.stringify(titleMap));
      } catch {}
    };
    if (!items.length) {
      empty.classList.remove("hidden");
      grid.classList.add("hidden");
      return;
    }
    await resolveMissingIds();
    items.forEach((item) => {
      const title = item.title || item.query || "";
      const knownId = item.malId || item.sourceId || null;
      if (knownId && title) {
        titleMap[norm(title)] = knownId;
      }
      if (!item.detailUrl) {
        if (knownId) {
          item.detailUrl = `detail.php?mal_id=${encodeURIComponent(knownId)}`;
        } else if (title) {
          item.detailUrl = `detail.php?q=${encodeURIComponent(title)}`;
        }
      }
    });
    // Solo guardamos si hay items o si queremos resetear explcitamente. 
    // Para evitar sobreescribir datos sincronizados con una lista vaca local:
    if (items.length > 0) {
      try {
        localStorage.setItem("anidex_continue_v1", JSON.stringify(items));
      } catch {}
    }
    
    if (items.length === 0) {
      // Intentar recuperar de localStorage si el render local fall pero hay datos sincronizados
      const synced = JSON.parse(localStorage.getItem("anidex_continue_v1") || "[]");
      if (synced.length > 0) {
          items = synced;
      }
    }

    if (items.length === 0) {
        empty.classList.remove("hidden");
        grid.classList.add("hidden");
        return;
    }

    empty.classList.add("hidden");
    grid.classList.remove("hidden");
    grid.innerHTML = items.map((item) => {
      const title = item.title || item.query || "Anime";
      const fallbackId = titleMap[norm(title)] || titleMap[norm(item.query)] || null;
      if (!item.malId && fallbackId) {
        item.malId = fallbackId;
      }
      const ep = item.episode || 1;
      const epTitle = item.episodeTitle || `Episodio ${ep}`;
      const cover = item.cover || "https://via.placeholder.com/160x220?text=Anime";
      const href = `detail.php?q=${encodeURIComponent(title)}`;
      return `
        <a href="${href}" data-title="${title.replace(/"/g, "&quot;")}" class="group relative w-full rounded-2xl border border-white/10 bg-surface-container-low/70 p-3 shadow-[0_12px_26px_rgba(0,0,0,0.3)] transition-all hover:-translate-y-1 z-0 hover:z-20 overflow-visible">
          <span class="pointer-events-none absolute -inset-2 rounded-2xl border border-violet-400/0 opacity-0 transition-all duration-200 group-hover:opacity-100 group-hover:border-violet-400/60"></span>
          <div class="relative aspect-square rounded-none overflow-hidden bg-surface-container-high">
            <img src="${cover}" alt="${title}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.06] rounded-none" />
            <span class="absolute bottom-2 left-2 rounded-full bg-black/70 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-white">EP ${ep}</span>
          </div>
          <div class="mt-3">
            <p class="text-[11px] text-on-surface-variant truncate">${title}</p>
          </div>
        </a>
      `;
    }).join("");

    grid.addEventListener("click", async (e) => {
      const link = e.target.closest("a[data-title]");
      if (!link) return;
      e.preventDefault();
      const title = link.getAttribute("data-title") || "";
      if (!title) {
        window.location.href = link.getAttribute("href");
        return;
      }
      try {
        const res = await fetch(
          `https://api.jikan.moe/v4/anime?q=${encodeURIComponent(title)}&limit=1&order_by=popularity&sort=asc`
        );
        if (res.ok) {
          const data = await res.json();
          const found = data?.data?.[0];
          if (found?.mal_id) {
            window.location.href = `detail.php?mal_id=${encodeURIComponent(found.mal_id)}`;
            return;
          }
        }
      } catch {}
      window.location.href = link.getAttribute("href");
    });

    if (window.AniDexLayout && typeof window.AniDexLayout.registerRefresh === "function") {
      window.AniDexLayout.registerRefresh(applyState);
    }
  })();
    </script>
    <script data-ui-unlock>document.documentElement.classList.remove("preload-ui");</script>
  </body>
</html>





