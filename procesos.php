<?php

use FontLib\Table\Type\name;

include ('conexion_j_i.php');

session_start();

// if (!isset( $_SESSION['id_usuario']) or $_SESSION['id_usuario']>0 ){  
//   header("location:index_usuarios.php");
// }

$validacon_pp = 0 ;

if(isset($_POST['insert_cpp'])){

    $nombreMateria = $_POST['nombre_materia_cpp'];
    $telefonoProfe = $_POST['cel_profe_cpp'];
    $aniosExperiencia = (int) $_POST['años_experi_cpp'];
    $idProfe = (int) $_POST['id_profe_cpp'];

    $sql_profesor_insert = $conexion_jardin->prepare("INSERT INTO  profesor (ID_profesor , materia, celular, years_experiencia)
    VALUES (:id,:materia,:celular,:y_xp);");
    $sql_profesor_insert ->bindParam(':id' , $idProfe ,PDO::PARAM_INT);
    $sql_profesor_insert->bindParam(':materia', $nombreMateria , PDO::PARAM_STR);   
    $sql_profesor_insert->bindParam(':celular', $telefonoProfe,PDO::PARAM_STR);
    $sql_profesor_insert->bindParam(':y_xp', $aniosExperiencia, PDO::PARAM_INT);
    
   
    if (!preg_match('/^[a-zA-Z]+$/', $nombreMateria)) {
        
        header("location:index_usuarios.php?vcpp=".$validacon_pp);
        
    }elseif (strlen($telefonoProfe) != 10) {
        
        header("location:index_usuarios.php?vcpp=".$validacon_pp);
        
    }elseif ($aniosExperiencia <= 0) {
       
        header("location:index_usuarios.php?vcpp=".$validacon_pp);
    
    }else{
        $sql_profesor_insert->execute();
        $validacon_pp = 1;

        header("location:index_usuarios.php?vcpp=".$validacon_pp);
    }
  

}

//  crer observacion del niño

$validacon_dn=0;
$fecha_hora_actual = date('Y-m-d H:i:s');

if(isset($_POST['create_observa'])){

$descripcion_obser = $_POST['decripcion_obser'];

$id_nino = (int) $_POST['id_nino_ob'];

    if(!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $descripcion_obser)){
        header("location:index_usuarios.php?dn_v=".$validacon_dn);
        
    }else
    {
$sql_observacion__insert =$conexion_jardin -> prepare("INSERT INTO  observaciones(descripcion,fecha_hora_creacion,id_nino_fk)
VALUES(:descrip,:fecha_all,:id) ;");  
$sql_observacion__insert->bindParam(':descrip', $descripcion_obser, PDO::PARAM_STR);

$sql_observacion__insert->bindParam(':fecha_all', $fecha_hora_actual,PDO::PARAM_STR);

$sql_observacion__insert->bindParam(':id',$id_nino,PDO::PARAM_INT);

if ($sql_observacion__insert->execute()){
    $validacon_dn = 1;

        header("location:index_usuarios.php?dn_v=".$validacon_dn);
   
}

    }

}

// MANDAR ID DEL AJAX  AL PHP , OBSERVAcioness del niño 



 
    if(isset($_POST['id_alumno'])) {
        $_SESSION['id_nino_ajax'] = $_POST['id_alumno'];
    
}

$validacon_dn_update=0;

if(isset($_POST['update_observa'])){

$descripcion_obser_up = $_POST['decripcion_obser_update'];

$id_ob = (int) $_POST['id_observacion'];

    if(!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $descripcion_obser_up)){

         header("location:index_usuarios.php?dn_v_up=".$validacon_dn_update);
        // echo 'no sirve exprecion';
    }else
    {
$sql_observacion__update =$conexion_jardin -> prepare("UPDATE observaciones set descripcion= :descrip  where id_observacion = :id ;");  
$sql_observacion__update->bindParam(':descrip', $descripcion_obser_up, PDO::PARAM_STR);

$sql_observacion__update->bindParam(':id',$id_ob,PDO::PARAM_INT);

if ($sql_observacion__update->execute()==true){
    $validacon_dn_update = 1;
    header("location:index_usuarios.php?dn_v_up=".$validacon_dn_update);

// echo 'el sql'   ;
}

    }

}else{
    // echo 'no entra al isset';
}

if (isset($_POST['eliminar_observacion'])){

$id_obse_dele = (int) $_POST['id_observacion_dele'];

$sql_dele_observacion= $conexion_jardin->prepare("DELETE FROM observaciones WHERE id_observacion = :id");

$sql_dele_observacion->bindParam(':id',$id_obse_dele, PDO::PARAM_INT);

if($sql_dele_observacion->execute()==true){
    $validacon_dn_update = 4;

    header("location:index_usuarios.php?dn_v_up=".$validacon_dn_update);

}

}

//  realizacion de la matricula  ---------------------------------

$validacion_matricula = 0 ;
 
if(isset($_POST['matricula_rol_3'])){

  $uploadedFile = $_FILES['file'];
  
  $nombre_nino = $_POST['nombre_nino_ma'];

  $apellido_nino = $_POST['apellido_nino_ma'];
  
  $edad_nino = $_POST['edad_nino_ma'];
  
  $nacimento_nino = $_POST['nacimiento_nino_ma'];

  $numre_doc_nino = $_POST['doc_nino_ma'];

  $id_tutor = (int) $_POST['tutor_id'];


  if ($uploadedFile['error'] !== 0) {
    $validacion_matricula  = 4;

     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);
     
    // echo "Error uploading file: " . $uploadedFile['error'];
    exit;
  }

    
  $mimeType = $uploadedFile['type'];
  if ($mimeType !== 'application/pdf') {
    $validacion_matricula  = 4;

     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);
     
    // echo "  Invalid file  tipo ";
    exit;
  }


  if (!preg_match('/^[a-zA-Záéíóúü ]+$/',  $nombre_nino ) || !preg_match('/^[a-zA-Záéíóúü ]+$/',   $apellido_nino ) ) {
        
     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);
    // echo 'fallo nombre o apellido ';
    
}elseif ( $edad_nino > 6  ||  $edad_nino < 1) {
    // echo 'edad ';

    
     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);
    
}elseif (  !preg_match('/^[\d+]+$/',   $numre_doc_nino  )) {
    // echo 'docuemnto ';

     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);

}else{

    $nombre_pdf = $uploadedFile['name'];
  $temportal_url = $uploadedFile['tmp_name'];

  $nombre_unico = uniqid() . '_' . $nombre_pdf;
  $ruta = "public/pdf/" . $nombre_unico;

  $nombre_unico = uniqid().'_' .$nombre_pdf;
      $ruta = "public/pdf/".$nombre_unico;
  
       move_uploaded_file($temportal_url,$ruta);
  
    //   echo $ruta; 

    $sql_insert_ninno = $conexion_jardin->prepare("INSERT INTO  alumno(ID_tutor,nombre_a,apellido_a , doc_identidad,fecha_nacimiento, edad , info_eps )
    value( :id , :nom ,:ape ,:doc, :fech ,:edad ,:eps );");

$sql_insert_ninno->bindParam(':id',$id_tutor, PDO::PARAM_INT);
$sql_insert_ninno->bindParam(':nom',$nombre_nino, PDO::PARAM_STR);
$sql_insert_ninno->bindParam(':ape',$apellido_nino, PDO::PARAM_STR);
$sql_insert_ninno->bindParam(':doc',$numre_doc_nino, PDO::PARAM_STR);
$sql_insert_ninno->bindParam(':fech',$nacimento_nino, PDO::PARAM_STR);
$sql_insert_ninno->bindParam(':edad',$edad_nino, PDO::PARAM_INT);
$sql_insert_ninno->bindParam(':eps',$ruta, PDO::PARAM_STR);



  if ( $sql_insert_ninno->execute() ==true ){ 
        $validacion_matricula  = 1;

         header("location:index_usuarios.php?v_mtr=".$validacion_matricula);
         
    //  echo "<br/> File uploaded successfully!";
    }else{
        // echo 'fallo la insercion'; 
    }

}
    

}

?>