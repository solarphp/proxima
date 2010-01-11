<?php
/**
 * 
 * Generic model application for members.
 * 
 */
class Proxima_App_Members extends Proxima_Controller_Bread {
    
    /**
     * 
     * The main model name.
     * 
     * @var string
     * 
     */
    public $model_name = 'members';
    
    /**
     * 
     * The record columns to show for the item.
     * 
     * @var array
     * 
     */
    public $item_cols = array('handle', 'moniker', 'uri');
    
    /**
     * 
     * The record columns to show for the list.
     * 
     * @var array
     * 
     */
    public $list_cols = array('handle', 'moniker', 'uri');
    
    /**
     * 
     * Use only these columns for the form in the given action, and when 
     * loading record data for that action.
     * 
     * When empty, uses all columns.
     * 
     * The format is `'action' => array('col', 'col', 'col' ...)`.
     * 
     * @var array
     * 
     */
    protected $_form_cols = array(
        'register' => array('handle', 'passwd_new', 'passwd_confirm', 'email'),
        'edit'     => array('moniker', 'email', 'uri'),
        'forgot'   => array('email'),
        'reset'    => array('passwd_new', 'passwd_confirm'),
        'passwd'   => array('passwd_new', 'passwd_confirm'),
    );
    
    /**
     * 
     * The columns to use for searches.
     * 
     * @var array
     * 
     */
    protected $_search_cols = array('handle', 'moniker');

    /**
     * 
     * A non-form feedback message.
     * 
     * @var string
     * 
     */
    public $feedback;
    
    /**
     * 
     * Displays a member record.
     * 
     * This decorates the parent method to allow it to catch flash messages
     * from other actions.
     * 
     * @param int $id The member ID.
     * 
     * @return void
     * 
     */
    public function actionRead($id = null)
    {
        // catch flash indicating a successful add
        if ($this->_session->getFlash('success_added')) {
            $this->feedback = 'SUCCESS_ADDED';
        }
        
        // perform the rest of the action
        return parent::actionRead($id);
    }
    
    /**
     * 
     * Overrides the parent method to redirect the user to the "register"
     * page.
     * 
     * We keep the "add" action because the boilerplate views use the "add"
     * link; it's easier to override here than to rewrite all the views to
     * change one link.
     * 
     * @return void
     * 
     */
    public function actionAdd()
    {
        // 301 Moved Permanently
        return $this->_redirect("/{$this->_controller}/register", 301);
    }
    
    /**
     * 
     * Allows the authenticated member to change their password.
     * 
     * @return void
     * 
     */
    public function actionPasswd()
    {
        // get the member record
        $this->item = $this->_model->members->fetch($this->user->auth->uid);
        
        // check if user is allowed to be here
        if (! $this->_isUserAllowed()) {
            return;
        }
        
        // process a password change?
        if ($this->_isProcess('passwd') && $this->_saveItem()) {
            // save a flash value for the next page
            $this->_session->setFlash('success_passwd', true);
            // redirect to reading using the primary-key value
            $id = $this->item->id;
            return $this->_redirectNoCache("/{$this->_controller}/read/$id");
        }
        
        // done!
        $this->_setFormItem();
    }
    
    /**
     * 
     * Registers a new member with the site.
     * 
     * @return void
     * 
     */
    public function actionRegister()
    {
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            // if user is authenticated already, redirect to member profile
            if ($this->user->auth->isValid()) {
                $action = "{$this->_controller}/read";
                $id = $this->user->auth->uid;
                $this->_redirect("/$action/$id");
            }
            // otherwise we're done
            return;
        }
        
        // process: cancel
        if ($this->_isProcess('cancel')) {
            // forward back to browse
            return $this->_redirect("/{$this->_controller}/browse");
        }
        
        // set a new record
        $this->_setItemNew();
        
        // process: save
        if ($this->_isProcess('save') && $this->_saveItem()) {
            $this->item->sendActivateEmail();
            $this->_view = 'activateSent';
        }
        
        // set the form-building hints for the item
        $this->_setFormItem();
    }
    
    /**
     * 
     * Sends an email to the member's address with a link to reset the 
     * password.
     * 
     * @return void
     * 
     */
    public function actionForgot()
    {
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            
            // if user is logged in, redirect to member profile
            if ($this->user->auth->isValid()) {
                $id = $this->user->auth->uid;
                $this->_redirect("/{$this->_controller}/read/$id");
            }
            
            // otherwise we're done
            return;
        }
        
        // get the POST data using the array name
        $array_name = $this->_model->members->array_name;
        $data = $this->_request->post($array_name, array());
        
        // process: send
        if ($this->_isProcess('send') && ! empty($data['email'])) {
            // find the user record
            $this->item = $this->_model->members->fetchOneByEmail($data['email']);
            // did we find it?
            if ($this->item) {
                // send the email
                $this->item->sendForgotEmail();
                // change the view
                $this->_view = 'forgotSent';
            } else {
                $this->feedback = 'TEXT_EMAIL_NOT_FOUND';
            }
        }
        
        // make sure we have an item to get a form with
        if (! $this->item) {
            $this->_setItemNew();
            $this->_loadItem();
        }
        
        // load from post data, and set form
        $this->_setFormItem();
    }
    
    /**
     * 
     * Given a confirmation hash, allows the user to reset his password
     * without having signed in.
     * 
     * @param string $hash The confirmation hash from the "forgot" email.
     * 
     * @return void
     * 
     */
    public function actionReset($hash = null)
    {
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            // if user is logged in, redirect to member password change
            if ($this->user->auth->isValid()) {
                $this->_redirect("/{$this->_controller}/passwd");
            }
            // otherwise we're done
            return;
        }
        
        // find the member record by confirmation type and hash
        $this->item = $this->_model->members->fetchOneByConfirm(
            Proxima_Model_Members::CONFIRM_TYPE_RESET,
            $hash
        );
        
        // did we find it?
        if (! $this->item) {
            return $this->_error('ERR_NO_SUCH_ITEM');
        }
        
        // unset the confirmation stuff
        $this->item->confirm_type = null;
        $this->item->confirm_hash = null;
        
        // process a password change?
        if ($this->_isProcess('passwd') && $this->_saveItem()) {
            // save a flash value for the next page
            $this->_session->setFlash('success_passwd', true);
            // redirect to reading using the primary-key value
            $id = $this->item->id;
            return $this->_redirectNoCache("/{$this->_controller}/login");
        }
        
        // done!
        $this->_setFormItem();
    }
    
    /**
     * 
     * Given a confirmation hash, activates a new member account.
     * 
     * @param string $hash The confirmation hash from the "activate" email.
     * 
     * @return void
     * 
     */
    public function actionActivate($hash = null)
    {
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            // if user is logged in, redirect to member profile
            if ($this->user->auth->isValid()) {
                $id = $this->user->auth->uid;
                $this->_redirect("/{$this->_controller}/read/$id");
            }
            // otherwise we're done
            return;
        }
        
        // find the member record by confirmation type and hash
        $this->item = $this->_model->members->fetchOneByConfirm(
            Proxima_Model_Members::CONFIRM_TYPE_ACTIVATE,
            $hash
        );
        
        // did we find it?
        if (! $this->item) {
            return $this->_error('ERR_NO_SUCH_ITEM');
        }
        
        // activate the member
        $this->item->activate();
        
        // keep a flash message for it, and redirect to login
        $this->_session->setFlash('success_activate', true);
        $this->_redirect("/{$this->_controller}/login");
    }
    
    /**
     * 
     * Explicit login handling for members.
     * 
     * Normally, Solar_Auth_Adapter::start() will do everything for you when
     * Solar_Auth_Adapter::$allow is true. This method makes it so that even 
     * when $allow is false, you can still process a login request.
     * 
     * @return void
     * 
     */
    public function actionLogin()
    {
        // turn off caching
        $this->_response->setNoCache();
        
        // convenience var
        $auth = $this->user->auth;
        
        // if already logged in, redirect to the profile page
        if ($auth->isValid()) {
            $id = $auth->uid;
            $this->_redirect("/{$this->_controller}/read/{$id}");
        }
        
        // is this a login request?
        if ($auth->isLoginRequest()) {
            // process it; this will honor any redirect in the form vars
            $auth->processLogin();
            
            // did it succeed?
            if ($auth->isValid()) {
                // honor a flash redirect if it exists
                $uri = $this->_session->getFlash('redirect');
                if ($uri) {
                    $this->_redirectNoCache($uri);
                }
                
                // final fallback: redirect to the profile page
                $id = $auth->uid;
                $this->_redirect("/{$this->_controller}/read/{$id}");
            }
        }
        
        // catch flash indicating a successful password change
        if ($this->_session->getFlash('success_passwd')) {
            $this->feedback = 'SUCCESS_PASSWD';
        }
        
        // catch flash indicating a successful account activation
        if ($this->_session->getFlash('success_activate')) {
            $this->feedback = 'SUCCESS_ACTIVATE';
        }
    }
    
    /**
     * 
     * Explicit logout handling for members.
     * 
     * Normally, Solar_Auth_Adapter::start() will do everything for you when
     * Solar_Auth_Adapter::$allow is true. This method makes it so that even 
     * when $allow is false, you can still process a logout request.
     * 
     * @return void
     * 
     */
    public function actionLogout()
    {
        // turn off caching
        $this->_response->setNoCache();
        
        // convenience var
        $auth = $this->user->auth;
        
        // is user already logged out?
        if (! $auth->isValid()) {
            // redirect to login
            $this->_redirectNoCache("/{$this->_controller}/login");
        }
        
        // process the logout; this will honor any redirect in the form vars
        $auth->processLogout();
        
        // if we have a flash redirect, honor it
        $uri = $this->_session->getFlash('redirect');
        if ($uri) {
            $this->_redirectNoCache($uri);
        }
    }
}
