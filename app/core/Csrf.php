<?php
// Simple CSRF helper
class Csrf
{
    const TOKEN_KEY = '_csrf_token';

    public static function token()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    public static function input()
    {
        $token = self::token();
        return '<input type="hidden" name="' . self::TOKEN_KEY . '" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
    }

    public static function validate($tokenFromRequest): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($tokenFromRequest)) {
            return false;
        }
        if (empty($_SESSION[self::TOKEN_KEY])) {
            return false;
        }
        $valid = hash_equals($_SESSION[self::TOKEN_KEY], $tokenFromRequest);
        // rotate token on successful validation to mitigate double-submit
        if ($valid) {
            unset($_SESSION[self::TOKEN_KEY]);
        }
        return $valid;
    }
}