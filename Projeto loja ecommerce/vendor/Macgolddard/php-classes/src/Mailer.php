<?php

namespace Macgolddard;

use Rain\Tpl;

class Mailer{

    const USERNAME  =  "glauberhitech@gmail.com";
    CONST PASSWORD  =  "A8j7d2e3-17-ti";
    CONST NAME_FROM =  "Glauber Macgolddard DEV Web";

    private $mail;

    public function __construct($toAddress, $toName, $toSubject, $tplName, $data = array())
    {
            //echo !extension_loaded('openssl')?"Not Available":"Available";

            // config
            $config = array(
                "tpl_dir"   => $_SERVER["DOCUMENT_ROOT"] . "/views/email/",
                "cache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views-cache/",
                "debug"     => false// set to false to improve the speed
            );

            Tpl::configure($config);

            $tpl = new Tpl;


            foreach ($data as $key => $value){

                $tpl->assign($key, $value);
            }

            $html = $tpl->draw($tplName, true);

            //Create a new PHPMailer instance
            $this->mail = new \PHPMailer;

            //Tell PHPMailer to use SMTP
            $this->mail->isSMTP();

            //Enable SMTP debugging
            // 0 = off (for production use)
            // 1 = client messages
            // 2 = client and server messages
            $this->mail->SMTPDebug = 2;

            //Set the hostname of the mail server
            $this->mail->Host = "smtp.gmail.com";

            //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
            $this->mail->Port = 587;

            //Set the encryption system to use - ssl (deprecated) or tls
            $this->mail->SMTPSecure = 'tls';

            //Whether to use SMTP authentication
            $this->mail->SMTPAuth = true;

            //Username to use for SMTP authentication - use full email address for gmail
            $this->mail->Username = Mailer::USERNAME;
            //$this->mail->Username = 'valdir10100@gmail.com';

            //Password to use for SMTP authentication
            $this->mail->Password = Mailer::PASSWORD;

            //Set who the message is to be sent from
            //$this->mail->setFrom('valdir10100@gmail.com', 'Valdir Silva - Curso de PHP 7');
            $this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

            //Set an alternative reply-to address
            //$this->mail->addReplyTo('replyto@example.com', 'First Last');

            //Set who the message is to be sent to
            //$this->mail->addAddress('valdirteste10@gmail.com', 'Gmail');
            $this->mail->addAddress($toAddress, $toName);

            //Set the subject line
            //$this->mail->Subject = 'PHPMailer GMail SMTP Teste por Valdir Silva';
            $this->mail->Subject = $toSubject;

            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            //$this->mail->msgHTML(file_get_contents('contents.html'), __DIR__);
            $this->mail->msgHTML($html);

            //Replace the plain text body with one created manually
            $this->mail->AltBody = 'Email para recuperação de senha.';

    }

    public function send(){

            return $this->mail->send();


//        //send the message, check for errors
//        if (!$this->mail->send()) {
//            echo "Mailer Error: " . $this->mail->ErrorInfo;
//        } else {
//            echo "Email enviado com sucesso!";
//            //Section 2: IMAP
//            //Uncomment these to save your message in the 'Sent Mail' folder.
//            #if (save_mail($this->mail)) {
//            #    echo "Message saved!";
//            #}
//        }
    }


}


?>