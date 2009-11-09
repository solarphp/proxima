<?php return array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'int',
    'size' => NULL,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => true,
    'autoinc' => true,
  ),
  'created' => 
  array (
    'name' => 'created',
    'type' => 'timestamp',
    'size' => NULL,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
  'updated' => 
  array (
    'name' => 'updated',
    'type' => 'timestamp',
    'size' => NULL,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
  'handle' => 
  array (
    'name' => 'handle',
    'type' => 'varchar',
    'size' => 32,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
  'passwd' => 
  array (
    'name' => 'passwd',
    'type' => 'varchar',
    'size' => 32,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
  'moniker' => 
  array (
    'name' => 'moniker',
    'type' => 'varchar',
    'size' => 64,
    'scope' => NULL,
    'default' => NULL,
    'require' => false,
    'primary' => false,
    'autoinc' => false,
  ),
  'email' => 
  array (
    'name' => 'email',
    'type' => 'varchar',
    'size' => 64,
    'scope' => NULL,
    'default' => NULL,
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
  'uri' => 
  array (
    'name' => 'uri',
    'type' => 'varchar',
    'size' => 64,
    'scope' => NULL,
    'default' => NULL,
    'require' => false,
    'primary' => false,
    'autoinc' => false,
  ),
  'status' => 
  array (
    'name' => 'status',
    'type' => 'varchar',
    'size' => 16,
    'scope' => NULL,
    'default' => 'new',
    'require' => true,
    'primary' => false,
    'autoinc' => false,
  ),
  'confirm_type' => 
  array (
    'name' => 'confirm_type',
    'type' => 'varchar',
    'size' => 16,
    'scope' => NULL,
    'default' => NULL,
    'require' => false,
    'primary' => false,
    'autoinc' => false,
  ),
  'confirm_hash' => 
  array (
    'name' => 'confirm_hash',
    'type' => 'varchar',
    'size' => 32,
    'scope' => NULL,
    'default' => NULL,
    'require' => false,
    'primary' => false,
    'autoinc' => false,
  ),
);