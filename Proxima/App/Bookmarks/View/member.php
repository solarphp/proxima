<h2><?php echo $this->escape(ucwords($this->controller)); ?></h2>
<h3><?php echo $this->getText('HEADING_MEMBER'); ?></h3>
<h4><?php echo $this->escape($this->member->handle); ?></h4>
<?php include $this->template('_browse'); ?>
