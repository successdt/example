<?php

/**
 * @author duythanh
 * @copyright 2012
 */
class CometsController extends AppController{
    var $name = "Comets";
    var $helpers = array(
        "Html",
        "Form");
    function index(){
        $twitter = new Twitter;
        //$twitter -> openAuthorizationUrl();
        $token = $twitter -> getRequestToken();
        debug($token);
    }
    function backend(){
        
        $notif=$this->Comet->find('all');
        $this->set('notif',$notif);
        //debug($notif);
    }
    function select(){
        $this->layout='';
    }
    function data(){
        $this->layout='';
    }
}
?>