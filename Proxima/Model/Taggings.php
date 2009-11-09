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
    protected function _setup()
    {
        $setup             = Solar_Class::dir(__CLASS__, 'Setup');
        $this->_table_name = Solar_File::load($setup . 'table_name.php');
        $this->_table_cols = Solar_File::load($setup . 'table_cols.php');
        $this->_index      = Solar_File::load($setup . 'index_info.php');
        
        // relationships
        $this->_belongsTo('node');
        $this->_belongsTo('tag');
    }
}
