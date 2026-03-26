<!DOCTYPE html>
<html class="dark" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link rel="icon" href="img/icon3.png" />
<title>NekoraList - Añadir Nuevo Anime</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
        }
        body {
            background-color: #0e0e0e;
            color: #e7e5e4;
            font-family: 'Inter', sans-serif;
        }
        .font-headline { font-family: 'Manrope', sans-serif; }
    </style>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "surface-variant": "#252626",
              "surface-container-highest": "#252626",
              "surface": "#0e0e0e",
              "primary-fixed": "#e8deff",
              "on-surface-variant": "#acabaa",
              "primary-dim": "#c0adff",
              "tertiary-container": "#ddddfe",
              "background": "#0e0e0e",
              "surface-container-lowest": "#000000",
              "secondary": "#a09da1",
              "error": "#ec7c8a",
              "outline": "#767575",
              "on-background": "#e7e5e4",
              "surface-container-low": "#131313",
              "primary-container": "#4f00d0",
              "surface-container-high": "#1f2020",
              "on-primary": "#4800bf",
              "surface-container": "#191a1a",
              "primary": "#cdbdff",
              "outline-variant": "#484848",
              "on-surface": "#e7e5e4"
            },
            fontFamily: {
              "headline": ["Manrope"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "1rem", "lg": "2rem", "xl": "3rem", "full": "9999px"},
          },
        },
      }
    </script>
</head>
<body class="flex min-h-screen" data-admin-page="add">
<!-- SideNavBar Component -->
<div data-admin-sidebar></div>
<main class="ml-64 flex-1 relative min-h-screen">
<!-- Form Content -->
<section class="pt-20 pb-20 px-12 max-w-6xl mx-auto">
<div class="flex flex-col gap-2 mb-12">
<h1 class="text-5xl font-headline font-extrabold tracking-tight text-on-surface">Añadir Nuevo Anime</h1>
<p class="text-on-surface-variant text-lg">Completa los detalles para catalogar una nueva obra maestra.</p>
</div>
<form id="form-añadir" class="grid grid-cols-12 gap-8">
<!-- Left Column: Core Data & Imagery -->
<div class="col-span-12 lg:col-span-8 space-y-8">
<!-- Basic Info Section -->
<div class="bg-surface-container-low p-8 rounded-lg shadow-sm space-y-6">
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Título del Anime</label>
<input id="in-titulo" required class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface" placeholder="Ej: Neon Genesis Evangelion" type="text"/>
</div>
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Sinopsis / Descripción</label>
<textarea id="in-sinopsis" required class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface resize-none" placeholder="Escribe una descripción detallada..." rows="6"></textarea>
</div>
</div>
<!-- Media Section -->
<div class="bg-surface-container-low p-8 rounded-lg shadow-sm">
<label class="text-sm font-bold uppercase tracking-wider text-primary mb-6 block">Material Visual (URL)</label>
<div class="space-y-4">
<p class="text-sm font-medium text-on-surface-variant">Imagen Principal (URL Web)</p>
<input type="url" id="in-imagen" required class="w-full bg-surface-container-lowest border border-outline-variant/30 rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface" placeholder="https://ejemplo.com/poster.jpg" />
<div class="relative group aspect-video mt-4 rounded-lg overflow-hidden bg-surface-container-lowest border-2 border-dashed border-outline-variant/30 flex items-center justify-center">
<p class="text-xs text-outline font-bold uppercase">Previsualización del póster no disponible temporalmente</p>
</div>
</div>
</div>
</div>
<!-- Right Column: Metadata & Categorization -->
<div class="col-span-12 lg:col-span-4 space-y-8">
<!-- Technical Specs Section -->
<div class="bg-surface-container-low p-8 rounded-lg shadow-sm space-y-6">
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Estado</label>
<select id="in-estado" class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface appearance-none cursor-pointer">
<option value="Airing">En Emisión (Airing)</option>
<option value="Finished">Finalizado (Finished Airing)</option>
<option value="Upcoming">Próximamente (Not yet aired)</option>
</select>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Temporada</label>
<select id="in-temporada" class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface appearance-none cursor-pointer">
<option value="winter">Invierno</option>
<option value="spring">Primavera</option>
<option value="summer">Verano</option>
<option value="fall">Otoño</option>
</select>
</div>
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Año</label>
<input id="in-anio" class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface" type="number" value="2024"/>
</div>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="space-y-2">
<label class="text-sm font-bold uppercase tracking-wider text-primary">Episodios</label>
<input id="in-episodios" class="w-full bg-surface-container-lowest border-none rounded-lg p-4 focus:ring-2 focus:ring-primary/40 text-on-surface" placeholder="0" type="number"/>
</div>
</div>
</div>
<!-- Genres Section -->
<div class="bg-surface-container-low p-8 rounded-lg shadow-sm">
<label class="text-sm font-bold uppercase tracking-wider text-primary mb-4 block">Géneros</label>
<div class="flex flex-wrap gap-2">
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer">
<input name="in-genero" value="Action" class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant">Acción</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer">
<input name="in-genero" value="Adventure" class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant">Aventura</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer">
<input name="in-genero" value="Romance" class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant">Romance</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer">
<input name="in-genero" value="Sci-Fi" class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant">Sci-Fi</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer">
<input name="in-genero" value="Drama" class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant">Drama</span>
</label>
<label class="group flex items-center gap-2 bg-surface-container-lowest px-4 py-2 rounded-full border border-outline-variant/10 cursor-pointer">
<input name="in-genero" value="Fantasy" class="rounded text-primary focus:ring-primary/20 bg-transparent border-outline-variant" type="checkbox"/>
<span class="text-xs font-medium text-on-surface-variant">Fantasía</span>
</label>
</div>
</div>
<!-- Actions -->
<div class="bg-surface-container-high p-4 rounded-lg flex flex-col gap-3 mt-4">
<button class="w-full py-4 bg-gradient-to-br from-[#cdbdff] to-[#4f00d0] text-white rounded-full font-headline font-extrabold text-sm uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/20" type="submit">
                            Subir Anime
                        </button>
</div>
</div>
</form>
</section>
</main>
<div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40" data-admin-save-toast>
  <div class="rounded-2xl bg-surface-container-high px-6 py-3 text-sm font-bold text-emerald-400 shadow-xl border border-primary/20 flex items-center gap-2">
    <span class="material-symbols-outlined text-lg">check_circle</span>
    Anime guardado en la base de datos exitosamente
  </div>
</div>
<script>
  (function () {
    const form = document.getElementById('form-añadir');
    const toast = document.querySelector('[data-admin-save-toast]');
    let isDirty = false;

    form.addEventListener('input', () => { isDirty = true; }, true);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const btn = form.querySelector('[type="submit"]');
      const textOrig = btn.innerText;
      btn.innerText = 'PROCESANDO...';
      btn.disabled = true;

      const generosChecks = document.querySelectorAll('input[name="in-genero"]:checked');
      const generos = Array.from(generosChecks).map(cb => cb.value);

      const payload = {
        titulo: document.getElementById('in-titulo').value,
        sinopsis: document.getElementById('in-sinopsis').value,
        estado: document.getElementById('in-estado').value,
        temporada: document.getElementById('in-temporada').value,
        anio: document.getElementById('in-anio').value,
        episodios: document.getElementById('in-episodios').value,
        imagen_url: document.getElementById('in-imagen').value,
        generos: generos
      };

      try {
        const res = await fetch('api/admin.php?action=add_anime', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        
        if (data.success) {
            isDirty = false;
            toast.classList.remove('hidden');
            toast.classList.add('flex');
            form.reset();
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('flex');
            }, 3000);
        } else {
            alert('Error del Servidor: ' + data.error);
        }
      } catch (err) {
        console.error(err);
        alert('Error de red al conectar con el servidor.');
      } finally {
        btn.innerText = textOrig;
        btn.disabled = false;
      }
    });
  })();
</script>
<script src="controllers/admin-layout.js"></script>
</body></html>
