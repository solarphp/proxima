<h2><?php echo $this->escape(ucwords($this->controller)); ?></h2>
<h3><?php echo $this->getText('HEADING_RESET'); ?></h3>

<p><?php echo $this->getText('TEXT_PASSWD'); ?></p>

<?php echo $this->form()
                ->auto($this->form)
                ->addProcess('passwd')
                ->fetch();
?>
