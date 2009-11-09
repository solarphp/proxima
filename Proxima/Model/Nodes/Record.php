<?php
/**
 * 
 * A single Proxima_Model_Nodes record.
 * 
 */
class Proxima_Model_Nodes_Record extends Proxima_Sql_Model_Record
{
    public function accessIsOwner(Solar_Auth_Adapter $auth, Solar_Role_Adapter $role)
    {
        return $auth->handle == $this->member_handle;
    }
    
    /**
     * 
     * Magic method to get the 'tags_as_string' property.
     * 
     * @return string
     * 
     */
    public function __getTagsAsString()
    {
        // populate for the first time
        if (empty($this->_data['tags_as_string'])) {
            // $this->tags forces the __get() call to the related object,
            // then only proceeds if there are tags there.
            if ($this->tags) {
                $this->_data['tags_as_string'] = $this->tags->getNamesAsString();
            }
        }
        
        return $this->_data['tags_as_string'];
    }
    
    /**
     * 
     * Magic method to set the 'tags_as_string' property.
     * 
     * Maintains the tags collection on-the-fly.
     * 
     * @param string $val A space-separated list of tags.
     * 
     * @return void
     * 
     */
    public function __setTagsAsString($val)
    {
        if (! $this->tags) {
            $this->tags = $this->newRelated('tags');
        }
        $this->tags->setNames($val);
        $this->_data['tags_as_string'] = $this->tags->getNamesAsString();
    }
    
    /**
     * 
     * Deletes all tag mappings, leaving tags in place.
     * 
     * @return void
     * 
     */
    protected function _postDelete()
    {
        if ($this->taggings) {
            $this->taggings->deleteAll();
        }
    }
}
