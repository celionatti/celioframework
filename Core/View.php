<?php

namespace App\Core;

use Exception;

class View
{
    private string $_title = '';
    private string $_header = 'Dashboard';
    private string $_metaTitle = '';
    private string $_metaDescription = '';
    private string $_metaKeywords = '';
    private string $_author = '';
    private array $_content = [];
    private $_currentContent;
    private $_buffer;
    private string $_layout;
    private string $_defaultViewPath;

    public function __construct($path = '')
    {
        $this->_defaultViewPath = $path;
        $this->_title = Config::get('title');
    }

    public function setLayout($layout): void
    {
        $this->_layout = $layout;
    }

    public function setTitle($title): void
    {
        $this->_title = $title;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Get the value of _header
     */ 
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * Set the value of _header
     *
     * @return  self
     */ 
    public function setHeader($_header)
    {
        $this->_header = $_header;

        return $this;
    }


    /**
     * @throws Exception
     */
    public function render($path = '', $params = []): void
    {
        if (empty($path)) {
            $path = $this->_defaultViewPath;
        }

        foreach ($params as $key => $value) {
            $$key = $value;
        }

        $layoutPath = base_path('templates/layouts/' . $this->_layout . '.php');
        $fullPath = base_path('templates' . DIRECTORY_SEPARATOR . $path . '.php');

        if (!file_exists($fullPath)) {
            throw new Exception("The view \"{$path}\" does not exist.");
        }
        if (!file_exists($layoutPath)) {
            throw new Exception("The layout \"{$this->_layout}\" does not exist.");
        }
        require($fullPath);
        require($layoutPath);
    }

    /**
     * @throws Exception
     */
    public function start($key): void
    {
        if (empty($key)) {
            throw new Exception("Your start method requires a valid key.");
        }
        $this->_buffer = $key;
        ob_start();
    }

    /**
     * @throws Exception
     */
    public function end(): void
    {
        if (empty($this->_buffer)) {
            throw new Exception("You must first run the start method.");
        }
        $this->_content[$this->_buffer] = ob_get_clean();
        $this->_buffer = null;
    }

    public function content($key): void
    {
        if (array_key_exists($key, $this->_content)) {
            echo $this->_content[$key];
        } else {
            echo '';
        }
    }

    public function partial($path): void
    {
        $fullPath = base_path('templates' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . $path . '.php');
        if (file_exists($fullPath)) {
            require($fullPath);
        }
    }

}