<h2><?php echo $this->escape(ucwords($this->controller)); ?></h2>
<h3><?php echo $this->getText('HEADING_READ'); ?></h3>

<p>[ <?php echo $this->action("/{$this->controller}", 'ACTION_BROWSE');?> ]</p>

<?php if ($this->feedback) {
    echo "<p>" . $this->getText($this->feedback) . "</p>";
} ?>

<?php echo $this->partial('_item', $this->item); ?>

<?php

    // allowed to edit?
    $allowed_edit = $this->user->access->isAllowed(
        $this->controller_class,
        'edit',
        $this->item
    );
    
    if ($allowed_edit) {
        $action = $this->action(
            "/{$this->controller}/edit/{$this->item->id}",
            'ACTION_EDIT'
        );
        echo "<p>$action</p>\n";
    }
    
    // allowed to change password?
    $allowed_passwd = $this->user->access->isAllowed(
        $this->controller_class,
        'passwd',
        $this->item
    );
    
    if ($allowed_edit) {
        $action = $this->action(
            "/{$this->controller}/passwd",
            'ACTION_PASSWD'
        );
        echo "<p>$action</p>\n";
    }
?>
