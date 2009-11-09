<h3><?php echo $this->getText('HEADING_ORDER_OPTIONS') ?></h3>
<ul class="clearfix">
<?php
    // the uri for the current action
    $uri = $this->actionUri();
    
    // clear the current page
    unset($uri->query['page']);
        
    // show links for each order option
    foreach ($this->order_options as $value => $label) {
        if ($this->order_active == $value) {
            echo "<li class=\"active\">"
               . $this->getText($label)
               . "</li>\n";
        } else {
            // set the order on the uri
            $uri->query['order'] = $value;
            // then show it as an action link
            echo "<li>" . $this->action($uri, $label) . "</li>\n";
        }
    }
?>
</ul>
