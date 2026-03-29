$path = "app/Views/pages/admin.php"
$content = Get-Content -Path $path -Raw

$approvedHeader = @"
<div class="relative w-[96%] max-w-6xl h-[88vh] rounded-3xl border border-white/10 bg-surface-container-low p-6 shadow-[0_30px_80px_rgba(0,0,0,0.55)]">
    <div class="flex items-start justify-between mb-4">
      <div>
        <h3 class="font-headline text-2xl font-bold">Solicitudes Aprobadas</h3>
        <p class="text-sm text-on-surface-variant">Listado de solicitudes aprobadas</p>
      </div>
      <div class="flex items-center gap-2">
        <button class="rounded-full bg-surface-container-high w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-on-surface" type="button" data-admin-approved-search-toggle>
          <span class="material-symbols-outlined text-[18px]">search</span>
        </button>
        <input class="hidden bg-surface-container-low text-on-surface text-xs rounded-full py-2 px-4 w-64 border-none focus:ring-1 ring-primary/20 transition-all placeholder:text-on-surface-variant/50" placeholder="Buscar (título o fecha)..." data-admin-approved-search />
        <button class="rounded-full bg-surface-container-high w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-on-surface" type="button" data-admin-approved-close>
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </div>
    </div>
"@

$rejectedHeader = @"
<div class="relative w-[96%] max-w-6xl h-[88vh] rounded-3xl border border-white/10 bg-surface-container-low p-6 shadow-[0_30px_80px_rgba(0,0,0,0.55)]">
    <div class="flex items-start justify-between mb-4">
      <div>
        <h3 class="font-headline text-2xl font-bold">Solicitudes Rechazadas</h3>
        <p class="text-sm text-on-surface-variant">Listado de solicitudes rechazadas</p>
      </div>
      <div class="flex items-center gap-2">
        <button class="rounded-full bg-surface-container-high w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-on-surface" type="button" data-admin-rejected-search-toggle>
          <span class="material-symbols-outlined text-[18px]">search</span>
        </button>
        <input class="hidden bg-surface-container-low text-on-surface text-xs rounded-full py-2 px-4 w-64 border-none focus:ring-1 ring-primary/20 transition-all placeholder:text-on-surface-variant/50" placeholder="Buscar (título o fecha)..." data-admin-rejected-search />
        <button class="rounded-full bg-surface-container-high w-9 h-9 flex items-center justify-center text-on-surface-variant hover:text-on-surface" type="button" data-admin-rejected-close>
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </div>
    </div>
"@

$patternApproved = '(?s)(<div id="admin-approved-modal"[\s\S]*?)(<div class="relative)[\s\S]*?(<div class="rounded-2xl)'
$patternRejected = '(?s)(<div id="admin-rejected-modal"[\s\S]*?)(<div class="relative)[\s\S]*?(<div class="rounded-2xl)'

$content = [regex]::Replace($content, $patternApproved, { param($m) $m.Groups[1].Value + $approvedHeader + $m.Groups[3].Value })
$content = [regex]::Replace($content, $patternRejected, { param($m) $m.Groups[1].Value + $rejectedHeader + $m.Groups[3].Value })

Set-Content -Path $path -Value $content
Write-Host "updated"
