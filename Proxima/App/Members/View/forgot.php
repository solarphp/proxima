<h2><?php echo $this->escape(ucwords($this->controller)); ?></h2>
<h3><?php echo $this->getText('HEADING_FORGOT'); ?></h3>

<?php if ($this->feedback) {
    echo "<p>" . $this->getText($this->feedback) . "</p>";
} ?>

<p><?php echo $this->getText('TEXT_FORGOT'); ?></p>

<?php echo $this->form()
                ->auto($this->form)
                ->addProcess('send')
                ->decorateAsPlain()
                ->fetch();
?>
