const AniDexDetailDataBoot = () => {
  const appUrl = window.AniDexShared?.buildAppUrl || ((path = "") => String(path || ""));
  const API = appUrl("api/jikan_proxy");
  
  const GENRE_ES = {
    Action: "Acci\u00f3n", Adventure: "Aventura", Comedy: "Comedia", Drama: "Drama",
    Fantasy: "Fantas\u00eda", Romance: "Romance", Suspense: "Suspenso", Mystery: "Misterio",
    SciFi: "Ciencia ficci\u00f3n", "Sci-Fi": "Ciencia ficci\u00f3n", Horror: "Terror",
    Sports: "Deportes", "Slice of Life": "Recuentos de la vida", Supernatural: "Sobrenatural",
    "Award Winning": "Premiado"
  };

  const baseNormalize = window.AniDexShared?.normalizeText || ((value) => String(value || "").toLowerCase().trim());
  const norm = (v) => baseNormalize(v).replace(/[^\w\s]/g, " ").replace(/\s+/g, " ").trim();

  const score = (q, t) => {
    const a = norm(q), b = norm(t);
    if (!a || !b) return 0;
    if (a === b) return 100;
    if (b.includes(a) || a.includes(b)) return 80;
    const at = a.split(" "), bt = b.split(" ");
    const overlap = at.filter((x) => bt.includes(x)).length;
    return Math.round((overlap / Math.max(at.length, bt.length)) * 70);
  };

  const delay = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

  const pickTitle = async (query, retries = 2) => {
    const r = await fetch(`${API}?endpoint=${encodeURIComponent('anime?q=' + encodeURIComponent(query) + '&limit=10&order_by=popularity&sort=asc')}`);
    if (r.status === 429 && retries > 0) { await delay(1000); return pickTitle(query, retries - 1); }
    if (!r.ok) return null;
    const j = await r.json();
    const list = j?.data || [];
    const best = list.map((it) => ({ it, s: Math.max(score(query, it?.title), score(query, it?.title_english), score(query, it?.title_japanese)) }))
                     .sort((a, b) => b.s - a.s)[0];
    return best?.it || list[0] || null;
  };

  const byId = async (id, suffix = "full", retries = 2) => {
    try {
      const r = await fetch(`${API}?endpoint=${encodeURIComponent('anime/' + id + '/' + suffix)}`);
      if (r.status === 429 && retries > 0) { await delay(1000); return byId(id, suffix, retries - 1); }
      if (!r.ok) return null;
      const j = await r.json(); return j?.data || null;
    } catch { return null; }
  };

  const fetchEpisodeMeta = async (id, retries = 2) => {
    if (!id) return [];
    try {
      const r = await fetch(`${API}?endpoint=${encodeURIComponent('anime/' + id + '/episodes')}`);
      if (r.status === 429 && retries > 0) { await delay(1000); return fetchEpisodeMeta(id, retries - 1); }
      if (!r.ok) return [];
      const j = await r.json(); return Array.isArray(j?.data) ? j.data : [];
    } catch { return []; }
  };

  const fetchEpisodeDetail = async (id, epN, retries = 1) => {
    if (!id || !epN) return null;
    try {
      const r = await fetch(`${API}?endpoint=${encodeURIComponent('anime/' + id + '/episodes/' + epN)}`);
      if (r.status === 429 && retries > 0) { await delay(1000); return fetchEpisodeDetail(id, epN, retries - 1); }
      if (!r.ok) return null;
      const j = await r.json(); return j?.data || null;
    } catch { return null; }
  };

  const translateToEs = async (text) => (window.AniDexShared?.translateAutoToEs ? window.AniDexShared.translateAutoToEs(text) : String(text || "").trim());

  const renderMetaBlock = (container, label, value) => {
    const wrap = document.createElement("div");
    wrap.className = "flex flex-col gap-1";
    wrap.innerHTML = `<span class="text-xs text-on-surface-variant uppercase tracking-widest font-bold">${label}</span><span class="text-on-surface font-medium">${value || "N/A"}</span>`;
    container.appendChild(wrap);
  };

  const toSpanishStatus = (v) => {
    const s = (v || "").toLowerCase();
    if (s.includes("finished")) return "Finalizado";
    if (s.includes("currently")) return "En emisi\u00f3n";
    if (s.includes("not yet")) return "Pr\u00f3ximamente";
    return v || "N/A";
  };

  const toSpanishDuration = (v) => (v || "").replace("min per ep", "min por episodio").replace("hr", "h");
  const toSpanishRating = (v) => (v || "").replace("R - 17+ (violence & profanity)", "R - 17+ (violencia y lenguaje expl\u00edcito)").replace("PG-13 - Teens 13 or older", "PG-13 - Mayores de 13 a\u00f1os").replace("PG - Children", "PG - P\u00fablico general").replace("G - All Ages", "G - Todas las edades").replace("Rx - Hentai", "Rx - Adultos");

  const shortenEpisodeText = (text, max = 170) => {
    const c = (text || "").replace(/\s+/g, " ").trim();
    if (!c || c.length <= max) return c;
    const m = c.match(/[^.!?]+[.!?]+/g) || [];
    let b = "";
    for (const s of m) { const n = `${b} ${s}`.trim(); if (n.length > max) break; b = n; }
    if (b && b.length >= 80) return b.trim();
    const t = c.slice(0, max);
    const p = Math.max(t.lastIndexOf('.'), t.lastIndexOf('!'), t.lastIndexOf('?'));
    if (p >= 80) return t.slice(0, p + 1).trim();
    const l = t.lastIndexOf(' ');
    const base = (l > 80 ? t.slice(0, l) : t).trim().replace(/[,:;\-\s]+$/, '');
    return `${base}.`;
  };

  const bindHorizontalArrows = (row, prev, next, perD = 5) => {
    if (!row || !prev || !next) return;
    const step = () => {
      const card = row.querySelector(".carousel-card") || row.firstElementChild;
      if (!card) return Math.max(260, Math.floor(row.clientWidth * 0.85));
      const s = getComputedStyle(row), g = parseFloat(s.columnGap || s.gap || "0") || 0;
      const p = parseInt(s.getPropertyValue("--cards")) || perD, w = card.getBoundingClientRect().width;
      return Math.max(260, Math.round((w + g) * p));
    };
    const setD = (b, d) => { b.disabled = d; b.classList.toggle("opacity-40", d); b.classList.toggle("pointer-events-none", d); };
    const update = () => {
      const max = row.scrollWidth - row.clientWidth;
      if (row.querySelectorAll(".carousel-card").length <= 4 || max <= 2) { prev.classList.add("hidden"); next.classList.add("hidden"); return; }
      prev.classList.remove("hidden"); next.classList.remove("hidden");
      setD(prev, row.scrollLeft <= 2); setD(next, row.scrollLeft >= max - 2);
    };
    prev.addEventListener("click", () => row.scrollBy({ left: -step(), behavior: "smooth" }));
    next.addEventListener("click", () => row.scrollBy({ left: step(), behavior: "smooth" }));
    row.addEventListener("scroll", () => requestAnimationFrame(update));
    window.addEventListener("resize", update); setTimeout(update, 0);
  };

  const slider = (title, items, renderHtml, idB) => {
    const s = document.createElement("div"); s.className = "space-y-4";
    s.innerHTML = `<div class="flex items-center justify-between"><h3 class="font-headline text-3xl font-bold">${title}</h3></div>`;
    const w = document.createElement("div"); w.className = "relative overflow-x-hidden";
    const p = document.createElement("button"); p.id = `${idB}-prev`; p.className = "absolute left-0 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-surface-container-high/80 border border-outline-variant/20 text-primary flex items-center justify-center shadow-md"; p.innerHTML = '<span class="material-symbols-outlined">chevron_left</span>';
    const n = document.createElement("button"); n.id = `${idB}-next`; n.className = "absolute right-0 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-surface-container-high/80 border border-outline-variant/20 text-primary flex items-center justify-center shadow-md"; n.innerHTML = '<span class="material-symbols-outlined">chevron_right</span>';
    const r = document.createElement("div"); r.id = `${idB}-row`; r.className = "carousel-row no-scrollbar pb-2 scroll-smooth";
    r.style.setProperty("--cards", idB === "detail-images" ? "4" : "5"); r.style.setProperty("--gap", "1rem");
    items.forEach((it) => { const c = document.createElement("div"); c.className = "carousel-card shrink-0"; c.innerHTML = renderHtml(it); r.appendChild(c); });
    w.append(p, n, r); s.appendChild(w); setTimeout(() => bindHorizontalArrows(r, p, n, idB === "detail-images" ? 4 : 5), 0); return s;
  };

  const fetchLocalAnime = async (malId, title, dbId) => {
    let url = `${appUrl("api/anime_data")}?`;
    if (dbId) url += `id=${encodeURIComponent(dbId)}&`;
    if (malId) url += `mal_id=${encodeURIComponent(malId)}&`;
    if (title) url += `q=${encodeURIComponent(title)}`;
    try { const res = await fetch(url.replace(/&$/, "")); if (!res.ok) return null; const j = await res.json(); return j.success ? j.data : null; } catch { return null; }
  };

  const _runInit = async () => {
    const isLogged = window.AniDexLayout ? window.AniDexLayout.isLoggedIn() : (localStorage.getItem("nekora_logged_in") === "true");
    const isPremium = window.AniDexLayout ? window.AniDexLayout.isPremium() : (localStorage.getItem("nekora_premium") === "true" || localStorage.getItem("nekora_admin") === "true");
    const canWatchEpisodes = isLogged && isPremium;
    const routeInfo = window.__DETAIL_ROUTE_INFO || { malId: "", query: "" };
    const malIdParam = String(routeInfo.ref || routeInfo.malId || "").match(/^\d+$/) ? String(routeInfo.ref || routeInfo.malId || "") : "";
    const query = (document.body?.dataset?.detailQuery || routeInfo.query || (!malIdParam ? String(routeInfo.ref || "").replace(/-/g, " ") : "") || "").trim();
    
    let full = null, selectedId = null, isLocal = false;
    const localData = await fetchLocalAnime(malIdParam, query, "");
    if (localData) {
      full = localData; selectedId = full.mal_id; isLocal = true;
      if (selectedId && (!full.studios?.length || (full.synopsis || "").length < 50)) {
        const jf = await byId(selectedId, "full");
        if (jf) {
          if (!full.studios?.length) full.studios = jf.studios;
          if (!full.genres?.length) full.genres = jf.genres;
          if (!full.synopsis || full.synopsis.length < 50) full.synopsis = jf.synopsis;
          if (!full.duration) full.duration = jf.duration; if (!full.rating) full.rating = jf.rating;
          if (!full.score) full.score = jf.score; if (!full.title_english) full.title_english = jf.title_english;
          if (!full.title_japanese) full.title_japanese = jf.title_japanese;
        }
      }
    } else {
      if (malIdParam) { selectedId = Number(malIdParam); if (selectedId) full = await byId(selectedId, "full"); }
      if (!full && query) { const f = await pickTitle(query); if (f) { selectedId = f.mal_id; full = await byId(selectedId, "full"); } }
    }
    if (!full) return;

    const preferredTitle = full.title_english || full.title || full.title_japanese || "Anime";
    const originalTitle = full.title || full.title_japanese || full.title_english || "N/A";
    const titleMain = document.querySelector("h1");
    if (titleMain) {
       titleMain.textContent = preferredTitle;
       let sub = document.getElementById("detail-original-title");
       if (!sub) { sub = document.createElement("p"); sub.id = "detail-original-title"; sub.className = "text-sm text-on-surface-variant font-medium mt-1"; titleMain.after(sub); }
       sub.textContent = originalTitle !== preferredTitle ? originalTitle : "";
       sub.style.display = sub.textContent ? "block" : "none";
    }
    document.title = `${preferredTitle} | NekoraList`;
    const src = full.images?.webp?.large_image_url || full.images?.jpg?.large_image_url || "";
    const poster = document.querySelector("section .aspect-\\[2\\/3\\] img"), bg = document.querySelector("section img[alt='Background Art']");
    if (poster && src) poster.src = src; if (bg && src) bg.src = src;

    // UI Info Update
    const statusMeta = document.getElementById("detail-status-meta");
    if (statusMeta) {
      statusMeta.innerHTML = `<span>Estado: ${toSpanishStatus(full.status)}</span><span class="text-outline-variant">&gt;&lt;</span><span>Episodios: ${full.episodes || "N/A"}</span><span class="text-outline-variant">&gt;&lt;</span><span>Duraci\u00f3n: ${toSpanishDuration(full.duration)}</span>`;
    }
    const genreC = document.getElementById("detail-genres");
    if (genreC && full.genres) genreC.innerHTML = full.genres.map(g => `<span class="px-4 py-1.5 bg-surface-container-high text-on-surface-variant text-sm rounded-full border border-outline-variant/10">${GENRE_ES[g.name] || g.name}</span>`).join("");
    const infoB = document.getElementById("detail-info-block");
    if (infoB) {
      infoB.innerHTML = "";
      renderMetaBlock(infoB, "Estudio", (full.studios || []).map(s => s.name).join(", "));
      renderMetaBlock(infoB, "Fuente", await translateToEs(full.source));
      renderMetaBlock(infoB, "Clasificaci\u00f3n", toSpanishRating(full.rating));
      if (full.score) renderMetaBlock(infoB, "Puntuaci\u00f3n", `${full.score} (${full.scored_by || 0} votos)`);
    }
    const synP = document.querySelector("h2 + p");
    if (synP) synP.textContent = await translateToEs(full.synopsis || "Sinopsis no disponible.");

    // Trailer Logic
    const buildYoutubeEmbed = (id, opts = {}) => {
      const p = new URLSearchParams({ autoplay: opts.autoplay ? "1" : "0", mute: opts.mute ? "1" : "0", controls: opts.controls ? "1" : "0", playsinline: "1", loop: opts.loop ? "1" : "0", playlist: opts.loop ? id : "", modestbranding: "1", rel: "0" });
      return `https://www.youtube.com/embed/${encodeURIComponent(id)}?${p.toString()}`;
    };
    const trailerSurface = document.getElementById("detail-trailer-surface"), trailerModal = document.getElementById("detail-trailer-modal"), trailerVideo = document.getElementById("detail-trailer-video"), trailerYt = document.getElementById("detail-trailer-yt"), trailerClose = document.getElementById("detail-trailer-close"), trailerOverlay = document.getElementById("detail-trailer-overlay"), bgVideo = document.getElementById("detail-bg-video"), bgYt = document.getElementById("detail-bg-yt");
    const trailerConfigByTitle = {
      "Frieren: Beyond Journey's End Season 2": { src: "https://files.catbox.moe/6w5jzl.mp4", bgVideo: true },
      "Jujutsu Kaisen: Shimetsu Kaiyuu - Zenpen": { src: "https://files.catbox.moe/jn0t8b.mp4", bgVideo: true },
      "Hell's Paradise Season 2": { src: "https://files.catbox.moe/gtdtry.mp4", bgVideo: true },
      "Sentenced to Be a Hero": { src: "https://files.catbox.moe/vlcrpl.mp4", bgVideo: true },
      "Oshi no Ko Season 3": { src: "https://files.catbox.moe/0k4krs.mp4", bgVideo: true }
    };
    const trailerCfg = trailerConfigByTitle[preferredTitle], trailerSrc = trailerCfg?.src, ytId = trailerCfg?.youtubeId || full?.trailer?.youtube_id;
    if ((trailerSrc || ytId) && trailerSurface && trailerModal) {
      trailerSurface.classList.remove("hidden"); trailerModal.classList.remove("hidden");
      if (trailerVideo && trailerSrc) trailerVideo.src = trailerSrc;
      const openT = () => {
        document.body.classList.add("trailer-open");
        if (ytId && trailerYt) trailerYt.src = buildYoutubeEmbed(ytId, { autoplay: true });
        else if (trailerVideo) trailerVideo.play();
      };
      const closeT = () => {
        document.body.classList.remove("trailer-open");
        if (trailerYt) trailerYt.src = "about:blank"; if (trailerVideo) { trailerVideo.pause(); trailerVideo.currentTime = 0; }
      };
      trailerSurface.addEventListener("click", openT);
      if (trailerOverlay) trailerOverlay.addEventListener("click", closeT);
      if (trailerClose) trailerClose.addEventListener("click", (e) => { e.stopPropagation(); closeT(); });
      if (trailerCfg?.bgVideo && bgVideo && trailerSrc) { bgVideo.src = trailerSrc; bgVideo.play().catch(() => {}); }
    }

    // Episodes Logic
    const synBlock = Array.from(document.querySelectorAll("h2")).find(x => /sinopsis/i.test(x.textContent || ""))?.parentElement;
    if (synBlock && !document.getElementById("detail-episodes") && (full.type || "").toLowerCase() !== "movie") {
      const epCount = Number(full.episodes) || 12;
      const titleK = [preferredTitle, query].filter(Boolean).join(" ");
      const epPrefix = /Frieren/i.test(titleK) ? "2" : /Jujutsu/i.test(titleK) ? "3" : /Hell's Paradise/i.test(titleK) ? "2" : /Sentenced/i.test(titleK) ? "1" : /Oshi no Ko/i.test(titleK) ? "3" : "1";
      const epLinks = /Frieren/i.test(titleK) ? { 1: "aHR0cHM6Ly91cWxvYWQuaXMvMmVlZXh2OWJ6eGE5Lmh0bWw=", 2: "aHR0cHM6Ly91cWxvYWQuaXMvNXRlcTJjNjF0Y2s1Lmh0bWw=", 3: "aHR0cHM6Ly91cWxvYWQuaXMvN3VzNHNlbDdreXh1Lmh0bWw=" } : /Jujutsu/i.test(titleK) ? { 1: "aHR0cHM6Ly91cWxvYWQuaXMvcHJhaWlmb3JmMGl1Lmh0bWw=", 2: "aHR0cHM6Ly91cWxvYWQuaXMvOTNlaW9kZnR4ZjQyLmh0bWw=", 3: "aHR0cHM6Ly91cWxvYWQuaXMvYTZ1OTdyc2s2bTQ1Lmh0bWw=" } : {};
      
      const decodeLink = (b64) => { try { return atob(b64); } catch { return b64; } };
      const toEmbed = (l) => { const d = decodeLink(l), m = d.match(/\/([a-z0-9]+)\.html/i); return m ? `https://uqload.is/embed-${m[1]}.html` : d; };
      
      const ensureLinkModal = () => {
        let m = document.getElementById("detail-link-modal"); if (m) return m;
        m = document.createElement("div"); m.id = "detail-link-modal"; m.className = "fixed inset-0 z-[110] hidden flex items-center justify-center p-4";
        m.innerHTML = `<div class="absolute inset-0 bg-black/80 backdrop-blur-md" data-link-backdrop></div><div class="relative w-full max-w-5xl aspect-video rounded-2xl bg-black overflow-hidden border border-white/10" data-link-shell><button type="button" data-link-close class="absolute top-4 right-4 w-12 h-12 rounded-full bg-black/50 text-white flex items-center justify-center z-30 transition-colors hover:bg-violet-600"><span class="material-symbols-outlined text-3xl">close</span></button><iframe data-link-frame class="w-full h-full border-none" allowfullscreen scrolling="no"></iframe></div>`;
        document.body.appendChild(m); return m;
      };
      const ensureImageModal = () => {
        let m = document.getElementById("detail-image-modal"); if (m) return m;
        m = document.createElement("div"); m.id = "detail-image-modal"; m.className = "fixed inset-0 z-[110] hidden flex items-center justify-center p-4";
        m.innerHTML = `<div class="absolute inset-0 bg-black/85 backdrop-blur-sm" data-image-backdrop></div><div class="relative max-w-full max-h-full rounded-2xl overflow-hidden shadow-2xl" data-image-shell><button type="button" data-image-close class="absolute top-4 right-4 w-12 h-12 rounded-full bg-black/50 text-white flex items-center justify-center z-30 transition-colors hover:bg-violet-600"><span class="material-symbols-outlined text-3xl">close</span></button><img data-image-frame class="max-w-[90vw] max-h-[85vh] object-contain block"/></div>`;
        document.body.appendChild(m); return m;
      };

      const [localEps, rawMeta] = await Promise.all([Array.isArray(full.episodes_data) ? full.episodes_data : [], fetchEpisodeMeta(selectedId)]);
      const metaMap = new Map((rawMeta || []).map(e => [Number(e.mal_id || e.episode_id || e.number), e]));
      const localMap = new Map(localEps.map(e => [Number(e.number), e]));

      const epItems = Array.from({ length: epCount }).map((_, i) => {
        const n = i + 1, meta = localMap.get(n) || metaMap.get(n) || {};
        return { epN: n, title: meta.title || `Episodio ${n}`, synopsis: shortenEpisodeText(meta.synopsis || `Sinopsis no disponible para el episodio ${n}.`), link: epLinks[n] ? decodeLink(epLinks[n]) : null };
      });

      const epSection = document.createElement("div"); epSection.id = "detail-episodes"; epSection.className = "space-y-6";
      epSection.innerHTML = `<h2 class="font-headline text-3xl font-bold">Episodios</h2><div class="space-y-4" data-ep-list></div><div class="flex justify-center"><button type="button" data-ep-more class="rounded-full border border-violet-400/40 bg-violet-500/10 px-6 py-3 text-sm font-bold uppercase tracking-widest text-violet-100 transition-all hover:bg-violet-500/20">Ver m\u00e1s episodios</button></div>`;
      const listEl = epSection.querySelector("[data-ep-list]"), moreBtn = epSection.querySelector("[data-ep-more]");
      let shown = 0, pageSize = 10;

      const seenKey = `anidex_seen_${selectedId || norm(preferredTitle)}`;
      const loadSeen = () => { try { return JSON.parse(localStorage.getItem(seenKey)) || {}; } catch { return {}; } };
      let seenMap = loadSeen();
      const updateSeenUI = (card, seen) => {
        card.classList.toggle("border-emerald-400/60", seen); card.classList.toggle("bg-emerald-500/10", seen);
        const b = card.querySelector("[data-seen-btn]"); if (b) b.classList.toggle("text-emerald-300", seen);
      };

      const renderCard = (it) => `
        <div class="episode-card flex flex-col gap-4 rounded-2xl border border-white/5 bg-surface-container-low/70 p-4 transition-all hover:border-violet-400/70 cursor-pointer md:flex-row" data-episode="${it.epN}" ${it.link ? `data-link="${it.link}" data-embed="${toEmbed(it.link)}"` : `data-img="${src}"`}>
          <div class="flex items-center gap-4 flex-shrink-0 w-40">
            <img src="${src}" class="w-20 h-20 rounded-md object-cover" />
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-violet-500/25 border border-violet-400/50 text-violet-100">${epPrefix}-${it.epN}</span>
          </div>
          <div class="flex-1">
            <h3 class="font-semibold text-white">${it.title}</h3>
            <p class="text-on-surface-variant text-sm mt-1">${it.synopsis}</p>
          </div>
          <div class="flex items-center"><button data-seen-btn class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white/50"><span class="material-symbols-outlined">visibility</span></button></div>
        </div>`;

      const bindCards = () => {
        const linkM = ensureLinkModal(), imgM = ensureImageModal();
        const closeL = () => { linkM.classList.add("hidden"); linkM.querySelector("iframe").src = ""; document.body.style.overflow = ""; };
        const closeI = () => { imgM.classList.add("hidden"); document.body.style.overflow = ""; };
        
        epSection.querySelectorAll(".episode-card").forEach(card => {
          if (card.dataset.bound) return; card.dataset.bound = "1";
          const ep = card.dataset.episode; updateSeenUI(card, !!seenMap[ep]);
          card.addEventListener("click", () => {
            seenMap[ep] = true; localStorage.setItem(seenKey, JSON.stringify(seenMap)); updateSeenUI(card, true);
            const l = card.dataset.embed || card.dataset.link, i = card.dataset.img;
            if (l) { linkM.classList.remove("hidden"); linkM.querySelector("iframe").src = l; document.body.style.overflow = "hidden"; }
            else if (i) { imgM.classList.remove("hidden"); imgM.querySelector("img").src = i; document.body.style.overflow = "hidden"; }
          });
        });
        linkM.querySelector("[data-link-close]").onclick = closeL; linkM.querySelector("[data-link-backdrop]").onclick = closeL;
        imgM.querySelector("[data-image-close]").onclick = closeI; imgM.querySelector("[data-image-backdrop]").onclick = closeI;
      };

      const append = () => {
        const next = epItems.slice(shown, shown + pageSize); if (!next.length) return;
        listEl.insertAdjacentHTML("beforeend", next.map(renderCard).join(""));
        shown += next.length; if (shown >= epItems.length) moreBtn?.classList.add("hidden");
        bindCards();
      };
      append(); moreBtn.onclick = append; synBlock.after(epSection);
    }

    // Characters Logic
    const charsRow = document.getElementById("chars-row") || document.querySelector(".carousel-row");
    if (charsRow && Array.isArray(chars)) {
       const topC = chars.slice(0, 20);
       charsRow.innerHTML = topC.map(c => `
         <div class="carousel-card flex flex-col items-center gap-2 shrink-0 text-center cursor-pointer" data-char-id="${c.character?.mal_id}">
           <div class="w-24 h-24 overflow-hidden border border-primary/20 rounded-full"><img class="w-full h-full object-cover" src="${c.character?.images?.jpg?.image_url || ""}"/></div>
           <span class="text-sm font-bold">${(c.character?.name || "").replace(/,/g, "")}</span>
           <span class="text-[11px] text-primary-dim uppercase">${c.role || ""}</span>
         </div>`).join("");
       
       const ensureCharM = () => {
         let m = document.getElementById("char-modal"); if (m) return m;
         m = document.createElement("div"); m.id = "char-modal"; m.className = "fixed inset-0 z-[110] hidden flex items-center justify-center p-4";
         m.innerHTML = `<div class="absolute inset-0 bg-black/75 backdrop-blur-sm" data-char-backdrop></div><div class="relative w-full max-w-lg bg-surface-container-high rounded-2xl overflow-hidden shadow-2xl p-6 border border-white/10" data-char-shell><button type="button" data-char-close class="absolute top-4 right-4 text-white"><span class="material-symbols-outlined text-3xl">close</span></button><div class="flex gap-6"><img data-char-img class="w-32 h-44 object-cover rounded-lg"/><div class="flex-1 flex flex-col gap-2"><h3 data-char-name class="text-2xl font-bold"></h3><p data-char-bio class="text-sm text-on-surface-variant overflow-y-auto max-h-60"></p></div></div></div>`;
         document.body.appendChild(m); return m;
       };
       const charM = ensureCharM(), closeCM = () => { charM.classList.add("hidden"); document.body.style.overflow = ""; };
       charsRow.querySelectorAll(".carousel-card").forEach(card => {
         card.onclick = async () => {
           const id = card.dataset.charId; if (!id) return;
           charM.classList.remove("hidden"); document.body.style.overflow = "hidden";
           charM.querySelector("[data-char-name]").textContent = card.querySelector(".text-sm").textContent;
           charM.querySelector("[data-char-img]").src = card.querySelector("img").src;
           charM.querySelector("[data-char-bio]").textContent = "Cargando...";
           const r = await fetch(`${API}?endpoint=${encodeURIComponent('characters/' + id + '/full')}`);
           if (r.ok) { const j = await r.json(); charM.querySelector("[data-char-bio]").textContent = (j.data?.about || "Sin descripci\u00f3n.").replace(/\\n/g, "\n"); }
         };
       });
       charM.querySelector("[data-char-close]").onclick = closeCM; charM.querySelector("[data-char-backdrop]").onclick = closeCM;
    }

    // Media Logic
    const mediaB = document.getElementById("detail-media");
    if (mediaB && pics?.length) {
      mediaB.appendChild(slider("Galer\u00eda", pics.slice(0, 15), (it) => `<img src="${it?.jpg?.large_image_url || ""}" class="h-56 w-full object-cover rounded-2xl cursor-zoom-in" onclick="this.requestFullscreen ? this.requestFullscreen() : null"/>`, "detail-pics"));
    }
    
    // Final UI Cleanup
    const external = Array.from(document.querySelectorAll("h3")).find(x => /enlaces externos/i.test(x.textContent || ""));
    if (external) external.parentElement?.remove();
  };

  const init = () => { if (document.readyState === "complete") _runInit(); else window.addEventListener("load", _runInit); };
  window.AniDexDetailData = { init };
};
AniDexDetailDataBoot(); window.AniDexDetailData?.init();
