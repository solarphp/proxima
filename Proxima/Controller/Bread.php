<?php
/**
 * 
 * Base page-controller class for models.
 * 
 */
abstract class Proxima_Controller_Bread extends Proxima_Controller_Page
{
    /**
     * 
     * A form for editing a single record.
     * 
     * @var Solar_Form
     * 
     */
    public $form;
    
    /**
     * 
     * A single record.
     * 
     * @var Solar_Sql_Model_Record
     * 
     */
    public $item;
    
    /**
     * 
     * The record columns to show for the item.
     * 
     * @var array
     * 
     */
    public $item_cols = array();
    
    /**
     * 
     * A collection of records.
     * 
     * @var Solar_Sql_Model_Collection
     * 
     */
    public $list;
    
    /**
     * 
     * The record columns to show for the list.
     * 
     * @var array
     * 
     */
    public $list_cols = array();
    
    /**
     * 
     * The name of the main model.
     * 
     * Override this in child classes.
     * 
     * @var string
     * 
     */
    public $model_name;
    
    /**
     * 
     * The current search term, if any.
     * 
     * @var string
     * 
     */
    public $search_term;
    
    /**
     * 
     * The current authenticated user.
     * 
     * @var Solar_User
     * 
     */
    public $user;
    
    /**
     * 
     * The default action when no action is specified.
     * 
     * @var string
     * 
     */
    protected $_action_default = 'browse';
    
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
        'add'  => array(),
        'edit' => array(),
    );
    
    /**
     * 
     * An instance of the model catalog.
     * 
     * @var Solar_Sql_Model_Catalog
     * 
     */
    protected $_model;
    
    /**
     * 
     * The default order to use for lists.
     * 
     * @var string|array
     * 
     */
    protected $_order = null;
    
    /**
     * 
     * Which columns to use when building a search query.
     * 
     * Search will be disabled if there are no columns listed here.
     * 
     * @var array
     * 
     */
    protected $_search_cols = array();
    
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
        $this->_model = Solar_Registry::get('model_catalog');
        $this->user   = Solar_Registry::get('user');
        
        // if model name not preset, use the last part of the class name.
        // e.g., `Vendor_Something_FooBar => foo_bar`.
        if (! $this->model_name) {
            $inflect = Solar_Registry::get('inflect');
            $name = strrchr(get_class($this), '_');
            $this->model_name = strtolower($inflect->camelToUnder($name));
        }
    }
    
    // -----------------------------------------------------------------------
    // 
    // Actions
    // 
    // -----------------------------------------------------------------------
    
    /**
     * 
     * Browse records by page.
     * 
     * @return void
     * 
     */
    public function actionBrowse()
    {
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            return;
        }
        
        // set the collection
        $this->_setList(array(
            'page'        => $this->_query('page', 1),
            'paging'      => $this->_query('paging', 10),
            'count_pages' => true,
        ));
    }
    
    /**
     * 
     * View one record by ID.
     * 
     * @param int $id The record ID to view.
     * 
     * @return void
     * 
     */
    public function actionRead($id = null)
    {
        // need an id
        if (! $id) {
            return $this->_error('ERR_NO_ID_SPECIFIED');
        }
        
        // set the record; does it exist?
        if (! $this->_setItem($id)) {
            return $this->_error('ERR_NO_SUCH_ITEM');
        }
        
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            return;
        }
    }
    
    /**
     * 
     * Edit a record by ID.
     * 
     * @param int $id The record id.
     * 
     * @return void
     * 
     */
    public function actionEdit($id = null)
    {
        // need an id
        if (! $id) {
            return $this->_error('ERR_NO_ID_SPECIFIED');
        }
        
        // process: cancel
        if ($this->_isProcess('cancel')) {
            // forward back to reading
            return $this->_redirect("/{$this->_controller}/read/$id");
        }
        
        // process: delete
        if ($this->_isProcess('delete')) {
            // forward to the delete method for confirmation
            return $this->_redirect("/{$this->_controller}/delete/$id");
        }
        
        // set the record; does it exist?
        if (! $this->_setItem($id)) {
            return $this->_error('ERR_NO_SUCH_ITEM');
        }
        
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            return;
        }
        
        // process: save
        if ($this->_isProcess('save')) {
            $this->_saveItem();
        }
        
        // set the form-building hints for the record
        $this->_setFormItem();
        
        // catch flash indicating a successful add
        if ($this->_session->getFlash('success_added')) {
            $this->form->setStatus(Solar_Form::STATUS_SUCCESS);
            $this->form->feedback = $this->locale('SUCCESS_ADDED');
        }
        
        // turn off http caching
        $this->_response->setNoCache();
    }
    
    /**
     * 
     * Add a new record.
     * 
     * @return void
     * 
     */
    public function actionAdd()
    {
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
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
            // save a flash value for the next page
            $this->_session->setFlash('success_added', true);
            // redirect to editing using the primary-key value
            $id = $this->item->getPrimaryVal();
            return $this->_redirectNoCache("/{$this->_controller}/edit/$id");
        }
        
        // set the form-building hints for the item
        $this->_setFormItem();
        
        // turn off http caching
        $this->_response->setNoCache();
    }
    
    /**
     * 
     * Delete a record by ID; asks for confirmation before actually deleting.
     * 
     * @param int $id The record ID.
     * 
     * @return void
     * 
     */
    public function actionDelete($id = null)
    {
        // need an id
        if (! $id) {
            return $this->_error('ERR_NO_ID_SPECIFIED');
        }
        
        // set the record; does it exist?
        if (! $this->_setItem($id)) {
            return $this->_error('ERR_NO_SUCH_ITEM');
        }
        
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            return;
        }
        
        // process: delete confirm
        if ($this->_isProcess('delete_confirm')) {
            // delete it
            $this->item->delete();
            // redirect to browse
            $this->_redirectNoCache("/{$this->_controller}");
        }
        
        // turn off http caching
        $this->_response->setNoCache();
    }
    
    /**
     * 
     * Search records for a term.
     * 
     * @return void
     * 
     */
    public function actionSearch()
    {
        // make sure search is enabled
        if (! $this->_search_cols) {
            return $this->_error('ERR_SEARCH_NOT_ENABLED');
        }
        
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            return;
        }
        
        // set the form, which also populates the values
        $this->_setFormSearch();
        
        // should we do a search?
        $this->search_term = $this->form->getValue('q');
        if (! $this->search_term) {
            return;
        }
        
        // force the term string to lower-case, and replace spaces with
        // wildcards, to allow a very generous result set.
        $term = strtolower($this->search_term);
        $term = str_replace(' ', '%', $term);
        
        // build the where clause and bind values
        $where = array();
        $bind  = array();
        foreach ($this->_search_cols as $col) {
            // force lower-case comparison
            $where[]    = "LOWER({$this->model_name}.{$col}) LIKE :$col";
            $bind[$col] = "%{$term}%";
        }
        
        // build the fetch params
        $fetch = array(
            'where'       => implode(' OR ', $where),
            'bind'        => $bind,
            'page'        => $this->_query('page', 1),
            'paging'      => $this->_query('paging', 10),
            'count_pages' => true,
        );
        
        // set the collection
        $this->_setList($fetch);
    }
    
    // -----------------------------------------------------------------------
    // 
    // Support methods.
    // 
    // -----------------------------------------------------------------------
    
    /**
     * 
     * Checks if user is allowed access to this class, the current action,
     * and the current item.
     * 
     * @return bool
     * 
     */
    protected function _isUserAllowed()
    {
        $allowed = $this->user->access->isAllowed(
            get_class($this),
            $this->_action,
            $this->item
        );
        
        if ($allowed) {
            return true;
        }
        
        // not allowed, but why?
        if ($this->user->auth->isValid()) {
            $this->_error('ERR_NOT_ALLOWED_ACCESS');
        } else {
            $this->_error('ERR_NOT_LOGGED_IN');
        }
        
        // _error() set status code 500, but 403 (Forbidden) is better here
        $this->_response->setStatusCode(403);
        return false;
    }
    
    /**
     * 
     * Sets $this->item from a primary key value.
     * 
     * @param int $id The primary key for the record.
     * 
     * @return Solar_Sql_Model_Record A record on success, or null on failure.
     * 
     */
    protected function _setItem($id)
    {
        $model = $this->_model->getModel($this->model_name);
        $this->item = $model->fetch($id);
        return $this->item;
    }
    
    /**
     * 
     * Sets $this->item as a new record.
     * 
     * @return Solar_Sql_Model_Record A new record.
     * 
     */
    protected function _setItemNew()
    {
        $model = $this->_model->getModel($this->model_name);
        $this->item = $model->fetchNew();
        return $this->item;
    }
    
    /**
     * 
     * Sets $this->list based on fetch parameters.
     * 
     * @param Solar_Sql_Model_Params_Fetch|array The fetch parameters.
     * 
     * @return Solar_Sql_Model_Collection A collection on success, or an empty
     * array on failure.
     * 
     */
    protected function _setList($fetch = null)
    {
        if ($this->_order && empty($fetch['order'])) {
            $fetch['order'] = $this->_order;
        }
        
        $model = $this->_model->getModel($this->model_name);
        $this->list = $model->fetchAll($fetch);
        return $this->list;
    }
    
    /**
     * 
     * Loads $this->item with data from the current request POST data, then
     * saves it.
     * 
     * @return bool True if the save was a success, false if it was a failure.
     * 
     */
    protected function _saveItem()
    {
        $this->_loadItem();
        return $this->item->save();
    }
    
    /**
     * 
     * Loads $this->item with data from the current request POST data.
     * 
     * @return void
     * 
     */
    protected function _loadItem()
    {
        $model      = $this->_model->getModel($this->model_name);
        $array_name = $model->array_name;
        $data       = $this->_request->post($array_name, array());
        
        $cols = array();
        if (! empty($this->_form_cols[$this->_action])) {
            $cols = $this->_form_cols[$this->_action];
        }
        
        $this->item->load($data, $cols);
    }
    
    /**
     * 
     * Sets $this->form using $this->item and $this->_form_cols for the form
     * hints.
     * 
     * @return Solar_Form
     * 
     */
    protected function _setFormItem()
    {
        $cols = array();
        if (! empty($this->_form_cols[$this->_action])) {
            $cols = $this->_form_cols[$this->_action];
        }
        
        $this->form = $this->item->newForm($cols);
        return $this->form;
    }
    
    /**
     * 
     * Sets $this->form as a search form.
     * 
     * @return Solar_Form
     * 
     */
    protected function _setFormSearch()
    {
        $this->form = Solar::factory('Solar_Form');
        
        $uri = Solar::factory('Solar_Uri_Action', array(
            'uri' => "/{$this->_controller}/search",
        ));
        
        $this->form->attribs['action'] = $uri->get();
        $this->form->attribs['method'] = "get";
        
        $this->form->setElement('q', array(
            'type'  => 'text',
        ));
        
        $this->form->populate();
        
        return $this->form;
    }
    
    /**
     * 
     * Sets the <title> string in the view before rendering.
     * 
     * @return Solar_Form
     * 
     */
    protected function _preRender()
    {
        parent::_preRender();
        
        $title = ucwords($this->_controller)
               . ' : '
               . ucwords($this->_action);
               
        $this->_view_object->head()->setTitle($title);
    }
}
