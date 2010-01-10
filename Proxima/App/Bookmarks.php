<?php
/**
 * 
 * Generic model application for bookmarks.
 * 
 */
class Proxima_App_Bookmarks extends Proxima_Controller_Bread
{
    /**
     * 
     * The main model name.
     * 
     * @var string
     * 
     */
    public $model_name = 'bookmarks';
    
    /**
     * 
     * The record columns to show for the item.
     * 
     * @var array
     * 
     */
    public $item_cols = array('subj', 'uri', 'created', 'member_handle', 'tags_as_string');
    
    /**
     * 
     * The record columns to show for the list.
     * 
     * @var array
     * 
     */
    public $list_cols = array('subj', 'uri', 'created', 'member_handle', 'tags_as_string');
    
    // which member bookmarks are being shown
    public $member;
    
    // which tags were supplied in the URI
    public $tags;
    
    // the list of all order options available
    public $order_options = array(
        'created_asc'  => 'TEXT_ORDER_CREATED_ASC',
        'created_desc' => 'TEXT_ORDER_CREATED_DESC',
        'pos_asc'      => 'TEXT_ORDER_POS_ASC',
        'pos_desc'     => 'TEXT_ORDER_POS_DESC',
        'subj_asc'     => 'TEXT_ORDER_SUBJ_ASC',
        'subj_desc'    => 'TEXT_ORDER_SUBJ_DESC',
    );
    
    public $order_active;
    
    // the list of all tags in the system
    public $tag_options;
    
    protected $_action_format = array(
        'browse' => array('rss'),
        'member' => array('rss'),
        'tags' => array('rss'),
    );
    
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
        'add'  => array('uri', 'subj', 'summ', 'pos', 'tags_as_string'),
        'edit' => array('uri', 'subj', 'summ', 'pos', 'tags_as_string'),
    );
    
    /**
     * 
     * The columns to use for searches.
     * 
     * @var array
     * 
     */
    protected $_search_cols = array('subj', 'body', 'uri');
    
    protected function _setOrder()
    {
        $key = strtolower($this->_query('order', 'created_desc'));
        if (! array_key_exists($key, $this->order_options)) {
            $key = 'created_desc';
        }
        
        // retain the order key being used for the view
        $this->order_active = $key;
        
        // 'created_desc' => 'bookmarks.created desc'
        $this->_order = "bookmarks." . str_replace('_', ' ', $key);
        
        Solar::dump($this->_order);
        
    }
    
    protected function _preRun()
    {
        parent::_preRun();
        $this->_setOrder();
    }
    
    protected function _postRun()
    {
        parent::_postRun();
        
        // rss feed?
        if ($this->_format == 'rss') {
            // always use the same RSS view, and we're done
            $this->_view = 'feed';
            return;
        }
        
        // not an RSS feed
        $this->tag_options = $this->_model->tags->fetchAllWithCount(array(
            'order' => 'tags.name ASC',
        ));
        
        $this->_setRssLink();
    }
    
    protected function _setRssLink()
    {
        $actions = array('browse', 'member', 'tags');
        if (! in_array($this->_action, $actions)) {
            return;
        }
        
        // get the URI to the current action
        $uri = Solar::factory('Solar_Uri_Action');
        
        // set the format to RSS
        $uri->format = 'rss';
        
        // add a link to the head layout
        $this->layout_head['link'][] = array(
            'rel'   => 'alternate',
            'type'  => 'application/rss+xml',
            'title' => implode('/', $uri->path),
            'href'  => $uri->get(true),
        );
    }
    
    public function actionMember($handle = null)
    {
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            return;
        }
        
        // was a member specified?
        if (! $handle) {
            return $this->_error('ERR_NO_MEMBER_SPECIFIED');
        }
        
        // does the member exist?
        $this->member = $this->_model->members->fetchByHandle($handle);
        if (! $this->member) {
            return $this->_error('ERR_NO_SUCH_MEMBER');
        }
        
        // set the collection
        $this->_setList(array(
            'where' => array(
                'member_handle = ?' => $handle,
            ),
            'page' => $this->_query('page', 1),
            'paging' => $this->_query('paging', 10),
            'count_pages' => true,
        ));
    }
    
    public function actionTags($tags = null)
    {
        // is the user allowed access?
        if (! $this->_isUserAllowed()) {
            return;
        }
        
        // were any tags specified?
        if (! $tags) {
            return $this->_error('ERR_NO_TAGS_SPECIFIED');
        }
        
        // set the collection; set directly this time
        $fetch = array(
            'eager' => array(
                'member',
                'tags',
            ),
            'order' => $this->_order,
        );
        
        // set the collection "manually"
        $this->list = $this->_model->bookmarks->fetchAllByTags($tags, $fetch);
        
        // local nav
        $this->layout_local_active = "tags/$tags";
        
        // set the list of tags used for the search
        $this->tags = $tags;
    }
    
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
        
        // get these values from the query string (via QuickMark)
        $uri  = $this->_query('uri');
        $subj = $this->_query('subj');
        
        // does the bookmark exist already for this member?
        $item = $this->_model->bookmarks->fetchOneByUriAndMemberHandle(
            $uri,
            $this->user->auth->handle
        );
        
        if ($item) {
            // yes, redirect to editing
            return $this->_redirect("{$this->_controller}/edit/{$item->id}");
        }
        
        // set a new record ...
        $this->_setItemNew();
        
        // .. and pre-populate with the QuickMark uri and subj
        $this->item->uri = $uri;
        $this->item->subj = $subj;
        
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
        
    }
    
    protected function _saveItem()
    {
        $this->item->member_handle = $this->user->auth->handle;
        $this->item->status = Proxima_Model_Nodes::STATUS_PUBLIC;
        return parent::_saveItem();
    }
    
    protected function _setList($fetch = null)
    {
        $fetch['eager'][] = 'member';
        $fetch['eager'][] = 'tags';
        return parent::_setList($fetch);
    }
}
