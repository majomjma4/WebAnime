(() => {
  const getCookie = (name) => {
    const escaped = name.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const match = document.cookie.match(new RegExp(`(?:^|; )${escaped}=([^;]*)`));
    return match ? decodeURIComponent(match[1]) : "";
  };

  const isSameOriginUrl = (value) => {
    try {
      const url = new URL(value, window.location.href);
      return url.origin === window.location.origin;
    } catch {
      return false;
    }
  };

  const getBasePath = () => {
    if (window.CI_BASE_URL) return window.CI_BASE_URL;
    const path = window.location.pathname.replace(/\\/g, "/");

    const scriptCandidates = Array.from(document.scripts || []);
    const sharedUtilsScript = scriptCandidates.find((script) =>
      typeof script.src === "string" && /\/assets\/js\/shared-utils\.js(?:\?|$)/i.test(script.src)
    );

    if (sharedUtilsScript?.src) {
      try {
        const scriptUrl = new URL(sharedUtilsScript.src, window.location.href);
        const assetPath = scriptUrl.pathname.replace(/\\/g, "/");
        const assetIndex = assetPath.toLowerCase().lastIndexOf("/assets/js/shared-utils.js");
        if (assetIndex >= 0) {
          const derivedBase = assetPath.slice(0, assetIndex + 1);
          if (derivedBase) {
            return derivedBase;
          }
        }
      } catch {}
    }

    const publicIndex = path.toLowerCase().indexOf("/public/");
    if (publicIndex >= 0) {
      return path.slice(0, publicIndex + "/public/".length);
    }
    
    if (path.toLowerCase().includes("/webanime-master/")) {
      return "/WebAnime-master/";
    }
    if (path.toLowerCase().includes("/replica/")) {
      return "/replica/";
    }

    if (path.toLowerCase().endsWith("/public")) {
      return `${path}/`;
    }
    return "/";
  };

  const buildAppUrl = (path = "") => {
    const basePath = getBasePath();
    const cleanPath = String(path || "").replace(/^\/+/, "");
    return cleanPath ? `${basePath}${cleanPath}` : basePath.replace(/\/$/, "");
  };

  const withSecurityDefaults = (url, options = {}) => {
    const normalized = { ...options };
    const method = String(normalized.method || "GET").toUpperCase();
    const headers = new Headers(normalized.headers || {});

    if (!normalized.credentials && isSameOriginUrl(url)) {
      normalized.credentials = "same-origin";
    }

    if (!headers.has("X-Requested-With") && isSameOriginUrl(url)) {
      headers.set("X-Requested-With", "XMLHttpRequest");
    }

    if (!["GET", "HEAD", "OPTIONS"].includes(method) && isSameOriginUrl(url)) {
      const csrf = getCookie("XSRF-TOKEN");
      if (csrf && !headers.has("X-CSRF-Token")) {
        headers.set("X-CSRF-Token", csrf);
      }
    }

    normalized.headers = headers;
    return normalized;
  };

  const nativeFetch = window.fetch.bind(window);

  // --- SMART FETCH JIKAN: Puente Inteligente ---
  const smartFetchJikan = async (endpoint) => {
    const appUrl = buildAppUrl("api/jikan_proxy");
    const cleanEndpoint = String(endpoint || "").replace(/^\/+/, "");
    
    // 1. Intentar el Proxy del Servidor (timeout agresivo 2.5s)
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 2500);
    
    try {
      const serverRes = await nativeFetch(`${appUrl}?endpoint=${encodeURIComponent(cleanEndpoint)}`, { 
        signal: controller.signal,
        headers: { "X-Requested-With": "XMLHttpRequest" }
      });
      clearTimeout(timeoutId);
      if (serverRes && serverRes.ok) {
        const data = await serverRes.json();
        if (data && !data.error) return data;
      }
    } catch (e) {
      console.warn("Proxy falló o tardó demasiado, usando puente directo a Jikan...");
    }

    // 2. Puente Directo a Jikan (Bypass de bloqueo de servidor)
    try {
      const jikanUrl = `https://api.jikan.moe/v4/${cleanEndpoint}${cleanEndpoint.includes("?") ? "&" : "?"}sfw=1`;
      const jikanRes = await nativeFetch(jikanUrl);
      if (jikanRes && jikanRes.ok) {
        const data = await jikanRes.json();
        
        // 3. Alimentar la Base de Datos local (segundo plano)
        if (data && data.data && !isRankingEndpoint(cleanEndpoint)) {
           nativeFetch(buildAppUrl("api/save_anime"), {
             method: "POST",
             headers: { "Content-Type": "application/json" },
             body: JSON.stringify({ mal_id: data.data.mal_id || 0, ...data.data, force_update: 1 })
           }).catch(() => {});
        }
        
        return data;
      }
    } catch (e) {
      console.error("Fallo total al conectar con Jikan:", e);
    }
    return null;
  };

  const isRankingEndpoint = (endpoint) => endpoint.includes("top/anime") || endpoint.includes("ranking");

  // Interceptar Fetch Globalmente para simplificar la integración
  window.fetch = (input, init = {}) => {
    const urlStr = typeof input === "string" || input instanceof URL ? String(input) : input?.url || "";
    
    // Si la llamada es al Jikan Proxy, usamos el Puente Inteligente
    if (urlStr.includes("api/jikan_proxy")) {
      const urlObj = new URL(urlStr, window.location.href);
      const endpoint = urlObj.searchParams.get("endpoint");
      if (endpoint) {
        return (async () => {
          const data = await smartFetchJikan(endpoint);
          return new Response(JSON.stringify(data), {
            status: data ? 200 : 504,
            headers: { "Content-Type": "application/json" }
          });
        })();
      }
    }

    // Para cualquier otra llamada, usamos fetch normal con seguridad
    const secureInit = withSecurityDefaults(urlStr, init);
    return nativeFetch(input, secureInit);
  };

  const normalizeText = (value) =>
    String(value || "")
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .trim();

  const fetchJson = async (url, options = {}, retries = 1, retryDelayMs = 700) => {
    try {
      const res = await window.fetch(url, options);
      if (!res || !res.ok) return null;
      return await res.json();
    } catch {
      return null;
    }
  };

  const slugify = (value) => {
    const normalized = String(value || "")
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "");
    return normalized || "anime";
  };

  window.AniDexShared = {
    buildAppUrl,
    getBasePath,
    getCookie,
    normalizeText,
    fetchJson,
    smartFetchJikan,
    slugify
  };
})();
