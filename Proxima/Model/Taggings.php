<?php
/**
 * 
 * Model class.
 * 
 */
class Proxima_Model_Taggings extends Proxima_Sql_Model
{
    /**
     * 
     * Model-specific setup.
     * 
     * @return void
     * 
     */
    protected function _preSetup()
    {
        // chain to parent
        parent::_preSetup();
        
        // use metadata generated from make-model
        $metadata          = Solar::factory('Proxima_Model_Taggings_Metadata');
        $this->_table_name = $metadata->table_name;
        $this->_table_cols = $metadata->table_cols;
        $this->_index_info = $metadata->index_info;
        
        // relationships
        $this->_belongsTo('node');
        $this->_belongsTo('tag');
    }
}
