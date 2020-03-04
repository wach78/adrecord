<?php
namespace Simpleframework\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Sendemail
{
    private $mail;
    
    public function __construct($mailconfig)
    {
        $this->mail = new PHPMailer(true);   
        $this->init($mailconfig);
    }
    
    private function init($mailconfig)
    {
        try
        {
            $this->mail->SMTPDebug = 3;                                
            $this->mail->isSMTP();
            $this->mail->WordWrap = 75;
            $this->mail->CharSet = 'UTF-8';
            $this->mail->SMTPAuth = true; 
            $this->mail->SMTPSecure = 'tls';
            $this->mail->Host       = $mailconfig['host']; 
            $this->mail->Port       = $mailconfig['port'];
            $this->mail->Username   = $mailconfig['username'];
            $this->mail->Password   = $mailconfig['pass'];
            
            $this->mail->SMTPOptions = array (
                'ssl' => array(
                    'verify_peer'  => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true));
            
            $this->mail->isHTML(true); 
            $this->mail->SetFrom($mailconfig['username'], 'myPortal');
        }
        catch (Exception $e)
        {
            var_dump($e->getMessage());
            var_dump($this->mail->ErrorInfo);
        }
    }
    
    public function setSubject($subject)
    {
        $this->mail->Subject = $subject;
    }
    
    public function messages($msg)
    {
        $this->mail->Body = $msg;
    }
    
    
    public function setReplayTo($replay)
    {
        $this->mail->addReplyTo($replay);
    }
    
    public function addsendToAddress($to,$name = '')
    {
        $this->mail->AddAddress($to,$name);
    }
    
    public function addCC($to,$name = '')
    {
        $this->mail->addCC($to,$name = '');
    }
    public function addBCC($to,$name = '')
    {
        $this->mail->addBCC($to,$name = '');
    }
    
    public function send()
    {
        try
        {
            ob_start();
            $this->mail->Send();
            ob_get_clean();
        }
        catch (Exception $e)
        {
            var_dump($e->getMessage());
            var_dump($this->mail->ErrorInfo);
        }
    }
    
}