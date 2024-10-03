<?php



include ('conexion_j_i.php');

session_start();

if (!isset( $_SESSION['id_usuario']) or $_SESSION['id_usuario']>0 ){  
  header("location:index_usuarios.php");
}

$validacon_pp = 0 ;
$materias_profesores_opciones = [
    "lenguaje",
  "motricidad",
  "ciencias naturales",
  "Psicomotricidad",
  "arte",
  "deportes",
  "desarrollo social",
  "educacion emocional",
  "Cognitivo",
  "musica",
  "salud",
  "cultura y valores"
  ];
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
   
    
   
    $materia_recibida = strtolower($nombreMateria);

    $sql_date_profe = $conexion_jardin->prepare("SELECT fechanacimiento FROM  usuarios WHERE  ID_usuario = '$idProfe'  ; " );
$sql_date_profe->execute();
$fecha_nacimineto_p = $sql_date_profe->fetch();
// validar xp
$edad_minima_trabajar = 18;
$fecha_nacimiento_dt = new DateTime($fecha_nacimineto_p['fechanacimiento']);

$fecha_actual_dt = new DateTime();

$edad_real = $fecha_actual_dt->diff($fecha_nacimiento_dt)->y;

$edad_laboral = $edad_real - $edad_minima_trabajar;

    if (!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/',  $nombreMateria ) ) {
       
        header("location:index_usuarios.php?vcpp=".$validacon_pp . '&p_err=2' );
  
     }elseif (!in_array($materia_recibida, $materias_profesores_opciones)){
           
        header("location:index_usuarios.php?vcpp=".$validacon_pp . '&p_err=2' );
        
    }elseif (!preg_match('/^[1-9]\d{9}$/',  $telefonoProfe )) {
        
        header("location:index_usuarios.php?vcpp=".$validacon_pp . '&p_err=2' );
        
    }elseif ($aniosExperiencia > $edad_laboral || $aniosExperiencia <= 0) {
       
        header("location:index_usuarios.php?vcpp=".$validacon_pp . '&xp_er=2' );

    }else{
        $sql_profesor_insert->execute();
        $validacon_pp = 1;

        header("location:index_usuarios.php?vcpp=".$validacon_pp);
    }
  

}

//  crer observacion del niño

$validacon_dn=0;



// $randon = mt_rand(1, 7);

// $randon_2 = mt_rand(1, 30);
// $randon_3 = mt_rand(1, 24);

// $fecha_hora_actual = date("Y-$randon-$randon_2 $randon_3:i:s");

$fecha_hora_actual = date("Y-m-d  H:i:s");

if(isset($_POST['create_observa'])){

$descripcion_obser = $_POST['decripcion_obser'];

$id_nino = (int) $_POST['id_nino_ob'];

    if(!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $descripcion_obser)){
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

    if(!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $descripcion_obser_up)){

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
$validation_age = 0;
$validation_document=0;

 
if(isset($_POST['matricula_rol_3'])){

  $uploadedFile = $_FILES['file'];

  $uploadedFileImage = $_FILES['fileimage'];

  $nombre_nino = $_POST['nombre_nino_ma'];

  $apellido_nino = $_POST['apellido_nino_ma'];
  
  $edad_nino = $_POST['edad_nino_ma'];
  
  $nacimiento_nino = $_POST['nacimiento_nino_ma'];

  $numre_doc_nino = $_POST['doc_nino_ma'];

  $id_tutor = (int) $_POST['tutor_id'];

 $date_now = new DateTime();
    $date_time = new DateTime($nacimiento_nino);
    $age_now = $date_now->diff($date_time)->y;
    $sql_verify = $conexion_jardin->prepare("SELECT doc_identidad FROM alumno WHERE doc_identidad=:doc_identidad");
    $sql_verify -> bindParam(':doc_identidad',$numre_doc_nino);
    $sql_verify -> execute();

  if ($uploadedFile['error'] !== 0 || $uploadedFileImage['error'] !== 0) {
    $validacion_matricula  = 4;

     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);
     
    // echo "Error uploading file: " . $uploadedFile['error'];
    exit;
  }

    
  $mimeType = $uploadedFile['type'];
  $mimeTypeImg = $uploadedFileImage['type'];
  $allowedMimeTypes = ['application/pdf'];
  $allowedImageMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];


  if (!in_array($mimeType, $allowedMimeTypes) || !in_array($mimeTypeImg, $allowedImageMimeTypes)) {
    $validacion_matricula = 4;
    header("location:index_usuarios.php?v_mtr=" . $validacion_matricula);
    exit;
}


  if (!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/',  $nombre_nino ) || !preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/',   $apellido_nino ) ) {
        
     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);
    // echo 'fallo nombre o apellido ';
    
}elseif ( $edad_nino > 6  ||  $edad_nino < 1) {
    // echo 'edad ';

    
     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);
    
}elseif (  !preg_match('/^[\d+]+$/',   $numre_doc_nino  )) {
    // echo 'docuemnto ';

     header("location:index_usuarios.php?v_mtr=".$validacion_matricula);

} elseif ($sql_verify->rowCount()>0){  
    $validation_document=1;
    header("location:index_usuarios.php?v_msd=" . $validation_document);
} else {
    if ($age_now == $edad_nino) {
        $nombre_pdf = $uploadedFile['name'];
        $nombre_image = $uploadedFileImage['name'];
        $temporal_url = $uploadedFile['tmp_name'];
        $temporal_url_img = $uploadedFileImage['tmp_name'];
        $nombre_unico = uniqid() . '_' . $nombre_pdf;
        $nombre_unico_img = uniqid() . '_' . $nombre_image;
        $ruta = "public/pdf/" . $nombre_unico;
        $ruta_img = "public/img/" . $nombre_unico_img;
        move_uploaded_file($temporal_url, $ruta);
        move_uploaded_file($temporal_url_img, $ruta_img);

        $sql_insert_nino = $conexion_jardin->prepare("INSERT INTO alumno (ID_tutor, nombre_a, apellido_a, doc_identidad, fecha_nacimiento, edad, info_eps, foto_alumno) VALUES (:id, :nom, :ape, :doc, :fech, :edad, :eps, :foto)");
        $sql_insert_nino->bindParam(':id', $id_tutor, PDO::PARAM_INT);
        $sql_insert_nino->bindParam(':nom', $nombre_nino, PDO::PARAM_STR);
        $sql_insert_nino->bindParam(':ape', $apellido_nino, PDO::PARAM_STR);
        $sql_insert_nino->bindParam(':doc', $numre_doc_nino, PDO::PARAM_STR);
        $sql_insert_nino->bindParam(':fech', $nacimiento_nino, PDO::PARAM_STR);
        $sql_insert_nino->bindParam(':edad', $edad_nino, PDO::PARAM_INT);
        $sql_insert_nino->bindParam(':eps', $ruta, PDO::PARAM_STR);
        $sql_insert_nino->bindParam(':foto', $ruta_img, PDO::PARAM_STR);

        if ($sql_insert_nino->execute()) {
            $validacion_matricula = 1;
            header("location:index_usuarios.php?v_mtr=" . $validacion_matricula);
        } else {
            $validacion_matricula = 5;
            header("location:index_usuarios.php?v_mtr=" . $validacion_matricula);
        }
    } else {
        $validation_age = 1;
        header("location:index_usuarios.php?v_ma=" . $validation_age);
 

    }
}
}


// completar perfil acudiente

$validacion_p_acudiente = 0; 
if(isset($_POST['insert_acudiente'])){

$cel_1 = $_POST['cel_acudiente'];
$cel_2 = $_POST['cel_alterno'];
$direccion_acu = $_POST['direccion_acudiente'];

$id_acudiente = (int) $_POST['id_acudiente_cp'];

  
   
if(!preg_match('/^[1-9]\d{9}$/', $cel_1) or !preg_match('/^[1-9]\d{9}$/', $cel_2) ){ 
        
    header("location:index_usuarios.php?v_acu=".$validacion_p_acudiente);
    
}elseif(!preg_match('/^[a-zA-Z0-9\s\]]+$/',$direccion_acu )){
    
    header("location:index_usuarios.php?v_acu=".$validacion_p_acudiente);

}else{
$sql_acudiente = $conexion_jardin->prepare("INSERT INTO acudientes(ID_usuario_fk,celular, direccion,emergencia_cel)
values(:id , :celular, :direcion , :cel_emer);");

$sql_acudiente->bindParam(':id',$id_acudiente , PDO::PARAM_INT);
$sql_acudiente->bindParam(':celular',$cel_1, PDO::PARAM_STR);
$sql_acudiente->bindParam(':direcion',$direccion_acu, PDO::PARAM_STR);
$sql_acudiente->bindParam(':cel_emer',$cel_2, PDO::PARAM_STR);

$sql_acudiente->execute();
$validacion_p_acudiente = 1 ;
header("location:index_usuarios.php?v_acu=".$validacion_p_acudiente);


} 
         
}



?>

