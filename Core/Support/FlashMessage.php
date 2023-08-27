<?php

namespace App\Core\Support;

use App\Core\Application;


class FlashMessage
{
    public static function bootstrap_alert()
    {
        if (Application::$app->session->getFlash('success')) {
            return self::bootstrap_success(Application::$app->session->getFlash('success'));
        }

        if (Application::$app->session->getFlash('error')) {
            return self::bootstrap_error(Application::$app->session->getFlash('error'));
        }

        return '';
    }

    private static function bootstrap_success($msg)
    {
        echo "
            <div class='alert alert-success alert-dismissible fade show mt-3 mx-2 shadow-lg text-uppercase text-center' role='alert' id='alert-message' style='z-index: 99999;'>
                <div>{$msg}</div>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        ";
    }

    private static function bootstrap_error($msg)
    {
        echo "
            <div class='alert alert-error alert-dismissible fade show mt-3 mx-2 shadow-lg text-uppercase text-center' role='alert' style='z-index: 99999;'>
                <div>{$msg}</div>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        ";
    }

    private static function bootstrap_info($msg)
    {
        echo "
            <div class='alert alert-error alert-dismissible fade show mt-3 mx-2 shadow-lg text-uppercase text-center' role='alert' style='z-index: 99999;'>
                <div>{$msg}</div>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        ";
    }
}