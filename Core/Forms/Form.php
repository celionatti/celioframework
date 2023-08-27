<?php

namespace App\Core\Forms;

use Exception;
use App\Core\Support\Csrf;

class Form
{
    public static function openForm($action, $method = 'POST', $enctype = null) {
        $csrf = new Csrf();
        $enctypeAttribute = $enctype ? ' enctype="' . $enctype . '"' : '';
        $form = '<form action="' . htmlspecialchars($action) . '" method="' . $method . '"' . $enctypeAttribute . '>';
        $csrfField = '<input type="hidden" name="_csrf_token" value="' . $csrf->getToken() . '">';
        return $form . $csrfField;
    }

    public static function closeForm() {
        return '</form>';
    }

    public static function method($method): string
    {
        return "<input type='hidden' value='{$method}' name='_method' />";
    }

    public static function hidden($name, $value): string
    {
        return "<input type='hidden' value='{$value}' id='{$name}' name='{$name}' />";
    }

    public static function processAttrs($attrs): string
    {
        $html = "";
        foreach ($attrs as $key => $value) {
            $html .= " {$key}='{$value}'";
        }
        return $html;
    }
}