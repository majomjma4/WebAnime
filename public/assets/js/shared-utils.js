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
    
    // Remoción de rutas estáticas forzadas para mayor dinamismo
    if (path.toLowerCase().includes("/nekoralist/")) {
      return "/Nekoralist/";
    }
    if (path.toLowerCase().includes("/webanime/")) {
      return "/WebAnime/";
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
      
      // Si el servidor responde con 504, 503 o 500, forzamos el catch para usar el puente directo
      if (serverRes && serverRes.status >= 500) {
        throw new Error("Server Error " + serverRes.status);
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

  const translateAutoToEs = async (text) => {
    if (!text || !/[a-zA-Z]/.test(text)) return text;
    try {
      const safeText = text.substring(0, 1500); // Evitar error 400 por URL larga
      const q = encodeURIComponent(safeText);
      const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=es&dt=t&q=${q}`;
      const res = await nativeFetch(url);
      if (res.ok) {
        const json = await res.json();
        const translated = (json?.[0] || []).map(x => x?.[0]).join('').trim();
        return translated || text;
      }
    } catch {}
    return text;
  };

  const buildDetailUrl = (malId, title, dbId) => {
    const ref = malId || slugify(title);
    if (!ref) return buildAppUrl("detail");
    return buildAppUrl(`detail/${ref}`);
  };

  window.AniDexShared = {
    buildAppUrl,
    buildDetailUrl,
    getBasePath,
    getCookie,
    normalizeText,
    fetchJson,
    smartFetchJikan,
    slugify,
    translateAutoToEs
  };
})();
