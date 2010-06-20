<?php
/**
 * 
 * A single Proxima_Model_Bookmarks record.
 * 
 */
class Proxima_Model_Bookmarks_Record extends Proxima_Model_Nodes_Record
{
    public function newForm($spec = null)
    {
        $form = parent::newForm($spec);
        
        $form->setAttribs('bookmark[subj]', array(
            'size' => '40',
        ));
        
        $form->setAttribs('bookmark[summ]', array(
            'rows' => '5',
            'cols' => '40',
        ));
        
        $form->setType('bookmark[body]', 'text');
        
        $form->setAttribs('bookmark[body]', array(
            'size' => '40',
        ));
        
        $form->setAttribs('bookmark[pos]', array(
            'size' => '3',
        ));
        
        $form->setAttribs('bookmark[tags_as_string]', array(
            'size' => '40',
        ));
        
        return $form;
    }
}
