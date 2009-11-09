    <div id="auth"><?php
    
        if ($this->user->auth->isValid()) {
            include $this->template('_authLogout');
        } else {
            include $this->template('_authLogin');
        }
        
        $status = Solar_Registry::get('user')->auth->getStatusText();
        echo "<p>"
           . nl2br(wordwrap($this->escape($status), 20))
           . "</p>\n";
    
    ?></div>