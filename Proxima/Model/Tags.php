<?php
/**
 * 
 * Model class.
 * 
 */
class Proxima_Model_Tags extends Proxima_Sql_Model {
    
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
        $metadata          = Solar::factory('Proxima_Model_Tags_Metadata');
        $this->_table_name = $metadata->table_name;
        $this->_table_cols = $metadata->table_cols;
        $this->_index_info = $metadata->index_info;
        
        $this->_hasMany('taggings');
        $this->_hasManyThrough('nodes', 'taggings');
    }
    
    /**
     * 
     * Fetches a collection of all tags, with an added "count" column saying
     * how many nodes use that tag.
     * 
     * @param array $params Added paramters for the select.
     * 
     * @return Solar_Model_Tags_Collection
     * 
     */
    public function fetchAllWithCount($params = null)
    {
        // fix up so we can manipulate easier, esp. to get the table alias
        $params = $this->_fixFetchParams($params);
        
        // count the number of nodes.
        $params->cols("COUNT(nodes.id) AS count");
        
        // group on primary key for counts
        $native_col = "{$params['alias']}.{$this->_primary_col}";
        $params->group($native_col);
        
        // eager-join to nodes for the count of nodes.
        // force the join even though we're not fetching nodes, so  that
        // the counts come back.
        $params->eager('nodes', array(
            'join_only' => true,
        ));
        
        // done with params
        return $this->fetchAll($params);
    }
    
    /**
     * 
     * Fetches a collection of all tags used by a particular member_handle 
     * with the count of nodes using each tag.
     * 
     * @param string $member_handle Only select tags in use by this 
     * member_handle.
     * 
     * @param array $params Added parameters for the select.
     * 
     * @return Solar_Model_Tags_Collection
     * 
     */
    public function fetchAllByMemberHandle($member_handle, $params = null)
    {
        $params['where']['nodes.member_handle = ?'] = $member_handle;
        return $this->fetchAllWithCount($params);
    }
}
