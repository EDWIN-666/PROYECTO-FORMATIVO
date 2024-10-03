<?php 
include('conexion_j_i.php');
session_start();

if (!isset( $_SESSION['id_usuario'])){ 
    header("location:index_sesion.php");
  }
  

if ($_SERVER['REQUEST_METHOD']==  'GET') {

    $parametros = $_GET;

    // Iterar sobre los parámetros y realizar acciones según el nombre
    foreach ($parametros as $nombre_parametro => $valor) {
        switch ($nombre_parametro) {
            case 'bloq':
              
                $correo = $valor;

                $sql_bloqueo = $conexion_jardin->prepare("UPDATE atencion_cliente SET bloqueo ='si' WHERE correo_a_cl = '$correo' ; ");
                if($sql_bloqueo->execute()==true){
                    header("location:mensajes.php?env= ". 1 );
                    break;
 
                }else{
                    header("location:mensajes.php?env= ". 2 );
                    break;
                }
                
               
            case 'desbloq':

                $correo = $valor;

                $sql_desbloqueo = $conexion_jardin->prepare("UPDATE atencion_cliente SET bloqueo ='no' WHERE correo_a_cl = '$correo' ; ");
                if($sql_desbloqueo->execute()==true){
                  header("location:mensajes.php?env= ". 1 );
                    // echo 'sql del desbloqueo';
                    break;
 
                }else{
                    header("location:mensajes.php?env= ". 2 );
                    break;
                }
            default:
                // Si no coincide con ningún caso, puedes manejarlo aquí
                echo "Parámetro desconocido: $nombre_parametro";
                break;
        }
    }
}
    


?>