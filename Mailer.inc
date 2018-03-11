<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'Log.inc';

/**
 * Mailing functions
 */
class Mailer
{

    /**
     * Email address from which email will be sent.
     *
     * @var string $fromDefaultEmail
     */
    public $fromDefaultEmail = 'no-reply@kaustubhk.com';


    public function sendHTMLMail(array $options, string $fromEmail=null)
    {
        if ($fromEmail === null) {
            $fromEmail = $this->fromDefaultEmail;
        }

        try {
            $mail = new PHPMailer();
            $mail->setFrom($fromEmail);
            foreach ($options['toEmail'] as $key => $value) {
                $mail->addAddress($value);
            }

            $mail->isHTML(true);
            $mail->Subject = $options['Subject'];
            $mail->Body    = $options['Body'];
            $mail->AddReplyTo($options['ReplyTo']);
            if (isset($options['AltBody']) === false) {
                $mail->AltBody = 'HTML not supported by Email client';
            } else {
                $mail->AltBody = $options['AltBody'];
            }

            $mail->send();
        } catch (Exception $e) {
            $log = new Log();
            $log->addError($mail->ErrorInfo);
        }//end try

    }//end sendHTMLMail()


}//end class
