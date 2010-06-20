<?php include $this->template('_auth'); ?>

<h2 class="accessibility">Local</h2>

<?php
    echo "<p>" . $this->action('/search', 'ACTION_SEARCH') . "</p>\n";
    include $this->template('_localAddMarkThis');
    include $this->template('_localTagOrder');
    include $this->template('_localTagList');
?>
