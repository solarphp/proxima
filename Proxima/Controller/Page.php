<?php
/**
 * 
 * Base page-controller class.
 * 
 */
abstract class Proxima_Controller_Page extends Solar_Controller_Page
{
    /**
     * 
     * The current authenticated user.
     * 
     * @var Solar_User
     * 
     */
    public $user;
    
    public $layout_head = array(
        'title'  => null,
        'base'   => null,
        'meta'   => array(),
        'style'  => array(),
        'script' => array(),
        'link'   => array(),
    );
    
    public $layout_type = 'navtop-localright';
    
    public $layout_nav = array();
    
    public $layout_nav_active = null;
    
    public $layout_local = array();
    
    public $layout_local_active = null;
    
    protected $_layout_default = 'cssfw';
    
    /**
     * 
     * Setup logic to register and retain a model catalog.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        // parent logic
        parent::_setup();
        
        // set properties from registry
        $this->user   = Solar_Registry::get('user');
    }
    
    protected function _preRender()
    {
        parent::_preRender();
        if (! $this->layout_head['title']) {
            $this->layout_head['title'] = ucfirst($this->_action)
                                        . ' '
                                        . ucfirst($this->_controller);
        }
    }
}
