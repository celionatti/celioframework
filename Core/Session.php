<?php

namespace App\Core;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        $this->start_session();

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            /** Mark to be removed. */
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    /** activate session if not yet started **/
    private static function start_session()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function exists($name)
    {
        $this->start_session();

        return isset($_SESSION[$name]);
    }

    public function set($key, $value)
    {
        $this->start_session();

        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        $this->start_session();

        if ($this->exists($key) && !empty($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return false;
    }

    public function remove($key)
    {
        $this->start_session();

        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        /** Iterate over Mark to be removed. */
        $this->removeFlashMessages();
    }

    public function removeFlashMessages()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}