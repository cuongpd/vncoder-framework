<?php

namespace VnCoder\Core\Queue;
use VnCoder\Helper\VnMailer;

class SendEmailQueue extends VnShouldQueue
{

    protected string $email;
    protected string $name;
    protected string $subject;
    protected string $message;

    public function __construct($toEmail, $subject, $message)
    {
        if(is_array($toEmail)) {
            $this->email = $toEmail[0] ?? '';
            $this->name = $toEmail[1] ?? '';
        } else {
            $this->email = $toEmail;
            $this->name = '';
        }
        $this->subject = $subject;
        $this->message = $message;
    }

    public function handle()
    {
        if ($this->email) {
            VnMailer::init()->to($this->email, $this->name)->subject($this->subject)->message($this->message)->send();
            logData( 'mailer','Send email to ' . $this->email . ' success!');
        }
    }
}
