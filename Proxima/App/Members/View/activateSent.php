<h2><?php echo $this->escape(ucwords($this->controller)); ?></h2>
<h3><?php echo $this->getText('HEADING_ACTIVATE'); ?></h3>

<p><?php echo $this->getText('TEXT_SENT_ACTIVATE_EMAIL', 1, array(
    'email' => $this->item->email
)); ?></p>
