<?php
/**
 * 
 * A single Proxima_Model_Bookmarks record.
 * 
 */
class Proxima_Model_Bookmarks_Record extends Proxima_Model_Nodes_Record
{
    protected function _setup()
    {
        parent::_setup();
        $this->_addFilter('uri', 'validateNotBlank');
        $this->_addFilter('subj', 'validateNotBlank');
    }
    
    public function newForm($spec = null)
    {
        $form = parent::newForm($spec);
        
        $form->setAttribs('bookmarks[subj]', array(
            'size' => '40',
        ));
        
        $form->setAttribs('bookmarks[summ]', array(
            'rows' => '5',
            'cols' => '40',
        ));
        
        $form->setAttribs('bookmarks[uri]', array(
            'size' => '40',
        ));
        
        $form->setAttribs('bookmarks[pos]', array(
            'size' => '3',
        ));
        
        $form->setAttribs('bookmarks[tags_as_string]', array(
            'size' => '40',
        ));
        
        return $form;
    }
}
