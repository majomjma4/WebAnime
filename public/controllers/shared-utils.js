(() => {
  const normalizeText = (value) =>
    String(value || "")
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .trim();

  const fetchJson = async (url, options = {}, retries = 1, retryDelayMs = 700) => {
    try {
      let res = await fetch(url, options);
      let remaining = retries;
      while (res && res.status === 429 && remaining > 0) {
        await new Promise((resolve) => setTimeout(resolve, retryDelayMs));
        res = await fetch(url, options);
        remaining -= 1;
      }
      if (!res || !res.ok) return null;
      return await res.json();
    } catch {
      return null;
    }
  };

  const translateAutoToEs = async (text) => {
    const raw = String(text || "").trim();
    if (!raw) return "";
    const url =
      "https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=es&dt=t&q=" +
      encodeURIComponent(raw);
    const data = await fetchJson(url, {}, 1, 700);
    const out = (data?.[0] || []).map((row) => row?.[0] || "").join("").trim();
    return out || raw;
  };

  const scoreTextMatch = (query, candidate) => {
    const q = normalizeText(query);
    const c = normalizeText(candidate);
    if (!q || !c) return 0;
    if (q === c) return 100;
    if (c.includes(q) || q.includes(c)) return 80;
    const qTokens = q.split(" ").filter(Boolean);
    const cTokens = c.split(" ").filter(Boolean);
    const overlap = qTokens.filter((token) => cTokens.includes(token)).length;
    if (!overlap) return 0;
    return Math.round((overlap / Math.max(qTokens.length, cTokens.length)) * 70);
  };

  window.AniDexShared = {
    normalizeText,
    fetchJson,
    translateAutoToEs,
    scoreTextMatch
  };
})();
