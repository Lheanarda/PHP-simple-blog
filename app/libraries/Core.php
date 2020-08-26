<?php
/*
 *App Core Class
 *Creates URL & Loads core controller
 *URL FORMAT -/controller/method/params
 */

 // Report all errors except E_NOTICE
// error_reporting(E_ALL & ~E_NOTICE);

class Core{
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct(){
        // print_r ($this->getUrl());
        $url = $this->getUrl();

        //Look in controllers for first value
        if(count($url)>0){
            if(file_exists('../app/controllers/'.ucwords($url[0]).'.php')){
                //If Exists, set as controller
                $this->currentController = ucwords($url[0]);
                //Unset 0 Index
                unset($url[0]);
            }   
        }
        
        //Require the controller
        require_once '../app/controllers/'.$this->currentController.'.php';
        //Instatiate controller class
        $this->currentController = new $this->currentController;

        //Check for second part of url
        if(isset($url[1])){
            //Check to see if method exist in controller
            if(method_exists($this->currentController, $url[1])){
                $this->currentMethod = $url[1];
                //Unset 1 index
                unset($url[1]);
                
            }
        }

        //Get Params
        $this->params = $url ? array_values($url) : [];

        //Call a callback with array of params
        call_user_func_array([$this->currentController, $this->currentMethod],$this->params);


    }

    public function getUrl(){
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'],'/');
            $url = filter_var($url,FILTER_SANITIZE_URL);
            $url = explode('/',$url);
            return $url;
        }else{
            return [];
        }
    }
}