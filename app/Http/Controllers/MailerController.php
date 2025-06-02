<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;
define('API_USER_ID', '095bbb5e4cec502b6a76606a6d5b2665');
define('API_SECRET', '3b950eedbd42abf1d26273974eb2fb73');
define('PATH_TO_ATTACH_FILE', dirname(__DIR__,2)."\cargados/");

class MailerController extends Controller{

public function correo_envio(){
    $mail[] = array('correo'=>'ventanillavirtual10@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual11@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual12@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual13@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual14@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual15@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual16@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual17@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual18@ugel01.gob.pe','clave'=>'Ugel012021');
    $mail[] = array('correo'=>'ventanillavirtual19@ugel01.gob.pe','clave'=>'Ugel012021');
    return $mail[rand(0,count($mail)-1)];
}

    public function pruebamail(){
        $this->sendMail_ventanilla('prueba','jacostaf@ugel01.gob.pe','obs obs obs obs obs obs obs obs obs obs obs obs obs obs ');
    }

    public function sendMail($asunto,$correo,$body,$adjunto=false){
        //Validar si es Array:
        //------------------------------------
            if (is_array($correo)) { $correo_a    = $correo; } 
            else {                   $correo_a[0] = $correo; }
        //------------------------------------
            //$From="buzondecomunicaciones01@ugel01.gob.pe";
            $FromName="Ventanilla virtual";

            //$this->load->library("My_PHPMailer");
            $FromName = $FromName;
            //$form_email=$From;
            $mail=new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth=true;
           $mail->Host="smtp.office365.com";
           //$mail->Host="smtp-pulse.com";
            
            $correo_envio = $this->correo_envio();
            $mail->From     = $correo_envio['correo'];
            $mail->Username = $correo_envio['correo'];
            $mail->Password = $correo_envio['clave'];
            
             //$mail->From     = "ONEM@ugel01.edu.pe";
             //$mail->Username = "locacion_jmmj@ugel01.gob.pe";
             //$mail->Password = "so6Kr9P8GQkHJ";
            
            
            
            //$mail->Username="buzondecomunicaciones01@ugel01.gob.pe";
            //$mail->Password="Tuf49458";
            $mail->CharSet="UTF-8";
            //$mail->Password="1234__sistemas1";
            $mail->Port=587;
            $mail->SMTPOptions = array(
            'ssl' => array(
            	'verify_peer' => false,
            	'verify_peer_name' => false,
            	'allow_self_signed' => true
            ));
            //$mail->SMTPDebug = 1;
            $mail->SMTPSecure="STARTTLS";
            $mail->IsHTML(true);
            //$mail->From=$form_email;
            $mail->FromName=$FromName;
            foreach ($correo_a as $email) {
                $mail->AddAddress($email);
            }
            $mail->Subject=$asunto;
            $mail->Body= $this->htmlbody($body);
            if($adjunto){
                $mail->addAttachment('.'.$adjunto,basename($adjunto));
            }

            if($mail->send()){
             return true;
             }else{
             return false;
            }
    }
    
    function htmlbody($texto=''){
        $body = '<html lang="en">';
        $body.= '<head>';
        $body.= '<meta charset="UTF-8">';
        $body.= '</head>';
        $body.= '<body>';
        $body.= $texto;
        $body.= '</body>';
        $body.= '</html>';
        return $body;
    }

    /*public function composeEmail(Request $request) {
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        try {

            // Email server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';             //  smtp host
            $mail->SMTPAuth = true;
            $mail->Username = 'sender-username';   //  sender username
            $mail->Password = 'sender-password';       // sender password
            $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
            $mail->Port = 587;                          // port - 587/465

            $mail->setFrom('sender-from-email', 'sender-from-name');
            $mail->addAddress($request->emailRecipient);
            $mail->addCC($request->emailCc);
            $mail->addBCC($request->emailBcc);

            $mail->addReplyTo('sender-reply-email', 'sender-reply-name');

            if(isset($_FILES['emailAttachments'])) {
                for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
                    $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
                }
            }


            $mail->isHTML(true);                // Set email content format to HTML

            $mail->Subject = $request->emailSubject;
            $mail->Body    = $request->emailBody;

            // $mail->AltBody = plain text version of email body;

            if( !$mail->send() ) {
                return back()->with("failed", "Email not sent.")->withErrors($mail->ErrorInfo);
            }
            
            else {
                return back()->with("success", "Email has been sent.");
            }

        } catch (Exception $e) {
             return back()->with('error','Message could not be sent.');
        }
    }*/

}
