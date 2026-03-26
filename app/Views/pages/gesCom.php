<!DOCTYPE html>
<html class="dark" lang="es"><head>
<link rel="icon" href="img/icon3.png" />
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>NekoraList - Gestión de Comentarios</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "secondary-container": "#3c3b3e",
              "on-tertiary": "#555671",
              "error-dim": "#b95463",
              "on-surface-variant": "#acabaa",
              "tertiary-fixed": "#ddddfe",
              "surface-container-lowest": "#000000",
              "on-primary-fixed": "#4700bd",
              "outline": "#767575",
              "on-background": "#e7e5e4",
              "tertiary-dim": "#cecfef",
              "primary": "#cdbdff",
              "surface-container-high": "#1f2020",
              "error-container": "#7f2737",
              "surface-container-low": "#131313",
              "tertiary-container": "#ddddfe",
              "on-primary": "#4800bf",
              "surface-container": "#191a1a",
              "error": "#ec7c8a",
              "primary-fixed-dim": "#dacdff",
              "surface-variant": "#252626",
              "surface": "#0e0e0e",
              "on-error": "#490013",
              "on-surface": "#e7e5e4"
            },
            fontFamily: {
              "headline": ["Manrope"],
              "body": ["Inter"]
            },
            borderRadius: {"DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body { background-color: #0e0e0e; color: #e7e5e4; font-family: 'Inter', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-background text-on-background selection:bg-primary-container selection:text-on-primary-container" data-admin-page="comments">
<div data-admin-sidebar></div>
<main class="ml-64 pt-28 px-12 pb-12 min-h-screen">
<div class="grid grid-cols-4 gap-6 mb-12">
<div class="bg-surface-container-low p-6 rounded-lg border-l-4 border-primary shadow-lg">
<p class="text-on-surface-variant text-xs uppercase font-bold tracking-widest mb-1">TOTAL COMENTARIOS</p>
<h3 class="text-3xl font-headline font-extrabold tracking-tighter" id="stat-total">0</h3>
<p class="text-[10px] text-primary mt-2 flex items-center gap-1">
<span class="material-symbols-outlined text-xs">trending_up</span> Base instalada
</p>
</div>
<div class="bg-surface-container-low p-6 rounded-lg border-l-4 border-error shadow-lg">
<p class="text-on-surface-variant text-xs uppercase font-bold tracking-widest mb-1">CONTENIDO REPORTADO</p>
<h3 class="text-3xl font-headline font-extrabold tracking-tighter text-error" id="stat-flagged">0</h3>
<p class="text-[10px] text-on-surface-variant mt-2 italic">Moderación requerida</p>
</div>
</div>
<!-- Table Container -->
<section class="bg-surface-container-low rounded-lg overflow-hidden shadow-2xl">
<div class="px-8 py-6 flex justify-between items-center border-b border-outline-variant/10">
<h2 class="font-headline font-bold text-xl tracking-tight">Interacciones Recientes</h2>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse" id="comments-table">
<thead>
<tr class="bg-surface-container text-on-surface-variant uppercase text-[10px] font-bold tracking-[0.2em]">
<th class="px-8 py-4">USUARIO</th>
<th class="px-8 py-4">SERIE DE ANIME</th>
<th class="px-8 py-4">FRAGMENTO DEL COMENTARIO</th>
<th class="px-8 py-4">FECHA</th>
<th class="px-8 py-4 text-right">ACCIONES</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/5">
<tr id="row-template" class="hidden hover:bg-surface-container-high transition-all duration-300 group">
<td class="px-8 py-6 whitespace-nowrap">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-xs" data-avatar>?</div>
<div>
<p class="text-sm font-bold text-on-surface" data-user>@User</p>
<p class="text-[10px] text-primary/70 uppercase font-bold" data-tag>TAG</p>
</div>
</div>
</td>
<td class="px-8 py-6">
<p class="text-sm font-headline font-bold text-on-surface" data-anime>Anime</p>
<p class="text-[10px] text-on-surface-variant" data-ep>General</p>
</td>
<td class="px-8 py-6 max-w-md">
<p class="text-sm text-on-surface-variant line-clamp-1 leading-relaxed italic" data-msg>Message</p>
</td>
<td class="px-8 py-6 whitespace-nowrap">
<p class="text-xs text-on-surface-variant" data-date>Date</p>
</td>
<td class="px-8 py-6 text-right whitespace-nowrap">
<div class="flex justify-end gap-2">
<button class="w-10 h-10 rounded-full flex items-center justify-center bg-surface-container hover:bg-error-container hover:text-white transition-all duration-300 text-on-surface-variant" title="Eliminar comentario" data-admin-comment-delete>
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-8 py-6 flex justify-between items-center bg-surface-container/50 border-t border-outline-variant/10">
<p class="text-xs text-on-surface-variant" id="comments-footer">Cargando comentarios...</p>
</div>
</section>
</main>
<script>
  (async function () {
    const tbody = document.querySelector('tbody');
    const template = document.getElementById('row-template');
    
    try {
        const res = await fetch('api/comments.php?action=list');
        const data = await res.json();
        if (data.success) {
            renderComments(data.data);
            document.getElementById('stat-total').textContent = data.data.length;
            document.getElementById('stat-flagged').textContent = data.data.filter(c => c.flagged).length;
            document.getElementById('comments-footer').innerHTML = `Mostrando <span class="text-on-surface font-bold">${data.data.length}</span> comentarios en vivo`;
        } else {
            console.error(data.error);
        }
    } catch (e) { console.error('Error fetching comments', e); }

    function renderComments(comments) {
        tbody.innerHTML = '';
        tbody.appendChild(template);
        
        comments.forEach(c => {
            const row = template.cloneNode(true);
            row.id = '';
            row.classList.remove('hidden');
            row.dataset.commentId = c.id;
            
            row.querySelector('[data-user]').textContent = c.user;
            row.querySelector('[data-tag]').textContent = c.tag;
            row.querySelector('[data-anime]').textContent = c.anime;
            row.querySelector('[data-ep]').textContent = c.ep;
            
            const msgNode = row.querySelector('[data-msg]');
            msgNode.textContent = c.msg;
            if (c.flagged) {
                msgNode.className = "text-sm text-error-dim line-clamp-1 leading-relaxed font-semibold";
                row.className = "bg-error-container/5 hover:bg-error-container/10 transition-all duration-300 group";
                row.querySelector('[data-tag]').className = "text-[10px] text-error font-bold uppercase";
            }
            
            row.querySelector('[data-date]').textContent = c.date;
            row.querySelector('[data-avatar]').textContent = c.user.replace('@','').charAt(0).toUpperCase();
            
            tbody.appendChild(row);
        });
    }

    document.addEventListener('click', async (e) => {
        const delBtn = e.target.closest('[data-admin-comment-delete]');
        if (delBtn) {
            e.preventDefault();
            const row = delBtn.closest('tr');
            if (!row) return;
            const id = row.dataset.commentId;
            if (!confirm('¿Seguro que deseas eliminar este comentario?')) return;
            
            try {
                const res = await fetch('api/comments.php?action=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });
                const data = await res.json();
                if (data.success) {
                    row.remove();
                    // Update count simply
                    let curr = parseInt(document.getElementById('stat-total').textContent);
                    document.getElementById('stat-total').textContent = Math.max(0, curr - 1);
                } else alert(data.error);
            } catch (err) { alert('Network Error'); }
        }
    });

  })();
</script>
<script src="controllers/admin-layout.js"></script>
</body></html>
