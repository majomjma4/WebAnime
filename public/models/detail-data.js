const AniDexDetailDataBoot = () => {
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
    Action: "Accin",
    Adventure: "Aventura",
    Comedy: "Comedia",
    Drama: "Drama",
    Fantasy: "Fantasa",
    Romance: "Romance",
    Suspense: "Suspenso",
    Mystery: "Misterio",
    SciFi: "Ciencia ficcin",
    "Sci-Fi": "Ciencia ficcin",
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
    if (v.includes("currently")) return "En emisin";
    if (v.includes("not yet")) return "Prximamente";
    return value || "N/A";
  };

  const toSpanishSeason = (value) => {
    const v = (value || "").toLowerCase();
    if (v === "winter") return "invierno";
    if (v === "spring") return "primavera";
    if (v === "summer") return "verano";
    if (v === "fall") return "otoo";
    return value || "";
  };

  const toSpanishType = (value) => {
    const v = (value || "").toLowerCase();
    if (v === "tv") return "Anime";
    if (v === "movie") return "Pelcula";
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
      .replace("R - 17+ (violence & profanity)", "R - 17+ (violencia y lenguaje explcito)")
      .replace("PG-13 - Teens 13 or older", "PG-13 - Mayores de 13 aos")
      .replace("PG - Children", "PG - Pblico general")
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
    const isLogged = localStorage.getItem("nekora_logged_in") === "true";
    const isPremium = localStorage.getItem("nekora_premium") === "true";
    const canWatchEpisodes = isLogged && isPremium;
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
    try {
      const mapKey = "anidex_title_id_map_v1";
      const mapRaw = localStorage.getItem(mapKey);
      const map = mapRaw ? JSON.parse(mapRaw) : {};
      const addMap = (val) => {
        const key = norm(val || "");
        if (key) map[key] = selectedId;
      };
      addMap(full.title);
      addMap(full.title_english);
      addMap(full.title_japanese);
      addMap(query);
      addMap(preTitle);
      localStorage.setItem(mapKey, JSON.stringify(map));
    } catch {}
    const chars = (await byId(selectedId, "characters")) || [];
    const vids = (await byId(selectedId, "videos")) || {};
    const pics = (await byId(selectedId, "pictures")) || [];
    const forceTitles = [
      "Jujutsu Kaisen: Shimetsu Kaiyuu - Zenpen"
    ];
    const requestedTitle = preTitle || query || "";
    const forceTitle = forceTitles.find((t) => norm(t) === norm(requestedTitle));
    const override = DETAIL_OVERRIDES[selectedId];
    if (override) {
      if (override.title) {
        full.title = override.title;
        full.title_english = override.title;
      }
      if (override.synopsis) full.synopsis = override.synopsis;
      if (override.episodes) full.episodes = override.episodes;
    }
    if (forceTitle) {
      full.title = forceTitle;
      full.title_english = forceTitle;
    }

    const preferredTitle = full.title_english || full.title || full.title_japanese || "Anime";
    const originalTitle = full.title || full.title_japanese || full.title_english || "N/A";
    const titleMain = document.querySelector("h1");
    if (titleMain) {
      if (preferredTitle === "Frieren: Beyond Journey's End Season 2") {
        titleMain.innerHTML = "Frieren: Beyond<br/>Journey&#39;s End<br/>Season 2";
      } else if (preferredTitle === "Jujutsu Kaisen: The Culling Game Part 1") {
        titleMain.innerHTML = "Jujutsu kaisen:<br/>The culling Game<br/>Parte 1";
      } else if (preferredTitle === "Jujutsu Kaisen: Shimetsu Kaiyuu - Zenpen") {
        titleMain.innerHTML = "Jujutsu Kaisen:<br/>Shimetsu Kaiyuu - Zenpen";
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
      sub.textContent = `Ttulo original: ${originalTitle}`;
    }
    document.title = `${preferredTitle} | AniDex`;

    const poster = document.querySelector("section .aspect-\\[2\\/3\\] img");
    const bg = document.querySelector("section img[alt='Background Art']");
    const src = imgOf(full);
    if (poster && src) poster.src = src;
    if (bg && src) bg.src = src;
    document.body.dataset.detailTitle = preferredTitle;
    document.body.dataset.detailImage = src || "";
    document.body.dataset.detailType = full?.type === "Movie" ? "Pelcula" : "Anime";
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
    const trailerYt = document.getElementById("detail-trailer-yt");
    const trailerClose = document.getElementById("detail-trailer-close");
    const trailerOverlay = document.getElementById("detail-trailer-overlay");
    const bgVideo = document.getElementById("detail-bg-video");
    const bgYt = document.getElementById("detail-bg-yt");
    const trailerConfigByTitle = {
      "Frieren: Beyond Journey's End Season 2": {
        src: "https://files.catbox.moe/6w5jzl.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Jujutsu Kaisen: Shimetsu Kaiyuu - Zenpen": {
        src: "https://files.catbox.moe/jn0t8b.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Hell's Paradise Season 2": {
        src: "https://files.catbox.moe/gtdtry.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Sentenced to Be a Hero": {
        src: "https://files.catbox.moe/vlcrpl.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      },
      "Oshi no Ko Season 3": {
        src: "https://files.catbox.moe/0k4krs.mp4",
        bgVideo: true,
        showPlay: false,
        originalQuality: true
      }
    };
    const trailerConfig = trailerConfigByTitle[preferredTitle];
    const trailerSrc = trailerConfig?.src;
    const youtubeId = trailerConfig?.youtubeId || full?.trailer?.youtube_id || full?.trailer?.youtubeId;
    const hasTrailer = Boolean(trailerSrc || youtubeId);
    const buildYoutubeEmbed = (id, opts = {}) => {
      const params = new URLSearchParams({
        autoplay: opts.autoplay ? "1" : "0",
        mute: opts.mute ? "1" : "0",
        controls: opts.controls ? "1" : "0",
        playsinline: "1",
        loop: opts.loop ? "1" : "0",
        playlist: opts.loop ? id : "",
        modestbranding: "1",
        rel: "0",
        iv_load_policy: "3"
      });
      return `https://www.youtube.com/embed/${encodeURIComponent(id)}?${params.toString()}`;
    };
    if (hasTrailer && trailerSurface && trailerModal) {
      document.body.classList.remove("bg-video-on");
      document.body.classList.remove("bg-video-original");
      document.body.classList.remove("bg-video-youtube");
      trailerSurface.classList.remove("hidden");
      trailerModal.classList.remove("hidden");
      if (trailerVideo) {
        if (trailerSrc) trailerVideo.src = trailerSrc;
        else {
          trailerVideo.pause();
          trailerVideo.removeAttribute("src");
          trailerVideo.load();
        }
        if (src) trailerVideo.poster = src;
        trailerVideo.preload = "metadata";
      }
      if (trailerYt) {
        trailerYt.src = "about:blank";
      }
      const useYouTube = Boolean(youtubeId);
      if (useYouTube) {
        document.body.classList.add("bg-video-youtube");
        if (trailerVideo) trailerVideo.classList.add("hidden");
        if (trailerYt) trailerYt.classList.remove("hidden");
      } else {
        if (trailerVideo) trailerVideo.classList.remove("hidden");
        if (trailerYt) trailerYt.classList.add("hidden");
      }
      let bgPlayTimer = null;
      const trailerIcon = document.getElementById("detail-trailer-icon");
      if (trailerIcon) {
        if (trailerConfig?.showPlay) {
          trailerIcon.classList.remove("hidden");
        } else {
          trailerIcon.classList.add("hidden");
        }
      }
      if (trailerConfig?.bgVideo) {
        document.body.classList.add("bg-video-off");
        if (useYouTube && bgYt && youtubeId) {
          if (bgVideo) {
            bgVideo.pause();
            bgVideo.removeAttribute("src");
            bgVideo.load();
          }
          bgYt.src = "about:blank";
          bgPlayTimer = setTimeout(() => {
            const ytSrc = buildYoutubeEmbed(youtubeId, { autoplay: true, mute: true, controls: false, loop: true });
            bgYt.src = ytSrc;
            document.body.classList.add("bg-video-on");
            document.body.classList.remove("bg-video-off");
          }, 2000);
        } else if (bgVideo && trailerSrc) {
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
          }, 2000);
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
            }, 2000);
          };
        }
      } else {
        if (bgVideo) {
          bgVideo.pause();
          bgVideo.removeAttribute("src");
          bgVideo.load();
        }
        if (bgYt) bgYt.src = "about:blank";
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
        if (useYouTube && trailerYt && youtubeId) {
          trailerYt.src = buildYoutubeEmbed(youtubeId, { autoplay: true, mute: false, controls: true, loop: false });
        } else if (trailerVideo) {
          trailerVideo.muted = false;
          trailerVideo.volume = 0.9;
          const playPromise = trailerVideo.play();
          if (playPromise?.catch) playPromise.catch(() => {});
        }
      };
      const closeTrailer = () => {
        document.body.classList.remove("trailer-open");
        if (useYouTube && trailerYt) {
          trailerYt.src = "about:blank";
        }
        if (trailerVideo) {
          trailerVideo.pause();
          trailerVideo.currentTime = 0;
        }
        if (trailerConfig?.bgVideo) {
          document.body.classList.remove("bg-video-on");
          document.body.classList.add("bg-video-off");
          if (bgPlayTimer) {
            clearTimeout(bgPlayTimer);
          }
          bgPlayTimer = setTimeout(() => {
            if (useYouTube && bgYt && youtubeId) {
              bgYt.src = buildYoutubeEmbed(youtubeId, { autoplay: true, mute: true, controls: false, loop: true });
              document.body.classList.add("bg-video-on");
              document.body.classList.remove("bg-video-off");
              return;
            }
            if (bgVideo) {
              const playBg = bgVideo.play();
              if (playBg?.then) {
                playBg.then(() => {
                  document.body.classList.add("bg-video-on");
                  document.body.classList.remove("bg-video-off");
                }).catch(() => {});
              }
            }
          }, 2000);
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
      const episodeItems = Array.from({ length: episodesTotal }).map((_, index) => {
        const epNumber = index + 1;
        const epCode = `${episodePrefix}-${epNumber}`;
        const epTitle = episodeTitles[index] || `Episodio ${epNumber}`;
        const epSynopsis = episodeSnippets[index % episodeSnippets.length];
        const linkUrl = linksForTitle[epNumber];
        return { epNumber, epCode, epTitle, epSynopsis, linkUrl };
      });

      const seenKey = selectedId
        ? `anidex_seen_eps_${selectedId}`
        : `anidex_seen_eps_${norm(preferredTitle || query || "anime")}`;
      const continueKey = "anidex_continue_v1";
      const loadSeen = () => {
        try {
          const raw = localStorage.getItem(seenKey);
          return raw ? JSON.parse(raw) : {};
        } catch {
          return {};
        }
      };
      const saveSeen = (map) => {
        try { localStorage.setItem(seenKey, JSON.stringify(map)); } catch {}
      };
      let seenMap = loadSeen();
      const loadContinue = () => {
        try {
          const raw = localStorage.getItem(continueKey);
          const arr = raw ? JSON.parse(raw) : [];
          const map = {};
          (arr || []).forEach((it) => {
            if (!it) return;
            const key = `${it.malId || norm(it.title || "")}-${it.episode}`;
            if (key && it.episode) map[key] = it;
          });
          return map;
        } catch {
          return {};
        }
      };
      const saveContinue = (map) => {
        try { localStorage.setItem(continueKey, JSON.stringify(Object.values(map))); } catch {}
      };
      let continueMap = loadContinue();
      const isSeen = (ep) => Boolean(seenMap[String(ep)]);
      const setSeen = (ep, seen) => {
        if (seen) seenMap[String(ep)] = true;
        else delete seenMap[String(ep)];
        saveSeen(seenMap);
      };
      const upsertContinue = (card) => {
        const ep = card?.getAttribute("data-episode");
        if (!ep) return;
        const epTitle =
          card.getAttribute("data-episode-title") ||
          card.querySelector("h3")?.textContent?.trim() ||
          `Episodio ${ep}`;
        const detailUrl = selectedId
          ? `detail.html?mal_id=${encodeURIComponent(selectedId)}`
          : `detail.html?q=${encodeURIComponent(preferredTitle || query || "")}`;
        const key = `${selectedId || norm(preferredTitle || "")}-${ep}`;
        continueMap[key] = {
          sourceId: selectedId || null,
          malId: selectedId || null,
          title: preferredTitle,
          query: preferredTitle || query || "",
          episode: Number(ep),
          episodeTitle: epTitle,
          cover: cover || "",
          detailUrl,
          lastSeen: Date.now()
        };
        saveContinue(continueMap);
      };
      const removeContinue = (card) => {
        const ep = card?.getAttribute("data-episode");
        if (!ep) return;
        const key = `${selectedId || norm(preferredTitle || "")}-${ep}`;
        if (continueMap[key]) {
          delete continueMap[key];
          saveContinue(continueMap);
        }
      };
      const updateSeenUI = (card, seen) => {
        card.classList.toggle("border-emerald-400/60", seen);
        card.classList.toggle("bg-emerald-500/10", seen);
        card.classList.toggle("shadow-[0_0_18px_rgba(16,185,129,0.25)]", seen);
        const btn = card.querySelector("[data-episode-seen]");
        if (btn) {
          btn.setAttribute("aria-pressed", seen ? "true" : "false");
          btn.classList.toggle("text-emerald-300", seen);
          btn.classList.toggle("border-emerald-300/40", seen);
          btn.classList.toggle("bg-emerald-500/15", seen);
          btn.classList.toggle("text-white/60", !seen);
          btn.classList.toggle("border-white/10", !seen);
          btn.classList.toggle("bg-white/5", !seen);
        }
      };
      const markSeenByCard = (card) => {
        const ep = card?.getAttribute("data-episode");
        if (!ep) return;
        setSeen(ep, true);
        upsertContinue(card);
        updateSeenUI(card, true);
      };

      const renderEpisodeCard = (item) => {
        const episodeAttrs = item.linkUrl
          ? `data-episode-link="${item.linkUrl}" data-episode-embed="${toEmbed(item.linkUrl)}"`
          : `data-episode-image="${cover}" data-episode-title="${item.epTitle}"`;
        const seenBtnClass = canWatchEpisodes ? "" : "hidden";
        return `
          <div class="episode-card flex gap-6 items-center rounded-2xl border border-white/5 bg-surface-container-low/70 p-4 transition-all duration-300 hover:border-violet-400/70 hover:shadow-[0_0_18px_rgba(139,92,246,0.35)] hover:-translate-y-0.5 cursor-pointer" data-episode="${item.epNumber}" ${episodeAttrs}>
            <div class="flex items-center gap-4 flex-shrink-0">
              <div class="w-20 h-20 rounded-[6px] overflow-hidden bg-surface-container-high">
                <img src="${cover}" alt="Episodio ${item.epCode}" class="w-full h-full object-cover" />
              </div>
              <span class="min-w-[3.5rem] text-center px-4 py-2 rounded-full text-sm font-bold uppercase tracking-widest text-violet-100 bg-violet-500/25 border border-violet-400/50">${episodePrefix}-${item.epNumber}</span>
            </div>
            <div class="min-w-0 ml-2 flex flex-col justify-center">
              <h3 class="font-semibold text-white text-base">${item.epTitle}</h3>
              <p class="text-on-surface-variant text-sm mt-2">${item.epSynopsis}</p>
            </div>
            <div class="ml-auto flex items-center justify-center">
              <button type="button" class="episode-seen-btn ${seenBtnClass} w-12 h-12 rounded-full border border-white/10 bg-white/5 text-white/60 flex items-center justify-center transition-all duration-200 hover:text-emerald-200 hover:border-emerald-300/50 hover:bg-emerald-500/10" data-episode-seen aria-pressed="false" title="Marcar como visto">
                <span class="material-symbols-outlined text-[28px]">visibility</span>
              </button>
            </div>
          </div>
        `;
      };

      const episodesSection = document.createElement("div");
      episodesSection.id = "detail-episodes";
      episodesSection.className = "space-y-6";
      episodesSection.innerHTML = `
        <h2 class="font-headline text-3xl font-bold">Episodios</h2>
        <div class="space-y-4" data-episodes-list></div>
        <div class="pt-2 flex justify-center">
          <button type="button" data-episodes-more class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-full border border-violet-400/40 bg-violet-500/10 px-6 py-3 text-sm font-bold uppercase tracking-widest text-violet-100 shadow-[0_0_14px_rgba(139,92,246,0.25)] transition-all duration-300 hover:bg-violet-500/20 hover:border-violet-300 hover:shadow-[0_0_22px_rgba(139,92,246,0.45)] hover:-translate-y-0.5">
            Ver ms episodios
          </button>
        </div>
      `;

      const listEl = episodesSection.querySelector("[data-episodes-list]");
      const moreBtn = episodesSection.querySelector("[data-episodes-more]");
      let shown = 0;
      const pageSize = 10;
      let bindEpisodeCards = null;
      const lockEpisodes = () => {
        const lockText = isLogged
          ? "Activa el modo premium para ver los episodios"
          : "Inicia Sesion y accede al modo premium para ver los episodios";
        const goPremium = () => {
          window.location.href = isLogged ? "pago.html" : "registro.html";
        };
        const cards = Array.from(episodesSection.querySelectorAll(".episode-card"));
        cards.forEach((card) => {
          if (!isLogged && card.hasAttribute("data-episode-video")) return;
          if (card.dataset.locked) return;
          card.dataset.locked = "1";
          card.classList.add("relative", "overflow-hidden", "cursor-not-allowed", "transition-transform", "duration-300", "hover:-translate-y-1", "hover:shadow-[0_10px_26px_rgba(0,0,0,0.4)]");
          card.insertAdjacentHTML(
            "beforeend",
            `<div class="absolute inset-0 bg-black/60 backdrop-blur-[1px] flex flex-col items-center justify-center gap-2 text-center px-4">
              <span class="material-symbols-outlined text-[28px] text-white">lock</span>
              <button type="button" class="text-xs font-semibold uppercase tracking-widest text-on-surface hover:text-white transition-colors" data-episode-login>
                ${lockText}
              </button>
            </div>`
          );
          const loginBtn = card.querySelector("[data-episode-login]");
          const overlay = card.querySelector(".absolute.inset-0");
          const goRegister = (e) => {
            e.stopPropagation();
            goPremium();
          };
          if (loginBtn) loginBtn.addEventListener("click", goRegister);
          if (overlay) overlay.addEventListener("click", goRegister);
        });
      };
      const ensureEpisodeHoverFix = () => {
        if (document.getElementById("episode-hover-fix")) return;
        const style = document.createElement("style");
        style.id = "episode-hover-fix";
        style.textContent = `
          body.episode-scroll-fix .episode-card{
            transform:none !important;
            box-shadow:none !important;
            border-color: rgba(255,255,255,0.05) !important;
          }
        `;
        document.head.appendChild(style);
        let scrollTimer = null;
        window.addEventListener("scroll", () => {
          document.body.classList.add("episode-scroll-fix");
          if (scrollTimer) clearTimeout(scrollTimer);
          scrollTimer = setTimeout(() => {
            document.body.classList.remove("episode-scroll-fix");
          }, 140);
        }, { passive: true });
      };
      ensureEpisodeHoverFix();
      const appendBatch = () => {
        const nextItems = episodeItems.slice(shown, shown + pageSize);
        if (!nextItems.length) return;
        listEl.insertAdjacentHTML("beforeend", nextItems.map(renderEpisodeCard).join(""));
        shown += nextItems.length;
        if (shown >= episodeItems.length) {
          moreBtn?.classList.add("hidden");
        } else {
          moreBtn?.classList.remove("hidden");
        }
        if (!isLogged || !isPremium) {
          lockEpisodes();
          return;
        }
        const freshCards = Array.from(episodesSection.querySelectorAll(".episode-card"));
        freshCards.forEach((card) => updateSeenUI(card, isSeen(card.getAttribute("data-episode"))));
        freshCards.forEach((card) => {
          const btn = card.querySelector("[data-episode-seen]");
          if (!btn || btn.dataset.bound) return;
          btn.dataset.bound = "1";
          btn.addEventListener("click", (e) => {
            e.stopPropagation();
            const ep = card.getAttribute("data-episode");
            const next = !isSeen(ep);
            setSeen(ep, next);
            if (next) upsertContinue(card);
            else removeContinue(card);
            updateSeenUI(card, next);
          });
        });
        if (typeof bindEpisodeCards === "function") {
          bindEpisodeCards();
        }
      };
      appendBatch();
      if (moreBtn) {
        moreBtn.addEventListener("click", appendBatch);
      }
      const episodesHost = document.getElementById("detail-episodes-host");
      if (episodesHost) {
        episodesHost.appendChild(episodesSection);
      } else {
        synopsisBlockEl.insertAdjacentElement("afterend", episodesSection);
      }

      const episodeModalId = "detail-episode-modal";
      let episodeModal = document.getElementById(episodeModalId);
      if (!episodeModal) {
        episodeModal = document.createElement("div");
        episodeModal.id = episodeModalId;
        episodeModal.className = "fixed inset-0 z-[90] hidden";
        episodeModal.innerHTML = `
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
          <div class="relative mx-auto mt-[12vh] w-[min(92vw,720px)] rounded-2xl bg-surface-container-high/95 border border-violet-500/30 p-4 shadow-2xl overflow-hidden" data-episode-shell>
            <button type="button" data-episode-close class="absolute top-3 right-3 w-9 h-9 rounded-full bg-surface-container-low text-on-surface-variant hover:text-on-surface flex items-center justify-center">x</button>
            <video data-episode-video-player class="w-full rounded-xl bg-black" controls playsinline preload="metadata">
              <source data-episode-video-source type="video/mp4" />
            </video>
          </div>
        `;
        document.body.appendChild(episodeModal);
      }

      const videoCard = episodesSection.querySelector("[data-episode-video]");
      const ensureLinkModal = () => {
        let linkModal = document.getElementById("detail-episode-link-modal");
        if (!linkModal) {
          linkModal = document.createElement("div");
          linkModal.id = "detail-episode-link-modal";
          linkModal.className = "fixed inset-0 z-[90] hidden";
          linkModal.innerHTML = `
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative mx-auto mt-[8vh] w-[min(94vw,1100px)] h-[min(80vh,720px)] overflow-visible" data-link-shell>
              <button type="button" data-link-close class="absolute -top-5 -right-5 w-10 h-10 rounded-full bg-violet-500 text-white text-lg font-bold shadow-lg shadow-violet-500/40 hover:bg-violet-400 flex items-center justify-center">&times;</button>
              <div class="w-full h-full overflow-hidden">
                <iframe data-link-frame class="w-full h-full bg-black" allow="autoplay; fullscreen" allowfullscreen referrerpolicy="no-referrer"></iframe>
              </div>
            </div>
          `;
          document.body.appendChild(linkModal);
        }
        return linkModal;
      };

      const ensureImageModal = () => {
        let imageModal = document.getElementById("detail-episode-image-modal");
        if (!imageModal) {
          imageModal = document.createElement("div");
          imageModal.id = "detail-episode-image-modal";
          imageModal.className = "fixed inset-0 z-[90] hidden";
          imageModal.innerHTML = `
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative w-full h-full flex items-center justify-center p-6">
              <div class="relative inline-flex overflow-visible" data-image-shell>
                <button type="button" data-image-close class="absolute -top-8 -right-12 w-10 h-10 rounded-full bg-violet-500/60 text-white text-lg font-bold shadow-lg shadow-violet-500/40 backdrop-blur hover:bg-violet-400/70 flex items-center justify-center">&times;</button>
                <img data-image-frame class="max-w-[92vw] max-h-[82vh] rounded-2xl object-contain" alt="Portada episodio" />
              </div>
            </div>
          `;
          document.body.appendChild(imageModal);
        }
        return imageModal;
      };

      bindEpisodeCards = () => {
        const linkModal = ensureLinkModal();
        const imageModal = ensureImageModal();
        const linkFrame = linkModal.querySelector("[data-link-frame]");
        const linkCloseBtn = linkModal.querySelector("[data-link-close]");
        const linkBackdrop = linkModal.firstElementChild;
        const closeLink = () => {
          if (linkFrame) linkFrame.src = "";
          linkModal.classList.add("hidden");
        };
        const openLink = (card) => {
          const link = card.getAttribute("data-episode-embed") || card.getAttribute("data-episode-link");
          if (!link || !linkFrame) return;
          markSeenByCard(card);
          linkFrame.src = link;
          linkModal.classList.remove("hidden");
        };
        const imageFrame = imageModal.querySelector("[data-image-frame]");
        const imageCloseBtn = imageModal.querySelector("[data-image-close]");
        const imageBackdrop = imageModal.firstElementChild;
        const closeImage = () => {
          if (imageFrame) imageFrame.removeAttribute("src");
          imageModal.classList.add("hidden");
        };
        const openImage = (card) => {
          const src = card.getAttribute("data-episode-image");
          const title = card.getAttribute("data-episode-title") || "Portada episodio";
          if (!src || !imageFrame) return;
          markSeenByCard(card);
          imageFrame.src = src;
          imageFrame.alt = title;
          imageModal.classList.remove("hidden");
        };
        const linkCards = Array.from(episodesSection.querySelectorAll("[data-episode-link]"));
        const imageCards = Array.from(episodesSection.querySelectorAll("[data-episode-image]"));
        linkCards.forEach((card) => {
          if (card.dataset.episodeBound) return;
          card.dataset.episodeBound = "1";
          card.addEventListener("click", () => openLink(card));
        });
        imageCards.forEach((card) => {
          if (card.dataset.episodeBound) return;
          card.dataset.episodeBound = "1";
          card.addEventListener("click", () => openImage(card));
        });
        if (linkCloseBtn && !linkCloseBtn.dataset.bound) {
          linkCloseBtn.dataset.bound = "1";
          linkCloseBtn.addEventListener("click", closeLink);
        }
        if (linkBackdrop && !linkBackdrop.dataset.bound) {
          linkBackdrop.dataset.bound = "1";
          linkBackdrop.addEventListener("click", closeLink);
        }
        if (!linkModal.dataset.outsideBound) {
          linkModal.dataset.outsideBound = "1";
          linkModal.addEventListener("click", (e) => {
            if (!e.target.closest("[data-link-shell]")) closeLink();
          });
        }
        if (imageCloseBtn && !imageCloseBtn.dataset.bound) {
          imageCloseBtn.dataset.bound = "1";
          imageCloseBtn.addEventListener("click", closeImage);
        }
        if (imageBackdrop && !imageBackdrop.dataset.bound) {
          imageBackdrop.dataset.bound = "1";
          imageBackdrop.addEventListener("click", closeImage);
        }
        if (!imageModal.dataset.outsideBound) {
          imageModal.dataset.outsideBound = "1";
          imageModal.addEventListener("click", (e) => {
            if (!e.target.closest("[data-image-shell]")) closeImage();
          });
        }
      };

      if (canWatchEpisodes) {
        bindEpisodeCards();
      }
      if (videoCard && episodeModal && (canWatchEpisodes || !isLogged)) {
        const player = episodeModal.querySelector("[data-episode-video-player]");
        const source = episodeModal.querySelector("[data-episode-video-source]");
        const closeBtn = episodeModal.querySelector("[data-episode-close]");
        const backdrop = episodeModal.firstElementChild;
        const openEpisode = () => {
          if (!player || !source) return;
          markSeenByCard(videoCard);
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
        if (!episodeModal.dataset.outsideBound) {
          episodeModal.dataset.outsideBound = "1";
          episodeModal.addEventListener("click", (e) => {
            if (!e.target.closest("[data-episode-shell]")) closeEpisode();
          });
        }
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
        ? `<span class="flex flex-col"><span class="text-primary-dim text-xs uppercase tracking-wider">Ao</span><span>${yearValue || "N/A"}</span></span>`
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

    const synopsisBlock = Array.from(document.querySelectorAll("h2"))
      .find((x) => /sinopsis/i.test(x.textContent || ""))?.parentElement;
    const infoBox = Array.from(document.querySelectorAll("h3")).find((x) => /informaci/i.test(x.textContent || ""));
    const infoList = infoBox?.parentElement?.querySelector(".space-y-6");
    if (infoList) {
      infoList.innerHTML = "";
      renderMetaBlock(infoList, "Ttulo (EN)", full.title_english || "N/A");
      renderMetaBlock(infoList, "Ttulo (JP)", full.title_japanese || "N/A");
      renderMetaBlock(infoList, "Duracin", toSpanishDuration(full.duration) || "N/A");
      renderMetaBlock(infoList, "Clasificacin", toSpanishRating(full.rating) || "N/A");
      renderMetaBlock(infoList, "Ranking", full.rank ? `# ${full.rank}` : "N/A");
      renderMetaBlock(infoList, "Estudio", full.studios?.[0]?.name || "N/A");
    }

    const alignMediaToInfo = () => {
      if (window.innerWidth < 1024) {
        if (synopsisBlock) synopsisBlock.style.minHeight = "";
        return;
      }
      const infoCard = infoBox?.parentElement || null;
      const leftCol = synopsisBlock?.closest(".detail-left-col") || null;
      if (!infoCard || !synopsisBlock || !leftCol) {
        if (synopsisBlock) {
          synopsisBlock.style.minHeight = "";
          synopsisBlock.style.display = "";
          synopsisBlock.style.flexDirection = "";
          synopsisBlock.style.justifyContent = "";
        }
        if (leftCol) {
          leftCol.style.minHeight = "";
          leftCol.style.display = "";
          leftCol.style.flexDirection = "";
          leftCol.style.justifyContent = "";
        }
        return;
      }

      const synRect = synopsisBlock.getBoundingClientRect();
      const infoRect = infoCard.getBoundingClientRect();
      const gap = infoRect.height - synRect.height;
      const episodesSection = document.getElementById("detail-episodes");

      if (episodesSection) {
        leftCol.style.minHeight = "";
        leftCol.style.display = "";
        leftCol.style.flexDirection = "";
        leftCol.style.justifyContent = "";
        synopsisBlock.style.marginTop = "0px";

        const leftTop = leftCol.getBoundingClientRect().top;
        const synRectNow = synopsisBlock.getBoundingClientRect();
        const centerOffset = Math.round((infoRect.height - synRectNow.height) / 2);
        if (centerOffset > 0) {
          const desiredTop = leftTop + centerOffset;
          const offset = Math.round(desiredTop - synRectNow.top);
          synopsisBlock.style.marginTop = offset > 0 ? `${offset}px` : "0px";
        }

        episodesSection.style.marginTop = "0px";
        const episodesRect = episodesSection.getBoundingClientRect();
        const delta = infoRect.bottom - episodesRect.top;
        if (Math.abs(delta) > 1) {
          episodesSection.style.marginTop = `${Math.round(delta)}px`;
        } else {
          episodesSection.style.marginTop = "0px";
        }
        return;
      }

      if (gap > 0) {
        leftCol.style.minHeight = `${Math.round(infoRect.height)}px`;
        if (gap > 80) {
          leftCol.style.display = "flex";
          leftCol.style.flexDirection = "column";
          leftCol.style.justifyContent = "center";
        } else {
          leftCol.style.display = "";
          leftCol.style.flexDirection = "";
          leftCol.style.justifyContent = "";
        }
      } else {
        leftCol.style.minHeight = "";
        leftCol.style.display = "";
        leftCol.style.flexDirection = "";
        leftCol.style.justifyContent = "";
        synopsisBlock.style.marginTop = "";
      }
    };

    requestAnimationFrame(() => {
      alignMediaToInfo();
      setTimeout(alignMediaToInfo, 120);
      window.addEventListener("load", alignMediaToInfo);
      window.addEventListener("resize", alignMediaToInfo);
    });

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
        const fallbackMini = roleLabel === "principal"
          ? `${cleanName} es un personaje clave que impulsa la historia.`
          : `${cleanName} es un personaje secundario que apoya la historia.`;
        const desc = fallbackMini;
        const charId = c.character?.mal_id || "";
        const charImg = c.character?.images?.jpg?.image_url || "";
        return `
        <div class="carousel-card flex flex-col items-center gap-2 shrink-0 text-center cursor-pointer" data-char-id="${charId}" data-char-name="${cleanName}" data-char-role="${roleLabel}" data-char-img="${charImg}">
          <div class="w-24 h-24 overflow-hidden border border-primary/20 rounded-full">
            <img alt="${c.character?.name || "Personaje"}" class="w-full h-full object-cover" src="${c.character?.images?.jpg?.image_url || ""}"/>
          </div>
          <span class="text-sm font-bold break-words whitespace-normal">${cleanName}</span>
          <span class="text-[11px] font-semibold text-primary-dim">${roleLabel}</span>
          <span data-char-mini class="text-xs text-on-surface-variant leading-snug break-words whitespace-normal">${desc}</span>
        </div>`;
      }).join("");
      const prev = document.getElementById("chars-prev");
      const next = document.getElementById("chars-next");
      bindHorizontalArrows(charsRow, prev, next, 5);

      const characterCache = new Map();
      const cleanVoiceName = (name) => {
        const raw = (name || "").trim();
        if (!raw) return "";
        if (raw.includes(",")) {
          const parts = raw.split(",").map((p) => p.trim()).filter(Boolean);
          const last = parts[0] || "";
          const first = parts[1] || "";
          const firstToken = first.split(/\s+/)[0] || "";
          const lastToken = last.split(/\s+/)[0] || "";
          return [firstToken, lastToken].filter(Boolean).join(" ").trim();
        }
        const tokens = raw.split(/\s+/).filter(Boolean);
        if (tokens.length === 1) return tokens[0];
        return `${tokens[0]} ${tokens[tokens.length - 1]}`.trim();
      };
      const stripBioFields = (value) => {
        let out = (value || "").replace(/\s+/g, " ").trim();
        if (!out) return "";
        out = out.replace(/\((?:[^)]*?cm[^)]*?|[^)]*?kg[^)]*?)\)/gi, "");
        out = out.replace(
          /\b(?:Age|Birthday|Height|Weight|Blood type|Affiliations|Occupation|Rank|Graduation Rank|Species)\s*:\s*[^.\n]+[.\n]?/gi,
          ""
        );
        return out.replace(/\s+/g, " ").trim();
      };
      const isPhysical = (s) =>
        /(aos|edad|altura|peso|cm|kg|estatura|apariencia|fsic|cuerpo|complexin|rostro|cabell|ojos|pelo|color de|mide|naci|nacido)/i.test(
          s
        );
      const getMiniSummary = async (aboutText) => {
        const cleaned = stripBioFields(aboutText || "");
        if (!cleaned) return "";
        const aboutEs = await translateToEs(cleaned);
        const sentences = aboutEs
          .split(/(?<=[.!?])\s+/)
          .map((s) => s.trim())
          .filter(Boolean);
        let chosen = "";
        for (const s of sentences) {
          if (isPhysical(s)) continue;
          const w = s.split(" ").filter(Boolean);
          if (w.length >= 6 && w.length <= 24) {
            chosen = s;
            break;
          }
        }
        if (!chosen) return "";
        const words = chosen.split(" ").filter(Boolean);
        if (words.length > 24) {
          chosen = words.slice(0, 24).join(" ");
          if (!/[.!?]$/.test(chosen)) chosen = `${chosen}.`;
        }
        return chosen;
      };
      const ensureCharacterModal = () => {
        let modal = document.getElementById("detail-character-modal");
        if (modal) return modal;
        modal = document.createElement("div");
        modal.id = "detail-character-modal";
        modal.className = "fixed inset-0 z-[95] hidden";
        modal.innerHTML = `
          <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-char-backdrop></div>
          <div class="relative w-full h-full flex items-center justify-center p-4">
            <div class="relative w-[min(92vw,560px)] rounded-2xl bg-surface-container-high/95 border border-white/10 shadow-2xl overflow-hidden" data-char-shell>
              <button type="button" data-char-close class="absolute top-3 right-3 w-9 h-9 rounded-full bg-surface-container-low text-on-surface-variant hover:text-on-surface flex items-center justify-center"></button>
              <div class="grid grid-cols-1 sm:grid-cols-[240px_1fr] gap-6 p-6 items-stretch">
                <div class="w-full h-full min-h-[260px] sm:min-h-[320px] rounded-2xl overflow-hidden bg-black/30 shadow-[0_0_24px_rgba(0,0,0,0.35)] flex items-center justify-center">
                  <img data-char-img class="max-w-full max-h-full object-contain block" alt="Personaje" />
                </div>
                <div class="space-y-4">
                  <div>
                    <h3 data-char-name class="text-2xl font-extrabold font-headline">Personaje</h3>
                    <p data-char-role class="text-xs uppercase tracking-widest text-primary-dim font-semibold mt-1">Rol</p>
                  </div>
                  <div class="h-px w-16 bg-violet-400/40"></div>
                  <div>
                    <h4 class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold">Descripcin</h4>
                    <p data-char-info class="text-sm text-on-surface-variant leading-relaxed mt-2">Cargando informacin del personaje...</p>
                  </div>
                  <div class="h-px w-16 bg-violet-400/40"></div>
                  <div data-char-fields-wrap>
                    <h4 class="text-xs uppercase tracking-widest text-on-surface-variant font-semibold">Datos personales</h4>
                    <div data-char-fields class="mt-2 grid grid-cols-[auto_1fr] gap-x-3 gap-y-2 text-sm text-on-surface-variant"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
        document.body.appendChild(modal);
        return modal;
      };

      const closeCharacterModal = () => {
        const modal = document.getElementById("detail-character-modal");
        if (modal) modal.classList.add("hidden");
      };

      const openCharacterModal = async (card) => {
        const modal = ensureCharacterModal();
        const imgEl = modal.querySelector("[data-char-img]");
        const nameEl = modal.querySelector("[data-char-name]");
        const roleEl = modal.querySelector("[data-char-role]");
        const infoEl = modal.querySelector("[data-char-info]");
        const fieldsWrap = modal.querySelector("[data-char-fields-wrap]");
        const fieldsEl = modal.querySelector("[data-char-fields]");
        const charId = card.getAttribute("data-char-id");
        const charName = card.getAttribute("data-char-name") || "Personaje";
        const charRole = card.getAttribute("data-char-role") || "";
        const charImg = card.getAttribute("data-char-img") || "";
        if (nameEl) nameEl.textContent = charName;
        if (roleEl) roleEl.textContent = charRole ? `Rol: ${charRole}` : "Rol";
        if (imgEl && charImg) imgEl.src = charImg;
        const miniFromCard = card.querySelector("[data-char-mini]")?.textContent?.trim() || "";
        if (infoEl) infoEl.textContent = miniFromCard || "";
        if (fieldsWrap) fieldsWrap.classList.add("hidden");
        if (fieldsEl) fieldsEl.innerHTML = "";
        modal.classList.remove("hidden");

        if (!charId) {
          if (infoEl) infoEl.textContent = miniFromCard || "";
          return;
        }
        if (characterCache.has(charId)) {
          const cached = characterCache.get(charId);
          if (cached.full) {
            if (infoEl) {
              const cachedMini = cached.miniSummary || "";
              infoEl.textContent = cachedMini || miniFromCard || "";
            }
            if (imgEl && cached.img) imgEl.src = cached.img;
            if (fieldsEl) fieldsEl.innerHTML = cached.fieldsHtml || "";
            if (fieldsWrap) {
              if (cached.fieldsHtml) fieldsWrap.classList.remove("hidden");
              else fieldsWrap.classList.add("hidden");
            }
            return;
          }
          if (imgEl && cached.img) imgEl.src = cached.img;
          if (infoEl && cached.miniSummary) infoEl.textContent = cached.miniSummary;
        }
        try {
          const res = await fetch(`${API}/characters/${charId}/full`);
          if (!res.ok) throw new Error("fetch failed");
          const json = await res.json();
          const data = json?.data || {};
          const aboutRaw = (data?.about || "").trim();
          const aboutClean = aboutRaw ? aboutRaw.replace(/\s+/g, " ").trim() : "";
          const fieldMap = {
            "Birthday": "Cumpleaos",
            "Height": "Altura"
          };
          const fields = [];
          const labelSplitRe = /\b(?:Birthday|Height)\b\s*:/i;
          const sanitizeValue = (label, value) => {
            if (value === undefined || value === null) return "";
            let clean = String(value).replace(/\s+/g, " ").trim();
            if (!clean) return "";
            if (labelSplitRe.test(clean)) clean = clean.split(labelSplitRe)[0].trim();
            if (label === "Altura") {
              const m = clean.match(/(\d+(?:[.,]\d+)?)\s*cm/i);
              if (m) return `${m[1].replace(",", ".")} cm`;
            }
            return clean;
          };
          const addField = (label, value) => {
            if (!label) return;
            const clean = sanitizeValue(label, value);
            if (!clean) return;
            if (fields.some((f) => f.label === label)) return;
            fields.push({ label, value: clean });
          };
          addField("Cumpleaos", data?.birthday);
          addField("Altura", data?.height);
          if (typeof data?.favorites === "number") {
            addField("Popularidad", ` ${data.favorites} favoritos`);
          }
          const voices = Array.isArray(data?.voices) ? data.voices : [];
          const voiceNames = (lang) =>
            voices
              .filter((v) => (v?.language || "").toLowerCase().includes(lang))
              .map((v) => cleanVoiceName(v?.person?.name))
              .filter(Boolean)
              .join(" ");
          const voiceJp = voiceNames("japanese");
          const voiceEs = voiceNames("spanish") || voiceNames("espaol");
          addField("Doblaje japons", voiceJp);
          addField("Doblaje espaol", voiceEs);
          let aboutStripped = aboutClean;
          Object.keys(fieldMap).forEach((key) => {
            const re = new RegExp(`${key}\\s*:\\s*([^\\.\\n]+)`, "i");
            const match = aboutStripped.match(re);
            if (match) {
              addField(fieldMap[key], match[1].trim());
              aboutStripped = aboutStripped.replace(match[0], "").trim();
            }
          });
          const escapeHtml = (value) =>
            (value || "").replace(/[&<>"']/g, (ch) => ({
              "&": "&amp;",
              "<": "&lt;",
              ">": "&gt;",
              '"': "&quot;",
              "'": "&#39;"
            }[ch]));
          const noTranslateLabels = new Set(["Doblaje japons", "Doblaje espaol"]);
          const translateValue = async (label, value) => {
            if (!value) return value;
            if (noTranslateLabels.has(label)) return value;
            if (!/[a-zA-Z]/.test(value)) return value;
            try {
              return await translateToEs(value);
            } catch {
              return value;
            }
          };
          const miniSummary = await getMiniSummary(aboutClean);
          const aboutHtml = miniSummary ? `<p>${escapeHtml(miniSummary)}</p>` : "";
          const fieldsHtml = fields.length
            ? (await Promise.all(
                fields.map(async (f) => {
                  const val = await translateValue(f.label, f.value);
                  return `<span class="text-primary-dim font-semibold text-right">${escapeHtml(f.label)}:</span><span>${escapeHtml(val)}</span>`;
                })
              )).join("")
            : "";
          const img =
            data?.images?.webp?.image_url ||
            data?.images?.jpg?.image_url ||
            charImg ||
            "";
          characterCache.set(charId, { aboutHtml, fieldsHtml, img, miniSummary, full: true });
          if (infoEl) infoEl.innerHTML = aboutHtml || (miniFromCard ? `<p>${escapeHtml(miniFromCard)}</p>` : "");
          if (imgEl && img) imgEl.src = img;
          if (fieldsEl) fieldsEl.innerHTML = fieldsHtml;
          if (fieldsWrap) {
            if (fieldsHtml) fieldsWrap.classList.remove("hidden");
            else fieldsWrap.classList.add("hidden");
          }
          const miniEl = card.querySelector("[data-char-mini]");
          if (miniEl && miniSummary) miniEl.textContent = miniSummary;
        } catch {
          if (infoEl) infoEl.textContent = miniFromCard || "";
        }
      };

      const modal = ensureCharacterModal();
      const modalClose = modal.querySelector("[data-char-close]");
      const modalBackdrop = modal.querySelector("[data-char-backdrop]");
      if (modalClose && !modalClose.dataset.bound) {
        modalClose.dataset.bound = "1";
        modalClose.addEventListener("click", closeCharacterModal);
      }
      if (modalBackdrop && !modalBackdrop.dataset.bound) {
        modalBackdrop.dataset.bound = "1";
        modalBackdrop.addEventListener("click", closeCharacterModal);
      }
      if (!modal.dataset.outsideBound) {
        modal.dataset.outsideBound = "1";
        modal.addEventListener("click", (e) => {
          if (!e.target.closest("[data-char-shell]")) closeCharacterModal();
        });
      }
      Array.from(charsRow.querySelectorAll("[data-char-id]")).forEach((card) => {
        if (card.dataset.bound) return;
        card.dataset.bound = "1";
        card.addEventListener("click", () => openCharacterModal(card));
      });

      const populateMiniSummaries = async () => {
        const cards = Array.from(charsRow.querySelectorAll("[data-char-id]"));
        for (const card of cards) {
          const miniEl = card.querySelector("[data-char-mini]");
          const charId = card.getAttribute("data-char-id");
          if (!miniEl || !charId) continue;
          const cached = characterCache.get(charId);
          if (cached?.miniSummary) {
            miniEl.textContent = cached.miniSummary;
            continue;
          }
          try {
            const res = await fetch(`${API}/characters/${charId}/full`);
            if (!res.ok) throw new Error("fetch failed");
            const json = await res.json();
            const data = json?.data || {};
            const aboutRaw = (data?.about || "").trim();
            const miniSummary = await getMiniSummary(aboutRaw);
            if (miniSummary) miniEl.textContent = miniSummary;
            const cachedImg =
              data?.images?.webp?.image_url ||
              data?.images?.jpg?.image_url ||
              card.getAttribute("data-char-img") ||
              "";
            characterCache.set(charId, { miniSummary, img: cachedImg, full: false });
          } catch {
            // ignore
          }
          await new Promise((r) => setTimeout(r, 180));
        }
      };
      populateMiniSummaries();
    }

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

    // Galera relacionada eliminada por solicitud.

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
      btn.dataset.itemType = (full.type || "").toLowerCase() === "movie" ? "Película" : "Anime";
      btn.dataset.itemId = String(selectedId || "");
    });
    document.body.dataset.detailTitle = preferredTitle;
    document.body.dataset.detailImage = src || "";
    document.body.dataset.detailType = (full.type || "").toLowerCase() === "movie" ? "Película" : "Anime";
    if (window.AniDexFavorites) window.AniDexFavorites.refresh();

    // Recomendados: si es pelcula, mostrar pelculas; si no, series.
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

    // Zoom simple para imgenes del slider
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
};

AniDexDetailDataBoot();

