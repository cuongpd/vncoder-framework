<?php

namespace VnCoder\Helper;
use VnCoder\Helper\PHPMailer\PHPMailer;

class VnMailer
{
    private PHPMailer $mailer;
    protected string $fromEmail = "", $replyEmail = "", $toEmail = "";

    static function init(){
        return with(new static())->start();
    }

    public function start()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->SMTPDebug  = 0;
        $this->mailer->isSMTP();
        $this->mailer->Host       = env('MAIL_HOST', 'smtp.gmail.com');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = env('MAIL_USERNAME', '');
        $this->mailer->Password   = env('MAIL_PASSWORD', '');
        $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
        $this->mailer->Port       = env('MAIL_PORT', 587);
        $this->mailer->isHTML(true);
        $this->mailer->XMailer = 'VnCoder Mailer 1.0';
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Priority = 1;
        return $this;
    }

    public function from($email, $name = null)
    {
        $this->mailer->setFrom($email, $name);
        $this->emailFrom = $email;
        return $this;
    }

    public function replyTo($email, $name = null)
    {
        $this->mailer->addReplyTo($email, $name);
        $this->replyEmail = $email;
        return $this;
    }

    public function to($email, $name = null)
    {
        $this->mailer->addAddress($email, $name);
        $this->toEmail = $email;
        return $this;
    }

    public function cc($email, $name = null)
    {
        $this->mailer->addCC($email, $name);
        return $this;
    }

    public function bcc($email, $name = null)
    {
        $this->mailer->addBCC($email, $name);
        return $this;
    }

    public function subject($subject)
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    public function message($content)
    {
        $this->mailer->Body = $content;
        return $this;
    }

    public function attachment($path, $name = '')
    {
        $this->mailer->addAttachment($path, $name);
        return $this;
    }

    public function embedded($path, $cid, $name = ''){
        $this->mailer->addEmbeddedImage($path, $cid, $name);
        return $this;
    }

    public function send()
    {
        if(!$this->fromEmail){
            $smtp_from = env('MAIL_FROM_ADDRESS', '');
            $smtp_name = env('MAIL_FROM_NAME', '');
            if($smtp_from && $smtp_name){
                $this->mailer->setFrom($smtp_from, $smtp_name);
            }
        }
        if($this->toEmail){
            return $this->mailer->send();
        }else{
            return false;
        }
    }
}
