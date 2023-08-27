<?php

use App\Core\Config;
use JetBrains\PhpStorm\NoReturn;
use App\Core\Support\Helpers\Image;

function dd($value): void
{
    echo "<pre>";
    echo "<div style='background-color:#155263; color:#fff; margin: 5px; padding:5px;border:3px solid; border-color:red;'>";
    echo "<h2 style='border:3px solid; border-color:teal; padding:5px; text-align:center;font-weight:bold;font-weight: bold;
    text-transform: uppercase;'>";
    echo "Error Type: Dump and die";
    echo "</h2>";
    var_dump($value);
    echo "</div>";
    echo "</pre>";

    die;
}

function generate_csrf_token()
{
    
}

function urlIs($value)
{
    return $_SERVER['REQUEST_URI'] === $value;
}

function query_string($params, $value)
{
    if (isset($_GET[$params])) {
        return $_GET[$params] === $value;
    }
    return false;
}

/**
 * @throws Exception
 */
function authorize($conditions, $status = \App\Core\Response::FORBIDDEN): void
{
    if (!$conditions) {
        abort($status);
    }
}

function base_path($path): string
{
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . $path;
}

function assets_path($path): string
{
    return Config::get('domain') . 'assets' . DIRECTORY_SEPARATOR . $path;
}

function console_logger($message): void
{
    echo "[" . date("Y-m-d H:i:s") . "] - " . $message . PHP_EOL;
}

#[NoReturn] function redirect($uri): void
{
    http_response_code(\App\Core\Response::FOUND);
    if (!headers_sent()) {
        header("Location: $uri");
    } else {
        echo '<script type="text/javascript">';
        echo 'window.location.href = "' . $uri . '"';
        echo '</script>';
        echo '<script>';
        echo '<meta http-equiv="refresh" content="0;url=' . $uri . '" />';
        echo '</script>';
    }
    exit();
}

function last_uri(): void
{
    if (!headers_sent()) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        die();
    }
}

/**
 * @throws Exception
 */
function view($path, $attributes = [], $layout = 'default'): void
{
    $view = new \App\Core\View();
    $view->setLayout($layout);
    $view->render($path, $attributes);
}

/**
 * @throws Exception
 */
#[NoReturn] function abort($code = \App\Core\Response::NOT_FOUND, $attributes = []): void
{
    http_response_code($code);

    view("errors/{$code}", $attributes);
    die();
}

function get_pagination_vars(): array
{
    $page_number = $_GET['page'] ?? 1;
    $page_number = empty($page_number) ? 1 : (int) $page_number;
    $page_number = max($page_number, 1);

    $current_link = $_GET['url'] ?? 'home';
    $current_link = "/" . $current_link;
    $query_string = "";
    foreach ($_GET as $key => $value) {
        if ($key != 'url') {
            $query_string .= "&" . $key . "=" . $value;
        }
    }

    if (!str_contains($query_string, "page=")) {
        $query_string .= "&page=" . $page_number;
    }

    $query_string = trim($query_string, "&");
    $current_link .= "?" . $query_string;

    $current_link = preg_replace("/page=.*/", "page=" . $page_number, $current_link);
    $next_link = preg_replace("/page=.*/", "page=" . ($page_number + 1), $current_link);
    $first_link = preg_replace("/page=.*/", "page=1", $current_link);
    $prev_page_number = $page_number < 2 ? 1 : $page_number - 1;
    $prev_link = preg_replace("/page=.*/", "page=" . $prev_page_number, $current_link);
    return [
        'current_link' => $current_link,
        'next_link' => $next_link,
        'prev_link' => $prev_link,
        'first_link' => $first_link,
        'page_number' => $page_number,
    ];
}

function get_image(mixed $file = '', string $type = 'post'): string
{
    $file = $file ?? '';

    if (file_exists($file)) {
        return Config::get('domain') . $file;
    } else {
        if (!empty($file)) {
            return $file;
        }
    }

    if ($type == 'user') {
        return assets_path("/img/user.webp");
    } elseif ($type == 'setting') {
        return assets_path("/img/setting.png");
    } else {
        return assets_path("/img/no_image.jpg");
    }
}

function remove_images_from_content($content, $folder = 'uploads/articles/posts/')
{

    preg_match_all("/&lt;img[^ &gt;]+/", $content, $matches);

    if (is_array($matches[0]) && count($matches[0]) > 0) {
        foreach ($matches[0] as $img) {

            if (!strstr($img, "data:")) {
                continue;
            }

            preg_match('/src=&quot;[^&quot;]+/', $img, $match);
            $parts = explode("base64,", $match[0]);

            preg_match('/data-filename=&quot;[^&quot;]+/', $img, $file_match);

            $filename = $folder . str_replace('data-filename="', "", $file_match[0]);
            dd($filename);
            $image = new Image();
            $image->resize($filename); // I add this.

            file_put_contents($filename, base64_decode($parts[1]));
            $content = str_replace($match[0], 'src="' . $filename, $content);
        }
    }
    return $content;
}

function add_root_to_images($content)
{

    preg_match_all("/<img[^>]+/", $content, $matches);

    if (is_array($matches[0]) && count($matches[0]) > 0) {
        foreach ($matches[0] as $img) {

            preg_match('/src="[^"]+/', $img, $match);
            $new_img = str_replace('src="', 'src="' . "/", $img);
            $content = str_replace($img, $new_img, $content);
        }
    }
    return $content;
}

function remove_root_from_content($content)
{

    $content = str_replace(Config::get('domain'), "", $content);

    return $content;
}

function generateMetaKeywords($title, $content)
{
    // Convert the title and content to lowercase
    $title = strtolower($title);
    $content = strtolower($content);

    // Remove any HTML tags from the title and content
    $title = strip_tags($title);
    $content = strip_tags($content);

    // Remove any punctuation marks from the title and content
    $title = preg_replace("/[^a-zA-Z0-9\s]/", "", $title);
    $content = preg_replace("/[^a-zA-Z0-9\s]/", "", $content);

    // Split the title and content into individual words
    $titleWords = explode(" ", $title);
    $contentWords = explode(" ", $content);

    // Combine the words from title and content
    $keywords = array_merge($titleWords, $contentWords);

    // Remove any empty elements and duplicates from the keywords array
    $keywords = array_filter($keywords);
    $keywords = array_unique($keywords);

    // Remove common words that are not useful as keywords
    $commonWords = array('a', 'an', 'the', 'and', 'or', 'of', 'in', 'on', 'at');
    $keywords = array_diff($keywords, $commonWords);

    // Limit the number of keywords to a certain maximum
    $maxKeywords = 10;
    $keywords = array_slice($keywords, 0, $maxKeywords);

    // Convert the keywords array back to a string
    $keywords = implode(", ", $keywords);

    // Return the generated meta keywords
    return $keywords;
}

function generateMetaTitle($title, $content)
{
    // Convert the title and content to lowercase
    $title = strtolower($title);
    $content = strtolower($content);

    // Remove any HTML tags from the title and content
    $title = strip_tags($title);
    $content = strip_tags($content);

    // Remove any punctuation marks from the title and content
    $title = preg_replace("/[^a-zA-Z0-9\s]/", "", $title);
    $content = preg_replace("/[^a-zA-Z0-9\s]/", "", $content);

    // Combine the title and content
    $metaTitle = $title . " - " . $content;

    // Limit the length of the meta title
    $maxLength = 60;
    if (strlen($metaTitle) > $maxLength) {
        $metaTitle = substr($metaTitle, 0, $maxLength - 3) . '...';
    }

    // Return the generated meta title
    return $metaTitle;
}

function generateMetaDescription($content, $maxLength)
{
    // Convert the content to lowercase
    $content = strtolower($content);

    // Remove any HTML tags from the content
    $content = strip_tags($content);

    // Trim the content to the desired maximum length
    $content = trim($content);
    if (strlen($content) > $maxLength) {
        $content = substr($content, 0, $maxLength - 3) . '...';
    }

    // Return the generated meta description
    return $content;
}
