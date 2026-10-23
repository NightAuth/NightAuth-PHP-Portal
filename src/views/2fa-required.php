<div class="w-full max-w-md mx-auto">
  <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
    <div class="mb-6 text-center">
      <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      </div>
      <h2 class="text-xl font-bold tracking-tight text-white"><?= htmlspecialchars(t('twoFactorTitle') ?: 'Two-Factor Authentication') ?></h2>
      <p class="text-xs text-zinc-400 mt-1"><?= htmlspecialchars(t('twoFactorDesc') ?: 'Enter the 6-digit code from your authenticator app or a backup recovery code') ?></p>
    </div>

    <?php if (!empty($errorMessage)): ?>
      <div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><?= htmlspecialchars($errorMessage) ?></span>
      </div>
    <?php endif; ?>

    <form method="POST" action="/2fa-required" class="space-y-4">
      <div>
        <label class="block text-xs font-medium text-zinc-300 mb-1.5"><?= htmlspecialchars(t('twoFactorCode') ?: 'Verification Code') ?></label>
        <input type="text" name="code" required autofocus autocomplete="one-time-code" placeholder="123456" class="w-full px-3.5 py-2.5 rounded-xl bg-zinc-950/60 border border-zinc-800 text-lg font-mono tracking-widest text-center text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-cyan-500/60 focus:ring-1 focus:ring-cyan-500/40 transition" />
      </div>

      <button type="submit" class="w-full mt-2 py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 font-medium text-sm text-white shadow-lg shadow-cyan-500/20 active:scale-[0.99] transition flex items-center justify-center gap-2">
        <span><?= htmlspecialchars(t('verify') ?: 'Verify & Sign In') ?></span>
      </button>
    </form>

    <div class="mt-6 pt-5 border-t border-zinc-800/60 text-center">
      <a href="/signin" class="text-xs text-zinc-400 hover:text-zinc-200 transition">← <?= htmlspecialchars(t('backToSignIn') ?: 'Back to Sign In') ?></a>
    </div>
  </div>
</div>
