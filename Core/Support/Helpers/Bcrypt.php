<?php

namespace App\Core\Support\Helpers;

class Bcrypt
{
    /**
     * Summary of hashPassword
     * Create a Password Hash.
     * creates a new password hash using a strong one-way hashing algorithm.
     * @param string $string
     * @param int $cost
     * @return string
     */
    public static function hashPassword(string $string, int $cost = 12): string
    {
        return password_hash($string, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    /**
     * Summary of comparePassword
     *
     * Verifies that the given hash matches the given password. password_verify() is compatible with crypt(). Therefore, password hashes created by crypt() can be used with password_verify().
     * @param string $password
     * @param string $hashPassword
     * @return bool
     */
    public static function comparePassword(string $password, string $hashPassword): bool
    {
        return password_verify($password, $hashPassword);
    }

    public function hash(string $string): string
    {
        return hash('sha256', $string);
    }

    public function hashCheck(string $know_string, string $user_string): bool
    {
        return hash_equals($know_string, $user_string);
    }
}