<h2 class="accessibility">Navigation</h2>
<ul class="clearfix">
    <?php
        foreach ((array) $this->layout_nav as $key => $val) {
            echo "<li";
            if ($this->layout_nav_active == $key) {
                echo ' class="active"';
            }
            echo '>';
            echo $this->action($key, $val);
            echo "</li>\n";
        }
    ?>
</ul>
