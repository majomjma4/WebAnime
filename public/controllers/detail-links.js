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
  const toDetailById = (malId, title = "", dbId = null) => {
    let url = `detail.php?`;
    if (dbId) url += `id=${encodeURIComponent(String(dbId))}&`;
    if (malId) url += `mal_id=${encodeURIComponent(String(malId))}`;
    if (title && !malId && !dbId) url += `q=${encodeURIComponent(title)}`;
    else if (title) url += `&q=${encodeURIComponent(title)}`;
    return url.replace(/\?$/, "").replace(/&$/, "");
  };

  const wireLinks = () => {
    document.querySelectorAll('a[href*="detail.php"]').forEach((a) => {
      const url = new URL(a.href, window.location.origin);
      if (url.searchParams.has("id") || url.searchParams.has("mal_id")) return;
      
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

    // Handler Universal Interceptor: Unifica clics en <a> y elementos con onclick/data-mal-id
    if (!document.body.dataset.detailLiveBound) {
      document.body.dataset.detailLiveBound = "1";
      document.addEventListener("click", (e) => {
        // 1. Buscar el objetivo (enlace o contenedor con datos)
        const target = e.target.closest('a[href*="detail.php"], [data-mal-id], [onclick*="detail.php"], [data-anime-card]');
        if (!target) return;

        // 2. Extraer metadatos del objetivo o sus padres
        const malId = target.getAttribute("data-mal-id") || target.closest("[data-mal-id]")?.getAttribute("data-mal-id");
        const title = pickTitleFromCard(target);
        const href = (target.tagName === 'A' ? target.getAttribute("href") : "") || "";
        
        let urlMalId = null;
        let urlDbId = null;
        if (href.includes("detail.php")) {
            try {
                const u = new URL(target.href, window.location.origin);
                urlMalId = u.searchParams.get("mal_id");
                urlDbId = u.searchParams.get("id");
            } catch(err) {}
        }

        // 3. NUCLEAR FIX: Si tenemos un malId explícito en el DOM, RE-CONSTRUIMOS la URL
        // Esto evita que IDs clonados (malIdInUrl != malId) nos lleven al anime equivocado.
        if (malId || urlMalId || urlDbId) {
            // Solo dejamos navegar libremente si el mal_id de la URL coincide exactamente con el del card
            if (urlMalId && malId && urlMalId === malId) {
                return; // Ya es correcto, dejar navegar
            }

            e.preventDefault();
            e.stopPropagation();
            
            // Prioridad: malId del DOM > malId de la URL > dbId de la URL
            const finalMalId = malId || urlMalId;
            const finalDbId = (!malId || malId === urlMalId) ? urlDbId : null;
            
            window.location.href = toDetailById(finalMalId, title, finalDbId);
        } else if (title && href.includes("detail.php")) {
            // Fallback por t&iacute;tulo si no hay IDs
            e.preventDefault();
            e.stopPropagation();
            window.location.href = toDetailUrl(title);
        }
      }, true); // Usar captura para interceptar antes que otros scripts
    }
  };

  window.AniDexDetailLinks = { init: wireLinks };
})();

