<div class="w-full max-w-md mx-auto">
  <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
    <div class="absolute -top-24 -left-24 w-48 h-48 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="mb-6 text-center">
      <h2 class="text-xl font-bold tracking-tight text-white"><?= htmlspecialchars(t('signIn')) ?></h2>
      <p class="text-xs text-zinc-400 mt-1"><?= htmlspecialchars(t('customerPortal') ?: 'Sign in to access your license, downloads, and account') ?></p>
    </div>

    <?php if (!empty($errorMessage)): ?>
      <div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><?= htmlspecialchars($errorMessage) ?></span>
      </div>
    <?php endif; ?>

    <!-- Auth Method Selector -->
    <div class="flex rounded-lg bg-zinc-950/60 p-1 mb-5 border border-zinc-800/60 text-xs">
      <button type="button" id="tab-credentials" class="flex-1 py-1.5 font-medium rounded-md bg-zinc-800 text-white shadow transition" onclick="switchAuthMode('credentials')">
        <?= htmlspecialchars(t('account') ?: 'Account') ?>
      </button>
      <button type="button" id="tab-license" class="flex-1 py-1.5 font-medium rounded-md text-zinc-400 hover:text-zinc-200 transition" onclick="switchAuthMode('license')">
        <?= htmlspecialchars(t('licenseKey') ?: 'License Key') ?>
      </button>
    </div>

    <form method="POST" action="/signin" class="space-y-4">
      <input type="hidden" name="mode" id="auth-mode" value="credentials" />

      <div id="credentials-fields" class="space-y-4">
        <div>
          <label class="block text-xs font-medium text-zinc-300 mb-1.5"><?= htmlspecialchars(t('usernameOrEmail') ?: 'Username or Email') ?></label>
          <input type="text" name="login" required autocomplete="username" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-950/60 border border-zinc-800 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-cyan-500/60 focus:ring-1 focus:ring-cyan-500/40 transition" placeholder="name@example.com" />
        </div>

        <div>
          <div class="flex items-center justify-between mb-1.5">
            <label class="text-xs font-medium text-zinc-300"><?= htmlspecialchars(t('password')) ?></label>
            <a href="/forgot-password" class="text-xs text-cyan-400 hover:underline"><?= htmlspecialchars(t('forgotPassword')) ?></a>
          </div>
          <input type="password" name="password" autocomplete="current-password" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-950/60 border border-zinc-800 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-cyan-500/60 focus:ring-1 focus:ring-cyan-500/40 transition" placeholder="••••••••" />
        </div>
      </div>

      <div id="license-fields" class="hidden space-y-4">
        <div>
          <label class="block text-xs font-medium text-zinc-300 mb-1.5"><?= htmlspecialchars(t('licenseKey')) ?></label>
          <input type="text" name="licenseKey" placeholder="XXXX-XXXX-XXXX-XXXX" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-950/60 border border-zinc-800 text-sm font-mono text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-cyan-500/60 focus:ring-1 focus:ring-cyan-500/40 transition uppercase" />
        </div>
      </div>

      <button type="submit" class="w-full mt-2 py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 font-medium text-sm text-white shadow-lg shadow-cyan-500/20 active:scale-[0.99] transition flex items-center justify-center gap-2">
        <span><?= htmlspecialchars(t('signIn')) ?></span>
      </button>
    </form>

    <div class="mt-6 pt-5 border-t border-zinc-800/60 text-center">
      <p class="text-xs text-zinc-400">
        <?= htmlspecialchars(t('noAccount') ?: 'Have a license key but no account?') ?>
        <a href="/signup" class="text-cyan-400 hover:underline font-medium ml-1"><?= htmlspecialchars(t('signUp')) ?></a>
      </p>
    </div>
  </div>
</div>

<script>
  function switchAuthMode(mode) {
    document.getElementById('auth-mode').value = mode;
    const tabCred = document.getElementById('tab-credentials');
    const tabLic = document.getElementById('tab-license');
    const credFields = document.getElementById('credentials-fields');
    const licFields = document.getElementById('license-fields');

    if (mode === 'credentials') {
      tabCred.className = 'flex-1 py-1.5 font-medium rounded-md bg-zinc-800 text-white shadow transition';
      tabLic.className = 'flex-1 py-1.5 font-medium rounded-md text-zinc-400 hover:text-zinc-200 transition';
      credFields.classList.remove('hidden');
      licFields.classList.add('hidden');
    } else {
      tabLic.className = 'flex-1 py-1.5 font-medium rounded-md bg-zinc-800 text-white shadow transition';
      tabCred.className = 'flex-1 py-1.5 font-medium rounded-md text-zinc-400 hover:text-zinc-200 transition';
      licFields.classList.remove('hidden');
      credFields.classList.add('hidden');
    }
  }
</script>
