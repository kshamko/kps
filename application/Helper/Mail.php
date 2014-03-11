<?php
/**
 * Helper to sent/queue emails  
 */
class Helper_Mail {

    private $_subject = '';
    private $_body = '';
    private $_recipient = null;

    /**
     * Sets subject
     * 
     * @param string $subject
     * @return \Helper_Mail 
     */
    public function setSubject($subject) {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * Sets body from template
     * 
     * @param string $template - name of the template with email body
     * @param array $params - values to assign into template
     * @return \Helper_Mail 
     */
    public function setBody($template, $params = array()) {
        $config = Kps_Application_Config::load();
        $view = new Zend_View();
        $view->addHelperPath("We/View/Helper", "We_View_Helper");
        $view->setScriptPath(APPLICATION_PATH . '/views/' . $config['view']['theme'] . '/emails');

        foreach ($params as $name => $param) {
            $view->$name = $param;
        }

        $this->_body = $view->render($template . '.phtml');
        return $this;
    }

    /**
     * Return HTML body of the email
     * @return string
     */
    public function getBodyHTML() {
        return $this->_body;
    }

    /**
     * Sets email body
     * 
     * @param string $body
     * @return \Helper_Mail 
     */
    public function setBodyHTML($body) {
        $this->_body = $body;
        return $this;
    }

    /**
     * Sets recipient's email address
     * @param string $recipient
     * @return \Helper_Mail 
     */
    public function setRecipient($recipient) {
        $this->_recipient = $recipient;
        return $this;
    }

    /**
     * Puts email to queue 
     */
    public function queue() {
        if ($this->_recipient) {
            $oQueue = new Model_EmailQueue();
            $oQueue->addEntry(array(
                'email_recipient' => $this->_recipient,
                'email_subject' => $this->_subject,
                'email_body' => $this->_body
            ));
        }
    }

    /**
     * Sends email 
     */
    public function send() {
        if ($this->_recipient) {
            $mailer = new Kps_Mailer();
            $mailer->addHeader('Precedence', 'bulk');
            $mailer->addHeader('X-Auto-Response-Suppress', 'All');
            $mailer->addTo($this->_recipient);
            $mailer->setSubject($this->_subject);
            $mailer->setBodyHtml($this->_body);//, null, Zend_Mime::ENCODING_8BIT);
            $mailer->send();
            unset($mailer);
        }
    }

}
