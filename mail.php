<?php
include('conexion_j_i.php');
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (!isset( $_SESSION['id_usuario']) ){ 
    header("location:index_sesion.php");
  }
  
 

    require 'libreria/vendor/autoload.php';

    
    $id_receptor = ( int ) $_SESSION['id_usuario'];
  
    $mail = new PHPMailer(true);
    
$validacion =2 ;

$fecha_hora_actual = date('Y-m-d H:i:s');

if(isset($_POST['enviar_mail'])){

$respuesta = $_POST['respuesta'];

$id_colsulta = $_POST['id_consut'];


if (!preg_match('/^[a-zA-ZáéíóúüÑñ0-9,.!?¿][a-zA-ZáéíóúüÑñ0-9,.!?¿\s]*$/', $respuesta)) {
    header("location:mensajes.php?env=". $validacion);
}else{

    $sql_update = $conexion_jardin->prepare("UPDATE atencion_cliente SET  respuesta_a_cl = '$respuesta' , ID_receptor = '$id_receptor' , date_respuesta_a_cl = '$fecha_hora_actual' , estado_consulta = 'respondido' , lectura= 'no leido' where ID_cunsulta = '$id_colsulta'  ");

    if($sql_update->execute()== true){
        
    $buscar_datos =  $conexion_jardin->prepare(" SELECT atc.* FROM atencion_cliente as atc  WHERE atc.ID_cunsulta = '$id_colsulta'; ");
    $buscar_datos->execute();
    $result_busqueda = $buscar_datos->fetch();



    

    try {
        $mail->isSMTP();
      //  $mail->SMTPDebug = SMTP::DEBUG_SERVER;  Opciones: SMTP::DEBUG_OFF, SMTP::DEBUG_CLIENT, SMTP::DEBUG_SERVER

        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jardin.mundo.acuarela@gmail.com';
        $mail->Password = 'nooe lvhs j sat eboc';
    //    $mail->SMTPSecure = 'SSL'; 
    // $mail->Port = 465;

    //  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
      

     $mail->setFrom('jardin.mundo.acuarela@gmail.com', 'jardin');
        $mail->addAddress($result_busqueda['correo_a_cl'], $result_busqueda['nombre_a_cl']. ' ' . $result_busqueda['apellido_a_cl']  );
        $mail->CharSet = 'UTF-8';

        $mail->isHTML(true);
        $mail->Subject = 'Notificacion De Respuesta';

        $ruta_imagen = "https://proyectosjevl.com/mundoacuarela/img/LogoLibros.png" ;
        
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                /* Estilos en línea para mejorar la compatibilidad */
                .email-container {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                    margin: 0 auto;
                    width: 100%;
                    max-width: 600px;
                    border: 1px solid #54b669;
                    border-radius: 5px;
                    text-align: center;
                }
                .email-header {
                    background: url(https://proyectosjevl.com/mundoacuarela/img/FondoMenu.jpg);
                    padding: 10px;
                    text-align: center; 
                    color: black;
                    border-radius: 5px 5px 0 0;
                    background-size: cover ;
                }
                .email-body {
                    background-color: white;
                    padding: 20px;
                    border-radius: 0 0 5px 5px;
                }
                .email-footer {
                    text-align: center;
                    margin-top: 20px;
                    font-size: 12px;
                    color: #888;
                }
                .btn-custom {
                    display: inline-block;
                    padding: 10px 20px;
                    margin: 20px 0;
                    font-size: 16px;
                    color: white;
                    background-color: #ff6f61;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .btn-custom:hover {
                    background-color: #e65d55;
                }
                .profile-img {
                    width: 100px;
                    height: 100px;
                   
                    
                }
                .content-container {
                    border: 1px solid #ddd;
                    padding: 20px;
                    border-radius: 5px;
                    background-color: #ffffff;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    margin: 0 auto;
                }
                a.btn-custom {
                    color: white;
                }
            </style>
        </head>
        <body>
            <table class="email-container">
                <tr>
                    <td>
                        <table class="email-header" style=" padding: 10px; text-align: center;  border-radius: 5px 5px 0 0; width: 100%;">
                            <tr>
                                <td>
                                    <h1 style="color: #397f1d;"> MUNDO ACUARELA 
</h1>
                                </td>
                                <img src="' . $ruta_imagen . '" alt="Profile Image" class="profile-img" style="width: 90px; height: 75px; ">
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table class="email-body" style="background-color: white; padding: 20px; border-radius: 0 0 5px 5px; width: 100%;">
                            <tr>
                                <td>
                                    <table class="content-container" style="border: 1px solid  white; padding: 20px; border-radius: 5px; background-color: #ffffff; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; margin: 0 auto; width: 100%;">
                                        <tr>
                                            <td>
                                           
                                                <p style="font-size: 16px;">Ya fue Respondido El Mensaje Enviado  </p>
                                                <p style="font-size: 16px;">El <strong>' . $result_busqueda['date_consulta_a_cl'] . '</strong></p>
                                                  <p style="font-size: 16px;"> Mensaje : <strong>' . $result_busqueda['consuta_a_cl'] . '</strong></p>

                                    <p style="font-size: 16px;">Respuesta : '. $result_busqueda['respuesta_a_cl'] . '</p>

                                                <p style="font-size: 16px;">Inicia Sesion Para Ver Mas Detalles como Quien Lo Respondio </p>
                                                <p><a href="https://proyectosjevl.com/mundoacuarela/index_sesion.php" class="btn btn-custom" style="display: inline-block; padding: 10px 20px; margin: 20px 0; font-size: 16px; color: white; background-color:#9561ff; text-decoration: none; border-radius: 5px;">Iniciar Sesion</a></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-size: 14px;">Si No Enviaste Ningun Mensaje , Por Favor Ignora Este Correo.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table class="email-footer" style="text-align: center; margin-top: 20px; font-size: 12px; color: #888; width: 100%;">
                            <tr>
                                <td>
                                    <p>© 2024 Jardin Infantil Mundo Acuarela. Todos los derechos reservados.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        // $mail->AltBody = '¡Estás a un paso de ser parte de nuestra familia Pan-Art! Solo tienes que digitar el siguiente código: ' ;

     if ( $mail->send()== true ){
        $validacion = 1;
       
        header("location:mensajes.php?env=". $validacion);

        }
        
    } catch (Exception $e) {
        echo "no se evio {$mail->ErrorInfo} ";
    }


    }else{
        echo 'error en el update' ;
    }






 

}


}    





?>