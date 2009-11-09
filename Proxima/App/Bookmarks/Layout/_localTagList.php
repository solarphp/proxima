<h3><?php echo $this->getText('HEADING_TAG_OPTIONS') ?></h3>
<ul class="clearfix">
    <?php
        // the uri for the current action
        $uri = $this->actionUri();
        
        // clear the current page
        unset($uri->query['page']);
        
        // build a series of links to tags
        foreach ($this->tag_options as $tag) {
            $path = "{$this->controller}/tags/{$tag->name}";
            $uri->setPath($path);
            echo "<li>" . $this->action($uri, $tag->name)
               . " ({$tag->count})</li>\n";
        }
    ?>
</ul>
