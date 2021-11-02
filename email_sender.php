<?php
header('Content-Type: text/html; charset=UTF-8');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'php_mailer/src/Exception.php';
require 'php_mailer/src/PHPMailer.php';
require 'php_mailer/src/SMTP.php';

function signUpConfirmation($recipient_email, $confirmation_code)
{
    $mail_body = file_get_contents("signup_confirmation.html");

        $mail_body = str_replace(
            array("confirm_signup", "cancel_signup"),
            array(
                BASE_URL . "confirm_signup.php?confirmation_code=$confirmation_code",
                BASE_URL . "cancel_signup.php?confirmation_code=$confirmation_code"
            ),
            $mail_body
        );

        // Instância da classe
        $mail = new PHPMailer(true);
        try
        {
            // Configurações do servidor
            $mail->isSMTP();
            $mail->SMTPAuth = true;

            $mail->Username   = 'contato.nous.app@gmail.com';
            $mail->Password   = 'tdah_6696';

            $mail->SMTPSecure = 'tls';

            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;

            $mail->setFrom('contato.nous.app@gmail.com', 'Contato NOÛS');
            $mail->addAddress($recipient_email, 'Destinatário');

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Confirmação de email';
            $mail->Body    = $mail_body;

            $mail->send();
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
}