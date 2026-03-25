<!DOCTYPE html>
<html class="dark" lang="es">
  <head>
    <script data-ui-preload>document.documentElement.classList.add("preload-ui");</script>
    <style>
  .preload-ui body { opacity: 0; }
    </style>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>NekoraList - Series</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
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
 borderRadius: {
 "DEFAULT": "1rem",
 "lg": "1.5rem",
 "xl": "2rem",
 "full": "9999px"
 },
 boxShadow: {
 "elevated": "0 16px 40px rgba(0,0,0,0.35)",
 "card": "0 10px 24px rgba(0,0,0,0.28)"
 },
 transitionTimingFunction: {
 "snappy": "cubic-bezier(0.2,0,0,1)"
 }
 }
 }
 }
    </script>
    <style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .filter-panel {
        overflow: visible;
        max-height: none;
        width: calc(100% + 1.7rem);
        min-height: calc(100vh - 10rem);
        padding-bottom: 1.5rem;
      }

    /* Normalize popular anime card sizes */
    section[aria-label="Grid de anime"] [data-anime-card] .space-y-2 > .flex {
      flex-wrap: nowrap;
      overflow: hidden;
    }
    section[aria-label="Grid de anime"] [data-anime-card] .space-y-2 > .flex > span {
      white-space: nowrap;
    }
    section[aria-label="Grid de anime"] [data-anime-card] h3 {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    [data-anime-card] {
      border: 1px solid transparent;
      transition: transform 300ms cubic-bezier(0.2,0,0,1), border-color 300ms ease, box-shadow 300ms ease;
    }
    [data-anime-card]:hover {
      border-color: rgba(168, 85, 247, 0.35);
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25), 0 0 16px rgba(168, 85, 247, 0.18);
    }
    .hover-preview-modal {
      position: fixed;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 220ms ease;
      z-index: 999;
    }
    .hover-preview-modal.is-open {
      opacity: 1;
      pointer-events: auto;
    }
    .hover-preview-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(8px);
    }
    .hover-preview-panel {
      position: relative;
      z-index: 1;
      width: min(88vw, 450px);
      min-height: 380px;
      display: flex;
      align-items: stretch;
      gap: 22px;
      padding: 26px;
      cursor: pointer;
      border-radius: 1.2rem;
      background: rgba(16, 16, 20, 0.96);
      border: 1px solid rgba(255, 255, 255, 0.08);
      box-shadow: 0 24px 50px rgba(0, 0, 0, 0.55);
      transform: scale(0.96);
      opacity: 0;
      transition: opacity 220ms ease, transform 220ms ease;
    }
    .hover-preview-modal.is-open .hover-preview-panel {
      opacity: 1;
      transform: scale(1);
    }
    .hover-preview-poster {
      width: 200px;
      flex: 0 0 200px;
      aspect-ratio: 2 / 3;
      border-radius: 0.9rem;
      overflow: hidden;
      background: #111113;
    }
    .hover-preview-poster img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    .hover-preview-info {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 0.5rem;
      min-width: 0;
      flex: 1;
    }
    .hover-preview-title {
      font-weight: 700;
      font-size: 1.25rem;
      line-height: 1.2;
    }
    .hover-preview-meta {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0.45rem;
      font-size: 0.75rem;
      color: rgba(229, 231, 235, 0.78);
    }
    .hover-preview-score,
    .hover-preview-year,
    .hover-preview-extra {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      justify-content: center;
      width: 100%;
      min-width: 0;
      text-align: center;
      white-space: nowrap;
      padding: 0.22rem 0.55rem;
      border-radius: 9999px;
      background: linear-gradient(135deg, rgba(99,102,241,0.35), rgba(168,85,247,0.35));
      border: 1px solid rgba(168, 85, 247, 0.45);
      box-shadow: 0 0 10px rgba(168, 85, 247, 0.25);
    }
    .hover-preview-meta > span:only-child,
    .hover-preview-meta > span:last-child:nth-child(odd) {
      grid-column: 1 / -1;
    }
    .hover-preview-score {
      color: #cdbdff;
      font-weight: 700;
    }
    .hover-preview-score .material-symbols-outlined {
      font-size: 0.8rem;
    }
    .hover-preview-synopsis {
      font-size: 0.86rem;
      line-height: 1.45;
      color: rgba(229, 231, 235, 0.75);
      flex: 1;
      display: flex;
      align-items: center;
      text-align: center;
    }
    .hover-preview-genres {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 0.4rem;
      margin-top: 0.25rem;
    }
    .hover-preview-genres span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      min-width: 0;
      text-align: center;
      white-space: nowrap;
      font-size: 0.7rem;
      padding: 0.18rem 0.55rem;
      border-radius: 9999px;
      background: linear-gradient(135deg, rgba(34,211,238,0.28), rgba(99,102,241,0.25));
      border: 1px solid rgba(59, 130, 246, 0.35);
      color: rgba(240, 249, 255, 0.95);
      box-shadow: 0 0 10px rgba(56, 189, 248, 0.18);
    }
    .hover-preview-genres span:only-child,
    .hover-preview-genres span:last-child:nth-child(odd),
    .hover-preview-genres span.is-wide {
      grid-column: 1 / -1;
    }
    .hover-preview-genres.is-stacked {
      grid-template-columns: 1fr;
    }
    .hover-preview-genres.is-stacked span {
      grid-column: 1 / -1;
    }
    </style>
    <link rel="icon" href="../img/icon3.png" />


  </head>
  <body class="bg-background text-on-background font-body selection:bg-[#AC85DB]/30 selection:text-[#996AD2]">
    <div class="min-h-screen">
      <!-- Navbar Component -->
      <div data-layout="header"></div>

      <main class="mx-auto max-w-screen-2xl px-6 pb-[clamp(0.9rem,1.6vw,1.6rem)] pt-28">
          <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
            <!-- Sidebar Component -->
          <aside class="lg:col-span-2">
            <section class="rounded-lg bg-surface-container-low p-6 filter-panel" aria-label="Filtros">
            <h2 class="font-headline text-xl font-bold text-on-surface">Filtros</h2>
            <div class="space-y-6">
                <div class="space-y-2">
                  <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="filter-search">Buscar</label>
                  <div class="flex items-center gap-2 rounded-xl bg-surface-container-high px-4 py-3">
                    <span class="material-symbols-outlined text-primary">search</span>
                    <input id="filter-search" class="w-full bg-transparent border-none focus:ring-0" type="search" placeholder="Ej: Frieren" aria-label="Buscar anime"/>
                  </div>
                </div>
                <div class="space-y-2">
                  <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant">GÃ©NekoraLists</label>
                  <!-- Dropdown de gÃ©NekoraLists renderizado por controllers/filters.js -->
                </div>
                <div class="space-y-4">
                  <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="filter-year">AÃ±o</label>
                    <select id="filter-year" class="w-full rounded-xl bg-surface-container-high px-4 py-3 text-on-surface" aria-label="Filtrar por ao">
                      <option>Todos</option>
                      <option>2026</option>
                      <option>2023</option>
                      <option>2022</option>
                    </select>
                  </div>
                  <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="filter-type">Tipo</label>
                    <select id="filter-type" class="w-full rounded-xl bg-surface-container-high px-4 py-3 text-on-surface" aria-label="Filtrar por tipo">
                      <option>Todos</option>
                      <option>Serie</option>
                      <option>PelÃ­cula</option>
                      <option>OVA</option>
                    </select>
                  </div>
                  <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant" for="filter-status">Estado</label>
                    <select id="filter-status" class="w-full rounded-xl bg-surface-container-high px-4 py-3 text-on-surface" aria-label="Filtrar por estado">
                      <option>Todos</option>
                      <option>En emisiÃ³n</option>
                      <option>Finalizado</option>
                    </select>
                  </div>
                </div>
                <div class="flex gap-3">
                  <button class="flex-1 rounded-full bg-surface-container-high py-3 text-sm font-semibold text-on-surface" type="button">Reiniciar</button>
                  <button class="flex-1 rounded-full bg-primary py-3 text-sm font-bold text-on-primary" type="button">Aplicar</button>
                </div>
              </div>
            </section>
            <section class="mt-6 rounded-lg bg-surface-container-low p-4" aria-label="Ranking">
              <div class="mb-3 flex items-center justify-between gap-3">
                <h3 class="text-sm font-bold uppercase tracking-widest text-on-surface-variant">Top 5 Ranking</h3>
                <a class="text-xs font-semibold text-primary hover:text-primary-dim" href="ranking\.php">Ver ranking</a>
              </div>
              <div id="sidebar-ranking" data-sidebar-ranking data-ranking-type="anime" class="space-y-3">
                <div class="text-xs text-on-surface-variant">Cargando ranking...</div>
              </div>
            </section>
          </aside>

          <!-- Catalog Content -->
          <section class="lg:col-span-10" aria-label="Resultados de catÃ¡logo">
            <header class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
              <div>
                <h1 class="font-headline text-4xl font-extrabold text-on-surface">Descubrimiento</h1>
              </div>
              <div class="flex items-center gap-3 text-sm text-on-surface-variant">
                <span>Ordenar por:</span>
                <button class="flex items-center gap-1 font-bold text-on-surface" type="button" aria-label="Ordenar resultados">
                  Popularidad <span class="material-symbols-outlined text-sm">expand_more</span>
                </button>
              </div>
            </header>

            <!-- States (loading/empty/error) -->
            <section class="space-y-6" aria-live="polite">
              <div class="hidden" data-state="loading">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                  <article class="animate-pulse rounded-lg bg-surface-container-low p-4">
                    <div class="aspect-[2/3] rounded-lg bg-surface-container-high"></div>
                    <div class="space-y-3">
                      <div class="h-3 w-48 rounded-full bg-surface-container-high"></div>
                      <div class="h-5 w-48/4 rounded-lg bg-surface-container-high"></div>
                      <div class="h-3 w-48/2 rounded-full bg-surface-container-high"></div>
                    </div>
                  </article>
                  <article class="animate-pulse rounded-lg bg-surface-container-low p-4">
                    <div class="aspect-[2/3] rounded-lg bg-surface-container-high"></div>
                    <div class="space-y-3">
                      <div class="h-3 w-48 rounded-full bg-surface-container-high"></div>
                      <div class="h-5 w-48/3 rounded-lg bg-surface-container-high"></div>
                      <div class="h-3 w-48/2 rounded-full bg-surface-container-high"></div>
                    </div>
                  </article>
                  <article class="animate-pulse rounded-lg bg-surface-container-low p-4">
                    <div class="aspect-[2/3] rounded-lg bg-surface-container-high"></div>
                    <div class="space-y-3">
                      <div class="h-3 w-48 rounded-full bg-surface-container-high"></div>
                      <div class="h-5 w-48/5 rounded-lg bg-surface-container-high"></div>
                      <div class="h-3 w-48/2 rounded-full bg-surface-container-high"></div>
                    </div>
                  </article>
                </div>
              </div>

              <div class="hidden" data-state="empty">
                <div class="rounded-lg bg-surface-container-low p-10 text-center">
                  <h2 class="text-2xl font-bold text-on-surface">Sin resultados</h2>
                  <p class="mt-2 text-on-surface-variant">Prueba otros filtros para ver mÃ¡s tÃ­tulos disponibles.</p>
                </div>
              </div>

              <div class="hidden" data-state="error">
                <div class="rounded-lg bg-error-container/30 p-10 text-center">
                  <h2 class="text-2xl font-bold text-on-error-container">Ocurrio un error</h2>
                  <p class="mt-2 text-on-error-container/80">No pudimos cargar el catÃ¡logo. IntÃ©ntalo nuevamente</p>
                  <button class="mt-6 rounded-full bg-primary px-6 py-3 text-sm font-bold text-on-primary" type="button">Reintentar</button>
                </div>
              </div>
            </section>

            <!-- AnimeCard Component -->
            <section class="mt-[clamp(0.6rem,0.9vw,0.8rem)] grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6" aria-label="Grid de anime">
              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="jujutsu kaisen" data-genres="accion,fantasia" data-year="2020" data-type="Serie de TV" data-status="En emisiÃ³n">
                <a class="block" href="detail\.php" aria-label="Jujutsu Kaisen">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Jujutsu Kaisen" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAf_5yu8ItByDxj1mtS86M1c4L-O9mK0YowNyDHJw9W3iBVZ8FyDg-EqWFT6-8OHsq_coAIAP0l958Z_MmlAFEJw4WhJphbR4l9vrMyWi2j3WVvXgYbjrEmsF1lNw_pei3FsECFfbJAjngFTubB3gifJd8ggZOoo0o4uF2DMo2NTvstvfpwoVeh2cIGu3izqmnqcGUvtn3bfztDQjI_3f_PqRWRxgPC6iAsoNpidsC32rJsZNIfPPYMhlo5KeRXtnq6jjUFrMUTk0d4" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2020</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Jujutsu Kaisen</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="monster" data-genres="thriller,drama" data-year="2004" data-type="OVA" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="Monster">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Monster" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAG7j1khN7oa42GFgVO0GGKUGrbVAHUi69ljVSoVZw2JzGhXLGZPmMJVg-fuQthZ2BjRmQPHtiljA6oG5wD0e2QgsOauHs8rHo6CfVW_-qbdDPyc6WFV6NSVCR2bXYGfJANdbVZu3JCvQHDHUgQb-65j6hS6Sxh3KsQpp8TrqHwC-smxyfYY2kHVMRaba4VaaF-d0mASZkln-xJrLjreN-5Eryk7As7G1yYpz_0v5C6rK-TlGmV91TVOYBi2hNx43byaOojLl2FHhT9" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2004</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Monster</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="cyberpunk: edgerunners" data-genres="accion,sci-fi" data-year="2022" data-type="ONA" data-status="Pausado">
                <a class="block" href="detail\.php" aria-label="Cyberpunk: Edgerunners">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Cyberpunk: Edgerunners" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAWK6GEmMfgg6EfbuobMqKnAGEoBR-JDGiUg00ci-kmNlIZtDkYI6Kn2Ke0sXVPIgNQCXFd-IsHB0kiCe8S2Zky7Vx8s8Yv0q9tzs5HYMvwxzBr6cc0oZ2o_86dXwOsGkjr4PDjv3ULdj9hX4Eanz5BPS7V2PEEqNSKPjrv61ZaMxz7RmFHx1Ah01hoqVReWKva7mpYkiw9VwRTTlov4AGu-uJhSG3Bln2hIug_IIGpsqGQXkCpk6x8T2qTeXcy2R7y7otUdEHtPV9T" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2022</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Cyberpunk: Edgerunners</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="demon slayer" data-genres="accion,fantasia" data-year="2019" data-type="Especiales" data-status="PrÃ³ximamente">
                <a class="block" href="detail\.php" aria-label="Demon Slayer">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Demon Slayer" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAR9x5iy6NpAOvezWnwzK2vesTKLBp29cvFNXUtwXht6dD1l_D5gxqns-j0rTWWHrKxrDe_gNz2hRv_bp72i7d3-B1XfbVcKL9UOdKUyxhkn7PVkTFK6gvR6FYot0OR1ZFv5kxq0aRiZ5uP-MPaYLYP3NbHMNLEF22YktmuRvb3820xC4ub0dVEhj3jZVy7-ZDMJBIgRaWhSUwsjWpaA_FPrQNkZspeF-urqaKWFj1HOZJbRz951KKRSdvfM9UeaS3zgtT822TzJSbT" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2019</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Demon Slayer</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="mushishi" data-genres="fantasia,sobrenatural" data-year="2005" data-type="Cortos" data-status="Cancelado">
                <a class="block" href="detail\.php" aria-label="Mushishi">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Mushishi" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCje1xHJ1WUA4g9yIIDEH_pR92E2kB6X1s837gGJD9UFj8E0mQUkxMN_rTpbmtI9LLd4eZnx3hIvb-U8EcBBpfSmNGAKWLzD_Eb1ppR87UR5wnTD3fPpOlY74X-I4P4p5J0Wpv-HTIa4kWox8n8dlfB6caMiMRLPuYkHJD_0lxfUZz4aT79i5VovpVuKkPEe3n5WiMUARuk4BRWUvsDr1xczcOQelSreIiJtPyQWQfnq0GKXptfgVdrHAE1Wxb4YsMhAapniXWKOFfH" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2005</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Mushishi</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="solo leveling" data-genres="accion,fantasia" data-year="2026" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="Solo Leveling">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Solo Leveling" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfEs1IvGWNIZxiSWnnh8wCUr59ea4njuZckziZXqicnQPNvJkan5uFN_-BmFdNvpNSE7c6Gy-mHlJOyMU4hG1QNd2nk5WYUB4_uFh5jJL-K6arAlskDJ_8qihvbrLMvZNDOsSiBXcBUPHNcfenZNrmyKDhpAa0TRP_9gxyxTMIHLtWKcJBfTyyYK2OT7w8mggHKLEv6KE0h3MMHguW46V5ghR79auKbPcZFbiWP4qDRaPBSBDdTGBjWbacwEx2LnRwcBnZnp4l3j-4" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2026</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Solo Leveling</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="the apothecary diaries" data-genres="drama,misterio" data-year="2023" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="The Apothecary Diaries">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="The Apothecary Diaries" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2q_skNN7IEXINa_mQLWLtLooGZISRCJFcyFjR3-NhCFoQIV4rVukiI28cqzl7NwA1WRT10f9y_sBVA5KkJuprPzW2QkHsxVtQR7BFHYdKPirCvmU9EAQOC4weVwBfy07uTKoRhP1y13VRDG3hA9igt5f2qH88HGxQ7HJ0NF_FJ80hss_-Tj-Z198bRHpqi85zQe-swEt4fP6Cj4nJcWzWyRxEPOSaQv6kswMt5oDI9hJkaV25CtItXBgjs9EUI-R4wQ4uD4lO2a_z" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2023</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">The Apothecary Diaries</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="undead unluck" data-genres="accion,comedia" data-year="2023" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="Undead Unluck">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Undead Unluck" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZs4Vw9PZ84i6Ys2r4zaN7qIUjHi2HdaV26z-79Pfi17ztATEPT0KLGu0_DQz9awHO680k4vEkXKbI8fxecyDrissO8rGhpL9NVVqM2kiBD6Y4ZYyACz-yNaK6doyItKZ2PQsrFzUyZWrJh3CH-YMfrUN5_z_9cxUYzO2Ndk-hV9TENhD2MlVYucSYbhM9xyGBr5aq1t2I2_h9llA0wSZqupDhEcmjfJccvzCwcKaeyEkRrAXEW_p7QMs_pt5XwUfXBAlV0As10pqp" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2023</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Undead Unluck</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="attack on titan" data-genres="accion,drama" data-year="2013" data-type="Serie" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="Attack on Titan">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Attack on Titan" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBcIQtxOd6qOsXx7ByE8oZn1oCXSPjEJzsUOHQIOSQYhHH0OYYB_eJlZhZNz3w4BHdnldRu7iEiS9w18kbo_Z0hKzmIiXP7aiQNfz2tM80qkstdNDxLa6JqFEVe0MaL19MslgUay1G49G96_nr3avtPuZ1bsDRBHEMkYXwbJXR78yArKNKgPuo_FS84akJ8xyLqb9-VO67Md2PDUJJhpFC-KvDBlFjJ6jb60bCEeVK3KhKkAeDGb_sY6HuC9kf3yivDQCPx_K043ssp" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2013</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Attack on Titan</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="frieren: beyond journey's end" data-genres="fantasia,aventura" data-year="2023" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="Frieren: Beyond Journey's End">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Frieren: Beyond Journey's End" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAJ6Ah-IV3lUQiVO02y1I0SuB8bLgACNC8qwVPEgr-NhJFTPAD2A_LQeg_h72ejiI56UKVnMh_Puk_E_i5W-zvO3byCKKRI_lYcmJvyZdFj6aX-w5fJQ2gDPxJdfbeYCsHOjPjaGO45hE1NcsL9FuhwWH2aaPbXfZfhrHmRYPEvNATzTxZIDy21c9Y27vxZmKoI6Z2oG5CWH7305d55-BRLe0KJwbYM5drbE-215zNLDf1nAWDi87ubZTX2HHsCJ-JVUzNOTOTlUsge" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2023</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Frieren: Beyond Journey's End</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="chainsaw man" data-genres="accion,terror" data-year="2022" data-type="Serie" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="Chainsaw Man">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Chainsaw Man" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDzVx7iJLr4Mfgd79RK6X6Km0uzNQjGsPP7jCamqWRwpAQFl-8CsoExOcyJkVD1eheVat1utCOZKROorocz3XsKQLFaGETmOawxeNH-136-XG_1PQcn5MXZ7g81t7Z55330se4DwC5s7SvRCtzVQRcFbfElkSuvdNfwK4HjUiJ6db3EX8fcQGB_DheO4xIpT2CNTuwAmeZane81aKqI2y5RC4gXhsupCBLUOUFf0ECvO-ti-ewKZAF-T3Q2aVq3uYDQwlzBrv3s4Cah" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2022</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Chainsaw Man</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="spy x family" data-genres="comedia,accion" data-year="2022" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="Spy x Family">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Spy x Family" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAf_5yu8ItByDxj1mtS86M1c4L-O9mK0YowNyDHJw9W3iBVZ8FyDg-EqWFT6-8OHsq_coAIAP0l958Z_MmlAFEJw4WhJphbR4l9vrMyWi2j3WVvXgYbjrEmsF1lNw_pei3FsECFfbJAjngFTubB3gifJd8ggZOoo0o4uF2DMo2NTvstvfpwoVeh2cIGu3izqmnqcGUvtn3bfztDQjI_3f_PqRWRxgPC6iAsoNpidsC32rJsZNIfPPYMhlo5KeRXtnq6jjUFrMUTk0d4" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2022</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Spy x Family</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="fullmetal alchemist: brotherhood" data-genres="accion,aventura" data-year="2009" data-type="Serie" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="Fullmetal Alchemist: Brotherhood">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Fullmetal Alchemist: Brotherhood" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAG7j1khN7oa42GFgVO0GGKUGrbVAHUi69ljVSoVZw2JzGhXLGZPmMJVg-fuQthZ2BjRmQPHtiljA6oG5wD0e2QgsOauHs8rHo6CfVW_-qbdDPyc6WFV6NSVCR2bXYGfJANdbVZu3JCvQHDHUgQb-65j6hS6Sxh3KsQpp8TrqHwC-smxyfYY2kHVMRaba4VaaF-d0mASZkln-xJrLjreN-5Eryk7As7G1yYpz_0v5C6rK-TlGmV91TVOYBi2hNx43byaOojLl2FHhT9" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2009</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Fullmetal Alchemist: Brotherhood</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="my hero academia" data-genres="accion,shonen" data-year="2016" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="My Hero Academia">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="My Hero Academia" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAWK6GEmMfgg6EfbuobMqKnAGEoBR-JDGiUg00ci-kmNlIZtDkYI6Kn2Ke0sXVPIgNQCXFd-IsHB0kiCe8S2Zky7Vx8s8Yv0q9tzs5HYMvwxzBr6cc0oZ2o_86dXwOsGkjr4PDjv3ULdj9hX4Eanz5BPS7V2PEEqNSKPjrv61ZaMxz7RmFHx1Ah01hoqVReWKva7mpYkiw9VwRTTlov4AGu-uJhSG3Bln2hIug_IIGpsqGQXkCpk6x8T2qTeXcy2R7y7otUdEHtPV9T" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2016</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">My Hero Academia</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="one piece" data-genres="aventura,accion" data-year="1999" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="One Piece">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="One Piece" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAR9x5iy6NpAOvezWnwzK2vesTKLBp29cvFNXUtwXht6dD1l_D5gxqns-j0rTWWHrKxrDe_gNz2hRv_bp72i7d3-B1XfbVcKL9UOdKUyxhkn7PVkTFK6gvR6FYot0OR1ZFv5kxq0aRiZ5uP-MPaYLYP3NbHMNLEF22YktmuRvb3820xC4ub0dVEhj3jZVy7-ZDMJBIgRaWhSUwsjWpaA_FPrQNkZspeF-urqaKWFj1HOZJbRz951KKRSdvfM9UeaS3zgtT822TzJSbT" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">1999</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">One Piece</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="vinland saga" data-genres="accion,drama" data-year="2019" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="Vinland Saga">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Vinland Saga" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCje1xHJ1WUA4g9yIIDEH_pR92E2kB6X1s837gGJD9UFj8E0mQUkxMN_rTpbmtI9LLd4eZnx3hIvb-U8EcBBpfSmNGAKWLzD_Eb1ppR87UR5wnTD3fPpOlY74X-I4P4p5J0Wpv-HTIa4kWox8n8dlfB6caMiMRLPuYkHJD_0lxfUZz4aT79i5VovpVuKkPEe3n5WiMUARuk4BRWUvsDr1xczcOQelSreIiJtPyQWQfnq0GKXptfgVdrHAE1Wxb4YsMhAapniXWKOFfH" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2019</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Vinland Saga</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="haikyuu!!" data-genres="deporte,comedia" data-year="2014" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="Haikyuu!!">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Haikyuu!!" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfEs1IvGWNIZxiSWnnh8wCUr59ea4njuZckziZXqicnQPNvJkan5uFN_-BmFdNvpNSE7c6Gy-mHlJOyMU4hG1QNd2nk5WYUB4_uFh5jJL-K6arAlskDJ_8qihvbrLMvZNDOsSiBXcBUPHNcfenZNrmyKDhpAa0TRP_9gxyxTMIHLtWKcJBfTyyYK2OT7w8mggHKLEv6KE0h3MMHguW46V5ghR79auKbPcZFbiWP4qDRaPBSBDdTGBjWbacwEx2LnRwcBnZnp4l3j-4" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2014</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Haikyuu!!</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="naruto shippuden" data-genres="accion,aventura" data-year="2007" data-type="Serie" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="Naruto Shippuden">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Naruto Shippuden" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2q_skNN7IEXINa_mQLWLtLooGZISRCJFcyFjR3-NhCFoQIV4rVukiI28cqzl7NwA1WRT10f9y_sBVA5KkJuprPzW2QkHsxVtQR7BFHYdKPirCvmU9EAQOC4weVwBfy07uTKoRhP1y13VRDG3hA9igt5f2qH88HGxQ7HJ0NF_FJ80hss_-Tj-Z198bRHpqi85zQe-swEt4fP6Cj4nJcWzWyRxEPOSaQv6kswMt5oDI9hJkaV25CtItXBgjs9EUI-R4wQ4uD4lO2a_z" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2007</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Naruto Shippuden</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="bleach" data-genres="accion,sobrenatural" data-year="2004" data-type="Serie" data-status="En emision">
                <a class="block" href="detail\.php" aria-label="Bleach">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Bleach" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCZs4Vw9PZ84i6Ys2r4zaN7qIUjHi2HdaV26z-79Pfi17ztATEPT0KLGu0_DQz9awHO680k4vEkXKbI8fxecyDrissO8rGhpL9NVVqM2kiBD6Y4ZYyACz-yNaK6doyItKZ2PQsrFzUyZWrJh3CH-YMfrUN5_z_9cxUYzO2Ndk-hV9TENhD2MlVYucSYbhM9xyGBr5aq1t2I2_h9llA0wSZqupDhEcmjfJccvzCwcKaeyEkRrAXEW_p7QMs_pt5XwUfXBAlV0As10pqp" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2004</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Bleach</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="steins;gate" data-genres="sci-fi,thriller" data-year="2011" data-type="Serie de TV" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="Steins;Gate">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Steins;Gate" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="../img/fondoanime.png" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2011</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Steins;Gate</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="one punch man" data-genres="accion,comedia" data-year="2015" data-type="Serie de TV" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="One Punch Man">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="One Punch Man" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="../img/fondoanime.png" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2015</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">One Punch Man</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="hunter x hunter" data-genres="accion,aventura" data-year="2011" data-type="Serie de TV" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="Hunter x Hunter">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Hunter x Hunter" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="../img/fondoanime.png" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2011</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Hunter x Hunter</h3>
                  </div>
                </a>
              </article>

              <article class="group rounded-lg bg-surface-container-low p-4 transition-transform duration-300 ease-snappy hover:scale-[1.02]" data-anime-card data-title="death note" data-genres="thriller,sobrenatural" data-year="2006" data-type="Serie" data-status="Finalizado">
                <a class="block" href="detail\.php" aria-label="Death Note">
                  <div class="relative aspect-[2/3] overflow-hidden rounded-lg bg-surface-container-high">
                    <img alt="Death Note" class="h-full w-full object-cover transition-transform duration-500 ease-snappy group-hover:scale-[1.03]" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBcIQtxOd6qOsXx7ByE8oZn1oCXSPjEJzsUOHQIOSQYhHH0OYYB_eJlZhZNz3w4BHdnldRu7iEiS9w18kbo_Z0hKzmIiXP7aiQNfz2tM80qkstdNDxLa6JqFEVe0MaL19MslgUay1G49G96_nr3avtPuZ1bsDRBHEMkYXwbJXR78yArKNKgPuo_FS84akJ8xyLqb9-VO67Md2PDUJJhpFC-KvDBlFjJ6jb60bCEeVK3KhKkAeDGb_sY6HuC9kf3yivDQCPx_K043ssp" loading="lazy"/>
                    <span class="absolute left-3 top-3 rounded-full bg-surface/80 px-3 py-1 text-xs font-bold text-on-surface">2006</span>
                  </div>
                  <div class="space-y-2 mt-2">
                    
                    <h3 class="font-headline text-lg font-bold text-on-surface">Death Note</h3>
                  </div>
                </a>
              </article>
            </section>

            <div class="mt-12 flex justify-center">
              <button class="rounded-full border border-primary/30 bg-primary/10 px-8 py-4 text-sm font-bold text-primary transition-colors hover:bg-primary/20 inline-flex items-center gap-2" type="button" aria-label="Cargar mÃ¡s resultados">
                <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">add_circle</span>
                Cargar mÃ¡s
              </button>
            </div>
          </section>
        </div>
      </main>

      <!-- Footer Component -->
      <div data-layout="footer"></div>
      <script src="../controllers/layout.js"></script>
    </div>
    <script src="../controllers/i18n.js"></script>
    <script src="../controllers/title-images.js?v=3"></script>
    <script src="../controllers/search.js"></script>
    <script src="../controllers/filters.js?v=11"></script>
    <script src="../controllers/detail-links.js"></script>
    <script src="../controllers/load-more.js?v=2"></script>
    <script>
  document.addEventListener("DOMContentLoaded", () => {
    // Ajuste global de etiquetas: Series -> Animes, Serie -> Anime
    if (document.title.includes("Series")) {
      document.title = document.title.replaceAll("Series", "Animes");
    }
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    const textNodes = [];
    while (walker.nextNode()) textNodes.push(walker.currentNode);
    textNodes.forEach((node) => {
      if (!node.nodeValue) return;
      node.nodeValue = node.nodeValue
        .replace(/Series\b/g, "Animes")
        .replace(/Serie(?!\s+de\s+TV)\b/g, "Anime");
      const fixes = [
        ["AcciÃ³n", "AcciÃ³n"],
        ["FantasÃ­a", "FantasÃ­a"],
        ["emision", "emisiÃ³n"],
        ["titulos", "tÃ­tulos"],
        ["Tambien", "TambiÃ©n"],
        ["tambien", "tambiÃ©n"],
        ["GeNekoraLists", "GÃ©NekoraLists"],
        ["Informacion", "InformaciÃ³n"],
        ["Duracion", "DuraciÃ³n"],
        ["Anio", "AÃ±o"]
      ];
      fixes.forEach(([bad, good]) => {
        node.nodeValue = node.nodeValue.replaceAll(bad, good);
      });
      node.nodeValue = node.nodeValue.replace(/Serie\s+([0-9+]+)\s+eps/g, "$1 ep");
    });

    // En animes: badge abajo izquierda con el aÃ±o de publicaciÃ³n
    const applyYearBadges = () => {
      document.querySelectorAll("span.absolute.left-3").forEach((badge) => {
        const card = badge.closest("[data-year]");
        const year = card ? card.getAttribute("data-year") : "";
        if (!year) return;
        if ((badge.textContent || "").trim() !== year) badge.textContent = year;
        badge.classList.remove("top-3");
        badge.classList.add("bottom-3");
      });
    };
    applyYearBadges();
    const badgeObserver = new MutationObserver(() => applyYearBadges());
    badgeObserver.observe(document.body, { childList: true, subtree: true, characterData: true });

    document.querySelectorAll("[data-type='Serie']").forEach((el) => {
      el.setAttribute("data-type", "Anime");
    });
    document.querySelectorAll("option").forEach((opt) => {
      if ((opt.textContent || "").trim() === "Serie") {
        opt.textContent = "Anime";
        opt.value = "Anime";
      }
    });

    if (window.AniDexI18n) window.AniDexI18n.init();
    if (window.AniDexTitleImages) window.AniDexTitleImages.init();
    if (window.AniDexSearch) window.AniDexSearch.init();
    if (window.AniDexFilters) window.AniDexFilters.init();
    if (window.AniDexDetailLinks) window.AniDexDetailLinks.init();
  });
    </script>
    <script data-ui-unlock>document.documentElement.classList.remove("preload-ui");</script>
  </body>
</html>

