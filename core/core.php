<?php

class Core {

    protected $currentController = 'AuthController';  
    protected $currentMethod = 'login';               
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        
        if (isset($url[0]) && file_exists(__DIR__ . '/../controllers/' . ucwords($url[0]) . '.php')) {
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
        }

        
        require_once __DIR__ . '/../controllers/' . $this->currentController . '.php';

        
        $this->currentController = new $this->currentController();

       
        if (isset($url[1])) {
            
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        } elseif (isset($url[0])) {
            
            if (method_exists($this->currentController, $url[0])) {
                $this->currentMethod = $url[0];
                unset($url[0]);
            }
        }

       
        $this->params = $url ? array_values($url) : [];

        
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['action'])) {
            $url = rtrim($_GET['action'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}