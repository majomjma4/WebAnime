(() => {
  const pickTitleFromCard = (el) => {
    if (!el) return "";
    const card = el.closest("[data-anime-card], .group, article, a, div") || el;
    const dataTitle = card.getAttribute?.("data-title");
    if (dataTitle) return dataTitle;
    const heading = card.querySelector?.("h1,h2,h3,h4,h5");
    if (heading?.textContent?.trim()) return heading.textContent.trim();
    const bolds = Array.from(card.querySelectorAll?.(".font-bold") || []);
    const textual = bolds
      .map((n) => (n.textContent || "").trim())
      .find((t) => /[a-zA-Z]/.test(t) && !/^\d+([.,]\d+)?$/.test(t));
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

