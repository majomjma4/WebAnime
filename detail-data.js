(() => {
  const API = "https://api.jikan.moe/v4";
  const DETAIL_OVERRIDES = {
    57658: {
      episodes: 12,
      synopsis:
        "Tras las masacres de Shibuya, Itadori, cargado de culpa y preocupado por el interes de Sukuna en Fushiguro, decide no volver a la Preparatoria de Hechiceria. Se une a Choso para exorcizar los espiritus liberados por Noritoshi Kamo. En medio del caos, la cupula jujutsu reactiva la ejecucion de Itadori y asigna a Yuta Okkotsu como su verdugo. Hechiceros modernos y antiguos, ahora jugadores del Juego de la Exterminacion, chocan con motivos opuestos y empujan al mundo hacia una nueva era dominada por la hechiceria."
    },
    60058: {
      title: "Oshi no Ko Season 3"
    }
  };
  const GENRE_ES = {
    Action: "Acción",
    Adventure: "Aventura",
    Comedy: "Comedia",
    Drama: "Drama",
    Fantasy: "Fantasía",
    Romance: "Romance",
    Suspense: "Suspenso",
    Mystery: "Misterio",
    SciFi: "Ciencia ficción",
    "Sci-Fi": "Ciencia ficción",
    Horror: "Terror",
    Sports: "Deportes",
    "Slice of Life": "Recuentos de la vida",
    Supernatural: "Sobrenatural",
    "Award Winning": "Premiado"
  };

  const norm = (v) =>
    (v || "")
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^\w\s]/g, " ")
      .replace(/\s+/g, " ")
      .trim();

  const score = (q, t) => {
    const a = norm(q);
    const b = norm(t);
    if (!a || !b) return 0;
    if (a === b) return 100;
    if (b.includes(a) || a.includes(b)) return 80;
    const at = a.split(" ");
    const bt = b.split(" ");
    const overlap = at.filter((x) => bt.includes(x)).length;
    return Math.round((overlap / Math.max(at.length, bt.length)) * 70);
  };

  const pickTitle = async (query) => {
    const r = await fetch(`${API}/anime?q=${encodeURIComponent(query)}&limit=10&order_by=popularity&sort=asc`);
    if (!r.ok) return null;
    const j = await r.json();
    const list = j?.data || [];
    const best = list
      .map((it) => ({
        it,
        s: Math.max(score(query, it?.title), score(query, it?.title_english), score(query, it?.title_japanese))
      }))
      .sort((a, b) => b.s - a.s)[0];
    return best?.it || list[0] || null;
  };

  const byId = async (id, suffix = "full") => {
    const r = await fetch(`${API}/anime/${id}/${suffix}`);
    if (!r.ok) return null;
    const j = await r.json();
    return j?.data || null;
  };

  const fetchTopByType = async (type, limit = 10) => {
    try {
      const r = await fetch(`${API}/top/anime?filter=bypopularity&type=${encodeURIComponent(type)}&limit=${limit}`);
      if (!r.ok) return [];
      const j = await r.json();
      return j?.data || [];
    } catch {
      return [];
    }
  };

  const imgOf = (it) =>
    it?.images?.webp?.large_image_url ||
    it?.images?.jpg?.large_image_url ||
    it?.images?.jpg?.image_url ||
    "";

  const toSpanishStatus = (value) => {
    const v = (value || "").toLowerCase();
    if (v.includes("finished")) return "Finalizado";
    if (v.includes("currently")) return "En emisión";
    if (v.includes("not yet")) return "Próximamente";
    return value || "N/A";
  };

  const toSpanishSeason = (value) => {
    const v = (value || "").toLowerCase();
    if (v === "winter") return "invierno";
    if (v === "spring") return "primavera";
    if (v === "summer") return "verano";
    if (v === "fall") return "otoño";
    return value || "";
  };

  const toSpanishType = (value) => {
    const v = (value || "").toLowerCase();
    if (v === "tv") return "Anime";
    if (v === "movie") return "Película";
    if (v === "ova") return "OVA";
    if (v === "special") return "Especial";
    return value || "N/A";
  };

  const toSpanishDuration = (value) => {
    const raw = value || "";
    return raw.replace("min per ep", "min por episodio").replace("hr", "h");
  };
  const toSpanishRating = (value) => {
    const v = value || "";
    return v
      .replace("R - 17+ (violence & profanity)", "R - 17+ (violencia y lenguaje explícito)")
      .replace("PG-13 - Teens 13 or older", "PG-13 - Mayores de 13 años")
      .replace("PG - Children", "PG - Público general")
      .replace("G - All Ages", "G - Todas las edades")
      .replace("Rx - Hentai", "Rx - Adultos");
  };
  const genresEs = (arr) => (arr || []).map((g) => GENRE_ES[g?.name] || g?.name).join(", ");

  const translateToEs = async (text) => {
    const raw = (text || "").trim();
    if (!raw) return "";
    try {
      const res = await fetch(
        "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=es&dt=t&q=" +
          encodeURIComponent(raw)
      );
      const data = await res.json();
      return (data?.[0] || []).map((r) => r?.[0] || "").join("").trim() || raw;
    } catch {
      return raw;
    }
  };

  const renderMetaBlock = (container, label, value) => {
    const wrap = document.createElement("div");
    wrap.className = "flex flex-col gap-1";
    wrap.innerHTML = `<span class="text-xs text-on-surface-variant uppercase tracking-widest font-bold">${label}</span><span class="text-on-surface font-medium">${value || "N/A"}</span>`;
    container.appendChild(wrap);
  };

  const bindHorizontalArrows = (row, prev, next, perDefault = 5) => {
    if (!row || !prev || !next) return;
    const step = () => {
      const card = row.querySelector(".carousel-card") || row.firstElementChild;
      if (!card) return Math.max(260, Math.floor(row.clientWidth * 0.85));
      const styles = getComputedStyle(row);
      const gap = parseFloat(styles.columnGap || styles.gap || "0") || 0;
      const per = parseInt(styles.getPropertyValue("--cards")) || perDefault;
      const cardWidth = card.getBoundingClientRect().width;
      return Math.max(260, Math.round((cardWidth + gap) * per));
    };
    const setDisabled = (btn, disabled) => {
      btn.disabled = disabled;
      btn.classList.toggle("opacity-40", disabled);
      btn.classList.toggle("pointer-events-none", disabled);
    };
    const updateState = () => {
      const max = row.scrollWidth - row.clientWidth;
      const totalItems = row.querySelectorAll(".carousel-card").length;
      if (totalItems <= 4 || max <= 2) {
        prev.classList.add("hidden");
        next.classList.add("hidden");
        setDisabled(prev, true);
        setDisabled(next, true);
        return;
      }
      prev.classList.remove("hidden");
      next.classList.remove("hidden");
      setDisabled(prev, row.scrollLeft <= 2);
      setDisabled(next, row.scrollLeft >= max - 2);
    };
    prev.addEventListener("click", () => row.scrollBy({ left: -step(), behavior: "smooth" }));
    next.addEventListener("click", () => row.scrollBy({ left: step(), behavior: "smooth" }));
    row.addEventListener("scroll", () => {
      requestAnimationFrame(updateState);
    });
    window.addEventListener("resize", updateState);
    setTimeout(updateState, 0);
  };

  const slider = (title, items, renderItemHtml, idBase) => {
    const section = document.createElement("div");
    section.className = "space-y-4";
    section.innerHTML = `<div class="flex items-center justify-between"><h3 class="font-headline text-3xl font-bold">${title}</h3></div>`;
    const rowWrap = document.createElement("div");
    rowWrap.className = "relative overflow-x-hidden";
    const prev = document.createElement("button");
    prev.id = `${idBase}-prev`;
    prev.type = "button";
    prev.className = "absolute left-0 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-surface-container-high/80 border border-outline-variant/20 text-primary flex items-center justify-center shadow-md";
    prev.innerHTML = '<span class="material-symbols-outlined text-base">chevron_left</span>';
    const next = document.createElement("button");
    next.id = `${idBase}-next`;
    next.type = "button";
    next.className = "absolute right-0 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-surface-container-high/80 border border-outline-variant/20 text-primary flex items-center justify-center shadow-md";
    next.innerHTML = '<span class="material-symbols-outlined text-base">chevron_right</span>';
    const row = document.createElement("div");
    row.id = `${idBase}-row`;
    row.className = "carousel-row no-scrollbar pb-2 scroll-smooth";
    row.style.setProperty("--cards", idBase === "detail-images" ? "4" : "5");
    row.style.setProperty("--gap", "1rem");
    row.style.setProperty("--pad", "0rem");
    row.style.paddingInline = "0";
    items.forEach((it) => {
      const card = document.createElement("div");
      card.className = "carousel-card shrink-0";
      card.innerHTML = renderItemHtml(it);
      row.appendChild(card);
    });
    rowWrap.appendChild(prev);
    rowWrap.appendChild(next);
    rowWrap.appendChild(row);
    section.appendChild(rowWrap);
    setTimeout(() => {
      bindHorizontalArrows(row, prev, next, idBase === "detail-images" ? 4 : 5);
    }, 0);
    return section;
  };

  const init = async () => {
    const params = new URLSearchParams(location.search);
    const query = params.get("q") || document.querySelector("h1")?.textContent?.trim() || "Solo Leveling";
    const malIdParam = params.get("mal_id");
    const preTitle = params.get("q");
    if (preTitle && document.querySelector("h1")) {
      document.querySelector("h1").textContent = preTitle;
    }
    let full = null;
    let selectedId = null;
    if (malIdParam) {
      selectedId = Number(malIdParam);
      if (selectedId) full = await byId(selectedId, "full");
    }
    if (!full) {
      const found = await pickTitle(query);
      if (!found?.mal_id) return;
      selectedId = found.mal_id;
      full = await byId(selectedId, "full");
    }
    if (!full) return;
    const chars = (await byId(selectedId, "characters")) || [];
    const vids = (await byId(selectedId, "videos")) || {};
    const pics = (await byId(selectedId, "pictures")) || [];
    const override = DETAIL_OVERRIDES[selectedId];
    if (override) {
      if (override.title) {
        full.title = override.title;
        full.title_english = override.title;
      }
      if (override.synopsis) full.synopsis = override.synopsis;
      if (override.episodes) full.episodes = override.episodes;
    }

    const preferredTitle = full.title_english || full.title || full.title_japanese || "Anime";
    const originalTitle = full.title || full.title_japanese || full.title_english || "N/A";
    const titleMain = document.querySelector("h1");
    if (titleMain) {
      if (preferredTitle === "Frieren: Beyond Journey's End Season 2") {
        titleMain.innerHTML = "Frieren: Beyond<br/>Journey&#39;s End<br/>Season 2";
      } else if (preferredTitle === "Jujutsu Kaisen: The Culling Game Part 1") {
        titleMain.innerHTML = "Jujutsu kaisen:<br/>The culling Game<br/>Parte 1";
      } else if (preferredTitle === "Hell's Paradise Season 2") {
        titleMain.innerHTML = "Hell&#39;s Paradise<br/>Season 2";
      } else if (preferredTitle === "Sentenced to Be a Hero") {
        titleMain.innerHTML = "Sentenced to<br/>Be a Hero";
      } else {
        titleMain.textContent = preferredTitle;
      }
      let sub = document.getElementById("detail-original-title");
      if (!sub) {
        sub = document.createElement("p");
        sub.id = "detail-original-title";
        sub.className = "text-sm text-on-surface-variant font-medium mt-1";
        titleMain.insertAdjacentElement("afterend", sub);
      }
      sub.textContent = `Título original: ${originalTitle}`;
    }
    document.title = `${preferredTitle} | AniDex`;

    const poster = document.querySelector("section .aspect-\\[2\\/3\\] img");
    const bg = document.querySelector("section img[alt='Background Art']");
    const src = imgOf(full);
    if (poster && src) poster.src = src;
    if (bg && src) bg.src = src;
    document.body.dataset.detailTitle = preferredTitle;
    document.body.dataset.detailImage = src || "";
    document.body.dataset.detailType = full?.type === "Movie" ? "Película" : "Anime";
    const isMovie = (full.type || "").toLowerCase() === "movie";
    const badge = document.getElementById("detail-badge");
    if (badge) {
      if (
        preferredTitle === "Frieren: Beyond Journey's End Season 2" ||
        preferredTitle === "Jujutsu Kaisen: The Culling Game Part 1" ||
        preferredTitle === "Jujutsu Kaisen: Shimetsu Kaiyuu - Zenpen" ||
        preferredTitle === "Hell's Paradise Season 2" ||
        preferredTitle === "Sentenced to Be a Hero" ||
        preferredTitle === "Oshi no Ko Season 3"
      ) {
        badge.textContent = "Lo m\u00e1s Nuevo";
        badge.className = "px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase text-white/90 bg-gradient-to-r from-fuchsia-500/40 via-sky-400/30 to-emerald-400/30 border border-fuchsia-300/60 shadow-[0_0_16px_rgba(139,92,246,0.45)]";
      } else {
        badge.textContent = toSpanishType(full.type) || "Anime";
        badge.className = "px-3 py-1 bg-primary/10 border border-primary/20 text-primary-dim text-xs font-bold rounded-full tracking-wider uppercase";
      }
    }

    const trailerSurface = document.getElementById("detail-trailer-surface");
    const trailerModal = document.getElementById("detail-trailer-modal");
    const trailerCard = document.getElementById("detail-trailer-card");
    const trailerVideo = document.getElementById("detail-trailer-video");
    const trailerClose = document.getElementById("detail-trailer-close");
    const trailerOverlay = document.getElementById("detail-trailer-overlay");
    const bgVideo = document.getElementById("detail-bg-video");
    const trailerConfigByTitle = {
      "Frieren: Beyond Journey's End Season 2": {
        src: "https://o.uguu.se/kRQnonbl.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Jujutsu Kaisen: The Culling Game Part 1": {
        src: "trailer/jjk.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Jujutsu Kaisen: Shimetsu Kaiyuu - Zenpen": {
        src: "https://d.uguu.se/GDuGpxiE.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Hell's Paradise Season 2": {
        src: "https://n.uguu.se/puhsOohL.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Sentenced to Be a Hero": {
        src: "https://d.uguu.se/aSchXjRO.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Oshi no Ko Season 3": {
        src: "https://h.uguu.se/cXblYGQt.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      }
    };
    const trailerConfig = trailerConfigByTitle[preferredTitle];
    const trailerSrc = trailerConfig?.src;
    if (trailerSrc && trailerSurface && trailerModal && trailerVideo) {
      document.body.classList.remove("bg-video-on");
      document.body.classList.remove("bg-video-original");
      trailerSurface.classList.remove("hidden");
      trailerModal.classList.remove("hidden");
      trailerVideo.src = trailerSrc;
      if (src) trailerVideo.poster = src;
      trailerVideo.preload = "metadata";
      let bgPlayTimer = null;
      const trailerIcon = document.getElementById("detail-trailer-icon");
      if (trailerIcon) {
        if (trailerConfig?.showPlay) {
          trailerIcon.classList.remove("hidden");
        } else {
          trailerIcon.classList.add("hidden");
        }
      }
      if (bgVideo && trailerConfig?.bgVideo) {
        document.body.classList.add("bg-video-off");
        bgVideo.src = trailerSrc;
        bgVideo.preload = "metadata";
        bgVideo.muted = true;
        bgVideo.loop = false;
        bgVideo.playsInline = true;
        if (trailerConfig?.originalQuality) {
          document.body.classList.add("bg-video-original");
        }
        bgPlayTimer = setTimeout(() => {
          const playBg = bgVideo.play();
          if (playBg?.then) {
            playBg.then(() => {
              document.body.classList.add("bg-video-on");
              document.body.classList.remove("bg-video-off");
            }).catch(() => {});
          }
        }, 5000);
        bgVideo.onended = () => {
          document.body.classList.remove("bg-video-on");
          document.body.classList.add("bg-video-off");
          if (bgPlayTimer) clearTimeout(bgPlayTimer);
          bgPlayTimer = setTimeout(() => {
            const playBg = bgVideo.play();
            if (playBg?.then) {
              playBg.then(() => {
                document.body.classList.add("bg-video-on");
                document.body.classList.remove("bg-video-off");
              }).catch(() => {});
            }
          }, 5000);
        };
      } else if (bgVideo) {
        bgVideo.pause();
        bgVideo.removeAttribute("src");
      }
      const openTrailer = () => {
        document.body.classList.add("trailer-open");
        document.body.classList.remove("bg-video-on");
        document.body.classList.add("bg-video-off");
        if (bgPlayTimer) {
          clearTimeout(bgPlayTimer);
          bgPlayTimer = null;
        }
        if (bgVideo) bgVideo.pause();
        trailerVideo.muted = false;
        trailerVideo.volume = 0.9;
        const playPromise = trailerVideo.play();
        if (playPromise?.catch) playPromise.catch(() => {});
      };
      const closeTrailer = () => {
        document.body.classList.remove("trailer-open");
        trailerVideo.pause();
        trailerVideo.currentTime = 0;
        if (bgVideo && trailerConfig?.bgVideo) {
          document.body.classList.remove("bg-video-on");
          document.body.classList.add("bg-video-off");
          if (bgPlayTimer) {
            clearTimeout(bgPlayTimer);
          }
          bgPlayTimer = setTimeout(() => {
            const playBg = bgVideo.play();
            if (playBg?.then) {
              playBg.then(() => {
                document.body.classList.add("bg-video-on");
                document.body.classList.remove("bg-video-off");
              }).catch(() => {});
            }
          }, 5000);
        }
      };
      trailerSurface.addEventListener("click", openTrailer);
      if (trailerOverlay) trailerOverlay.addEventListener("click", closeTrailer);
      if (trailerClose) trailerClose.addEventListener("click", (e) => {
        e.stopPropagation();
        closeTrailer();
      });
      if (trailerCard) trailerCard.addEventListener("click", (e) => e.stopPropagation());
    }

    const syn = document.querySelector("h2 + p");
    if (syn) syn.textContent = (await translateToEs(full.synopsis)) || "Sinopsis no disponible.";

    const synopsisBlockEl = Array.from(document.querySelectorAll("h2"))
      .find((x) => /sinopsis/i.test(x.textContent || ""))?.parentElement;
    const titleKey = [preferredTitle, query, preTitle].filter(Boolean).join(" ");
    const isFrieren = /Frieren:\s*Beyond Journey's End Season 2/i.test(titleKey);
    const isJujutsuZenpen = /Jujutsu Kaisen:\s*Shimetsu Kaiyuu\s*-\s*Zenpen/i.test(titleKey);
    const isHellsParadise = /Hell's Paradise Season 2/i.test(titleKey);
    const isSentencedHero = /Sentenced to\s*Be a Hero/i.test(titleKey);
    const isOshiNoKo = /Oshi no Ko Season 3/i.test(titleKey);
    if (
      !isMovie &&
      synopsisBlockEl &&
      !document.getElementById("detail-episodes")
    ) {
      const episodesTotal = Number(full.episodes) || 12;
      const cover =
        full.images?.jpg?.large_image_url ||
        full.images?.webp?.large_image_url ||
        full.images?.jpg?.image_url ||
        "";
      const episodePrefix = isFrieren
        ? "2"
        : isJujutsuZenpen
          ? "3"
          : isHellsParadise
            ? "2"
            : isSentencedHero
              ? "1"
              : isOshiNoKo
                ? "3"
                : "1";
      const linksForTitle = isFrieren
        ? {
            1: "https://uqload.is/2eeexv9bzxa9.html",
            2: "https://uqload.is/5teq2c61tck5.html",
            3: "https://uqload.is/7us4sel7kyxu.html"
          }
        : isJujutsuZenpen
          ? {
              1: "https://uqload.is/praiiforf0iu.html",
              2: "https://uqload.is/93eiodftxf42.html",
              3: "https://uqload.is/a6u97rsk6m45.html"
            }
          : isHellsParadise
            ? {
                1: "https://uqload.is/rxbcak4w8fv2.html",
                2: "https://uqload.is/0mu7b2lg2b9y.html",
                3: "https://uqload.is/e2vhqb3mp4v8.html"
              }
            : isSentencedHero
              ? {
                  1: "https://uqload.is/l7qqqbie1dag.html",
                  2: "https://uqload.is/s2sw5qm1wq35.html",
                  3: "https://uqload.is/hdv22owq6n74.html"
                }
              : isOshiNoKo
                ? {
                    1: "https://uqload.is/8p4gel5ytxm0.html",
                    2: "https://uqload.is/upmo7gybfcap.html",
                    3: "https://uqload.is/22odusepde14.html"
                  }
                : {};
      const toEmbed = (link) => {
        const match = link.match(/\/([a-z0-9]+)\.html/i);
        if (!match) return link;
        return `https://uqload.is/embed-${match[1]}.html`;
      };
      const episodeSnippets = [
        "Frieren y su equipo enfrentan un reto inesperado mientras avanzan en su viaje. La respuesta pone a prueba su confianza y sus limites.",
        "Un recuerdo del pasado revela una decision clave que cambia el rumbo del grupo. Las consecuencias se sienten en cada paso.",
        "La magia antigua vuelve a aparecer y pone a prueba la determinacion de todos. Un descubrimiento altera su mision.",
        "Un nuevo aliado surge en el camino, con un objetivo que no es lo que parece. La verdad obliga a elegir con cuidado.",
        "La calma se rompe cuando una amenaza obliga a tomar una decision dificil. El equipo debe actuar sin margen de error.",
        "Un episodio emotivo que profundiza los lazos y el crecimiento del equipo. Cada uno enfrenta su propio miedo."
      ];
      const episodeTitles = [
        "Ecos del viaje",
        "Promesa en silencio",
        "Magia olvidada",
        "El precio del recuerdo",
        "Guardianes del bosque",
        "La prueba del valor",
        "Sombras del pasado",
        "Rastro de esperanza",
        "Votos y despedidas",
        "El camino de los heroes",
        "Huellas de luz",
        "Destino compartido"
      ];
      const episodesMarkup = Array.from({ length: episodesTotal }).map((_, index) => {
        const epNumber = index + 1;
        const epCode = `${episodePrefix}-${epNumber}`;
        const epTitle = episodeTitles[index] || `Episodio ${epNumber}`;
        const epSynopsis = episodeSnippets[index % episodeSnippets.length];
        const linkUrl = linksForTitle[epNumber];
        const episodeAttrs = linkUrl
          ? `data-episode-link="${linkUrl}" data-episode-embed="${toEmbed(linkUrl)}"`
          : `data-episode-image="${cover}" data-episode-title="${epTitle}"`;
        const episodeClasses = "cursor-pointer";
        return `
          <div class="flex gap-6 items-center rounded-2xl border border-white/5 bg-surface-container-low/70 p-4 transition-all duration-300 hover:border-violet-400/70 hover:shadow-[0_0_18px_rgba(139,92,246,0.35)] hover:-translate-y-0.5 ${episodeClasses}" data-episode="${epNumber}" ${episodeAttrs}>
            <div class="flex items-center gap-4 flex-shrink-0">
              <div class="w-20 h-20 rounded-[6px] overflow-hidden bg-surface-container-high">
                <img src="${cover}" alt="Episodio ${epCode}" class="w-full h-full object-cover" />
              </div>
              <span class="min-w-[3.5rem] text-center px-4 py-2 rounded-full text-sm font-bold uppercase tracking-widest text-violet-100 bg-violet-500/25 border border-violet-400/50">2-${epNumber}</span>
            </div>
            <div class="min-w-0 ml-2 flex flex-col justify-center">
              <h3 class="font-semibold text-white text-base">${epTitle}</h3>
              <p class="text-on-surface-variant text-sm mt-2">${epSynopsis}</p>
            </div>
          </div>
        `;
      }).join("");

      const episodesSection = document.createElement("div");
      episodesSection.id = "detail-episodes";
      episodesSection.className = "space-y-6";
      episodesSection.innerHTML = `
        <h2 class="font-headline text-3xl font-bold">Episodios</h2>
        <div class="space-y-4">${episodesMarkup}</div>
      `;
      synopsisBlockEl.insertAdjacentElement("afterend", episodesSection);

      const episodeModalId = "detail-episode-modal";
      let episodeModal = document.getElementById(episodeModalId);
      if (!episodeModal) {
        episodeModal = document.createElement("div");
        episodeModal.id = episodeModalId;
        episodeModal.className = "fixed inset-0 z-[90] hidden";
        episodeModal.innerHTML = `
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
          <div class="relative mx-auto mt-[12vh] w-[min(92vw,720px)] rounded-2xl bg-surface-container-high/95 border border-violet-500/30 p-4 shadow-2xl overflow-hidden">
            <button type="button" data-episode-close class="absolute top-3 right-3 w-9 h-9 rounded-full bg-surface-container-low text-on-surface-variant hover:text-on-surface flex items-center justify-center">x</button>
            <video data-episode-video-player class="w-full rounded-xl bg-black" controls playsinline preload="metadata">
              <source data-episode-video-source type="video/mp4" />
            </video>
          </div>
        `;
        document.body.appendChild(episodeModal);
      }

      const videoCard = episodesSection.querySelector("[data-episode-video]");
      const linkCards = Array.from(episodesSection.querySelectorAll("[data-episode-link]"));
      const imageCards = Array.from(episodesSection.querySelectorAll("[data-episode-image]"));

      if (linkCards.length) {
        let linkModal = document.getElementById("detail-episode-link-modal");
        if (!linkModal) {
          linkModal = document.createElement("div");
          linkModal.id = "detail-episode-link-modal";
          linkModal.className = "fixed inset-0 z-[90] hidden";
          linkModal.innerHTML = `
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative mx-auto mt-[8vh] w-[min(94vw,1100px)] h-[min(80vh,720px)] overflow-visible">
              <button type="button" data-link-close class="absolute -top-5 -right-5 w-10 h-10 rounded-full bg-violet-500 text-white text-lg font-bold shadow-lg shadow-violet-500/40 hover:bg-violet-400 flex items-center justify-center">&times;</button>
              <div class="w-full h-full overflow-hidden">
                <iframe data-link-frame class="w-full h-full bg-black" allow="autoplay; fullscreen" allowfullscreen referrerpolicy="no-referrer"></iframe>
              </div>
            </div>
          `;
          document.body.appendChild(linkModal);
        }
        const linkFrame = linkModal.querySelector("[data-link-frame]");
        const closeBtn = linkModal.querySelector("[data-link-close]");
        const backdrop = linkModal.firstElementChild;
        const closeLink = () => {
          if (linkFrame) linkFrame.src = "";
          linkModal.classList.add("hidden");
        };
        const openLink = (card) => {
          const link = card.getAttribute("data-episode-embed") || card.getAttribute("data-episode-link");
          if (!link || !linkFrame) return;
          linkFrame.src = link;
          linkModal.classList.remove("hidden");
        };
        linkCards.forEach((card) => {
          card.addEventListener("click", () => openLink(card));
        });
        if (closeBtn) closeBtn.addEventListener("click", closeLink);
        if (backdrop) backdrop.addEventListener("click", closeLink);
      }

      if (imageCards.length) {
        let imageModal = document.getElementById("detail-episode-image-modal");
        if (!imageModal) {
          imageModal = document.createElement("div");
          imageModal.id = "detail-episode-image-modal";
          imageModal.className = "fixed inset-0 z-[90] hidden";
          imageModal.innerHTML = `
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative mx-auto mt-[10vh] w-[min(90vw,720px)] overflow-visible">
              <button type="button" data-image-close class="absolute -top-5 -right-5 w-10 h-10 rounded-full bg-violet-500 text-white text-lg font-bold shadow-lg shadow-violet-500/40 hover:bg-violet-400 flex items-center justify-center">&times;</button>
              <img data-image-frame class="w-full rounded-2xl bg-black object-cover" alt="Portada episodio" />
            </div>
          `;
          document.body.appendChild(imageModal);
        }
        const imageFrame = imageModal.querySelector("[data-image-frame]");
        const closeBtn = imageModal.querySelector("[data-image-close]");
        const backdrop = imageModal.firstElementChild;
        const closeImage = () => {
          if (imageFrame) imageFrame.removeAttribute("src");
          imageModal.classList.add("hidden");
        };
        const openImage = (card) => {
          const src = card.getAttribute("data-episode-image");
          const title = card.getAttribute("data-episode-title") || "Portada episodio";
          if (!src || !imageFrame) return;
          imageFrame.src = src;
          imageFrame.alt = title;
          imageModal.classList.remove("hidden");
        };
        imageCards.forEach((card) => {
          card.addEventListener("click", () => openImage(card));
        });
        if (closeBtn) closeBtn.addEventListener("click", closeImage);
        if (backdrop) backdrop.addEventListener("click", closeImage);
      }
      if (videoCard && episodeModal) {
        const player = episodeModal.querySelector("[data-episode-video-player]");
        const source = episodeModal.querySelector("[data-episode-video-source]");
        const closeBtn = episodeModal.querySelector("[data-episode-close]");
        const backdrop = episodeModal.firstElementChild;
        const openEpisode = () => {
          if (!player || !source) return;
          source.src = videoCard.getAttribute("data-episode-video") || "";
          player.load();
          player.currentTime = 0;
          episodeModal.classList.remove("hidden");
          const playPromise = player.play();
          if (playPromise?.catch) playPromise.catch(() => {});
        };
        const closeEpisode = () => {
          if (player && source) {
            player.pause();
            source.removeAttribute("src");
            player.load();
          }
          episodeModal.classList.add("hidden");
        };
        videoCard.addEventListener("click", openEpisode);
        if (closeBtn) closeBtn.addEventListener("click", closeEpisode);
        if (backdrop) backdrop.addEventListener("click", closeEpisode);
      }
    }

    const scoreEl = document.querySelector(".font-bold.text-lg");
    if (scoreEl) scoreEl.textContent = (full.score || "N/A").toString();

    const statusLine = document.getElementById("detail-status-meta") || document.querySelector("h1 + p");
    if (statusLine) {
      const yearValue = full.year || "";
      const episodesValue = full.episodes || "";
      statusLine.className = "text-on-surface-variant font-medium space-y-2 text-sm lg:text-base";
      const yearBlock = (!isMovie || yearValue)
        ? `<span class="flex flex-col"><span class="text-primary-dim text-xs uppercase tracking-wider">Año</span><span>${yearValue || "N/A"}</span></span>`
        : "";
      const episodesBlock = !isMovie
        ? `<div><span class="flex flex-col"><span class="text-primary-dim text-xs uppercase tracking-wider">Episodios</span><span>${episodesValue || "N/A"}</span></span></div>`
        : "";
      statusLine.innerHTML = `
        <div class="flex flex-wrap items-start gap-x-10 gap-y-2">
          <span class="flex flex-col"><span class="text-primary-dim text-xs uppercase tracking-wider">Estado</span><span>${toSpanishStatus(full.status)}</span></span>
          ${yearBlock}
        </div>
        ${episodesBlock}
      `;
    }

    const genres = document.querySelector("h1")?.parentElement?.querySelector(".flex.flex-wrap.gap-2");
    if (genres) {
      genres.innerHTML = (full.genres || [])
        .map((g) => `<span class="px-4 py-1.5 rounded-full text-sm font-semibold text-white/90 bg-gradient-to-r from-violet-500/25 via-sky-500/10 to-fuchsia-500/20 border border-violet-400/30 shadow-[0_0_12px_rgba(139,92,246,0.35)]">${g.name}</span>`)
        .join("");
    }

    const infoBox = Array.from(document.querySelectorAll("h3")).find((x) => /informaci/i.test(x.textContent || ""));
    const infoList = infoBox?.parentElement?.querySelector(".space-y-6");
    if (infoList) {
      infoList.innerHTML = "";
      renderMetaBlock(infoList, "Título (EN)", full.title_english || "N/A");
      renderMetaBlock(infoList, "Título (JP)", full.title_japanese || "N/A");
      renderMetaBlock(infoList, "Título Original", full.title || "N/A");
      renderMetaBlock(infoList, "Tipo", toSpanishType(full.type));
      if (!isMovie) {
        renderMetaBlock(infoList, "Episodios", full.episodes || "N/A");
      }
      renderMetaBlock(infoList, "Duración", toSpanishDuration(full.duration) || "N/A");
      if (!isMovie || full.year) {
        renderMetaBlock(infoList, "Año", `${full.year || "N/A"}`);
      }
      renderMetaBlock(infoList, "Temporadas", `${((full?.relations || []).filter((r) => /sequel/i.test(r?.relation || "")).length + 1) || 1}`);
      renderMetaBlock(infoList, "Estado", toSpanishStatus(full.status));
      renderMetaBlock(infoList, "Géneros", (genresEs(full.genres) || "N/A").replace(/,/g, " ·"));
      renderMetaBlock(infoList, "Clasificación", toSpanishRating(full.rating) || "N/A");
      renderMetaBlock(infoList, "Ranking", full.rank ? `# ${full.rank}` : "N/A");
      renderMetaBlock(infoList, "Estudio", full.studios?.[0]?.name || "N/A");
    }

    const charsRow = document.querySelector(".hide-scrollbar");
    if (charsRow) {
      const mains = chars.filter((c) => /main/i.test(c?.role || ""));
      const supports = chars.filter((c) => !/main/i.test(c?.role || ""));
      const topChars = [...mains, ...supports].slice(0, 20);
      charsRow.classList.add("carousel-row");
      charsRow.style.setProperty("--cards", "5");
      charsRow.style.setProperty("--gap", "1rem");
      charsRow.style.setProperty("--pad", "0rem");
      charsRow.style.paddingInline = "0";
      charsRow.innerHTML = topChars.map((c, idx) => {
        const cleanName = (c.character?.name || "Personaje").replace(/,/g, "");
        const roleLabel = /main/i.test(c?.role || "") ? "principal" : "secundario";
        const desc =
          roleLabel === "principal"
            ? "Figura clave que impulsa la historia con metas y conflictos."
            : "Personaje de apoyo que enriquece la trama con matices propios.";
        return `
        <div class="carousel-card flex flex-col items-center gap-2 shrink-0 text-center">
          <div class="w-24 h-24 overflow-hidden border border-primary/20 rounded-full">
            <img alt="${c.character?.name || "Personaje"}" class="w-full h-full object-cover" src="${c.character?.images?.jpg?.image_url || ""}"/>
          </div>
          <span class="text-sm font-bold break-words whitespace-normal">${cleanName}</span>
          <span class="text-[11px] font-semibold text-primary-dim">${roleLabel}</span>
          <span class="text-xs text-on-surface-variant leading-snug break-words whitespace-normal">${desc}</span>
        </div>`;
      }).join("");
      const prev = document.getElementById("chars-prev");
      const next = document.getElementById("chars-next");
      bindHorizontalArrows(charsRow, prev, next, 5);
    }

    const synopsisBlock = Array.from(document.querySelectorAll("h2")).find((x) => /sinopsis/i.test(x.textContent || ""))?.parentElement;
    const mediaBlock = document.getElementById("detail-media") || synopsisBlock;
    if (mediaBlock) {
      const images = pics.slice(0, 20);
      if (images.length) {
        mediaBlock.appendChild(slider("Galeria", images, (it) => `
          <img data-zoomable src="${it?.jpg?.large_image_url || it?.jpg?.image_url || ""}" class="h-56 w-full object-cover cursor-zoom-in rounded-2xl" />
        `, "detail-images"));
      }
      const clips = [vids?.promo?.[0], ...(vids?.promo || []).slice(1, 8)]
        .filter(Boolean)
        .filter((it) => it?.trailer?.url || it?.trailer?.youtube_id);
      if (clips.length) {
        mediaBlock.appendChild(slider("Videos / Clips", clips, (it) => `
          <a href="${it?.trailer?.url || (it?.trailer?.youtube_id ? `https://www.youtube.com/watch?v=${it.trailer.youtube_id}` : "#")}" target="_blank" rel="noopener noreferrer" class="block h-36 w-36 overflow-hidden border border-outline-variant/20 bg-surface-container-high relative">
            <img src="${it?.trailer?.images?.medium_image_url || it?.trailer?.images?.image_url || ""}" class="h-full w-full object-cover"/>
            <span class="absolute inset-0 bg-black/30 flex items-center justify-center text-xs font-bold">Ver clip</span>
          </a>
        `, "detail-videos"));
      }
    }

    // Galería relacionada eliminada por solicitud.

    const external = Array.from(document.querySelectorAll("h3")).find((x) => /enlaces externos/i.test((x.textContent || "").toLowerCase()));
    if (external) external.parentElement?.remove();

    const trailerBtn = document.getElementById("detail-trailer-btn");
    if (trailerBtn) {
      const turl =
        (full?.trailer?.youtube_id ? `https://www.youtube.com/watch?v=${full.trailer.youtube_id}` : "") ||
        full?.trailer?.url ||
        `https://www.youtube.com/results?search_query=${encodeURIComponent((full?.title || query || "") + " trailer")}`;
      trailerBtn.href = turl;
    }

    const actionBtns = document.querySelectorAll("[data-add-my-list], [data-add-favorite]");
    actionBtns.forEach((btn) => {
      btn.dataset.itemTitle = preferredTitle;
      btn.dataset.itemImage = src || "";
      btn.dataset.itemType = (full.type || "").toLowerCase() === "movie" ? "Pelicula" : "Anime";
      btn.dataset.itemId = String(selectedId || "");
    });
    document.body.dataset.detailTitle = preferredTitle;
    document.body.dataset.detailImage = src || "";
    document.body.dataset.detailType = (full.type || "").toLowerCase() === "movie" ? "Pelicula" : "Anime";
    if (window.AniDexFavorites) window.AniDexFavorites.refresh();

    // Recomendados: si es película, mostrar películas; si no, series.
    const recCards = Array.from(document.querySelectorAll("section a.group.cursor-pointer"));
    if (recCards.length) {
      const recType = (full.type || "").toLowerCase() === "movie" ? "movie" : "tv";
      const recItems = await fetchTopByType(recType, recCards.length);
      recCards.forEach((a, i) => {
        const it = recItems[i];
        if (!it) return;
        const img = a.querySelector("img");
        const h = a.querySelector("h4,h3,h5");
        const p = a.querySelector("p");
        const scoreEl = a.querySelector(".text-xs.font-bold");
        if (img) img.src = imgOf(it) || img.src;
        if (h) h.textContent = it.title || h.textContent;
        if (p) p.textContent = (it.genres || []).map((g) => g.name).slice(0, 2).join(", ");
        if (scoreEl && typeof it.score === "number") scoreEl.textContent = it.score.toFixed(1);
        if (it?.mal_id) a.setAttribute("data-mal-id", String(it.mal_id));
        a.href = `detail.html?mal_id=${encodeURIComponent(String(it.mal_id || ""))}&q=${encodeURIComponent(it.title || "")}`;
      });
    }

    // Zoom simple para imágenes del slider
    document.addEventListener("click", (e) => {
      const img = e.target.closest("img[data-zoomable]");
      if (!img) return;
      const overlay = document.createElement("div");
      overlay.className = "fixed inset-0 z-[120] bg-black/90 flex items-center justify-center p-6";
      overlay.innerHTML = `<img src="${img.src}" class="max-h-[92vh] max-w-[92vw] object-contain border border-zinc-700" />`;
      overlay.addEventListener("click", () => overlay.remove());
      document.body.appendChild(overlay);
    });
  };

  window.AniDexDetailData = { init };
})();

