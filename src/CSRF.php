<?php

namespace IconicCodes\LightUtils;

class CSRF {

    public static function generate() {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    public static function validate($token) {
        return $token === $_SESSION['csrf_token'];
    }

    public static function get() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = self::generate();
        }
        return $_SESSION['csrf_token'];
    }

    public static function get_hidden_input() {
        return '<input type="hidden" name="csrf_token" value="' . self::get() . '">';
    }
}