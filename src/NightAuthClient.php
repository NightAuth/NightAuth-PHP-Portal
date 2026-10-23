<?php

class NightAuthClient {
    private string $apiUrl;
    private string $portalId;
    private static ?array $cachedInfo = null;

    public function __construct() {
        $this->apiUrl = rtrim(getenv('NIGHTAUTH_API_URL') ?: 'http://localhost:13100', '/');
        $this->portalId = getenv('PORTAL_ID') ?: 'demo-app';
    }

    public function getPortalInfo(): array {
        if (self::$cachedInfo !== null) {
            return self::$cachedInfo;
        }

        $res = $this->request('/api/customer/info?portalId=' . urlencode($this->portalId));
        if ($res['ok'] && !empty($res['data'])) {
            self::$cachedInfo = $res['data'];
            return self::$cachedInfo;
        }

        return ['app' => ['name' => 'NightAuth Customer Portal', 'accentColor' => '#38bdf8']];
    }

    public function sealPayload(array $payload): array {
        $info = $this->getPortalInfo();
        $sealKey = $info['app']['sealedBoxPublicKey'] ?? null;

        if (!$sealKey || !extension_loaded('sodium')) {
            return $payload;
        }

        try {
            $keyBin = base64_decode($sealKey, true);
            if ($keyBin === false) return $payload;

            $jsonPlain = json_encode($payload, JSON_UNESCAPED_SLASHES);
            $sealed = sodium_crypto_box_seal($jsonPlain, $keyBin);

            return [
                'portalId' => $payload['portalId'],
                'payload' => base64_encode($sealed)
            ];
        } catch (\Throwable $e) {
            error_log('Sealing failed: ' . $e->getMessage());
            return $payload;
        }
    }

    public function request(string $path, array $options = []): array {
        $method = strtoupper($options['method'] ?? 'GET');
        $body = $options['body'] ?? null;
        $sealed = $options['sealed'] ?? false;

        $url = $this->apiUrl . $path;
        $ch = curl_init($url);

        $headers = ['Accept: application/json'];
        
        // Forward stored session cookies
        $sessionCookie = $_SESSION['nightauth_cookie'] ?? '';
        if ($sessionCookie) {
            $headers[] = 'Cookie: ' . $sessionCookie;
        }

        if ($body !== null) {
            $payload = array_merge(['portalId' => $this->portalId], $body);
            if ($sealed) {
                $payload = $this->sealPayload($payload);
            }
            $jsonBody = json_encode($payload);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            return ['ok' => false, 'status' => 0, 'data' => ['error' => 'network_error', 'message' => $err]];
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $rawHeaders = substr($response, 0, $headerSize);
        $rawBody = substr($response, $headerSize);

        // Parse Set-Cookie headers
        preg_match_all('/^Set-Cookie:\s*([^;\r\n]+)/mi', $rawHeaders, $matches);
        if (!empty($matches[1])) {
            $cookies = [];
            foreach ($matches[1] as $c) {
                $cookies[] = $c;
            }
            $_SESSION['nightauth_cookie'] = implode('; ', $cookies);
        }

        $data = json_decode($rawBody, true) ?? [];
        return [
            'ok' => ($httpCode >= 200 && $httpCode < 300),
            'status' => $httpCode,
            'data' => $data,
        ];
    }

    public function signIn(array $params): array {
        return $this->request('/api/customer/auth/signin', [
            'method' => 'POST',
            'body' => $params,
            'sealed' => true,
        ]);
    }

    public function signUp(array $params): array {
        return $this->request('/api/customer/auth/signup', [
            'method' => 'POST',
            'body' => $params,
            'sealed' => true,
        ]);
    }

    public function verify2fa(string $code): array {
        return $this->request('/api/customer/auth/2fa-verify', [
            'method' => 'POST',
            'body' => ['code' => trim($code)],
            'sealed' => true,
        ]);
    }

    public function forgotPassword(string $email): array {
        return $this->request('/api/customer/auth/forgot-password', [
            'method' => 'POST',
            'body' => ['email' => trim($email)],
            'sealed' => true,
        ]);
    }

    public function resetPassword(string $token, string $password): array {
        return $this->request('/api/customer/auth/reset-password', [
            'method' => 'POST',
            'body' => ['token' => trim($token), 'password' => $password],
            'sealed' => true,
        ]);
    }

    public function getCustomerData(): array {
        return $this->request('/api/customer/data?portalId=' . urlencode($this->portalId));
    }

    public function resetHwid(): array {
        return $this->request('/api/customer/actions/hwid-reset', [
            'method' => 'POST',
            'body' => [],
        ]);
    }

    public function renewLicense(string $licenseKey): array {
        return $this->request('/api/customer/actions/renew-license', [
            'method' => 'POST',
            'body' => ['licenseKey' => trim($licenseKey)],
            'sealed' => true,
        ]);
    }

    public function killSession(string $sessionId): array {
        return $this->request('/api/customer/actions/kill-session', [
            'method' => 'POST',
            'body' => ['sessionId' => $sessionId],
        ]);
    }

    public function logout(): void {
        $this->request('/api/customer/auth/logout', ['method' => 'POST', 'body' => []]);
        unset($_SESSION['nightauth_cookie']);
        session_destroy();
    }
}
