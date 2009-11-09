            <?php
                echo $this->form()
                          ->text(array(
                                'name'    => 'handle',
                                'label'   => 'LABEL_HANDLE',
                                'attribs' => array('size' => 10, 'id' => 'login-handle'),
                          ))
                          ->password(array(
                                'name'    => 'passwd',
                                'label'   => 'LABEL_PASSWD',
                                'attribs' => array('size' => 10, 'id' => 'login-password')
                          ))
                          ->addProcess('login', array('id' => 'login-process'))
                          ->fetch();
            ?>
            
            <p><?php
                echo $this->action("/members/register", 'LINK_REGISTER')
                   . " | "
                   . $this->action("/members/forgot", 'LINK_FORGOT');
            ?></p>
