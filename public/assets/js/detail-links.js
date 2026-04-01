(() => {
  const pickTitleFromCard = (el) => {
    if (!el) return "";
    const card = el.closest("[data-mal-id], [data-title], [data-anime-card], .group, article, a, button") || el.closest("div") || el;

    const dataTitle = card.getAttribute?.("data-title");
    if (dataTitle && dataTitle.length > 2) return dataTitle;

    const heading = card.querySelector?.("h1,h2,h3,h4,h5");
    if (heading?.textContent?.trim()) {
      const hText = heading.textContent.trim();
      if (!/^(Anime|Película|Movie|TV|OVA|Special|Hoy)$/i.test(hText)) return hText;
    }

    const bolds = Array.from(card.querySelectorAll?.(".font-bold, .font-semibold, .font-headline") || []);
    const textual = bolds
      .map((n) => (n.textContent || "").trim())
      .find((t) => {
        if (!t || t.length <= 2) return false;
        if (/^(Anime|Película|Movie|TV|OVA|Special|Finalizado|En emision|En emisión|Hoy|Favoritos)$/i.test(t)) return false;
        return /[a-zA-Z]/.test(t) && !/^\d+([.,]\d+)?$/.test(t);
      });

    return textual || "";
  };

  const toDetailUrl = (title) => `detail?q=${encodeURIComponent((title || "").trim())}`;
  const toDetailById = (malId, title = "", dbId = null) => {
    let url = "detail?";
    if (dbId) url += `id=${encodeURIComponent(String(dbId))}&`;
    if (malId) url += `mal_id=${encodeURIComponent(String(malId))}`;
    if (title && !malId && !dbId) url += `q=${encodeURIComponent(title)}`;
    else if (title) url += `&q=${encodeURIComponent(title)}`;
    return url.replace(/\?$/, "").replace(/&$/, "");
  };

  const parseDetailHref = (href) => {
    if (!href || !href.includes("detail")) return null;
    try {
      const url = new URL(href, window.location.origin);
      return {
        malId: url.searchParams.get("mal_id"),
        dbId: url.searchParams.get("id"),
        q: url.searchParams.get("q") || ""
      };
    } catch {
      return null;
    }
  };

  const wireLinks = () => {
    document.querySelectorAll('a[href*="detail"]').forEach((a) => {
      const parsed = parseDetailHref(a.getAttribute("href") || a.href || "");
      if (parsed?.malId || parsed?.dbId) return;

      const ownMalId = a.getAttribute("data-mal-id");
      const parentMalId = a.closest("[data-mal-id]")?.getAttribute("data-mal-id");
      const malId = ownMalId || parentMalId;
      const t = pickTitleFromCard(a);

      if (malId) a.href = toDetailById(malId, t);
      else if (t) a.href = toDetailUrl(t);
    });

    document.querySelectorAll("[onclick*='detail']").forEach((node) => {
      const malId = node.getAttribute("data-mal-id") || node.closest("[data-mal-id]")?.getAttribute("data-mal-id");
      const t = pickTitleFromCard(node);
      node.onclick = () => {
        if (malId) window.location.href = toDetailById(malId, t);
        else window.location.href = toDetailUrl(t);
      };
    });

    if (!document.body.dataset.detailLiveBound) {
      document.body.dataset.detailLiveBound = "1";
      document.addEventListener(
        "click",
        (e) => {
          const anchor = e.target.closest('a[href*="detail"]');
          if (anchor) {
            const parsed = parseDetailHref(anchor.getAttribute("href") || anchor.href || "");
            if (parsed?.malId || parsed?.dbId) {
              return;
            }
          }

          const target = e.target.closest("[data-mal-id], [onclick*='detail'], [data-anime-card]");
          if (!target) return;

          const malId = target.getAttribute("data-mal-id") || target.closest("[data-mal-id]")?.getAttribute("data-mal-id");
          const title = pickTitleFromCard(target);

          if (malId) {
            e.preventDefault();
            e.stopPropagation();
            window.location.href = toDetailById(malId, title);
            return;
          }

          if (anchor && title) {
            e.preventDefault();
            e.stopPropagation();
            window.location.href = toDetailUrl(title);
          }
        },
        true
      );
    }
  };

  window.AniDexDetailLinks = { init: wireLinks };
})();
