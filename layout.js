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
    { name: "Inicio", href: "index.html", icon: "home" },
    { name: "Animes", href: "series.html", icon: "live_tv" },
    { name: "Películas", href: "peliculas.html", icon: "movie" },
    { name: "Destacados", href: "destacados.html", icon: "star" },
    { name: "Ranking", href: "ranking.html", icon: "leaderboard" }
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
      if (document.querySelector(`style[data-layout-style=\"${id}\"]`)) return;
      const clone = styleEl.cloneNode(true);
      clone.setAttribute("data-layout-style", id);
      document.head.appendChild(clone);
    });
  }

  async function loadLayout() {
    const headerTargets = document.querySelectorAll('[data-layout="header"]');
    const footerTargets = document.querySelectorAll('[data-layout="footer"]');
    if (!headerTargets.length && !footerTargets.length) return;

    const res = await fetch("partials/layout.html", { cache: "no-store" });
    if (!res.ok) {
      throw new Error("No se pudo cargar partials/layout.html");
    }
    const html = await res.text();

    const container = document.createElement("div");
    container.innerHTML = html;

    injectLayoutStyles(container);

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

  function getGuestName() {
    const saved = localStorage.getItem("anidex_profile_name");
    if (saved && saved.trim()) return saved.trim();
    return `NekoraUser_${getOrCreateUserSuffix()}`;
  }

  function isLoggedIn() {
    return localStorage.getItem("nekora_logged_in") === "true";
  }

  function isAdmin() {
    return (
      localStorage.getItem("nekora_admin") === "true" &&
      localStorage.getItem("nekora_user") === "Admin99"
    );
  }

  function setupGuestMenu() {
    const favLink = document.querySelector("[data-favorites-link]");
    const profileBtn = document.querySelector("[data-profile-trigger]");
    const guestMenu = document.querySelector("[data-guest-menu]");
    const nameEls = document.querySelectorAll("[data-profile-name]");
    const logged = isLoggedIn();

    if (favLink) favLink.classList.toggle("hidden", !logged);
    if (guestMenu) guestMenu.classList.add("hidden");
    nameEls.forEach((el) => { el.textContent = getGuestName(); });

    if (profileBtn && !profileBtn.dataset.bound) {
      profileBtn.dataset.bound = "1";
      profileBtn.addEventListener("click", (e) => {
        if (isLoggedIn()) {
          window.location.href = "user.html";
          return;
        }
        e.preventDefault();
        if (!guestMenu) return;
        const isOpen = !guestMenu.classList.contains("hidden");
        guestMenu.classList.toggle("hidden", isOpen);
        profileBtn.setAttribute("aria-expanded", isOpen ? "false" : "true");
      });
      document.addEventListener("click", (e) => {
        if (isLoggedIn()) return;
        if (!guestMenu || guestMenu.classList.contains("hidden")) return;
        if (profileBtn.contains(e.target) || guestMenu.contains(e.target)) return;
        guestMenu.classList.add("hidden");
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
    try {
      const savedAvatar = localStorage.getItem("anidex_profile_avatar");
      if (savedAvatar) {
        document.querySelectorAll("img[data-profile-avatar]").forEach((img) => {
          img.src = savedAvatar;
        });
      }
    } catch {}
    setupGuestMenu();
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

  window.AniDexLayout = { onReady };
})();

