(() => {
  const pickTitleFromCard = (el) => {
    if (!el) return "";
    const card = el.closest("[data-mal-id], [data-title], [data-anime-card], .group, article, a, button") || el.closest("div") || el;
    
    // 1. Prioridad: atributo data-title
    const dataTitle = card.getAttribute?.("data-title");
    if (dataTitle && dataTitle.length > 2) return dataTitle;
    
    // 2. Prioridad: Encabezado (H1-H5)
    const heading = card.querySelector?.("h1,h2,h3,h4,h5");
    if (heading?.textContent?.trim()) {
        const hText = heading.textContent.trim();
        if (!/^(Anime|Película|Movie|TV|OVA|Special|Hoy)$/i.test(hText)) return hText;
    }
    
    // 3. Fallback: Texto en negrita, pero filtrando palabras comunes de metadatos
    const bolds = Array.from(card.querySelectorAll?.(".font-bold, .font-semibold, .font-headline") || []);
    const textual = bolds
      .map((n) => (n.textContent || "").trim())
      .find((t) => {
          if (!t || t.length <= 2) return false;
          if (/^(Anime|Película|Movie|TV|OVA|Special|Finalizado|En emision|Hoy|Favoritos)$/i.test(t)) return false;
          return /[a-zA-Z]/.test(t) && !/^\d+([.,]\d+)?$/.test(t);
      });
      
    return textual || "";
  };

  const toDetailUrl = (title) => `detail.php?q=${encodeURIComponent((title || "").trim())}`;
  const toDetailById = (id, title = "") =>
    `detail.php?mal_id=${encodeURIComponent(String(id))}${title ? `&q=${encodeURIComponent(title)}` : ""}`;

  const wireLinks = () => {
    document.querySelectorAll('a[href="detail.php"]').forEach((a) => {
      const malId = a.getAttribute("data-mal-id") || a.closest("[data-mal-id]")?.getAttribute("data-mal-id");
      const t = pickTitleFromCard(a);
      if (malId) {
        a.href = toDetailById(malId, t);
      } else if (t) {
        a.href = toDetailUrl(t);
      }
    });

    document.querySelectorAll("[onclick*='detail.php']").forEach((node) => {
      const malId = node.getAttribute("data-mal-id") || node.closest("[data-mal-id]")?.getAttribute("data-mal-id");
      const t = pickTitleFromCard(node);
      node.onclick = () => {
        if (malId) window.location.href = toDetailById(malId, t);
        else window.location.href = toDetailUrl(t);
      };
    });

    // Handler en vivo: evita href viejos cuando el card cambia dinmicamente.
    if (!document.body.dataset.detailLiveBound) {
      document.body.dataset.detailLiveBound = "1";
      document.addEventListener("click", (e) => {
        const a = e.target.closest("a");
        if (a) {
          const href = a.getAttribute("href") || "";
          if (href.includes("detail.php")) {
            e.preventDefault();
            const malId = a.getAttribute("data-mal-id") || a.closest("[data-mal-id]")?.getAttribute("data-mal-id");
            const title = pickTitleFromCard(a);
            if (malId) window.location.href = toDetailById(malId, title);
            else window.location.href = toDetailUrl(title);
            return;
          }
        }
        const node = e.target.closest("[onclick*='detail.php']");
        if (node) {
          e.preventDefault();
          e.stopPropagation();
          const malId = node.getAttribute("data-mal-id") || node.closest("[data-mal-id]")?.getAttribute("data-mal-id");
          const title = pickTitleFromCard(node);
          if (malId) window.location.href = toDetailById(malId, title);
          else window.location.href = toDetailUrl(title);
        }
      });
    }
  };

  window.AniDexDetailLinks = { init: wireLinks };
})();

