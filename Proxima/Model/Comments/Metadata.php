<?php
/**
 * 
 * Table metadata for the Proxima_Model_Comments model class.
 * 
 * This class is auto-generated by make-model; any changes you make will be
 * overwritten the next time you use make-model.  Modify the Proxima_Model_Comments
 * class instead of this one.
 * 
 */
class Proxima_Model_Comments_Metadata extends Proxima_Sql_Model_Metadata
{
    public $table_name = 'comments';
    
    public $table_cols = array (
      'id' => array (
        'name' => 'id',
        'type' => 'int',
        'size' => NULL,
        'scope' => NULL,
        'default' => NULL,
        'require' => true,
        'primary' => true,
        'autoinc' => true,
      ),
      'created' => array (
        'name' => 'created',
        'type' => 'timestamp',
        'size' => NULL,
        'scope' => NULL,
        'default' => NULL,
        'require' => false,
        'primary' => false,
        'autoinc' => false,
      ),
      'updated' => array (
        'name' => 'updated',
        'type' => 'timestamp',
        'size' => NULL,
        'scope' => NULL,
        'default' => NULL,
        'require' => false,
        'primary' => false,
        'autoinc' => false,
      ),
      'node_id' => array (
        'name' => 'node_id',
        'type' => 'int',
        'size' => NULL,
        'scope' => NULL,
        'default' => NULL,
        'require' => true,
        'primary' => false,
        'autoinc' => false,
      ),
      'email' => array (
        'name' => 'email',
        'type' => 'varchar',
        'size' => 255,
        'scope' => NULL,
        'default' => NULL,
        'require' => true,
        'primary' => false,
        'autoinc' => false,
      ),
      'uri' => array (
        'name' => 'uri',
        'type' => 'varchar',
        'size' => 255,
        'scope' => NULL,
        'default' => NULL,
        'require' => false,
        'primary' => false,
        'autoinc' => false,
      ),
      'body' => array (
        'name' => 'body',
        'type' => 'clob',
        'size' => NULL,
        'scope' => NULL,
        'default' => NULL,
        'require' => true,
        'primary' => false,
        'autoinc' => false,
      ),
    );
    
    public $index_info = array (
    );
}
