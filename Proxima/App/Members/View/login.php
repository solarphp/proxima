<h2><?php echo $this->escape(ucwords($this->controller)); ?></h2>
<h3><?php echo $this->getText('HEADING_LOGIN'); ?></h3>

<?php
    if ($this->feedback) {
        echo "<p>" . $this->getText($this->feedback) . "</p>\n";
    }
    
    $text = $this->user->auth->getStatusText();
    if ($text) {
        echo "<p>" . $this->getText($text) . "</p>\n";
    }
    
    echo $this->form()
              ->text(array(
                    'name'    => 'handle',
                    'label'   => 'LABEL_HANDLE',
                    'attribs' => array('size' => 15, 'id' => 'login-handle'),
              ))
              ->password(array(
                    'name'    => 'passwd',
                    'label'   => 'LABEL_PASSWD',
                    'attribs' => array('size' => 15, 'id' => 'login-password')
              ))
              ->addProcess('login', array('id' => 'login-process'))
              ->decorateAsTable()
              ->fetch();
?>

<p><?php
    echo $this->action("/members/register", 'LINK_REGISTER')
       . " | "
       . $this->action("/members/forgot", 'LINK_FORGOT');
?></p>
