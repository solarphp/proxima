<?php include $this->template('_auth'); ?>

<h2 class="accessibility">Local</h2>
<ul class="clearfix">
    <?php
        foreach ((array) $this->layout_local as $key => $val) {
            echo "<li";
            if ($this->layout_local_active == $key) {
                echo ' class="active"';
            }
            echo '>';
            echo $this->action("{$this->controller}/$key", $val);
            echo "</li>\n";
        }
    ?>
</ul>
