<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Load Composer's autoLoader
require __DIR__ . '\..\..\vendor\autoload.php';

class mail {

    private $mailTo;
    private $subject;
    private $body;

    public function __construct($mailTo,$subject,$body) {
        $this->mailTo = $mailTo;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function send() : bool {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'abdulsalammohamed962@gmail.com';       //SMTP username
            $mail->Password   = 'mlepreeomqhtlrpy';                        //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('abdulsalammohamed962@gmail.com', 'E-Commerce');
            $mail->addAddress($this->mailTo, 'Joe User');     //Add a recipient
            $mail->addAddress('ellen@example.com');     //Name is optional

            //Content
            $mail->isHTML(true);                        //Set email format to HTML
            $mail->Subject = $this->subject;
            $mail->Body    = $this->body;

            $mail->send();
            // echo 'Message has been sent';
            return true;
            
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }
}