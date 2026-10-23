<?php

$GLOBALS['na_i18n_data'] = require __DIR__ . '/i18n/translations.php';

function get_langs(): array {
    return $GLOBALS['na_i18n_data']['langs'] ?? [];
}

function get_current_lang(): string {
    $langs = get_langs();
    $codes = array_column($langs, 'code');

    if (!empty($_GET['lang']) && in_array($_GET['lang'], $codes, true)) {
        $selected = $_GET['lang'];
        setcookie('na_lang', $selected, time() + 365 * 86400, '/');
        $_COOKIE['na_lang'] = $selected;
        return $selected;
    }

    if (!empty($_COOKIE['na_lang']) && in_array($_COOKIE['na_lang'], $codes, true)) {
        return $_COOKIE['na_lang'];
    }

    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $browser = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        if (in_array($browser, $codes, true)) {
            return $browser;
        }
    }

    return getenv('DEFAULT_LANG') ?: 'en';
}

function t(string $key, array $params = []): string {
    $lang = get_current_lang();
    $translations = $GLOBALS['na_i18n_data']['translations'];

    $val = $translations[$lang][$key]
        ?? $translations['en'][$key]
        ?? $key;

    if (!empty($params)) {
        foreach ($params as $k => $v) {
            $val = str_replace('{' . $k . '}', (string)$v, $val);
        }
    }

    return $val;
}

function error_message(array $result, string $fallbackKey = 'requestFailed'): string {
    $status = $result['status'] ?? 0;
    $data = $result['data'] ?? [];

    if ($status === 0) return t('networkError');
    if (($data['error'] ?? '') === 'captcha_required') return t('captchaRequired');
    if (($data['error'] ?? '') === 'captcha_unavailable') return t('captchaUnavailable');

    if ($status === 429 || ($data['error'] ?? '') === 'rate_limited') {
        $seconds = (int)($data['retry_after'] ?? 60);
        $mins = (int)floor($seconds / 60);
        $secs = $seconds % 60;
        return t('tooManyAttempts', ['time' => $mins > 0 ? "{$mins}m {$secs}s" : "{$secs}s"]);
    }

    $errorKeys = $GLOBALS['na_i18n_data']['error_keys'] ?? [];
    $errCode = $data['error'] ?? '';
    if (isset($errorKeys[$errCode])) {
        return t($errorKeys[$errCode]);
    }

    return $data['message'] ?? t($fallbackKey);
}
