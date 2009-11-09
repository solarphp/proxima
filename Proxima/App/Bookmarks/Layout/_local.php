<?php include $this->template('_auth'); ?>

<h2 class="accessibility">Local</h2>

<?php
    include $this->template('_localAddQuick');
    include $this->template('_localTagOrder');
    include $this->template('_localTagList');
?>
