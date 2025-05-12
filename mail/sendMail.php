<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require __DIR__ . '/../vendor/autoload.php';



$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();



//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
  //Server settings
  $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
  $mail->isSMTP();                                            //Send using SMTP
  $mail->Host       = $_ENV['MAIL_HOST'];                     //Set the SMTP server to send through
  $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
  $mail->Username   = $_ENV['MAIL_USER'];                   //SMTP username
  $mail->Password   = str_replace("_", " ", $_ENV['MAIL_PASSWORD']);                               //SMTP password
  $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];             //Enable implicit TLS encryption
  $mail->Port       = $_ENV['MAIL_PORT'];             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

  //Recipients
  $mail->setFrom($_ENV['MAIL_FROM'], 'JavDev');
  $mail->addAddress('jhergon8@gmail.com');     //Add a recipient
  //$mail->addAddress('');               //Name is optional
  $mail->addReplyTo('info@example.com', 'Information');
  //$mail->addCC('');
  //$mail->addBCC('');

  //Attachments
  //$mail->addAttachment('');         //Add attachments
  //$mail->addAttachment('');    //Optional name

  //Content
  $mail->isHTML(true);                                  //Set email format to HTML
  $mail->Subject = 'Here is the subject';
  $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  $mail->send();
  echo 'Message has been sent';
} catch (Exception $e) {
  echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
