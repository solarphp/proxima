            <p>
                <?php echo $this->getText('TEXT_AUTH_USERNAME'); ?><br />
                <strong><?php
                    echo $this->escape($this->user->auth->handle);
                ?></strong>
            </p>
            <?php
                echo $this->form()
                          ->addProcess('logout', array('id' => 'logout-process'))
                          ->fetch();
            ?>
