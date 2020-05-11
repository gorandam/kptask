<?php
declare(strict_types=1);

namespace Kptask\Core\Email;

use Zend\Mail;

/**
 * Class Mailer
 * @package Kptask\Core\Email
 */
class Mailer implements MailerInterface
{

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        // TODO: think about refactoring
        $mailData = new Mail\Message();
        $mailData->setBody('Welcome to Kptask, keep coding...');
        $mailData->setFrom('kptask@mail.com');
        $mailData->addTo('goran.dam@gmail.com');
        $mailData->setSubject('Welcome to Kptask');

        $mailTransport = new Mail\Transport\Sendmail();

        //try to send email
        try {
            //TODO: need to finish this, clean bugs
//            $mailTransport->send($mailData);
        } catch (\Exception $e) {
            echo $e;
        }
    }


    /**
     * Notice - new solution in send().
     * @param $data
     */
    protected function sendEmail($data)
    {
        //TODO refactor this mail implementation
        $to = 'goran.dam@gmail.com';
        $subject = "Welcome to Kptask";

        $message = "<b>Welcome to Kptask, keep coding...</b>";

        $header = "From:kptask@mail.com \r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html\r\n";

        try {
            mail($to, $subject, $message, $header);
//            $this->flash->success('Verification email sent.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
        }
    }
}
