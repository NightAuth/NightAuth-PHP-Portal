<?php
$user = $userData['user'] ?? [];
$app = $portalInfo['app'] ?? [];
$plan = $userData['plan'] ?? ($user['plan'] ?? 'Default');
$expiresAt = !empty($user['expiresAt']) ? date('Y-m-d', strtotime($user['expiresAt'])) : (t('lifetime') ?: 'Lifetime');
$hwid = $user['hwid'] ?? (t('notBound') ?: 'Not bound');
$activeTab = $_GET['tab'] ?? 'overview';
?>

<div class="w-full max-w-5xl mx-auto space-y-6">
  <!-- Feedback banner -->
  <?php if (!empty($_SESSION['flash_message'])): ?>
    <?php $flash = $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
    <div class="p-3 rounded-xl <?= $flash['type'] === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border border-red-500/20 text-red-400' ?> text-xs flex items-center gap-2">
      <span><?= htmlspecialchars($flash['text']) ?></span>
    </div>
  <?php endif; ?>

  <!-- Header Card -->
  <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-indigo-600 p-0.5 shadow-lg shadow-cyan-500/20">
          <div class="w-full h-full bg-zinc-950 rounded-[14px] flex items-center justify-center text-xl font-bold text-white">
            <?= htmlspecialchars(strtoupper(substr($user['username'] ?? 'U', 0, 2))) ?>
          </div>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($user['username'] ?? 'User') ?></h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20"><?= htmlspecialchars($plan) ?></span>
          </div>
          <p class="text-xs text-zinc-400 mt-0.5"><?= htmlspecialchars($user['email'] ?? '') ?></p>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <?php if (!empty($app['downloadUrl'])): ?>
          <a href="<?= htmlspecialchars($app['downloadUrl']) ?>" target="_blank" class="px-4 py-2 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 text-cyan-400 text-xs font-medium transition flex items-center gap-1.5 shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <span><?= htmlspecialchars(t('download') ?: 'Download') ?></span>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quick Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-zinc-800/60 text-xs">
      <div class="p-3 rounded-xl bg-zinc-950/50 border border-zinc-800/50">
        <span class="text-zinc-500 block mb-1"><?= htmlspecialchars(t('expires') ?: 'Expiration') ?></span>
        <span class="font-medium text-zinc-200 text-sm"><?= htmlspecialchars($expiresAt) ?></span>
      </div>
      <div class="p-3 rounded-xl bg-zinc-950/50 border border-zinc-800/50">
        <span class="text-zinc-500 block mb-1"><?= htmlspecialchars(t('hwid') ?: 'HWID Lock') ?></span>
        <span class="font-mono text-zinc-200 text-sm truncate block" title="<?= htmlspecialchars($hwid) ?>"><?= htmlspecialchars($hwid) ?></span>
      </div>
      <div class="p-3 rounded-xl bg-zinc-950/50 border border-zinc-800/50">
        <span class="text-zinc-500 block mb-1"><?= htmlspecialchars(t('balance') ?: 'Account Balance') ?></span>
        <span class="font-medium text-emerald-400 text-sm">$<?= number_format((float)($user['balance'] ?? 0), 2) ?> USD</span>
      </div>
    </div>
  </div>

  <!-- Navigation Tabs -->
  <div class="flex border-b border-zinc-800/80 gap-2 overflow-x-auto text-sm">
    <a href="?tab=overview" class="px-4 py-2.5 font-medium border-b-2 <?= $activeTab === 'overview' ? 'border-cyan-500 text-cyan-400' : 'border-transparent text-zinc-400 hover:text-zinc-200' ?> whitespace-nowrap transition">
      <?= htmlspecialchars(t('overview') ?: 'Overview') ?>
    </a>
    <a href="?tab=variables" class="px-4 py-2.5 font-medium border-b-2 <?= $activeTab === 'variables' ? 'border-cyan-500 text-cyan-400' : 'border-transparent text-zinc-400 hover:text-zinc-200' ?> whitespace-nowrap transition">
      <?= htmlspecialchars(t('cloudVariables') ?: 'Cloud Variables') ?>
    </a>
    <a href="?tab=licenses" class="px-4 py-2.5 font-medium border-b-2 <?= $activeTab === 'licenses' ? 'border-cyan-500 text-cyan-400' : 'border-transparent text-zinc-400 hover:text-zinc-200' ?> whitespace-nowrap transition">
      <?= htmlspecialchars(t('licenses') ?: 'Licenses') ?>
    </a>
    <a href="?tab=sessions" class="px-4 py-2.5 font-medium border-b-2 <?= $activeTab === 'sessions' ? 'border-cyan-500 text-cyan-400' : 'border-transparent text-zinc-400 hover:text-zinc-200' ?> whitespace-nowrap transition">
      <?= htmlspecialchars(t('sessions') ?: 'Sessions') ?>
    </a>
    <a href="?tab=2fa" class="px-4 py-2.5 font-medium border-b-2 <?= $activeTab === '2fa' ? 'border-cyan-500 text-cyan-400' : 'border-transparent text-zinc-400 hover:text-zinc-200' ?> whitespace-nowrap transition">
      <?= htmlspecialchars(t('security') ?: 'Security (2FA)') ?>
    </a>
  </div>

  <!-- Tab Content -->
  <div>
    <?php if ($activeTab === 'overview'): ?>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
          <h3 class="text-base font-semibold text-white mb-2"><?= htmlspecialchars(t('hardwareLock') ?: 'Hardware Binding (HWID)') ?></h3>
          <p class="text-xs text-zinc-400 mb-4"><?= htmlspecialchars(t('hwidDescription') ?: 'Your license is locked to your PC hardware profile. If you upgrade your PC, you can reset your hardware link.') ?></p>

          <div class="p-3 rounded-xl bg-zinc-950/60 border border-zinc-800 mb-4 text-xs font-mono text-zinc-300 break-all">
            <?= htmlspecialchars($user['hwid'] ?? (t('noHwidBound') ?: 'No hardware profile locked yet.')) ?>
          </div>

          <form method="POST" action="/actions/hwid-reset">
            <button type="submit" class="w-full py-2 px-4 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-zinc-100 text-xs font-medium transition flex items-center justify-center gap-2 border border-zinc-700">
              <?= htmlspecialchars(t('resetHwid') ?: 'Reset Hardware Lock') ?>
            </button>
          </form>
        </div>

        <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
          <h3 class="text-base font-semibold text-white mb-2"><?= htmlspecialchars(t('renewSubscription') ?: 'Renew Subscription') ?></h3>
          <p class="text-xs text-zinc-400 mb-4"><?= htmlspecialchars(t('renewDescription') ?: 'Extend your active license validity by redeeming a new activation key.') ?></p>

          <form method="POST" action="/actions/renew-license" class="space-y-3">
            <input type="text" name="licenseKey" required placeholder="XXXX-XXXX-XXXX-XXXX" class="w-full px-3.5 py-2 rounded-xl bg-zinc-950/60 border border-zinc-800 text-xs font-mono uppercase text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-cyan-500 transition" />
            <button type="submit" class="w-full py-2 px-4 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-zinc-950 text-xs font-bold transition shadow-lg shadow-cyan-500/20">
              <?= htmlspecialchars(t('applyKey') ?: 'Redeem Key') ?>
            </button>
          </form>
        </div>
      </div>

    <?php elseif ($activeTab === 'variables'): ?>
      <?php $vars = $userData['userVars'] ?? ($userData['vars'] ?? []); ?>
      <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
        <h3 class="text-base font-semibold text-white mb-1"><?= htmlspecialchars(t('cloudVariables') ?: 'Cloud Variables') ?></h3>
        <p class="text-xs text-zinc-400 mb-4"><?= htmlspecialchars(t('variablesDescription') ?: 'Application variables and configurations assigned to your license.') ?></p>
        
        <?php if (empty($vars)): ?>
          <div class="py-12 text-center text-xs text-zinc-500"><?= htmlspecialchars(t('noVariables') ?: 'No cloud variables configured for your account.') ?></div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="border-b border-zinc-800 text-zinc-400">
                  <th class="py-2.5 px-3">Variable Name</th>
                  <th class="py-2.5 px-3">Value</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-zinc-800/60 font-mono">
                <?php foreach ($vars as $v): ?>
                  <tr>
                    <td class="py-2.5 px-3 text-cyan-400 font-semibold"><?= htmlspecialchars($v['name'] ?? ($v['key'] ?? '')) ?></td>
                    <td class="py-2.5 px-3 text-zinc-300 break-all"><?= htmlspecialchars($v['value'] ?? '') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    <?php elseif ($activeTab === 'licenses'): ?>
      <?php $licenses = $userData['licenses'] ?? (!empty($user['licenseKey']) ? [['key' => $user['licenseKey'], 'status' => 'Active']] : []); ?>
      <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
        <h3 class="text-base font-semibold text-white mb-1"><?= htmlspecialchars(t('licenses') ?: 'Licenses') ?></h3>
        <p class="text-xs text-zinc-400 mb-4"><?= htmlspecialchars(t('licensesDescription') ?: 'All license keys associated with your profile.') ?></p>

        <?php if (empty($licenses)): ?>
          <div class="py-12 text-center text-xs text-zinc-500"><?= htmlspecialchars(t('noLicenses') ?: 'No active licenses found.') ?></div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="border-b border-zinc-800 text-zinc-400">
                  <th class="py-2.5 px-3">Key</th>
                  <th class="py-2.5 px-3">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-zinc-800/60 font-mono">
                <?php foreach ($licenses as $lic): ?>
                  <tr>
                    <td class="py-2.5 px-3 text-zinc-200 select-all"><?= htmlspecialchars($lic['key'] ?? (is_string($lic) ? $lic : '')) ?></td>
                    <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded-full text-[11px] bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= htmlspecialchars($lic['status'] ?? 'Active') ?></span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    <?php elseif ($activeTab === 'sessions'): ?>
      <?php $sessions = $userData['sessions'] ?? []; ?>
      <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 shadow-xl">
        <h3 class="text-base font-semibold text-white mb-1"><?= htmlspecialchars(t('sessions') ?: 'Active Client Sessions') ?></h3>
        <p class="text-xs text-zinc-400 mb-4"><?= htmlspecialchars(t('sessionsDescription') ?: 'Review and revoke active connections to your account.') ?></p>

        <?php if (empty($sessions)): ?>
          <div class="py-12 text-center text-xs text-zinc-500"><?= htmlspecialchars(t('noSessions') ?: 'No active desktop or bot sessions recorded.') ?></div>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead>
                <tr class="border-b border-zinc-800 text-zinc-400">
                  <th class="py-2.5 px-3">IP / Host</th>
                  <th class="py-2.5 px-3">Last Active</th>
                  <th class="py-2.5 px-3 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-zinc-800/60">
                <?php foreach ($sessions as $s): ?>
                  <tr>
                    <td class="py-2.5 px-3 font-mono text-zinc-200"><?= htmlspecialchars($s['ip'] ?? 'Unknown') ?></td>
                    <td class="py-2.5 px-3 text-zinc-400"><?= htmlspecialchars(!empty($s['lastActive']) ? date('Y-m-d H:i', strtotime($s['lastActive'])) : 'Just now') ?></td>
                    <td class="py-2.5 px-3 text-right">
                      <form method="POST" action="/actions/kill-session" class="inline">
                        <input type="hidden" name="sessionId" value="<?= htmlspecialchars($s['id'] ?? '') ?>" />
                        <button type="submit" class="px-2.5 py-1 rounded bg-red-500/10 text-red-400 hover:bg-red-500/20 text-[11px] font-medium border border-red-500/20 transition">
                          Revoke
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    <?php elseif ($activeTab === '2fa'): ?>
      <?php $is2faEnabled = !empty($user['twoFactorEnabled']); ?>
      <div class="bg-zinc-900/60 backdrop-blur-xl border border-zinc-800/80 rounded-2xl p-6 shadow-xl max-w-xl">
        <h3 class="text-base font-semibold text-white mb-1"><?= htmlspecialchars(t('twoFactorTitle') ?: 'Two-Factor Authentication (2FA)') ?></h3>
        <p class="text-xs text-zinc-400 mb-4"><?= htmlspecialchars(t('twoFactorSecurityDesc') ?: 'Add an additional layer of security to your customer account using an authenticator app.') ?></p>

        <div class="p-4 rounded-xl bg-zinc-950/60 border border-zinc-800 flex items-center justify-between mb-4">
          <div>
            <span class="text-xs font-semibold text-zinc-200 block"><?= htmlspecialchars(t('status') ?: 'Status') ?></span>
            <span class="text-xs <?= $is2faEnabled ? 'text-emerald-400' : 'text-zinc-500' ?>"><?= $is2faEnabled ? 'Enabled' : 'Disabled' ?></span>
          </div>
          <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $is2faEnabled ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-zinc-800 text-zinc-400' ?>">
            <?= $is2faEnabled ? 'Active' : 'Inactive' ?>
          </span>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
