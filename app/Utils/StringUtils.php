<?php

namespace App\Utils;

class StringUtils
{
    public static function maskCpf(string $cpf): string
    {
        return substr($cpf, 0, 2) . '****' . substr($cpf, -3);
    }

    public static function maskEmail(string $email): string
    {
        return substr($email, 0, 2) . str_repeat('*', strlen($email) - 2) . substr($email, strpos($email, '@'));
    }
}
