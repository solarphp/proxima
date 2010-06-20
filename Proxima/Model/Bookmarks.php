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
     * We keep the bookmark URI in `body`, not in `uri`, because some
     * URIs are just way too long for 255 chars max (e.g. Dice.com).
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        parent::_setup();
        $this->_addFilter('subj', 'validateNotBlank');
        $this->_addFilter('body', 'validateUri');
        $this->_addFilter('tags_as_string', 'validateNotBlank');
    }
}
