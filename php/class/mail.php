<?php namespace Oxmosys;

include "php/third-part/phpmailer/PHPMailer.php";
include "php/third-part/phpmailer/Exception.php";
include "php/third-part/phpmailer/SMTP.php";
include "php/third-part/phpmailer/OAuth.php";

use Oxmosys\AppConfig;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\OAuth;
use PHPMailer\PHPMailer\Exception;

class OxMosysMail
{
    private static $head, $params, $clientRagsoc, $clientCodfisc;

    private static $smtp_host, $smtp_username, $smtp_password, $smtp_secure, $smtp_port, $smtp_debug;

    public function __construct($head = null, $params = null)
    {
        if ($head == null) {
            $app = new AppConfig();
            $headers = "From: " . $app::$appConfig["install-info"]["email"]["smtp_username"] . "\r\n";
            $headers .= "Reply-To: " . $app::$appConfig["install-info"]["email"]["smtp_username"] . "\r\n";
            self::$head = $headers;

            self::$clientRagsoc = $app::$appConfig["install-info"]["ragsoc"];
            self::$clientCodfisc = $app::$appConfig["install-info"]["cfisc"];

            // email config
            self::$smtp_host = $app::$appConfig["install-info"]["email"]["smtp_host"];
            self::$smtp_username = $app::$appConfig["install-info"]["email"]["smtp_username"];
            self::$smtp_password = $app::$appConfig["install-info"]["email"]["smtp_password"];
            self::$smtp_secure = $app::$appConfig["install-info"]["email"]["smtp_secure"];
            self::$smtp_port = $app::$appConfig["install-info"]["email"]["smtp_port"];
            self::$smtp_debug = $app::$appConfig["install-info"]["email"]["smtp_debug"];
        } else {
            self::$head = $head;
        }

        self::$params = $params;
    }

    public function SendDebug($subj, $mess)
    {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = self::$smtp_debug;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = self::$smtp_host;                   // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = self::$smtp_username;                 // SMTP username
            $mail->Password = self::$smtp_password;                           // SMTP password
            $mail->SMTPSecure = self::$smtp_secure;                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = self::$smtp_port;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom(self::$smtp_username, self::$clientRagsoc);
            $mail->addAddress('oxmosys-debug@fastservice.com', 'OxMosys Debug Ticket');     // Add a recipient
            //$mail->addAddress('contact@example.com');               // Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subj;
            $mail->Body    = $mess;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            //echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }

    // public function SendDebug($subj, $mess)
    // {
    //     $headers = self::$head;
    //     $headers .= "MIME-Version: 1.0\r\n";
    //     $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    //     $mailSend = mail("oxmosys-debug@fastservice.com", $subj, $mess, $headers, self::$params);

    //     if (!$mailSend) {
    //         throw new Exception("Errore in invio mail", 1);
    //     };
    // }
}
