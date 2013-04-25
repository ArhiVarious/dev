<?

class Profbis_Mail_Send extends Zend_Mail 
{

    protected $templateVariables = array();
    protected $templateName;
    protected $recipient;
    protected $templatePath;

 
    public function __construct()
    {
        
        $config=new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini');
        parent::__construct($config->production->resources->mail->charset);
        $this->templatePath = $config->production->email->templatePath;
        
    }
 
    /**
     * Set variables for use in the templates
     *
     * @param string $name  The name of the variable to be stored
     * @param mixed  $value The value of the variable
     */
    public function __set($name, $value)
    {
        $this->templateVariables[$name] = $value;
    }
 
    /**
     * Set the template file to use
     *
     * @param string $filename Template filename
     */
    public function setTemplate($filename)
    {
        $this->templateName = $filename;
    }
 
    /**
     * Set the recipient address for the email message
     *
     * @param string $email Email address
     */
    public function setRecipient($email)
    {
        $this->recipient = $email;
    }
 
    /**
     * Send email
     *
     * @todo Add from name
     */
    public function send()
    {
        $viewContent = realpath($this->templatePath) . DIRECTORY_SEPARATOR . $this->templateName . '.tpl';
        //Zend_Debug::dump($sql);die;
        $stringMail = file_get_contents($viewContent);
        $html = vsprintf($stringMail, $this->templateVariables);
        $this->addTo($this->recipient);
        $this->setBodyHtml($html);
        parent::send();
 
    }
}