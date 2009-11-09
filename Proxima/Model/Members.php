<?php
/**
 * 
 * Model class.
 * 
 */
class Proxima_Model_Members extends Proxima_Sql_Model 
{
    /**
     * 
     * Status constants.
     * 
     */
    const STATUS_NEW    = 'new';
    const STATUS_ACTIVE = 'active';
   
    /**
     *  
     *  Confirm type constants.
     *
     */
    const CONFIRM_TYPE_RESET  = 'reset';
    
    /**
     * 
     * Model-specific setup.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        // basic setup
        $setup             = Solar_Class::dir(__CLASS__, 'Setup');
        $this->_table_name = Solar_File::load($setup . 'table_name.php');
        $this->_table_cols = Solar_File::load($setup . 'table_cols.php');
        $this->_index      = Solar_File::load($setup . 'index_info.php');
        
        // filters on 'handle'
		$this->_addFilter('handle', 'validateWord');
		$this->_addFilter('handle', 'validateMinLength', 6);
		$this->_addFilter('handle', 'validateUnique');
        
        // filters on 'email'
		$this->_addFilter('email', 'validateEmail');
		$this->_addFilter('email', 'validateUnique');
		
		// filters on 'uri'
		$this->_addFilter('uri', 'validateUri');
        
        // note that the 'passwd_new' and 'passwd_confirm' columns don't 
        // actually exist. the validation will occur only if these are
        // set on the record by hand.
		$this->_addFilter('passwd_confirm', 'validateEquals', 'passwd_new');
		
		// relationships
		$this->_hasMany('nodes', array(
		    'native_col'  => 'handle',
		    'foreign_col' => 'member_handle',
		);
    }
    
    public function fetchOneByConfirm($type, $hash)
    {
        $where = array(
            'confirm_type = ?' => $type,
            'confirm_hash = ?' => $hash,
        );
        
        return $this->fetchOne(array(
            'where' => $where,
        ));
    }
}
