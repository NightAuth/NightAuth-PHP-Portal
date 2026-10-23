<?php
$currentLang = get_current_lang();
$langs = get_langs();
$currentLangObj = null;
foreach ($langs as $l) {
    if ($l['code'] === $currentLang) {
        $currentLangObj = $l;
        break;
    }
}
if (!$currentLangObj) $currentLangObj = $langs[0];

$appName = $portalInfo['app']['name'] ?? 'NightAuth Customer Portal';
$accentColor = $portalInfo['app']['accentColor'] ?? '#38bdf8';
$flagMap = [
    'en' => '/assets/flags/usa.png',
    'es' => '/assets/flags/spain.png',
    'fr' => '/assets/flags/france.png',
    'de' => '/assets/flags/germany.png',
    'it' => '/assets/flags/italy.png',
    'pt' => '/assets/flags/brazil.png',
    'ru' => '/assets/flags/russia.png',
    'tr' => '/assets/flags/turkey.png',
    'pl' => '/assets/flags/poland.png',
    'zh' => '/assets/flags/china.png',
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>" class="dark">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= !empty($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= htmlspecialchars($appName) ?></title>
  <link rel="stylesheet" href="/assets/css/portal.css" />
  <style>
    :root {
      --portal-accent: <?= htmlspecialchars($accentColor) ?>;
    }
  </style>
</head>
<body class="bg-[#060608] text-zinc-100 min-h-screen flex flex-col font-sans antialiased relative overflow-x-hidden selection:bg-cyan-500/30 selection:text-cyan-200">
  <canvas id="canvas" class="fixed inset-0 pointer-events-none z-0"></canvas>

  <header class="relative z-10 w-full max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
    <a href="/" class="flex items-center gap-3 group">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500/20 to-indigo-500/20 border border-cyan-500/30 flex items-center justify-center shadow-lg shadow-cyan-500/10 group-hover:border-cyan-500/50 transition">
        <?php if (!empty($portalInfo['app']['logoUrl'])): ?>
          <img src="<?= htmlspecialchars($portalInfo['app']['logoUrl']) ?>" alt="Logo" class="w-6 h-6 object-contain" />
        <?php else: ?>
          <svg class="w-5 h-5 text-cyan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        <?php endif; ?>
      </div>
      <div>
        <h1 class="text-base font-bold tracking-tight text-white group-hover:text-cyan-400 transition"><?= htmlspecialchars($appName) ?></h1>
        <p class="text-xs text-zinc-400">Customer Portal (PHP)</p>
      </div>
    </a>

    <div class="flex items-center gap-3">
      <!-- Language Selector -->
      <div class="relative" id="lang-dropdown-wrapper">
        <button id="lang-menu-btn" type="button" class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-zinc-900/80 border border-zinc-800 text-xs font-medium text-zinc-300 hover:text-white hover:border-zinc-700 transition" onclick="document.getElementById('lang-dropdown').classList.toggle('hidden')">
          <img src="<?= $flagMap[$currentLang] ?? $flagMap['en'] ?>" alt="" class="w-4 h-3 object-cover rounded-sm shadow" />
          <span><?= htmlspecialchars($currentLangObj['label']) ?></span>
          <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div id="lang-dropdown" class="hidden absolute right-0 mt-2 w-44 rounded-xl bg-[#0e0e14] border border-zinc-800 shadow-2xl py-1 z-50">
          <?php foreach ($langs as $l): ?>
            <a href="?lang=<?= htmlspecialchars($l['code']) ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs text-zinc-300 hover:text-white hover:bg-zinc-800/60 transition <?= $l['code'] === $currentLang ? 'bg-zinc-800/40 text-cyan-400 font-semibold' : '' ?>">
              <img src="<?= $flagMap[$l['code']] ?? $flagMap['en'] ?>" alt="" class="w-4 h-3 object-cover rounded-sm" />
              <span><?= htmlspecialchars($l['label']) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if (!empty($isLoggedIn)): ?>
        <form method="POST" action="/actions/logout" class="inline">
          <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-xs font-medium text-red-400 hover:bg-red-500/20 transition">
            <?= htmlspecialchars(t('logout') ?: 'Logout') ?>
          </button>
        </form>
      <?php endif; ?>
    </div>
  </header>

  <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-8">
    <?= $content ?>
  </main>

  <footer class="relative z-10 w-full max-w-6xl mx-auto px-4 py-6 text-center text-xs text-zinc-400 border-t border-zinc-900/60">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
      <p>Powered by <a href="https://nightauth.org" target="_blank" class="text-zinc-300 hover:text-cyan-400 font-medium transition">NightAuth</a></p>
      <div class="flex items-center gap-4">
        <a href="https://github.com/NightAuth/NightAuth-PHP-Portal" target="_blank" class="hover:text-zinc-200 transition">GitHub</a>
        <a href="https://nightauth.org/docs" target="_blank" class="hover:text-zinc-200 transition">Docs</a>
      </div>
    </div>
  </footer>

  <script src="/assets/js/grid.js"></script>
  <script src="/assets/cap/widget.js"></script>
  <script>
    document.addEventListener('click', (e) => {
      const wrapper = document.getElementById('lang-dropdown-wrapper');
      const dropdown = document.getElementById('lang-dropdown');
      if (wrapper && dropdown && !wrapper.contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });
  </script>
</body>
</html>
