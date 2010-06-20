<?php
/**
 * 
 * Model class.
 * 
 */
class Proxima_Model_Nodes extends Proxima_Sql_Model
{
    /**
     * 
     * Status constants.
     * 
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLIC = 'public';
    
    /**
     * 
     * Model-specific setup.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        // use metadata generated from make-model
        $metadata          = Solar::factory('Proxima_Model_Nodes_Metadata');
        $this->_table_name = $metadata->table_name;
        $this->_table_cols = $metadata->table_cols;
        $this->_index_info = $metadata->index_info;
        
        // calculated columns
        $this->_calculate_cols[] = 'tags_as_string';
        
        // filters
        $this->_addFilter('email', 'validateEmail');
        $this->_addFilter('uri', 'validateUri');
        $this->_addFilter('status', 'validateInList', array(
            self::STATUS_DRAFT,
            self::STATUS_PUBLIC,
        ));
        
        // relationships
        $this->_belongsTo('member', array(
            'native_col'  => 'member_handle',
            'foreign_col' => 'handle',
        ));
        
        $this->_hasMany('taggings');
        $this->_hasManyThrough('tags', 'taggings');
    }
    
    public function fetchAllByMemberHandle($handle, $fetch = null)
    {
        $fetch = $this->_fixFetchParams($fetch);
        $fetch->eager['member']['join_cond']['member.handle = ?'] = $handle;
        return $this->fetchAll($fetch);
    }
    
    /**
     * 
     * Fetches a collection of nodes with certain tags.
     * 
     * @param array $tag_list Fetch only nodes with all of these tags. If
     * empty, will fetch all nodes.
     * 
     * @param array $fetch Added parameters for the SELECT.
     * 
     * @return Solar_Model_Nodes_Collection
     * 
     */
    public function fetchAllByTags($tag_list, $fetch = null)
    {
        $fetch = $this->_fixFetchParams($fetch);
        $this->_modFetchJoinTags($fetch, $tag_list);
        return $this->fetchAll($fetch);
    }
    
    /**
     * 
     * Support method to "fix" tag-list arrays: no duplicates, no spaces, etc.
     * 
     * @param array $tag_list The list of tags to "fix".
     * 
     * @return array The fixed tag list.
     * 
     */
    protected function _fixTagList($tag_list)
    {
        // convert to array
        if (! is_array($tag_list)) {
            $tag_list = preg_split('/\s+/', trim((string) $tag_list));
        }
        
        // no duplicates allowed
        $tag_list = array_unique($tag_list);
        
        // if the string tag-list was empty, the preg-split leaves one empty
        // element in the array.
        if (count($tag_list) == 1 && reset($tag_list) == '') {
            $tag_list = array();
        }
        
        // done!
        return $tag_list;
    }
    
    /**
     * 
     * Modifies a params object **in place** to add joins for a tag list.
     * 
     * The methodology is taken and modified from
     * <http://forge.mysql.com/wiki/TagSchema#Items_Having_All_of_A_Set_of_Tags>.
     * 
     * @param Solar_Sql_Model_Params_Fetch $fetch The fetch params.
     * 
     * @param array $tags A list of unique tags.
     * 
     * @return void
     * 
     */
    protected function _modFetchJoinTags(Solar_Sql_Model_Params_Fetch $fetch, $tags)
    {
        // normalize the tag list
        $tags = $this->_fixTagList($tags);
        
        // if no tags, no need to modify
        if (! $tags) {
            return;
        }
        
        // since this model uses single-table inheritance, we need the model
        // alias, not just the table name.
        $alias = $this->_model_name;
        
        // for each tag, add a join to tags and taggings, chaining each
        // subsequent join to the previous one.
        // the first tag join-pair is special; we connect it to the nodes
        // table directly, since we don't have a previous join to chain from.
        $fetch->join(array(
            'type' => "inner",
            'name' => "taggings AS taggings1",
            'cond' => "taggings1.node_id = {$alias}.id"
        ));
        
        $fetch->join(array(
            'type' => "inner",
            'name' => "tags AS tags1",
            'cond' => "taggings1.tag_id = tags1.id"
        ));
        
        // take the first tag off the top of the list
        $val = array_shift($tags);
        $fetch->where("tags1.name = ?", $val);
        
        // now deal with all remaining tags, chaining each current join to the
        // previous one.
        foreach ($tags as $key => $val) {
            $curr = $key + 2; // because keys are zero-based, and we already shifted one
            $prev = $key + 1;
            
            // the "through" table
            $fetch->join(array(
                'type' => "inner",
                'name' => "taggings AS taggings{$curr}",
                'cond' => "taggings{$curr}.node_id = taggings{$prev}.node_id"
            ));
            
            // the "tags" table
            $fetch->join(array(
                'type' => "inner",
                'name' => "tags AS tags{$curr}",
                'cond' => "taggings{$curr}.tag_id = tags{$curr}.id"
            ));
            
            // the WHERE condition for the tag name
            $fetch->where("tags{$curr}.name = ?", $val);
        }
    }
}
