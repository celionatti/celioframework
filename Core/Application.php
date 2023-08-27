<?php

namespace App\Core;

use Exception;
use App\Core\Config;
use App\Core\Request;
use App\Core\Session;
use App\models\Users;
use App\Core\Response;
use App\Core\Container;
use App\Core\Database\Database;
use App\Core\Support\Csrf;

class Application
{
    public Session $session;
    public Request $request;
    public Response $response;
    public Router $router;
    public Database $database;
    public Csrf $csrf;

    protected static Container $container;
    private static Application $instance;
    public static Application $app;
    public $currentUser = null;
    public $url;
    public $queryParams;
    public $currentPage;
    public $queryString;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        self::$app = $this;
        $this->session = new Session();
        self::$container = new Container();
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
        $this->csrf = new Csrf();

        $this->configs();
        $this->definitions_calls();
        $this->currentUser = Users::getCurrentUser();
    }

    /**
     * @throws Exception
     */
    private function definitions_calls(): void
    {
        self::$container->bind(Database::class, function () {
            $config = require base_path('configs/config.php');

            return new Database($config['database']);
        });

        $this->database = static::resolve(Database::class);
    }

    private function configs(): void
    {
        ini_set('default_charset', 'UTF-8');

        $minPhpVersion = '7.4';
        if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
            $message = sprintf(
                'Your PHP version must be %s or higher to run Laragon Framework. Current version: %s',
                $minPhpVersion,
                PHP_VERSION
            );

            exit($message);
        }
        $this->checkExtensions();

        $this->url = $_SERVER['REQUEST_URI'];

        // Extract the query string from the URL
        $queryString = parse_url($this->url, PHP_URL_QUERY);

        // Remove the query string from the URL
        $this->url = preg_replace('/(\?.+)/', '', $this->url);

        // Process the query string if it exists
        if ($queryString) {
            parse_str($queryString, $queryParams);

            $this->queryParams = $queryParams;
            // Access the query parameters
            // For example, if the URL is "example.com?page=2&sort=asc"
            // $queryParams will be ['page' => 2, 'sort' => 'asc']
            // You can use $queryParams['page'] to get the value of 'page' parameter
            // and $queryParams['sort'] to get the value of 'sort' parameter
        }

        if (!Config::get('domain')) {
            $this->url = str_replace(Config::get('domain'), '', $this->url);
        } else {
            $this->url = ltrim($this->url, '/');
        }



        $this->currentPage = $this->url;
        $this->queryString = $queryString;
    }

    private function checkExtensions(): void
    {
        $required_extensions = [
            'gd',
            'mysqli',
            'pdo_mysql',
            'pdo_sqlite',
            'curl',
            'fileinfo',
            'intl',
            'exif',
            'mbstring',
        ];

        $not_loaded = [];

        foreach ($required_extensions as $ext) {

            if (!extension_loaded($ext)) {
                $not_loaded[] = $ext;
            }
        }

        if (!empty($not_loaded)) {
            echo "Please load the following extensions in your php.ini file: <br>" . implode("<br>", $not_loaded);
            die;
        }
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (Exception $e) {
            abort();
        }
    }

    public static function init(): static
    {
        if (!isset(self::$instance)) {
            self::$instance = new Application();
        }
        return self::$instance;
    }

    public static function setContainer($container): void
    {
        static::$container = $container;
    }

    public static function container(): Container
    {
        return static::$container;
    }

    public static function bind(string $key, $resolver): void
    {
        static::container()->bind($key, $resolver);
    }

    /**
     * @throws Exception
     */
    public static function resolve($key)
    {
        return static::container()->resolve($key);
    }
}
