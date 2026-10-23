<?php
// NightAuth PHP Customer Portal — Front Controller

if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

session_start();

require_once __DIR__ . '/../src/i18n.php';
require_once __DIR__ . '/../src/NightAuthClient.php';

$client = new NightAuthClient();
$portalInfo = $client->getPortalInfo();
$isLoggedIn = !empty($_SESSION['nightauth_cookie']);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

function render(string $viewPath, array $vars, string $pageTitle = '', ?array $portalInfo = null, bool $isLoggedIn = false) {
    global $client;
    $portalInfo = $portalInfo ?? $client->getPortalInfo();
    extract($vars);
    ob_start();
    require __DIR__ . '/../src/views/' . $viewPath;
    $content = ob_get_clean();
    require __DIR__ . '/../src/views/layout.php';
    exit;
}

// Router
switch ($uri) {
    case '/signin':
        if ($method === 'POST') {
            $mode = $_POST['mode'] ?? 'credentials';
            $params = ($mode === 'license')
                ? ['licenseKey' => trim($_POST['licenseKey'] ?? '')]
                : ['login' => trim($_POST['login'] ?? ''), 'password' => $_POST['password'] ?? ''];

            $res = $client->signIn($params);
            if (!$res['ok']) {
                $err = error_message($res, 'signInFailed');
                render('signin.php', ['errorMessage' => $err], t('signIn'), $portalInfo, false);
            }

            if (!empty($res['data']['requires2FA'])) {
                header('Location: /2fa-required');
                exit;
            }

            header('Location: /');
            exit;
        }

        render('signin.php', [], t('signIn'), $portalInfo, false);
        break;

    case '/signup':
        if ($method === 'POST') {
            $params = [
                'licenseKey' => trim($_POST['licenseKey'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
            ];

            $res = $client->signUp($params);
            if (!$res['ok']) {
                $err = error_message($res, 'signUpFailed');
                render('signup.php', ['errorMessage' => $err], t('signUp'), $portalInfo, false);
            }

            header('Location: /');
            exit;
        }

        render('signup.php', [], t('signUp'), $portalInfo, false);
        break;

    case '/2fa-required':
        if ($method === 'POST') {
            $res = $client->verify2fa($_POST['code'] ?? '');
            if (!$res['ok']) {
                $err = error_message($res, 'twoFactorFailed');
                render('2fa-required.php', ['errorMessage' => $err], t('twoFactorTitle'), $portalInfo, false);
            }

            header('Location: /');
            exit;
        }

        render('2fa-required.php', [], t('twoFactorTitle'), $portalInfo, false);
        break;

    case '/forgot-password':
        if ($method === 'POST') {
            $res = $client->forgotPassword($_POST['email'] ?? '');
            if (!$res['ok']) {
                $err = error_message($res, 'requestFailed');
                render('forgot-password.php', ['errorMessage' => $err], t('forgotPassword'), $portalInfo, false);
            }

            $success = t('resetEmailSent') ?: 'If an account exists with that email, a password reset link has been dispatched.';
            render('forgot-password.php', ['successMessage' => $success], t('forgotPassword'), $portalInfo, false);
        }

        render('forgot-password.php', [], t('forgotPassword'), $portalInfo, false);
        break;

    case '/reset-password':
        if ($method === 'POST') {
            $res = $client->resetPassword($_POST['token'] ?? '', $_POST['password'] ?? '');
            if (!$res['ok']) {
                $err = error_message($res, 'requestFailed');
                render('reset-password.php', ['errorMessage' => $err], t('resetPassword'), $portalInfo, false);
            }

            header('Location: /signin');
            exit;
        }

        render('reset-password.php', [], t('resetPassword'), $portalInfo, false);
        break;

    case '/actions/hwid-reset':
        if ($method === 'POST') {
            $res = $client->resetHwid();
            if ($res['ok']) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => t('hwidResetSuccess') ?: 'Hardware lock reset successfully.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => error_message($res, 'hwidResetFailed')];
            }
        }
        header('Location: /?tab=overview');
        exit;

    case '/actions/renew-license':
        if ($method === 'POST') {
            $res = $client->renewLicense($_POST['licenseKey'] ?? '');
            if ($res['ok']) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'License redeemed successfully.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => error_message($res, 'renewFailed')];
            }
        }
        header('Location: /?tab=overview');
        exit;

    case '/actions/kill-session':
        if ($method === 'POST') {
            $res = $client->killSession($_POST['sessionId'] ?? '');
            if ($res['ok']) {
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Session revoked.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'text' => error_message($res, 'sessionRevokeFailed')];
            }
        }
        header('Location: /?tab=sessions');
        exit;

    case '/actions/logout':
        $client->logout();
        header('Location: /signin');
        exit;

    case '/':
    default:
        $res = $client->getCustomerData();
        if (!$res['ok']) {
            header('Location: /signin');
            exit;
        }

        render('dashboard.php', ['userData' => $res['data']], 'Dashboard', $portalInfo, true);
        break;
}
