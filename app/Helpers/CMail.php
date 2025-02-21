<?php
namespace App\Helpers;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class CMail{
    public static function send($config){
        $mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = config('services.mail.host');                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = config('services.mail.username');                     //SMTP username
    $mail->Password   = config('services.mail.password');                               //SMTP password
    $mail->SMTPSecure =config('services.mail.encryption');            //Enable implicit TLS encryption
    $mail->Port       = config('services.mail.port');                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom(
        isset($config['from']['address'])?$config['from']['address']:config('services.mail.from.address'),
        isset($config['from']['name'])?$config['from']['name']:config('services.mail.from.name')
    );
    $mail->addAddress(
        $config['to']['address'],
        $config['to']['name']
    );     //Add a recipient   //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $config['subject'];
    $mail->Body    = $config['body'];

    if(!$mail->send()){
        return false;
    }else{
        return true;
    }
} catch (Exception $e) {
    return false;

    }
}
}