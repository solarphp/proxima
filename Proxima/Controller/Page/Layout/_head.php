<head>
<?php
    // add meta tags
    foreach ($this->layout_head['meta'] as $val) {
        $this->head()->addMeta($val);
    }
    
    // set the title
    $this->head()->setTitle($this->layout_head['title']);
    
    // set the uri base
    $this->head()->setBase($this->layout_head['base']);
    
    // add links
    foreach ($this->layout_head['link'] as $val) {
        $this->head()->addLink($val);
    }
    
    // add baseline styles
    $this->head()->addStyleBase("Proxima/Controller/Page/cssfw/tools.css")
                 ->addStyleBase("Proxima/Controller/Page/cssfw/typo.css")
                 ->addStyleBase("Proxima/Controller/Page/cssfw/forms.css")
                 ->addStyleBase("Proxima/Controller/Page/cssfw/layout-{$this->layout_type}.css");
    
    // additional baseline styles
    foreach ($this->layout_head['style'] as $val) {
        $this->head()->addStyleBase($val);
    }
    
    // additional baseline scripts
    foreach ($this->layout_head['script'] as $val) {
        $this->head()->addScriptBase($val);
    }
    
    // done!
    echo $this->head()->fetch();
?>
</head>
