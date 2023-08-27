<?php

namespace App\Core\Support;

use App\Core\Config;
use App\Core\Application;


class Pagination
{
    public static function bootstrap_prev_next($prevPage, $nextPage)
    {
        $prevActive = !$prevPage ? "disabled" : "";
        $nextActive = !$nextPage ? "disabled" : "";

        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        $baseURL = Config::get('domain') . Application::$app->currentPage;

        // Remove the existing 'page' parameter from the query string
        $queryParams = Application::$app->queryParams;
        unset($queryParams['page']);

        $queryString = !empty($queryParams) ? http_build_query($queryParams) : '';

        if (!empty($queryString)) {
            $baseURL .= '?' . $queryString;
        }

        // Add the appropriate character (? or &) based on whether there was a query string
        $pageSeparator = (strpos($baseURL, '?') === false) ? '?' : '&';

        $prevPageLink = "";
        $nextPageLink = "";

        if ($prevPage) {
            $prevPageLink = $baseURL . $pageSeparator . "page=" . ($currentPage - 1);
        }

        if ($nextPage) {
            // Prevent 'page' parameter from exceeding the last available page
            $nextPageValue = $currentPage + 1;
            if ($nextPageValue <= $nextPage) {
                $nextPageLink = $baseURL . $pageSeparator . "page=" . $nextPageValue;
            }
        }

        echo "
            <nav aria-label='Pagination'>
                <ul class='d-flex justify-content-evenly align-items-center my-1 pagination'>
                    <li class='page-item $prevActive' aria-current='page'>
                        <a class='page-link'
                            href='$prevPageLink'>Prev</a>
                    </li>
                    <li class='page-item $nextActive' aria-current='page'>
                        <a class='page-link'
                            href='$nextPageLink'>Next</a>
                    </li>
                </ul>
            </nav>
        ";
    }

    public static function bootstrap_quiz_next($name, $task_id, $user_id)
    {
        $link = Config::get('domain') . $name . "?task_id=" . $task_id . "&user_id=" . $user_id . "&submit=true";
        echo "
            <button type='submit' class='btn btn-sm btn-primary w-100 mx-2'>Next</button>
        ";
    }
}
