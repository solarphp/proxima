<h2><?php echo $this->escape(ucwords($this->controller)); ?></h2>
<h3><?php echo $this->getText('HEADING_REGISTER'); ?></h3>

<?php echo $this->form()
                ->auto($this->form)
                ->addProcessGroup(array(
                    'save',
                    'cancel',
                ))
                ->fetch();
?>
