<?php namespace DevSwert\LaCrud;

final class Configuration {

    protected $title = '';
    protected $subtitle = '';
    protected $userInfo;
    protected $theme = null;
    protected $moreDataFooter = array();
    protected $moreDataHeader = array();

    //Nombre y Title de la app
    public function title($title = null){
        if(is_null($title))
            return $this->title;
        else
            $this->title = $title;
    }

    public function subtitle($subtitle = null){
        if(is_null($subtitle))
            return $this->subtitle;
        else
            $this->subtitle = $subtitle;
    }

    public function userInfo($data = null){
        if(is_null($data))
            return $this->userInfo;
        else
            $this->userInfo = $data;
    }

    public function theme($name = null){
        if(is_null($name))
            return $this->theme;
        else
            $this->theme = $name;
    }

    public function moreDataFooter($data = array()){
        if(count($data) > 0)
            $this->moreDataFooter = $data;
        else
            return $this->moreDataFooter;
    }

    public function moreDataHeader($data = array()){
        if(count($data) > 0)
            $this->moreDataHeader = $data;
        else
            return $this->moreDataHeader;
    }

}