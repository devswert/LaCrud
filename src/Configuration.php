<?php namespace DevSwert\LaCrud;

final class Configuration {

    /**
     * Title of template.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Subtitle of template, this is optional 
     * an depends of theme used.
     *
     * @var string
     */
    protected $subtitle = '';

    /**
     * The name of Theme used by default.
     *
     * @var string
     */
    protected $theme = null;

    /**
     * Array of key-value for custom information
     * on footer template.
     *
     * @var array
     */
    protected $moreDataFooter = array();

    /**
     * Array of key-value for custom information
     * on header template.
     *
     * @var array
     */
    protected $moreDataHeader = array();

    /**
     * Get/Set fot title
     *
     * @param sting / null
     * @return $theme
     */
    public function title($title = null){
        if(is_null($title))
            return $this->title;
        else
            $this->title = $title;
    }

    /**
     * Get/Set fot subtitle
     *
     * @param sting / null
     * @return $theme
     */
    public function subtitle($subtitle = null){
        if(is_null($subtitle))
            return $this->subtitle;
        else
            $this->subtitle = $subtitle;
    }

    /**
     * Get/Set fot theme
     *
     * @param sting / null
     * @return $theme
     */
    public function theme($name = null){
        if(is_null($name))
            return $this->theme;
        else
            $this->theme = $name;
    }

    /**
     * Get/Set fot Footer more Info
     *
     * @param array
     * @return $moreDataFooter
     */
    public function moreDataFooter($data = array()){
        if(count($data) > 0)
            $this->moreDataFooter = $data;
        else
            return $this->moreDataFooter;
    }

    /**
     * Get/Set fot Header more Info
     *
     * @param array
     * @return $moreDataHeader
     */
    public function moreDataHeader($data = array()){
        if(count($data) > 0)
            $this->moreDataHeader = $data;
        else
            return $this->moreDataHeader;
    }

}