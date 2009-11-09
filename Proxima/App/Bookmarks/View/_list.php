    <ul class="bookmarks-list">
    <?php foreach ($list as $item): ?>
        <li class="bookmarks-item"><?php
            echo $this->anchor($item->uri, $item->subj);
        ?><ul>
        
            <li>created by <?php
                echo $this->action(
                    "{$this->controller}/member/{$item->member_handle}",
                    $item->member_handle
                );
            ?> on <?php
                echo $this->timestamp($item->created);
            ?></li>
            
            <li>tagged <?php
                $tags = array();
                foreach ($item->tags as $tag) {
                    $tags[] = $this->action(
                        "{$this->controller}/tags/{$tag->name}",
                        $tag->name
                    );
                }
                
                echo implode(' ', $tags);
            ?></li>
            
            <?php
                $allowed = $this->user->access->isAllowed(
                    $this->controller_class,
                    'edit',
                    $item
                );
                
                if ($allowed) {
                    $action = $this->action(
                       "{$this->controller}/edit/{$item->id}",
                       'ACTION_EDIT'
                    );
                    echo "            <li>$action</li>";
                }
            ?>
        
        </ul></li>
    <?php endforeach; ?>
    </ul>
