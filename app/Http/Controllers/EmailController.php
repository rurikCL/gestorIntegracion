<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use SendGrid;

//use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail()
    {

        // METODO 1 : SENDGRID
        /*$sendgrid = new SendGrid('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');
        try {

            $email = new Mail();
            $email->setFrom("desarrollo@pompeyo.cl", "Pompeyo Mailer");
            $email->addTo("cristian.fuentealba@pompeyo.cl", "Cristian");
            $email->setSubject("Sending with SendGrid is Fun");
            $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
            $email->addContent(
                "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
            );

            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }*/


        // METODO 2 : MAIL
       /* try {
            Mail::send(['text' => 'welcome'],
                ['msg'=>"Mensaje de prueba"], function ($message) {
                    $message->from('roma@mailpompeyo.cl', 'Roma');
                    $message->to('cristian.fuentealba@pompeyo.cl');
                });
        }catch (Exception $e) {
            dd($e->getMessage());
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }*/


        // METODO 3 : PHPMAILER
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Host = "smtp.sendgrid.net";
        $mail->From = "roma@mailpompeyo.cl";
        $mail->FromName = "Roma";
        $mail->CharSet = 'UTF-8';
        $mail->AltBody = "";

        $mail->AddAddress('cristian.fuentealba@pompeyo.cl');
        $mail->Subject = 'Pruaba asdf';
        $mail->SMTPAuth = true;
        $mail->MsgHTML('Prueba de correo');
        $mail->Username = "apikey";
        $mail->Password = "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";
        $mail->SMTPSecure = 'ssl';
        $mail->Port = "465";
        if ($mail->Send()) {
            print_r("Correo enviado con exito");
        } else {
            print_r("Error al enviar el correo");
        }
        print_r($mail->ErrorInfo);


    }


}
