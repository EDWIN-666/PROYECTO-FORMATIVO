<?php 
include('conexion_j_i.php');
session_start();



if (!isset( $_SESSION['id_usuario'])){ 
  header("location:index_sesion.php");
}


if($_SERVER['REQUEST_METHOD']== 'GET'){
    $id = $_GET['del'];

    $sql  = $conexion_jardin->prepare("DELETE FROM atencion_cliente   WHERE ID_cunsulta = '$id'; ");

    if($sql->execute() == true){

    header("location:mensajes.php?env= ". 1 );

    }else{
        header("location:mensajes.php?env= ". 2 );
    }

}
?>