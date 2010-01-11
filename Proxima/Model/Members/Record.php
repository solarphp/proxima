<?php
/**
 * 
 * A single Proxima_Model_Members record.
 * 
 */
class Proxima_Model_Members_Record extends Proxima_Sql_Model_Record
{
    /**
     * 
     * User-defined configuration values.
     * 
     * Keys are ...
     * 
     * `email_forgot_from`
     * : (string) The "From:" address when sending password-reset emails.
     * 
     * `email_forgot_subj`
     * : (string) The "Subject:" line when sending password-reset emails.
     * 
     * `email_forgot_body`
     * : (string) The body of the password-reset email.
     * 
     * Note that the `email_*_body` keys can use {:col_name} placeholders; 
     * the corresponding values of the record will be interpolated into the
     * text before sending.
     * 
     * @var array
     * 
     */
    protected $_Proxima_Model_Members_Record = array(
        'email_forgot_from'        => null,
        'email_forgot_subj'        => null,
        'email_forgot_body'        => null,
    );
    
    public function accessIsOwner(Solar_Auth_Adapter $auth, Solar_Role_Adapter $role)
    {
        return $auth->handle == $this->handle;
    }
    
    /**
     * 
     * Is this a new (unconfirmed) member?
     * 
     * @return bool
     * 
     */
    public function isStatusNew()
    {
        return $this->status_code == Proxima_Model_Members::STATUS_NEW;
    }
    
    /**
     * 
     * Is this an active (confirmed) member?
     * 
     * @return bool
     * 
     */
    public function isStatusActive()
    {
        return $this->status_code == Proxima_Model_Members::STATUS_ACTIVE;
    }
    
    /**
     *
     * Is the confirm-type "reset"?
     *
     * #return bool
     *
     */
    public function isConfirmTypeReset()
    {
        return $this->confirm_type == Proxima_Model_Members::CONFIRM_TYPE_RESET;
    }
    
    /**
     * 
     * Convenience method to set the status code using a string instead of
     * having to address the constant directly.
     * 
     * @param string $status_code The status code to set; e.g., 'active' will
     * translate to `Proxima_Model_Members::STATUS_ACTIVE`.
     * 
     * @return void
     * 
     */
    public function setStatusCode($status_code)
    {
        $const = 'STATUS_' . strtoupper($status_code);
        $this->status_code = constant("Proxima_Model_Members::$const");
    }
    
    /**
     * 
     * Pre-save to hash the passwd_new value, if it exists.
     * 
     * @return void
     * 
     */
	public function _preFilter()
	{
		if ($this->__isset('passwd_new')) {
		    $salt = Solar_Config::get('Solar_Auth_Adapter_Sql', 'salt');
			$this->passwd = hash('md5', $salt . $this->passwd_new);
		}
	}
    
    /**
     * 
     * Returns a form suitable for member record editing; in particular, sets
     * the `passwd` cols to input type `password`.
     * 
     * @return Solar_Form
     * 
     */
	public function newForm($cols = null)
	{
		$form = parent::newForm($cols);
		$form->setType('member[passwd_new]', 'password');
		$form->setType('member[passwd_confirm]', 'password');
		return $form;
	}
    
    /**
     * 
     * Pre-insert method to force the status code to 'active'.
     * 
     * @return void
     * 
     */
    protected function _preInsert()
    {
        $this->status_code  = Proxima_Model_Members::STATUS_ACTIVE;
    }
    
    /**
     *
     * Sends a confirmation email to members for reset password.
     *
     * @return void
     *
     */
    public function sendForgotEmail()
    {
        // set the confirmation values
        $this->confirm_type = Proxima_Model_Members::CONFIRM_TYPE_RESET;
        $this->confirm_hash = $this->getRandomConfirmHash();
        $this->save();
        
        // build email text
        $text = $this->interpolate($this->_config['email_forgot_body']);
        
        // build email message
        $mail = Solar::factory('Solar_Mail_Message');
        $mail->setFrom($this->_config['email_forgot_from'])
             ->addTo($this->email)
             ->setSubject($this->_config['email_forgot_subj'])
             ->setText($text);
        
        // send it!
        $mail->send();
    }
    
    /**
     * 
     * Given a text string, replaces all {:col_name} placeholders with the
     * corresponding values from this member record.
     * 
     * @param string $text The text with placeholders.
     * 
     * @return string The text with values in place.
     * 
     */
    public function interpolate($text)
    {
        foreach ($this->_data as $key => $val) {
            if (! is_scalar($val)) {
                continue;
            }
            $find = "{:$key}";
            $repl = (string) $val;
            $text = str_replace($find, $repl, $text);
        }
        return $text;
    }
    
    /**
     * 
     * Gets a random hash string for confirmation values.
     * 
     * @param int $min The minimum number of password characters.
     * 
     * @param int $max The maximum number of password characters.
     * 
     * @return string A random password string.
     * 
     */
    public function getRandomConfirmHash()
    {
        return hash('md5', uniqid(rand(), true));
    }
}
