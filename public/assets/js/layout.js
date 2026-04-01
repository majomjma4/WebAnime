(() => {
  if ("scrollRestoration" in history) {
    history.scrollRestoration = "manual";
  }

  const forceScrollTop = () => {
    try {
      window.scrollTo({ top: 0, left: 0, behavior: "auto" });
    } catch {
      window.scrollTo(0, 0);
    }
  };

  const menuItems = [
    { name: "Inicio", href: "index", icon: "home" },
    { name: "Animes", href: "series", icon: "live_tv" },
    { name: "Películas", href: "peliculas", icon: "movie" },
    { name: "Destacados", href: "destacados", icon: "star" },
    { name: "Ranking", href: "ranking", icon: "leaderboard" }
  ];

  const readyCallbacks = [];
  let isReady = false;

  function onReady(fn) {
    if (isReady) {
      fn();
      return;
    }
    readyCallbacks.push(fn);
  }

  function injectLayoutStyles(container) {
    const styles = container.querySelectorAll("style");
    styles.forEach((styleEl, index) => {
      const id = styleEl.getAttribute("data-layout-style") || `layout-style-${index}`;
      if (document.querySelector(`style[data-layout-style="${id}"]`)) return;
      const clone = styleEl.cloneNode(true);
      clone.setAttribute("data-layout-style", id);
      document.head.appendChild(clone);
    });
  }

  async function loadLayout() {
    const headerTargets = document.querySelectorAll('[data-layout="header"]');
    const footerTargets = document.querySelectorAll('[data-layout="footer"]');
    if (!headerTargets.length && !footerTargets.length) return;

    const basePath = window.location.pathname.includes("/views/") ? "../" : "";
    const res = await fetch(`${basePath}partials/layout?v=final2`, { cache: "no-store", credentials: "same-origin" });
    if (!res.ok) {
      throw new Error("No se pudo cargar partials/layout");
    }
    const html = await res.text();

    const container = document.createElement("div");
    container.innerHTML = html;

    injectLayoutStyles(container);

    // Inyectar todos los templates en el body para que los scripts puedan encontrarlos
    container.querySelectorAll("template").forEach((tpl) => {
      if (tpl.id && !document.getElementById(tpl.id)) {
        document.body.appendChild(tpl.cloneNode(true));
      }
    });

    const headerTpl = container.querySelector("#layout-header");
    const footerTpl = container.querySelector("#layout-footer");

    const headerHtml = headerTpl ? headerTpl.innerHTML.trim() : "";
    const footerHtml = footerTpl ? footerTpl.innerHTML.trim() : "";

    if (headerHtml) {
      headerTargets.forEach((el) => {
        el.innerHTML = headerHtml;
      });
    }
    if (footerHtml) {
      footerTargets.forEach((el) => {
        el.innerHTML = footerHtml;
      });
    }
  }

  function setActiveMenu() {
    const currentPage = window.location.pathname.split("/").pop();
    const menuEl = document.getElementById("main-menu");
    if (!menuEl) return;

    menuEl.innerHTML = menuItems
      .map((item) => {
        const isActive = item.href === currentPage;
        const base = "transition-colors";
        const active = "text-violet-400 font-bold border-b-2 border-violet-500 pb-1";
        const inactive = "text-neutral-400 hover:text-neutral-100";
        const iconClass = isActive ? "nav-icon nav-icon--active" : "nav-icon";
        const icon = item.icon
          ? `<span class="material-symbols-outlined text-[18px] leading-none ${iconClass}">${item.icon}</span>`
          : "";
        const label = item.icon
          ? `<span class="inline-flex items-center gap-2">${icon}<span class="nav-label">${item.name}</span></span>`
          : item.name;
        return `<a href="${item.href}" class="${isActive ? active : inactive + " " + base}">${label}</a>`;
      })
      .join("");
  }

  function getOrCreateUserId() {
    try {
      let id = localStorage.getItem("anidex_user_id");
      if (!id || !id.startsWith("NK-")) {
        const rand = Math.floor(100000 + Math.random() * 900000);
        id = `NK-${rand}`;
        localStorage.setItem("anidex_user_id", id);
      }
      return id;
    } catch {
      return `NK-${Math.floor(100000 + Math.random() * 900000)}`;
    }
  }

  function getOrCreateUserSuffix() {
    try {
      let suffix = localStorage.getItem("anidex_user_suffix");
      if (!suffix) {
        suffix = String(Math.floor(10 + Math.random() * 90));
        localStorage.setItem("anidex_user_suffix", suffix);
      }
      return suffix;
    } catch {
      return String(Math.floor(10 + Math.random() * 90));
    }
  }

  function getIsolatedKey(baseKey) {
    const id = (authState && authState.logged && authState.userId) ? authState.userId : getOrCreateUserId();
    return `${baseKey}_${id}`;
  }

  function formatHours(hours) {
    const total = parseFloat(hours) || 0;
    const totalMinutes = Math.max(0, Math.round(total * 60));
    const h = Math.floor(totalMinutes / 60);
    const m = totalMinutes % 60;

    if (h <= 0) return `${m} min`;
    if (m === 0) return `${h} h`;
    return `${h} h ${String(m).padStart(2, "0")} min`;
  }

  function getGuestName() {
    const saved = localStorage.getItem("anidex_profile_name");
    if (saved && saved.trim()) return saved.trim();
    return `NekoraUser_${getOrCreateUserSuffix()}`;
  }

  // FIX: Ahora acepta preReadGuestId para evitar leer el ID YA sobreescrito.
  function migrateGuestData(realUserId, preReadGuestId) {
    try {
      // IMPORTANTE: usar el ID leÃ­do ANTES de sobrescribir anidex_user_id
      const guestId = preReadGuestId || localStorage.getItem("anidex_user_id");
      if (!guestId || guestId === realUserId) return;

      const keysToMigrate = [
        "anidex_profile_hours",
        "anidex_profile_prefs",
        "anidex_continue_v1",
        "anidex_profile_desc",
        "anidex_profile_color",
        "anidex_profile_avatar",
        "anidex_my_list_v1",
        "anidex_favorites_v1",
        "anidex_status_v1"
      ];

      keysToMigrate.forEach(baseKey => {
        const guestKey = `${baseKey}_${guestId}`;
        const realKey = `${baseKey}_${realUserId}`;
        const guestVal = localStorage.getItem(guestKey);
        if (!guestVal) return;

        const realVal = localStorage.getItem(realKey);

        if (baseKey === "anidex_profile_hours") {
          // Para horas: sumar (el usuario ya vio ese tiempo como invitado)
          const total = (parseFloat(guestVal || "0") + parseFloat(realVal || "0")).toFixed(2);
          localStorage.setItem(realKey, total);
          localStorage.removeItem(guestKey);
          return;
        }

        // Para el resto: solo migrar si el valor real estÃ¡ vacÃ­o
        if (!realVal || realVal === "0" || realVal === "[]" || realVal === "{}") {
          localStorage.setItem(realKey, guestVal);
          localStorage.removeItem(guestKey);
        }
      });
    } catch (e) { console.error("Migration error:", e); }
  }

  let authState = { logged: false, username: "", userId: null, role: "Invitado", isAdmin: false, isPremium: false };

  async function checkAuth() {
    try {
      const res = await fetch("api/auth.php?action=check", { cache: "no-store", credentials: "same-origin" });
      if (!res.ok) throw new Error("HTTP error " + res.status);
      authState = await res.json();
      // Sync legacy localStorage
      if (authState.logged) {
        localStorage.setItem("nekora_logged_in", "true");
        localStorage.setItem("nekora_user", authState.username);
        if (authState.isAdmin) localStorage.setItem("nekora_admin", "true");
        if (authState.isPremium) localStorage.setItem("nekora_premium", "true");
        else localStorage.removeItem("nekora_premium");

        if (authState.userId) {
          const oldGuestId = localStorage.getItem("anidex_user_id");
          localStorage.setItem("anidex_user_id", authState.userId);
          if (authState.publicUserId) localStorage.setItem("anidex_public_user_id", authState.publicUserId);
          migrateGuestData(authState.userId, oldGuestId);
        }
      } else {
        localStorage.removeItem("nekora_logged_in");
        localStorage.removeItem("nekora_admin");
        localStorage.removeItem("nekora_premium");
        localStorage.removeItem("anidex_profile_name");
      }
    } catch (e) {
      console.error("Auth check failed:", e);
      authState = { logged: false, username: "", userId: null, role: "Invitado", isAdmin: false, isPremium: false };
      localStorage.removeItem("nekora_logged_in");
      localStorage.removeItem("nekora_admin");
      localStorage.removeItem("nekora_premium");
      localStorage.removeItem("anidex_profile_name");
    }
    return authState;
  }

  function isLoggedIn() {
    return authState.logged;
  }

  function isAdmin() {
    return authState.isAdmin;
  }

  function isPremium() {
    return authState.isPremium;
  }

  function getRole() {
    return authState.role || "Invitado";
  }

  function syncProfileAvatar() {
    try {
      const savedAvatar = localStorage.getItem(getIsolatedKey("anidex_profile_avatar")) || localStorage.getItem("anidex_profile_avatar");
      if (!savedAvatar) return;
      document.querySelectorAll("img[data-profile-avatar]").forEach((img) => {
        img.src = savedAvatar;
      });
    } catch {}
  }

  async function setupProfileMenu() {
    const profileBtn = document.querySelector("[data-profile-trigger]");
    const guestMenu = document.querySelector("[data-guest-menu]");
    const nameEls = document.querySelectorAll("[data-profile-name]");
    const logged = isLoggedIn();
    const role = getRole();

    nameEls.forEach((el) => {
        el.textContent = logged ? authState.username : getGuestName();
    });

    if (logged) {
      // Si estÃ¡ logueado, eliminar el menÃº de invitado y redirigir directamente
      if (guestMenu) {
        guestMenu.remove();
      }
    }

    if (profileBtn && !profileBtn.dataset.bound) {
      profileBtn.dataset.bound = "1";
      profileBtn.addEventListener("click", (e) => {
        if (isLoggedIn()) {
          window.location.href = "user";
          return;
        }
        e.preventDefault();
        const menu = document.querySelector("[data-guest-menu]");
        if (!menu) return;
        const isOpen = !menu.classList.contains("hidden");
        menu.classList.toggle("hidden", isOpen);
        profileBtn.setAttribute("aria-expanded", isOpen ? "false" : "true");
      });
      document.addEventListener("click", (e) => {
        if (isLoggedIn()) return;
        const menu = document.querySelector("[data-guest-menu]");
        if (!menu || menu.classList.contains("hidden")) return;
        if (profileBtn.contains(e.target) || menu.contains(e.target)) return;
        menu.classList.add("hidden");
        profileBtn.setAttribute("aria-expanded", "false");
      });
    }
  }

  function initI18nWhenReady() {
    if (window.AniDexI18n) {
      window.AniDexI18n.init();
      return;
    }

    let tries = 0;
    const timer = setInterval(() => {
      tries += 1;
      if (window.AniDexI18n) {
        window.AniDexI18n.init();
        clearInterval(timer);
      } else if (tries >= 20) {
        clearInterval(timer);
      }
    }, 50);
  }

  function initSearchWhenReady() {
    if (window.AniDexSearch) {
      window.AniDexSearch.init();
      return;
    }

    let tries = 0;
    const timer = setInterval(() => {
      tries += 1;
      if (window.AniDexSearch) {
        window.AniDexSearch.init();
        clearInterval(timer);
      } else if (tries >= 20) {
        clearInterval(timer);
      }
    }, 50);
  }

  function initSessionTimer() {
    if (!isLoggedIn()) return;

    let activeSince = document.visibilityState === "visible" ? Date.now() : null;

    const syncDelta = (deltaMs) => {
      if (!deltaMs || deltaMs < 1000 || !isLoggedIn()) return;

      const deltaHours = deltaMs / (1000 * 60 * 60);

      try {
        const key = getIsolatedKey("anidex_profile_hours");
        const currentHours = parseFloat(localStorage.getItem(key) || "0");
        const newTotal = (currentHours + deltaHours).toFixed(4);
        localStorage.setItem(key, newTotal);

        const hoursEl = document.querySelector("[data-profile-hours]");
        if (hoursEl) hoursEl.textContent = formatHours(newTotal);

        fetch("api/activity.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "time_sync", delta: deltaHours.toFixed(4) })
        }).catch(() => {});
      } catch (e) {}
    };

    const pauseTracking = () => {
      if (!activeSince) return;
      const now = Date.now();
      syncDelta(now - activeSince);
      activeSince = null;
    };

    const resumeTracking = () => {
      if (!isLoggedIn() || document.visibilityState !== "visible") return;
      activeSince = Date.now();
    };

    document.addEventListener("visibilitychange", () => {
      if (document.visibilityState === "visible") resumeTracking();
      else pauseTracking();
    });

    window.addEventListener("focus", resumeTracking);
    window.addEventListener("blur", pauseTracking);
    window.addEventListener("pagehide", pauseTracking);
    window.addEventListener("beforeunload", pauseTracking);

    setInterval(() => {
      if (!isLoggedIn() || !activeSince || document.visibilityState !== "visible") return;
      const now = Date.now();
      const deltaMs = now - activeSince;
      if (deltaMs < 30000) return;
      syncDelta(deltaMs);
      activeSince = Date.now();
    }, 5000);
  }

  function finalizeLayout() {
    setActiveMenu();
    const adminBtn = document.getElementById("admin-mode-btn");
    if (!isLoggedIn() || localStorage.getItem("nekora_user") !== "Admin99") {
      try { localStorage.removeItem("nekora_admin"); } catch {}
    }
    if (isLoggedIn() && isAdmin()) {
      try { localStorage.setItem("nekora_premium", "true"); } catch {}
    }
    if (adminBtn) {
      const showAdmin = isLoggedIn() && isAdmin();
      adminBtn.classList.toggle("hidden", !showAdmin);
      adminBtn.style.display = showAdmin ? "inline-flex" : "none";
    }
    syncProfileAvatar();
    setupProfileMenu();
    syncProfileAvatar();
    initSessionTimer();

    // LÃ³gica de sincronizaciÃ³n global
    if (isLoggedIn()) {
      getOrCreateUserId();
      restoreFromDB();
    }

    isReady = true;
    while (readyCallbacks.length) {
      const fn = readyCallbacks.shift();
      try {
        fn();
      } catch (err) {
        console.error("Error en callback de layout:", err);
      }
    }
    initI18nWhenReady();
    initSearchWhenReady();
  }

  async function initLayout() {
    try {
      await checkAuth();
      await loadLayout();
    } catch (err) {
      console.warn("No se pudo cargar header/footer:", err);
    } finally {
      finalizeLayout();
      requestAnimationFrame(forceScrollTop);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initLayout);
  } else {
    initLayout();
  }

  window.addEventListener("load", () => {
    requestAnimationFrame(forceScrollTop);
  });

  window.__anidex_restoring = false;

  async function saveProfileToDB() {
    if (!authState.logged || window.__anidex_restoring) return;
    const data = {
      profile_name: localStorage.getItem("anidex_profile_name") || "",
      profile_desc: localStorage.getItem(getIsolatedKey("anidex_profile_desc")) || "",
      profile_color: localStorage.getItem(getIsolatedKey("anidex_profile_color")) || "",
      profile_avatar: localStorage.getItem(getIsolatedKey("anidex_profile_avatar")) || "",
      profile_member_since: localStorage.getItem(getIsolatedKey("anidex_profile_member_since")) || "",
      profile_hours: localStorage.getItem(getIsolatedKey("anidex_profile_hours")) || "0",
      anidex_profile_prefs: JSON.parse(localStorage.getItem(getIsolatedKey("anidex_profile_prefs")) || "[]"),
      anidex_continue_v1: JSON.parse(localStorage.getItem(getIsolatedKey("anidex_continue_v1")) || "[]"),
      my_list: JSON.parse(localStorage.getItem(getIsolatedKey("anidex_my_list_v1")) || "[]"),
      favorites: JSON.parse(localStorage.getItem(getIsolatedKey("anidex_favorites_v1")) || "[]"),
      status_list: JSON.parse(localStorage.getItem(getIsolatedKey("anidex_status_v1")) || "[]")
    };
    try {
      await fetch("api/profile.php?action=save", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
      });
    } catch (e) { console.error("Error saving profile to DB:", e); }
  }

  async function restoreFromDB() {
    if (!authState.logged) return;
    window.__anidex_restoring = true;
    try {
      const res = await fetch("api/profile.php?action=get");
      const json = await res.json();
      if (json.success && json.data) {
        const d = json.data;

        // FIX: Conjunto explÃ­cito de claves que tienen lÃ³gica propia de fusiÃ³n.
        // El bucle genÃ©rico NUNCA debe procesar estas claves porque cada una
        // tiene reglas distintas (MAX, suma sin sufijo, etc.).
        //
        //  anidex_profile_hours      â†’ FusiÃ³n MAX debajo (el mayor gana)
        //  anidex_user_id            â†’ Es el ID real del servidor; no va con sufijo aislado
        //  anidex_profile_name/desc/
        //  color/avatar/member_since â†’ Manejadas por bloque especial debajo
        const LOOP_EXCLUSION = new Set([
          "anidex_profile_hours",
          "anidex_user_id",
          "anidex_profile_name",
          "anidex_profile_desc",
          "anidex_profile_color",
          "anidex_profile_avatar",
          "anidex_profile_member_since"
        ]);

        // Bucle genÃ©rico: solo listas/objetos con prefijo anidex_ no excluidos
        Object.keys(d).forEach(key => {
          if (!key.startsWith("anidex_") || LOOP_EXCLUSION.has(key)) return;

          const serverVal = d[key];
          const localKey = getIsolatedKey(key);
          const localValRaw = localStorage.getItem(localKey);
          let finalVal = serverVal;

          // Mezcla Inteligente para Listas
          if (Array.isArray(serverVal)) {
            let localArr = [];
            try { localArr = localValRaw ? JSON.parse(localValRaw) : []; } catch(e) {}
            if (!Array.isArray(localArr)) localArr = [];
            const seen = new Set();
            const merged = [];
            // Locales primero (mÃ¡s recientes que el servidor)
            [...localArr, ...serverVal].forEach(item => {
              const id = item.malId || item.id || (item.title ? item.title.toLowerCase() : null);
              if (id && !seen.has(id)) { seen.add(id); merged.push(item); }
            });
            finalVal = merged;
          } else if (typeof serverVal === "object" && serverVal !== null) {
            // Mezcla de objetos (status_v1, etc.): local prevalece
            let localObj = {};
            try { localObj = localValRaw ? JSON.parse(localValRaw) : {}; } catch(e) {}
            finalVal = Object.assign({}, serverVal, localObj);
          }

          const valStr = (typeof finalVal === "object") ? JSON.stringify(finalVal) : String(finalVal);
          if (valStr && valStr !== "[]" && valStr !== "{}" && valStr !== "null") {
            localStorage.setItem(localKey, valStr);
          }
        });

        // â”€â”€â”€ Bloque especial: campos de perfil â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        // El API los envÃ­a con prefijo anidex_ (anidex_profile_name, etc.).
        // profile_name se guarda SIN sufijo; el resto CON sufijo aislado.
        if (d.anidex_profile_name)         localStorage.setItem("anidex_profile_name",                              d.anidex_profile_name);
        if (d.anidex_profile_desc)         localStorage.setItem(getIsolatedKey("anidex_profile_desc"),         d.anidex_profile_desc);
        if (d.anidex_profile_color)        localStorage.setItem(getIsolatedKey("anidex_profile_color"),        d.anidex_profile_color);
        if (d.anidex_profile_avatar)       localStorage.setItem(getIsolatedKey("anidex_profile_avatar"),       d.anidex_profile_avatar);
        if (d.anidex_profile_avatar)       localStorage.setItem("anidex_profile_avatar", d.anidex_profile_avatar);
        if (d.anidex_profile_member_since) localStorage.setItem(getIsolatedKey("anidex_profile_member_since"), d.anidex_profile_member_since);
        if (d.anidex_public_user_id)       localStorage.setItem("anidex_public_user_id", d.anidex_public_user_id);

        // â”€â”€â”€ FUSIÃ“N MAX de horas vistas â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        // Regla de oro: el contador NUNCA puede retroceder al refrescar la pÃ¡gina.
        // Se usa Math.max en vez de "if server > local" para garantizar atomicidad.
        // Clave canÃ³nica del API: 'anidex_profile_hours'
        const serverHoursRaw = d.anidex_profile_hours;
        if (serverHoursRaw !== undefined && serverHoursRaw !== null) {
          const localKey = getIsolatedKey("anidex_profile_hours");
          const local  = parseFloat(localStorage.getItem(localKey) || "0");
          const server = parseFloat(serverHoursRaw || "0");
          const winner = Math.max(local, server);  // El mayor gana â€” sin excepciones
          localStorage.setItem(localKey, winner.toFixed(2));
        }

        syncProfileAvatar();
        if (window.AniDexLayout && typeof window.AniDexLayout.triggerRefresh === "function") {
          window.AniDexLayout.triggerRefresh();
        }
        syncProfileAvatar();
      } else if (json.success) {
        // Sin datos previos en el servidor â†’ guardar lo local como punto de partida
        saveProfileToDB();
      }
    } catch (e) {
      console.error("Error restoring profile from DB:", e);
    } finally {
      window.__anidex_restoring = false;
    }
  }

  const refreshCallbacks = [];
  function registerRefresh(fn) {
    if (typeof fn === "function") refreshCallbacks.push(fn);
  }
  function triggerRefresh() {
    refreshCallbacks.forEach(fn => {
      try { fn(); } catch (e) { console.error("Global Refresh Error:", e); }
    });
  }

  window.AniDexLayout = {
    onReady,
    checkAuth,
    isLoggedIn,
    isAdmin,
    isPremium,
    getRole,
    registerRefresh,
    triggerRefresh,
    authState: () => authState
  };

  window.AniDexProfile = {
    saveToDB: saveProfileToDB,
    restoreFromDB: restoreFromDB,
    getOrCreateUserId: getOrCreateUserId,
    getOrCreateUserSuffix: getOrCreateUserSuffix,
    getIsolatedKey: getIsolatedKey,
    formatHours: formatHours
  };
})();








