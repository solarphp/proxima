<?php
    $allowed = $this->user->access->isAllowed(
        $this->controller_class,
        'add'
    );

    if ($allowed) {
        
        // normal add
        $action = $this->action("/{$this->controller}/add", 'ACTION_ADD');
        echo "<p>$action</p>\n";
        
        // Mark This
        $uri = $this->actionUri();
        $uri->set("{$this->controller}/add");
        $href = $uri->get(true);
        
        $js = "javascript:location.href='$href?body='"
            . "+encodeURIComponent(location.href)"
            . "+'&subj='+encodeURIComponent(document.title)";
        
        echo "<p>"
           . $this->getText('TEXT_DRAG_THIS')
           . $this->anchor($js, 'TEXT_MARK_THIS')
           . "</p>\n";
    }
?>
