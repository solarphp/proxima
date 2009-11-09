<?php
/**
 * 
 * Inherited model class.
 * 
 */
class Proxima_Model_Bookmarks extends Proxima_Model_Nodes
{
    /**
     * 
     * Model-specific setup.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        parent::_setup();
        $this->_addFilter('subj', 'validateNotBlank');
        $this->_addFilter('uri', 'validateNotBlank');
        $this->_addFilter('tags_as_string', 'validateNotBlank');
    }
}
