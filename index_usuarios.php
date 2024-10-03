<?php 
include ('conexion_j_i.php');
session_start();

$id_usuario_index = $_SESSION['id_usuario'];
 $consul_rol_usuario_index = $conexion_jardin -> prepare("SELECT usuarios.* , rol_usuario.* from usuarios inner join rol_usuario on usuarios.rol_u = rol_usuario.id_rol  where  usuarios.ID_usuario= '$id_usuario_index' and usuarios.activo = 1 ;");
 $consul_rol_usuario_index ->execute();
 $resul_index_rol = $consul_rol_usuario_index->fetch();

 $cambio_z_indexprofesor = 3;


if (!isset( $_SESSION['id_usuario']) or $resul_index_rol['activo']==0){  //id para poder sacar el rol 
  header("location:index_sesion.php");
}


 // consulta por si el rol es  profesor, completar su perfil 
if($resul_index_rol['rol_u']==2){
  $sql_perfil_profesor = $conexion_jardin->prepare("SELECT p.* from profesor as p  WHERE p.ID_profesor =  '$id_usuario_index' ");
  $sql_perfil_profesor -> execute();
  if ($sql_perfil_profesor->rowCount()>0){
    // echo '<h1 class="text-light">profesor con perfil completo (esta en la tabla profesor )</h1>';
  }else{
    //  echo '<h1 class="text-light">profesor sin perfil completo ( NOOO esta en la tabla profesor )</h1>'; 
    $cambio_z_indexprofesor =2;
    echo '<script type="text/javascript">',
'localStorage.setItem("abrir_modal", "true");',
         '</script>';
         

  }

}elseif($resul_index_rol['rol_u']==3){
  $sql_perfil_profesor = $conexion_jardin->prepare("SELECT cu.ID_acudiente from acudientes as cu where  cu.ID_usuario_fk = '$id_usuario_index' ");
  $sql_perfil_profesor -> execute();
  if ($sql_perfil_profesor->rowCount()>0){
    // echo '<h1 class="text-light">profesor con perfil completo (esta en la tabla profesor )</h1>';
  }else{
    //  echo '<h1 class="text-light">profesor sin perfil completo ( NOOO esta en la tabla profesor )</h1>'; 
    $cambio_z_indexprofesor =2;
    echo '<script type="text/javascript">',
'localStorage.setItem("modal_acudiente", "true");',
         '</script>';
         

  }

}
  
// echo  $cambio_z_indexprofesor ;

//  pedir acceso 
 $consul_peticiones = $conexion_jardin-> prepare("SELECT * FROM usuarios where activo = 0 ;");
$consul_peticiones->execute();
$resultado_peticiones = $consul_peticiones -> fetchAll();

$actualizacion=0;//cambios
$eliminacion=0;//cambios
$agregacion=0;//cambios


if (isset($_POST['activar_cuenta'])) {
    $id_ac = $_POST['id_cuenta'];
    $tipo_rol_reg = $_POST['tipo_rol_reg'];
    
    $activar_cuenta = $conexion_jardin->prepare("UPDATE usuarios SET activo = 1, rol_u = :rol_reg WHERE ID_usuario = :id_ac");
    $activar_cuenta->bindParam(':rol_reg', $tipo_rol_reg, PDO::PARAM_INT);
    $activar_cuenta->bindParam(':id_ac', $id_ac, PDO::PARAM_INT);
    
    if ($activar_cuenta->execute()) {
      $actualizacion=1;//cambios
      header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios
        
    } else {
        // Manejar el error de actualización aquí
        echo "Error al actualizar la cuenta.";
    }
}


if(isset($_POST['eliminar_cuenta'])){
  $id_ac = $_POST['id_cuenta_dele'];
  $eliminar_cuenta = $conexion_jardin ->prepare("DELETE from  usuarios  where  ID_usuario = '$id_ac' ;");
  $eliminar_cuenta ->execute();
  $eliminacion=1;//cambios
  header("location:index_usuarios.php?el_n=".$eliminacion);//cambios
  }

  // grupos
  $resultado_grupos=null;
  $consul_grupos = null; 
  if ($resul_index_rol['rol_u'] == 1 || $resul_index_rol['rol_u']== 4 || $resul_index_rol['rol_u'] == 5 || $resul_index_rol['rol_u'] == 6) {
    $consul_grupos = $conexion_jardin->prepare("SELECT g.* , p.* ,u.nombre_u,u.apellido_u,u.correo_u,u.fechanacimiento , nd.* from grupos_clases as g 
    LEFT join profesor as p on g.id_profesor_fk = p.ID_tabla_p 
    LEFT join usuarios as u on u.ID_usuario = p.ID_profesor 
    LEFT JOIN nivel_educ as nd  on nd.ID_nivel = g.nivel  ORDER BY  g.num_aula ;"); // where u.rol_u = 2
    if ($consul_grupos) {
      $consul_grupos->execute();
      $resultado_grupos = $consul_grupos -> fetchAll();

    } else {
     
      
    }
  } elseif ($resul_index_rol['rol_u'] == 2) {
    $consul_grupos = $conexion_jardin->prepare("SELECT g.* , p.* ,u.nombre_u,u.apellido_u,u.correo_u,u.fechanacimiento , nd.* from grupos_clases as g 
    LEFT join profesor as p on g.id_profesor_fk = p.ID_tabla_p 
    LEFT join usuarios as u on u.ID_usuario = p.ID_profesor 
    LEFT JOIN nivel_educ as nd  on nd.ID_nivel = g.nivel where p.ID_profesor =' $id_usuario_index' ORDER BY  g.num_aula ;"); // where u.rol_u = 2
  //  var_dump($id_usuario_index);
    if ($consul_grupos) {
      $consul_grupos->execute();
      $resultado_grupos = $consul_grupos -> fetchAll();

    } else {
     
      echo 'errror 2 if ';

    }
  }
 $grupo_va_back = 3 ;

if(isset($_POST['update_g'])){
  $id_g = (int) $_POST['id_grupo'];
  $ficha = $_POST['new_ficha'] ;
  $aula = (int)$_POST['new_aula'];
  $profe =  (int)$_POST['new_profe_g'];
  $nivel =  (int)$_POST['new_nivel_g'];


  if (!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $ficha  )){
    $grupo_va_back = 2 ;
   header("location:index_usuarios.php?g_er=".$grupo_va_back);
   // echo ' no entro ' . $ficha ;
    return;
    
  }elseif($aula>20 || !preg_match('/^(1[0-9]|20|[1-9])$/', $aula  )){
    $grupo_va_back = 2 ;
    header("location:index_usuarios.php?g_er=".$grupo_va_back);
  }else{
  
  $update_grupo = $conexion_jardin ->prepare("UPDATE  grupos_clases set ficha=:new_ficha , num_aula = :new_aula   , id_profesor_fk = :new_profe , nivel = :nivel   where  ID_g_c = '$id_g' ;");
$update_grupo -> bindParam(':new_ficha' , $ficha, pdo::PARAM_STR);
$update_grupo -> bindParam(':new_aula', $aula, pdo::PARAM_INT);
$update_grupo->bindParam(':new_profe',$profe, pdo::PARAM_INT) ;
$update_grupo->bindParam(':nivel',$nivel, pdo::PARAM_INT) ;

$update_grupo->execute() ;
$actualizacion=1;//cambios
header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios
  }
  }

 
  if(isset($_POST['insert_g'])){  
    $ficha = $_POST['insert_ficha'] ;
    $aula = (int)$_POST['insert_aula'];
    $profe =  (int)$_POST['insert_profe_g'];
    $nivel  =  (int)$_POST['insert_nivel_g'];

if (!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $ficha  )){
  $grupo_va_back = 2 ;
 header("location:index_usuarios.php?g_er=".$grupo_va_back);
 // echo ' no entro ' . $ficha ;
  return;
  
}elseif($aula>20 || !preg_match('/^(1[0-9]|20|[1-9])*$/', $aula  )){
  $grupo_va_back = 2 ;
  header("location:index_usuarios.php?g_er=".$grupo_va_back);
}else{

   $insert_grupo = $conexion_jardin ->prepare("INSERT INTO  grupos_clases (ficha,num_aula,id_profesor_fk , nivel) values (:new_ficha , :new_aula  , :new_profe , :new_nivel) ; ");
  $insert_grupo -> bindParam(':new_ficha' , $ficha, pdo::PARAM_STR);
  $insert_grupo -> bindParam(':new_aula', $aula, pdo::PARAM_INT);
  $insert_grupo->bindParam(':new_profe',$profe, pdo::PARAM_INT) ;
  $insert_grupo->bindParam(':new_nivel',$nivel, pdo::PARAM_INT) ;

  $insert_grupo->execute() ;
  $agregacion=1;//cambios
  header("location:index_usuarios.php?ag_n=".$agregacion);//cambios
// echo 'se executo ' . $ficha ; 
}

   
    }
  if(isset($_POST['eliminar_g'])){
    $id_dele_g = (int) $_POST['id_g_dele'];
    $eliminar_grupo = $conexion_jardin ->prepare("DELETE from  grupos_clases  where  ID_g_c = '$id_dele_g' ;");
    $eliminar_grupo ->execute();
    $eliminacion=1;//cambios
    header("location:index_usuarios.php?el_n=".$eliminacion);//cambios
     }

//niños -------------------------------------------
$colsulta_niños = null ;

if ($resul_index_rol['rol_u']<>2){
  $colsulta_niños = $conexion_jardin-> prepare('SELECT a.*, u.*, g.* FROM alumno AS a LEFT JOIN usuarios AS u ON u.ID_usuario = a.ID_tutor LEFT JOIN grupos_clases AS g ON g.ID_g_c = a.ID_grupo_fk;');
  $colsulta_niños ->execute();

}else {

  $sql_id_tabla_profesor = $conexion_jardin->prepare("SELECT ID_tabla_p FROM  profesor where ID_profesor = '$id_usuario_index';");
$sql_id_tabla_profesor->execute();
$result_sql_id_tp= $sql_id_tabla_profesor->fetch();

if ($sql_id_tabla_profesor->rowCount()>0){
  // $colsulta_niños = $conexion_jardin-> prepare("SELECT a.* , u.*, g.*  from alumno as a INNER join usuarios as u on u.ID_usuario = a.ID_tutor INNER join grupos_clases AS g on g.ID_g_c = a.ID_grupo_fk; ");
$colsulta_niños = $conexion_jardin-> prepare("SELECT a.* , u.*, g.*  from alumno as a INNER join usuarios as u on u.ID_usuario = a.ID_tutor INNER join grupos_clases AS g on g.ID_g_c = a.ID_grupo_fk where g.id_profesor_fk = '$result_sql_id_tp[ID_tabla_p]' ; ");
  $colsulta_niños ->execute();
}else {
  $colsulta_niños = $conexion_jardin-> prepare('SELECT a.* , u.*, g.*  from alumno as a INNER join usuarios as u on u.ID_usuario = a.ID_tutor INNER join grupos_clases AS g on g.ID_g_c = a.ID_grupo_fk;');
  $colsulta_niños ->execute();
}

}

$resultado_niños = $colsulta_niños-> fetchAll();


if(isset($_POST['update_ni'])){
  $id_nino = (int)$_POST['id_nino'];
 $nombre_n = $_POST['update_nombre_nino'];
 $apellido_n = $_POST['update_apellido_nino'];
 $edad_ni = (int)$_POST['update_edad_nino'];
 $doc_ni = $_POST['update_doc_nino'];
 $fecha_n = $_POST['update_fecha_nino'];

 // $eps_nin = $_POST['update_eps_nino'];

  $ficha_n = (int)$_POST['update_ficha_nino'] ;
  $tu =  (int)$_POST['update_tutor_nino'];



  //verificar edad y documento no se repita 
  $date_now = new DateTime();
  $date_time = new DateTime($fecha_n);
  $age_now = $date_now->diff($date_time)->y;
 //  $sql_verify = $conexion_jardin->prepare("SELECT doc_identidad FROM alumno WHERE doc_identidad=:doc_identidad");
 //  $sql_verify -> bindParam(':doc_identidad',$doc_ni);
 //  $sql_verify -> execute();
 //  $sql_verify_doc = $sql_verify->fetch();



 $pdf_update = 0;
 $foto_update = 0;
 $sql_verify_1 = $conexion_jardin->prepare("SELECT doc_identidad FROM alumno WHERE doc_identidad= :doc_identidad AND ID_alumno <> '$id_nino' ; ");
    $sql_verify_1 -> bindParam(':doc_identidad',$doc_ni);
    $sql_verify_1 -> execute();

  


if (!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/',  $nombre_n ) || !preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/',  $apellido_n ) ) {
     
 $actualizacion = 2;
 header("location:index_usuarios.php?ac_n=".$actualizacion);


}elseif ( $edad_ni > 6  ||  $edad_ni < 1) {

$actualizacion = 2;
  header("location:index_usuarios.php?ac_n=".$actualizacion);

}elseif (  !preg_match('/^[\d+]+$/',   $doc_ni  ) || $sql_verify_1->rowCount()>0 ) {


 header("location:index_usuarios.php?v_msd=1&ac_n=4");



}else {
 if ($age_now == $edad_ni) {
 // operaciones para actulizacion de foto y pdf eps


  $sql_nino_update = 'UPDATE alumno set 
ID_tutor=:new_tu, ID_grupo_fk= :new_ficha , nombre_a=:new_nombre,
apellido_a=:new_apellido, doc_identidad=:new_documento,
 fecha_nacimiento= :new_fecha  ,edad =:new_edad 
' ;


//verificacion para pdf update
if (isset($_FILES['update_eps_nino']) && $_FILES['update_eps_nino']['error'] !== UPLOAD_ERR_NO_FILE) {

   $uploadedFile = $_FILES['update_eps_nino'];


   if ($uploadedFile['error'] !== 0 ) {
     $actualizacion = 2;
         header("location:index_usuarios.php?ac_n=".$actualizacion);
   //echo "Error uploading file dd: " . $uploadedFile['error'];
     exit;
   }
   
   $mimeType = $uploadedFile['type'];
   $allowedMimeTypes = ['application/pdf'];
 
   if (!in_array($mimeType, $allowedMimeTypes) ) {
     $actualizacion = 2;
     header("location:index_usuarios.php?ac_n=".$actualizacion);
   // echo 'tipo de pdf '; 
    exit;
 }

 $sql_eps_old = $conexion_jardin ->prepare("SELECT info_eps  from  alumno where ID_alumno = '$id_nino';");
 $sql_eps_old->execute();
 $ruta_bd_eps = $sql_eps_old->fetch();

 
 $nombre_pdf = $uploadedFile['name'];
 $temporal_url = $uploadedFile['tmp_name'];
 $nombre_unico = uniqid() . '_' . $nombre_pdf;
 $ruta = "public/pdf/" . $nombre_unico;


$ruta_delet_old =  __DIR__ . '/'.$ruta_bd_eps['info_eps'];


  if(file_exists($ruta_delet_old)){
   if (unlink($ruta_delet_old)) {
    // echo 'si se elimino  el archivo' ;

   move_uploaded_file($temporal_url, $ruta);

      //sql cuando envia pfp update
        $sql_nino_update  .= ', info_eps = :eps' ;

        $pdf_update = 1;



     

    

   }else{
     echo 'no se puedo eliminar ' ;
   }

  }else{
    echo 'no se contro la ruta ' . $ruta_delet_old ;
  }



 }
 //verificacion para  img  update

 if (isset($_FILES['foto_nino_update']) && $_FILES['foto_nino_update']['error'] !== UPLOAD_ERR_NO_FILE){

   $uploadedFileImage = $_FILES['foto_nino_update'];

   if ($_FILES['foto_nino_update']['error'] !== 0) {
     echo "Error uploading file no se: " . $_FILES['foto_nino_update']['error'];
     exit;
 }

   if ($uploadedFileImage['error'] !== 0) {
     $actualizacion = 2;
         header("location:index_usuarios.php?ac_n=".$actualizacion);
    // echo "Error uploading file ee: " . $uploadedFileImage['error'];
     exit;
   }

   $mimeTypeImg = $uploadedFileImage['type'];
 $allowedImageMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
   if (!in_array($mimeTypeImg, $allowedImageMimeTypes) ) {
     $actualizacion = 2;
     header("location:index_usuarios.php?ac_n=".$actualizacion);
    //echo 'tipo de  foto '; 
    exit;
 }

 $sql_foto_old = $conexion_jardin ->prepare("SELECT foto_alumno  from  alumno where ID_alumno = '$id_nino';");
 $sql_foto_old->execute();
 $ruta_bd_foto = $sql_foto_old->fetch();

 
 $nombre_image = $uploadedFileImage['name'];
 $temporal_url_img = $uploadedFileImage['tmp_name'];
 $nombre_unico_img = uniqid() . '_' . $nombre_image;
 $ruta_img = "public/img/" . $nombre_unico_img;
 

$ruta_delet_old_foto =  __DIR__ . '/'.$ruta_bd_foto['foto_alumno'];


  if(file_exists($ruta_delet_old_foto)){
   if (unlink($ruta_delet_old_foto)) {
     //echo 'si se elimino  el archivo foto ' ;

   move_uploaded_file($temporal_url_img, $ruta_img);
   
      //sql cuando envia pfp update
        $sql_nino_update  .= ', foto_alumno = :foto' ;

        $foto_update = 1;

 

    

   }else{
     echo 'no se puedo eliminar ' ;
   }

  }else{
    echo 'no se contro la ruta ' . $ruta_delet_old_foto ;
  }



 }

 $sql_nino_update .= " WHERE ID_alumno = :id_nino";

 // Preparar y ejecutar la consulta SQL
 $insert_ninoss = $conexion_jardin->prepare($sql_nino_update);

 if ($pdf_update == 1) {
     $insert_ninoss->bindParam(':eps', $ruta, PDO::PARAM_STR);
 }
 if ($foto_update == 1) {
     $insert_ninoss->bindParam(':foto', $ruta_img, PDO::PARAM_STR);
 }

 $insert_ninoss->bindParam(':new_tu', $tu, PDO::PARAM_INT);
 $insert_ninoss->bindParam(':new_ficha', $ficha_n, PDO::PARAM_INT);
 $insert_ninoss->bindParam(':new_nombre', $nombre_n, PDO::PARAM_STR);
 $insert_ninoss->bindParam(':new_apellido', $apellido_n, PDO::PARAM_STR);
 $insert_ninoss->bindParam(':new_documento', $doc_ni, PDO::PARAM_STR);
 $insert_ninoss->bindParam(':new_fecha', $fecha_n, PDO::PARAM_STR);
 $insert_ninoss->bindParam(':new_edad', $edad_ni, PDO::PARAM_INT);
 $insert_ninoss->bindParam(':id_nino', $id_nino, PDO::PARAM_INT);

 $insert_ninoss->execute();
$actualizacion=1;//cambios

header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios



}
}

   }


     if(isset($_POST['eliminar_ni'])){
      $id_dele_ni = (int)$_POST['id_ni_dele'];
      $eliminar_ni = $conexion_jardin ->prepare("DELETE from  alumno  where  ID_alumno = '$id_dele_ni' ;");
      //  echo var_dump($id_ni_dele);
       
        $eliminar_ni ->execute();//cambios
        $eliminacion=1;
        header("location:index_usuarios.php?el_n=".$eliminacion);//cambios

        }

     //profesores

     $validacion_com_perf_prof = $_GET['vcpp'] ?? 3 ;

     $validacion_observacion_nino = $_GET['dn_v'] ?? 3 ;

 $validacion_observacion_nino_up = $_GET['dn_v_up'] ?? 3 ;


$consul_profesores = $conexion_jardin -> prepare('SELECT p.* , u.* from profesor as p INNER JOIN usuarios as u on p.ID_profesor = u.ID_usuario;');
$consul_profesores -> execute();
$resultado_profesor  = $consul_profesores-> fetchAll();

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
$p_error = 3 ;
if(isset($_POST['update_p'])){
$id_profe_tabla = (int) $_POST['id_profe_update'];
  $materia_p_u = $_POST['nombre_materia_update'];
  $cel_p_u = (int) $_POST['cel_profe_update'] ;
  $xp_p_u = (int)$_POST['xp_profe_update'];
  $id_p_u = (int) $_POST['profe_update'];

  
  $materia_recibida = strtolower($materia_p_u);

  
  $sql_date_profe = $conexion_jardin->prepare("SELECT fechanacimiento FROM  usuarios WHERE  ID_usuario = '$id_p_u'  ; " );
  $sql_date_profe->execute();
  $fecha_nacimineto_p = $sql_date_profe->fetch();
  // validar xp
  $edad_minima_trabajar = 18;
  $fecha_nacimiento_dt = new DateTime($fecha_nacimineto_p['fechanacimiento']);
  
  $fecha_actual_dt = new DateTime();
  
  $edad_real = $fecha_actual_dt->diff($fecha_nacimiento_dt)->y;
  
  $edad_laboral = $edad_real - $edad_minima_trabajar;
  if (!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/',  $materia_p_u ) ) {
     
    $p_error = 2 ;
    header("location:index_usuarios.php?p_err=".$p_error);

   }elseif (!in_array($materia_recibida, $materias_profesores_opciones)){
    $p_error = 2 ;
    header("location:index_usuarios.php?p_err=".$p_error);

    }elseif (!preg_match('/^[1-9]\d{9}$/',  $cel_p_u )) {
      $p_error = 2 ;
    header("location:index_usuarios.php?p_err=".$p_error);
    }elseif ($xp_p_u > $edad_laboral || $xp_p_u <= 0) {
      header("location:index_usuarios.php?xp_er=2");

    }else{
      $update_porfesor = $conexion_jardin->prepare("UPDATE profesor SET  
      ID_profesor=:id_profe, materia=:materia, celular=:celular,years_experiencia=:xp
    where  ID_tabla_p = '$id_profe_tabla';");
    $update_porfesor-> bindParam(':id_profe', $id_p_u, pdo::PARAM_INT);
    $update_porfesor -> bindParam(':materia' , $materia_recibida, pdo::PARAM_STR);
    $update_porfesor-> bindParam(':celular', $cel_p_u, pdo::PARAM_INT);
    $update_porfesor-> bindParam(':xp', $xp_p_u, pdo::PARAM_INT);
    $update_porfesor ->execute();
    $actualizacion=1;//cambios
  
    header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios
    }

   
 }
if(isset($_POST['eliminar_profe'])){
  $id_dele_prof = (int)$_POST['id_profe_dele'];
  $eliminar_prof= $conexion_jardin ->prepare("DELETE from  profesor  where  ID_tabla_p = '$id_dele_prof' ;");
  //  echo var_dump($id_ni_dele);
    $eliminar_prof ->execute();
    $eliminar_prof ->execute();
    $eliminacion=1;//cambios

    header("location:index_usuarios.php?el_n=".$eliminacion);//cambios

}
// usuarios
$consul_usuarios_activos = $conexion_jardin-> prepare("SELECT u.* , nombre_rol from usuarios as u inner join rol_usuario on u.rol_u = rol_usuario.id_rol  where u.activo = 1 ;");
$consul_usuarios_activos->execute();
$resultado_usuarios = $consul_usuarios_activos -> fetchAll();

if(isset($_POST['update_user'])){
$id_user= (int)$_POST['id_usuario_update'];
$rol_user = (int)$_POST['rol_usuario_update'];
// $contra_user= $_POST['contrasena_usuario_update'];
// $error = politica_contra(($contra_user));
$correo_user= $_POST['correo_usuario_update'];
$apellido_user= $_POST['apellido_usuario_update'];
$nombre_user=$_POST['nombre_usuario_update'];
// if (empty($error)){
  if(!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $correo_user) ||  !preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $apellido_user) 
  || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/' , $correo_user )){
    $actualizacion_perfil_propio=2;
                  
    header("Location:index_usuarios.php?up_per=".$actualizacion_perfil_propio);
    return;
  
  }else{
  $update_user = $conexion_jardin ->prepare("UPDATE  usuarios set nombre_u= :nombre, apellido_u= :apellido, correo_u = :correo,  rol_u = :rol where ID_usuario = '$id_user'; ");
  $update_user-> bindParam(':nombre', $nombre_user, pdo::PARAM_STR);
  $update_user -> bindParam(':apellido' , $apellido_user, pdo::PARAM_STR);
  $update_user-> bindParam(':correo', $correo_user, pdo::PARAM_STR);
  // $update_user-> bindParam(':contrasena', $contra_user, pdo::PARAM_STR);
  $update_user-> bindParam(':rol', $rol_user, pdo::PARAM_INT);
  $update_user->execute();
  $actualizacion=1;//cambios

  header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios

  }
} else{
  //echo '<h2 class="text-light">surgió un error al actualizar la contraseña</h2>';
}

// actulizacion del perfil  propio 

$errorpolitica = false;
$alertamenor = false;
$password_p = '';

$actualizacion_perfil_propio = '3';

$actualizacion_perfil_propio_url= $_GET['up_per'] ?? 3 ;

if (isset($_POST['update_perfil'])) {
    $id_porfile = (int)$_POST['porfile_id'];
    $name_p = $_POST['porfile_name'];
    $lastname_p = $_POST['porfile_lastname'];
    $email_p = $_POST['porfile_email'];
    $password_p = $_POST['porfile_password'];
    $birthdate_p = $_POST['porfile_birthdate'];

 
   if($password_p !== ''){
    $error = politica_contra($password_p);
    
}

 
if(!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $name_p) ||  !preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $lastname_p) 
|| !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/' , $email_p )){
  $actualizacion_perfil_propio=2;
                
  header("Location:index_usuarios.php?up_per=".$actualizacion_perfil_propio);
  return;

}
    // Calcula la edad del usuario
    $current_date = new DateTime();
    $time_date = new DateTime($birthdate_p);
    $age = $current_date->diff($time_date)->y;

   
    if ($age < 18 || $age >= 90){
        $alertamenor = true;
        $actualizacion_perfil_propio_url=2;
        
    } else {
        if (empty($error)) {
            if ($password_p === '' ) {
            
          
          
                // Actualiza el perfil sin cambiar la contraseña
                $update_porfile = $conexion_jardin->prepare("UPDATE usuarios SET nombre_u=:new_name_p, apellido_u=:new_lastname_p, correo_u=:new_email_p, fechanacimiento=:new_birthdate_p WHERE ID_usuario=:id_porfile");
                $update_porfile->bindParam(':new_name_p', $name_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_lastname_p', $lastname_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_email_p', $email_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_birthdate_p', $birthdate_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':id_porfile', $id_porfile, PDO::PARAM_INT);
                $update_porfile->execute();
                
                $actualizacion_perfil_propio=1;
                
                header("Location:index_usuarios.php?up_per=".$actualizacion_perfil_propio);
                 echo 'se ejecuto  SISIIIIN  contraseña';

            } else {
                // Actualiza el perfil cambiando la contraseña
                $password_hash = password_hash($password_p, PASSWORD_BCRYPT);
                $update_porfile = $conexion_jardin->prepare("UPDATE usuarios SET nombre_u=:new_name_p, apellido_u=:new_lastname_p, correo_u=:new_email_p, Contrasena_u=:new_password_p, fechanacimiento=:new_birthdate_p WHERE ID_usuario=:id_porfile");
                $update_porfile->bindParam(':new_name_p', $name_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_lastname_p', $lastname_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_email_p', $email_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_password_p', $password_hash, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_birthdate_p', $birthdate_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':id_porfile', $id_porfile, PDO::PARAM_INT);
                $update_porfile->execute();

                $actualizacion_perfil_propio=1;
                
                header("Location:index_usuarios.php?up_per=".$actualizacion_perfil_propio);    
               echo 'se ejecuto con contraseña';
            }
        } else {
            // echo "if del empity error ";
            // var_dump($errores); 
             $errorpolitica = true;
             $actualizacion_perfil_propio_url=2;
        }
    }
} else {
    // echo 'isset';
    // var_dump($password_p);
  
} 



  
  
  
function politica_contra($contraseña) {
  $error = [];
  if ($contraseña !== null  && strlen($contraseña) < 6) {
    $error[] = "La contraseña debe tener al menos 6 caracteres ";
}
  if (!preg_match('/[A-Z]/', $contraseña)) {
      $error[] = "La contraseña debe contener al menos una letra mayúscula.";
  }
  if (!preg_match('/[a-z]/', $contraseña)) {
      $error[] = "La contraseña debe contener al menos una letra minúscula.";
  }
  if (!preg_match('/[0-9]/', $contraseña)) {
      $error[] = "La contraseña debe contener al menos un número.";
  }
  return $error;
}

if (isset($_POST['eliminar_user'])) {//cambios
  $id_dele_user = (int)$_POST['id_usuario_dele'];

  // Iniciar una transacción
  $conexion_jardin->beginTransaction();
  try {
      // Primero eliminar las filas en la tabla alumno que referencian a los grupos de clases
      $eliminar_alumno = $conexion_jardin->prepare(
          "DELETE FROM alumno 
           WHERE ID_grupo_fk IN (
               SELECT ID_g_c 
               FROM grupos_clases 
               WHERE id_profesor_fk IN (
                   SELECT ID_tabla_p 
                   FROM profesor 
                   WHERE ID_profesor = :id
               )
           )"
      );
      $eliminar_alumno->bindParam(':id', $id_dele_user);
      $eliminar_alumno->execute();

      // Luego eliminar las filas en la tabla grupos_clases que referencian al profesor
      $eliminar_grupos_clases = $conexion_jardin->prepare(
          "DELETE FROM grupos_clases 
           WHERE id_profesor_fk IN (
               SELECT ID_tabla_p 
               FROM profesor 
               WHERE ID_profesor = :id
           )"
      );
      $eliminar_grupos_clases->bindParam(':id', $id_dele_user);
      $eliminar_grupos_clases->execute();

      // Luego eliminar las filas en la tabla profesor que referencian al usuario
      $eliminar_profesor = $conexion_jardin->prepare("DELETE FROM profesor WHERE ID_profesor = :id");
      $eliminar_profesor->bindParam(':id', $id_dele_user);
      $eliminar_profesor->execute();

      // Luego eliminar el usuario
      $eliminar_user = $conexion_jardin->prepare("DELETE FROM usuarios WHERE ID_usuario = :id");
      $eliminar_user->bindParam(':id', $id_dele_user);
      $eliminar_user->execute();

      // Confirmar la transacción
      $conexion_jardin->commit();
      
      $eliminacion = 1;
      header("Location: index_usuarios.php?el_n=" . $eliminacion);
  } catch (Exception $e) {
      // Revertir la transacción si algo falla
      $conexion_jardin->rollBack();
      echo "Error: " . $e->getMessage();
  }
}

if(isset($_POST['desactivar_user'])){
  $id_des = $_POST['id_desac_user'];
  $desac_cuenta = $conexion_jardin ->prepare("UPDATE  usuarios set activo = 0  where  ID_usuario = '$id_des' ;");
  $desac_cuenta ->execute();
  $actualizacion=1;//cambios

  header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios
  }


 if($resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']== 5 or $resul_index_rol['rol_u']== 6  or $resul_index_rol['rol_u']== 1 ){  
$sql_notificacion = $conexion_jardin->prepare("SELECT COUNT(atc.ID_cunsulta) as total_pendientes FROM atencion_cliente as atc WHERE atc.estado_consulta = 'pendiente'; ");
$sql_notificacion->execute();
$result_notificacion = $sql_notificacion->fetch();

 }else{
  $sql_notificacion = $conexion_jardin->prepare("SELECT COUNT(atc.ID_cunsulta) as total_pendientes FROM atencion_cliente as atc WHERE atc.estado_consulta = 'respondido' AND atc.lectura = 'no leido' and atc.correo_a_cl = '$resul_index_rol[correo_u]' ; ");
$sql_notificacion->execute();
$result_notificacion = $sql_notificacion->fetch();


 }

  $actualizacion = $_GET['ac_n'] ?? 3 ;//cambios
$eliminacion = $_GET['el_n'] ?? 3 ;//cambios
$agregacion = $_GET['ag_n'] ?? 3 ;//cambios

$matricula = $_GET['v_mtr'] ?? 3 ;
$mistake_document= $_GET['v_msd'] ?? 3;
$mistake_age =$_GET['v_ma'] ?? 3;

$certificado_v = $_GET['certi_v'] ?? 3  ;

$grupo_va = $_GET['g_er'] ?? 3 ;

$profe_va = $_GET['p_err'] ?? 3 ;

// acudiente perfil 

$perfil_acudiente = $_GET['v_acu'] ?? 3 ;

$xp_err = $_GET['xp_er'] ?? 3

?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/vnd.icon" href="img/LogoLibros.png">
    <!-- <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link  rel="stylesheet"  href="css_J_I/estilo_usuarios.css">
    <!-- <link rel="stylesheet" href="CSS/MUNDOACUARELA.CSS"> -->
    <title>JARDIN INFANTIL </title>
  

    <style>
      @import url('https://fonts.googleapis.com/css2?family=Chewy&family=Handlee&family=Lobster&display=swap');
      @media only screen and (max-width: 450px) {

      .texto {
      font-size: 16px; /* Reducir el tamaño de la fuente para pantallas más pequeñas */
    }
  .textor{
      font-size: 30px; /* Reducir el tamaño de la fuente para pantallas más pequeñas */
    }
    .textop{
      font-size: 12px; /* Reducir el tamaño de la fuente para pantallas más pequeñas */
    }
/*   
        body{
          background-attachment: fixed;
          overflow-x: hidden;
        }
         table{
          width: 300px;
         }
         .main{
          height: 550px; 
         
  }*/
}

body{

  font-family: 'Handlee',cursive;
}
.xd{
  background: rgb(255, 213, 250);
}
    </style>
</head>
<body>
<script>
    function checkScreenSize() {
        var inputGroup = document.querySelector('.input-group');
        
        if (window.innerWidth <= 500) {
 
            inputGroup.classList.remove('input-group', 'flex-nowrap');
        } else {
           
            inputGroup.classList.add('input-group', 'flex-nowrap');
        }
    }

    // Ejecutar al cargar la página
    window.onload = checkScreenSize;

    // Ejecutar al redimensionar la ventana
    window.onresize = checkScreenSize;
</script>


<!-- alerta para las sesiones  -------------------- -->
<?php  if($errorpolitica==true ){ ?>

 <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position:absolute; z-index: 1059;  margin-top : 5px ;" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>La contraseña no cumple con las politicas de seguridad</strong> </p>
  <?php
            if (isset($error) && !empty($error)) {
                echo '<div class="alert alert-danger" role="alert">
                        <ul>';
                foreach ($error as $errores) {
                    echo '<li>' . $errores . '</li>';
                }
                echo '  
                
                </ul>
                      </div>';
            }
            ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Por Favor vuelva a intentarlo</p>
</div>
 </div>
<?php  }elseif($alertamenor==true){  ?>
<!-- alerta para las sesiones -->
 <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute; z-index: 1059;  margin-top : 5px ;" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>La cantidad de años no es lo habitable de una persona o el usuario es menor de 18 años</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Le recomendamos verificar su información </p>
</div>
</div>
<?php } ?>

<?php  if($actualizacion_perfil_propio_url== 1){  ?>

  <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="check-circle-fill" viewBox="0 0 16 16">
    <path fill="green" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
	</svg>
  <div class="col-4 mx-auto">
<div class="alert alert-success  ajuste_color_alerta  fade show alert-dismissible" style="position: absolute; z-index: 1059;  margin-top : 5px ;" role="alert">
  <h4 class="alert-heading">  	<svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"  style="width: 20px; height: 20px;" ><use xlink:href="#check-circle-fill"/></svg>¡ ACTUALIZACION EXITOSA !</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0"> <strong>Puede Continuar Con Normalidad </strong> </p>

</div>
  </div>

  <?php  }elseif($actualizacion_perfil_propio_url== 2){  ?>
    <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible ajuste_alerta_variada" style="position: absolute; z-index: 1059; margin-top : 5px; height:auto ;" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>Se enviaron mal los datos no se adminten espacios en blanco o caracteres espaciales diferentes de los indicados en cada campo</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Le recomendamos verificar su información </p>
</div>
</div>
<?php }?>
<?php if ( $validacion_com_perf_prof == 1 or $perfil_acudiente ==1 ){?>
  <div class="col-4 mx-auto">
<div class="alert alert-info  fade show alert-dismissible" style="position: absolute;  z-index: 1059;" role="alert">
  <h4 class="alert-heading">PERFIL COMPLETADO</h4>
  <p> <strong>Puede Continuar Con Normalidad </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Cualquier Novedad Hable Con Su Superior O Con  Administracion  </p>
</div>
</div>
 <?php


}elseif( $validacion_com_perf_prof == 0 or $perfil_acudiente == 0 ){?>
 

  <?php if ($validacion_com_perf_prof==0){ echo '<script type="text/javascript">',
'localStorage.setItem("abrir_modal", "true");',
         '</script>';   
  }else{
    echo '<script type="text/javascript">',
    'localStorage.setItem("modal_acudiente", "true");',
             '</script>';  
  }
         }

         
  if($validacion_observacion_nino==0 || $actualizacion==2 || $actualizacion==5 || $certificado_v==2 || $grupo_va == 2  || $profe_va == 2 || $xp_err==2 ){?>
          <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible ajuste_alerta_variada" style="position: absolute;  z-index: 1059;  margin-top : 5px ;" role="alert">
  <h4 class="alert-heading">ERROR AL  ENVIAR LOS DATOS </h4>
  <p> <strong>Se Enviaron Datos De Forma Inconrrecta  </strong> <?php if ($actualizacion==5 ) {echo 'El documeto ya existe';}elseif ($certificado_v == 2) {
    echo 'NO exite el documento ingresado ';
  }elseif ($grupo_va==2) {
    echo 'NO se adminten espacios en blanco o caracteres espaciales diferentes de los indicados en cada campo y solo existen 20 aulas ';
  }elseif($profe_va==2){echo 'NO se adminten espacios en blanco o caracteres espaciales diferentes de los indicados en cada campo tambien le recordamos las opciones de materias admitidas   <div>  <ul>' ; 
    foreach ($materias_profesores_opciones as $materia) {
      echo '<li>' . $materia . '</li>';
  }
  echo '  
  
  </ul>
        </div>';
  }elseif($xp_err==2){
    echo 'Los años de experiencia ingresados no concuerdan con su edad - tambien  no se adminten espacios en blanco o caracteres espaciales diferentes de los indicados en cada campo  ';

  }
  
  
  ?> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"  style=" z-index: 1059;"></button>
  <hr>
  <p class="mb-0"> Vuelva A Intentarlo 🤙 </p>
</div>
</div>

<?php }elseif($validacion_observacion_nino==1 ){?>
  <div class="col-4 mx-auto">
    <div class="alert alert-info  fade show alert-dismissible" style="position: absolute; z-index: 1059;" role="alert">
  <h4 class="alert-heading">OBSERVACIÓN AGREGADA</h4>
  <p> <strong>Puede Continuar Con Normalidad </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0"></p>
</div>
</div>

<?php }?> 

<?php if($validacion_observacion_nino_up==1 ){?>
  <div class="col-4 mx-auto">
    <div class="alert alert-success  fade show alert-dismissible" style="position: absolute; z-index: 1059;" role="alert">
  <h4 class="alert-heading">OBSERVACIÓN ACTUALIZADA</h4>
  <p> <strong>Puede Continuar Con Normalidad </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0"></p>
</div>
</div>
  <?php }elseif($validacion_observacion_nino_up==0 || $matricula==0 ){?>
    <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute; z-index: 1059;" role="alert">
  <h4 class="alert-heading">ERROR AL  ENVIAR LOS DATOS </h4>
  <p> <strong>Se Enviaron Datos De Forma Inconrrecta  </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"  style=" z-index: 1059;"></button>
  <hr>
  <p class="mb-0"> Vuelva A Intentarlo 🤙 </p>
</div>
</div>
  <?php }elseif($validacion_observacion_nino_up==4 ){?>
  <div class="col-4 mx-auto">
    <div class="alert alert-success  fade show alert-dismissible" style="position: absolute; z-index: 1059;" role="alert">
  <h4 class="alert-heading">SE ELIMINO LA OBSERVACIÓN</h4>
  <p> <strong>Puede Continuar Con Normalidad </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0"></p>
</div>
</div>
  <?php } ?>

  <?php if($actualizacion==1 ){  //cambios?>
  <div class="col-4 mx-auto">
<div class="alert alert-info  fade show alert-dismissible" style="position: absolute; z-index: 1059;" role="alert">
  <h4 class="alert-heading">ACTUALIZACIÓN EXITOSA</h4>
  <p> <strong>Se han actualizado correctamente los datos </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" ></button>
  <hr>
  <p class="mb-0">Puede continuar con normalidad</p>
</div>
</div>
<?php  }elseif($eliminacion==1){  //cambios?>
<!-- alerta para las sesiones -->
 <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute; z-index: 1059;" role="alert">
  <h4 class="alert-heading">Se ha eliminado correctamente</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" ></button>

  <!-- <p> <strong>El usuario debe ser mayor de 18 años</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Le recomendamos verificar su información </p> -->
</div>
</div>
<?php  }elseif($agregacion==1 || $matricula==1 ){  //cambios?>
<!-- alerta para las sesiones -->
<div class="col-4 mx-auto">
<div class="alert alert-info  fade show alert-dismissible" style="position: absolute; z-index: 1059;" role="alert">
  <h4 class="alert-heading">  <?php if ($matricula ==1 ){ echo 'Se Realizo La Matricula';  }else{
echo ' SE AGREGO EXITOSAMENTE';
  } ?>
 </h4>
  <?php if ($matricula ==1 ){ echo '<p> <strong>Espere La  Asignacion De Ficha </strong> </p>';  } ?>

  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" ></button>
  <!-- <p> <strong>Se han actualizado correctamente los datos </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Puede continuar con normalidad</p> -->
</div>
</div>
  <?php } ?>

  <?php if ($matricula == 4 || $mistake_age == 1 || $mistake_document == 1) { ?>
    <div class="col-4 mx-auto">
        <div class="alert alert-danger fade show alert-dismissible" style="position: absolute; z-index: 1059;" role="alert">
            <h4 class="alert-heading">
                <?php  
                if ($matricula == 4) {
                    echo "ERROR AL ENVIAR EL PDF";
                } elseif ($mistake_document == 1 && $actualizacion != 4 ) {
                    echo "Error al matricular:";
                }elseif($actualizacion == 4){
                  echo "Error al actualizar";
                }else {
                    echo "Error de Edad";
                } 
                ?>
            </h4>
            <p> 
                <?php 
                if ($matricula == 4) {
                    echo "<strong>Se Envió Otro Formato</strong>"; 
                } elseif ($mistake_document == 1) {
                    echo "El documento ya está registrado";
                } else {
                    echo "<strong>La edad no coincide con la cantidad de años que han transcurrido desde su nacimiento</strong>";
                } 
                ?> 
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="z-index: 1059;"></button>
            <hr>
            <p class="mb-0"> Vuelva A Intentarlo 🤙 </p>
        </div>
    </div>
<?php } ?>

<!-- FIN DEL  LAS ALERTAS ------------------------------------- -->


<header>
    <h2 class="texto_header_1"> JARDIN INFANTIL MUNDO ACUARELA</h2>
    <nav>
        <div class="btn-group espacio_drop" <?php if ( $validacion_com_perf_prof == 0 ) { echo 'style="margin-right: 10px; z-index: 1058;"'; } elseif ( $cambio_z_indexprofesor == 2 ) { echo 'style="margin-right: 10px; z-index: 1058;"'; } else { echo 'style="margin-right: 10px;"'; } 
        ?>>
            <button type="button" class="btn btn-primary col-12" id="reset_b">
                <p class="texto_header"> BIENVENIDO </p>
            </button>
            <button type="button" class="btn btn-primary dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">
             <?php if ($result_notificacion['total_pendientes']>0){ ?>
            <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
           <?php }?>
          </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" ><p style="margin-bottom: -5px"> BIENVENIDO <?php echo $resul_index_rol['nombre_u']," | ",$resul_index_rol['nombre_rol'];?></p></a></li>
        
    <li><a class="dropdown-item" href="mensajes.php"  >MENSAJES

    <?php if ($result_notificacion['total_pendientes']>0){ 
   echo " <span class='position-absolute 
    badge rounded-pill bg-danger ajuste_badge'> " .  $result_notificacion['total_pendientes'] ;
    
     } ?>
  </span> </a></li>
                <li><a class="dropdown-item" id="salir" href="salir.php">SALIR</a></li>
            
            </ul>
        </div>
    </nav>
</header>


<!-- modal  de completar perfil del profesor   --------------------------------------------------------------- -->
<div class="col ajuste-col  ">
    <div class="modal fade  z"  
        id="modal1" 
        tabindex="1" 
        aria-hidden="true" 
        aria-labelledby="label-modal1"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollabel rotate-scale-up">
            <div class="modal-content ajuste_modal_color ajuste_perfil_profe_espacio">
                <div class="modal-header">
                    <h4 class="modal-tittle">

                        <div class="my-title_2"> COMPLETE SU PERFIL DE PROFESOR  </div>
                    </h4>
                </div>
                <div class="modal-body ajuste_perfil_profe">
                   
<form action="procesos.php" method="post" >

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in"   placeholder="" name="nombre_materia_cpp" pattern="[a-zA-Záéíóúü ]+" title="Solo letras " required> 
<label for="floatingInput_in">MATERIA</label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i"   placeholder=""  name="cel_profe_cpp" pattern="[0-9]{10}"  title="Solo N° De Celular " required>
<label for="floatingPassword_i">TELEFONO</label>
</div>
<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_e"  placeholder=""  name="años_experi_cpp" pattern="\d+" title="Solo el número de años de experiencia" required>
<label for="floatingPassword_e">EXPERIENCIA </label>
</div>
</div>

<input  type="text" class="input_invisible"  name="id_profe_cpp" value="<?php echo htmlspecialchars($_SESSION['id_usuario']); ?> " readonly>


                </div>
                <div class="modal-footer">

                <div class="d-flex justify-content-center">
<input  type="submit" name="insert_cpp" class="btn btn-success btn-md b_p"  value="ENVIAR"  aria-label="Cerrar" >
</div>
</form>
                
            </div>
        </div>
    </div>
</div>

<!-- modal para acudiente  -->
<div class="col ajuste-col">
    <div class="modal fade  z"  
        id="acudiente_modal" 
        tabindex="1" 
        aria-hidden="true" 
        aria-labelledby="label-modal1"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollabel rotate-scale-up">
            <div class="modal-content ajuste_modal_color">
                <div class="modal-header">
                    <h4 class="modal-tittle">

                        <div class="my-title_2"> COMPLETE SU PERFIL DE ACUDIENTE  </div>
                    </h4>
                </div>
                <div class="modal-body">
                   
<form action="procesos.php" method="post" >

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in"   placeholder="" name="cel_acudiente" pattern="[0-9]{10}"  title="Solo N° De Celular "  required> 
<label for="floatingInput_in">Celular </label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i"  placeholder=""   name="cel_alterno"  pattern="[0-9]{10}"  title="Solo N° De Celular " required>
<label for="floatingPassword_i">Cel - Emergencia</label>
</div>
<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_e"  placeholder=""  name="direccion_acudiente"   pattern="[a-zA-Z0-9\s\]]+"  title="Solo Direcciones "  required>
<label for="floatingPassword_e">Direccion</label>
</div>
</div>

<input  type="text" class="input_invisible"  name="id_acudiente_cp" value="<?php echo htmlspecialchars($_SESSION['id_usuario']); ?> " readonly>


                </div>
                <div class="modal-footer">

                <div class="d-flex justify-content-center">
<input  type="submit" name="insert_acudiente" class="btn btn-success btn-md b_p"  value="ENVIAR"  aria-label="Cerrar" >
</div>
</form>
                
            </div>
        </div>
    </div>
</div>




 <!-- cotaine para el admin  -->
<div class="container_index mt-3">
<aside class="espacio_opciones">
<button class="btn btn-info d-sm-none  tama_btn " type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasResponsive" aria-controls="offcanvasResponsive">🟰</button>
<div class="alert  d-none d-sm-block color_botones ">
  <div id="botones">
    <?php  if($resul_index_rol['rol_u']==1 or $resul_index_rol['rol_u']==6 ){  ?>
    <button class="boton col-12 boton_js text_btn" id="boton-1">Activación</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==5 or $resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']==2){  ?>
    <button class="boton col-12  boton_js text_btn" id="boton-2">Grupos Estudio</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']==2){  ?>
    <button class="boton col-12  boton_js text_btn" id="boton-3">Alumnos</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==5 or $resul_index_rol['rol_u']==4){  ?>
    <button class="boton col-12 boton_js text_btn" id="boton-4">Profesores</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==1){  ?>
    <button class="boton col-12   boton_js text_btn" id="boton-5">Usuarios</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']>0){  ?>
    <button class="boton col-12  boton_js text_btn" id="boton-6">Perfil</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==3 or $resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']==6 or $resul_index_rol['rol_u']==5){  ?>
    <button class="boton col-12  boton_js text_btn" id="boton-7">Certificado</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==3){  ?>
    <button class="boton col-12  boton_js text_btn" id="boton-8">Observación</button>
    <?php } ?>
    <?php if($resul_index_rol['rol_u']==3){  ?>
    <button class="boton col-12  boton_js text_btn" id="boton-9">Matricula</button>
    <?php } ?>
    <?php if($resul_index_rol['rol_u']==3){  ?>
    <button class="boton col-12  boton_js text_btn" id="boton-10">Carnet</button>
    <?php } ?> 
    <?php if($resul_index_rol['rol_u']<>3){  ?>
    <a class="boton col-12  btn btn-primary text_btn " href="informes.php" id="boton-10">Informes</a>
    <?php } ?>

    <!-- <button class="boton" id="boton-7">Botón 7</button>
    <button class="boton" id="boton-8">Botón 8</button> -->
  </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasResponsive" aria-labelledby="offcanvasResponsiveLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasResponsiveLabel"> MENU </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#offcanvasResponsive" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="mb-0">
      
        <div id="botones col-sm-12 bg-info">
        <?php  if($resul_index_rol['rol_u']==1 or $resul_index_rol['rol_u']==6 ){  ?>
    <button class="btn btn-primary col-12 m-2  boton_js" id="boton-1" aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Activación</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==5 or $resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']==2){  ?>
    <button class="btn btn-primary col-12 m-2 boton_js" id="boton-2" aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Grupos Estudio</button>
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']==2){  ?>
      <button class="btn btn-primary col-12 m-2 boton_js" id="boton-3"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Alumnos</button>
      <?php }?>
      <?php  if($resul_index_rol['rol_u']==5 or $resul_index_rol['rol_u']==4){  ?>
      <button class="btn btn-primary col-12 m-2 boton_js" id="boton-4"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Profesores</button>
      <?php }?>
      <?php  if($resul_index_rol['rol_u']==1){  ?>
      <button class="btn btn-primary col-12 m-2 boton_js" id="boton-5"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Usuarios</button>
      <?php }?>
      
      <?php  if($resul_index_rol['rol_u']>0){  ?>
        <button class=" btn btn-primary col-12 m-2 boton_js" id="boton-6"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Perfil</button>
      <?php }?>
      <?php  if($resul_index_rol['rol_u']==3 or $resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']==6 or $resul_index_rol['rol_u']==5){  ?>
        <button class=" btn btn-primary col-12 m-2 boton_js" id="boton-7"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Certificado</button>
      <?php }?>
      <?php  if($resul_index_rol['rol_u']==3){  ?>
        <button class=" btn btn-primary col-12 m-2 boton_js" id="boton-8"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Observación</button>
      <?php }?>
      <?php  if($resul_index_rol['rol_u']==3){  ?>
        <button class=" btn btn-primary col-12 m-2 boton_js" id="boton-9"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Matricula</button>
      <?php }?>
      <?php  if($resul_index_rol['rol_u']==3){  ?>
        <button class=" btn btn-primary col-12 m-2 boton_js" id="boton-10"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Carnet</button>
      <?php }?>
      <?php  if($resul_index_rol['rol_u']<>3){  ?>
        <a class=" col-12  btn btn-primary  m-2" href="informes.php" id="boton-10">Informes</a>
        <?php }?>
      <!-- <button class="boton" id="boton-6" aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">tutores</button> -->
    <!-- <button class="boton" id="boton-7"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Botón 7</button>
    <button class="boton" id="boton-8"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Botón 8</button>
  --></div> 
    </div>
  </div>
</div>


  </aside>  
  <div class="espacio_whap  <?php if($resul_index_rol['rol_u']==3){echo 'redesfooter_contac';}else{echo 'redesfooter' ;} ?> " >   
             
             <ul class="col-12">
              
               <li class=" <?php if($resul_index_rol['rol_u']==3){echo 'contac';}else{echo 'w' ;} ?> " >
                   <span class="iconw"></span>

                 <a  <?php if($resul_index_rol['rol_u']==3){echo "href='formulario_atencion.php' ";  }else{echo  "href='https://wa.me/573212600725?text=Hola, necesito ayuda con algo..' ";   } ?>  target="_blank" class="titulo text-light  " style=" text-decoration: none;"><span >AYUDA  O INFORMACION ?</span></a>
               </li >
              
           </ul>
</div>
<div class="main"> 
<div class=" contenidos principal p-2 text-secondary tb_scroll" ><!--//cambios-->
<div class=""><!--//cambios-->

  <!-- <img src="IMG/cuadro.png" alt="" width="150px"> -->
<h2 class="text-center">BIENVENIDO A MUNDO ACUARELA</h2><!--//cambios-->
<p class="fs-5">
<br> Bienvenidos al Jardín Infantil Mundo Acuarela

<br> Nuestra Historia

Fundado en 1995, Mundo Acuarela es un lugar mágico donde los niños aprenden, crecen y se divierten. Nuestra misión es fomentar la curiosidad, la creatividad y el amor por el aprendizaje desde temprana edad.

<br> Nuestro Equipo

<br>Contamos con un equipo apasionado de educadores y cuidadores comprometidos con el bienestar de los niños. Cada miembro de nuestro personal está capacitado para brindar un entorno seguro y estimulante.

<br> Instalaciones y Servicios

<br>- Aulas luminosas y coloridas
<br>- Patios de recreo con juegos interactivos
<br>- Áreas verdes para explorar la naturaleza
<br>- Programas educativos personalizados
<br>- Cuidado infantil de calidad

</div>
</div>
<!-- contenido  -->
<!-- <div id="contenidos">   ALGO PASA CON ESTO NO ME ACUERDO QUE TENIA QUE CONTENER Y EL RESPANDO ESTA MAS 
  CONFUNSOO PERO FUNCIONA CON Y SIN EL PERO NO LO TOQUEMOS MAS  -Edwin   -->
<?php  if($resul_index_rol['rol_u'] == 1 || $resul_index_rol['rol_u'] == 6) {  ?>
    <div class="contenido" id="contenido-1">
    <h1 id="my-title" class="text-info">Activación de cuentas</h1>
     <div class="tb_scroll  scrol_usuarios">
    <table>
    <tr class="xd">
        <td>NOMBRE</td>
        <td>APELLIDO</td>
        <td>CORREO</td>        
        <td>ROL</td>
        <td colspan="2">Acción</td>
    </tr>
    <?php foreach($resultado_peticiones as $fila_p): ?>
    <tr class="cuerpo_form ">
    <td><?php echo htmlspecialchars($fila_p['nombre_u']); ?></td>
    <td><?php echo htmlspecialchars($fila_p['apellido_u']); ?></td>
    <td><?php echo htmlspecialchars($fila_p['correo_u']); ?></td> 
    <td>
        <form action="index_usuarios.php" method="post">
            <select class="form-select" name="tipo_rol_reg" id="inputGroupSelect01">
            <?php
                $select_rol = $conexion_jardin->prepare("SELECT nombre_rol, id_rol FROM rol_usuario");
                $select_rol->execute();
                while ($tipo_rol = $select_rol->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"{$tipo_rol['id_rol']}\">" . htmlspecialchars($tipo_rol['nombre_rol']) . "</option>";
                }
            ?>
            </select>
    </td>
    <td>
            <input type="hidden" name="id_cuenta" value="<?php echo htmlspecialchars($fila_p['ID_usuario']); ?>">
            <input type="submit" name="activar_cuenta" class="btn btn-success" value="Activar">
        </form>
    </td>
    <td>
        <form action="index_usuarios.php" method="post">
            <input type="hidden" name="id_cuenta_dele" value="<?php echo htmlspecialchars($fila_p['ID_usuario']); ?>">
            <input type="submit" name="eliminar_cuenta" class="btn btn-danger" value="Eliminar">
        </form>
    </td>
    </tr>
    <?php endforeach ?>
    </table>
    </div><!--scroll de las tablas -->
    </div>
<?php } ?>
<!-- </div>   lo mismo de arriba es su complamento --edwin -->
  <!--fin  peticiones de ingreso -->
  
 <?php  if($resul_index_rol['rol_u']==5 or $resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']==2){  ?>
     <div class="contenido" id="contenido-2">  <!-- grupos de salon -->
     <h1 id="my-title"> Grupos de estudio </h1>
    <div class="tb_scroll scrol_usuarios">
    <table >
      <tbody>
    <tr class="xd">
        <td>PROFESOR</td>
        <td>CORREO</td>
        <td>CELUlLAR</td>
        <td>MATERIA</td>
        <td>FICHA</td>
        <td >N° AULA</td>
        <td>Nivel</td>
      <?php if ($resul_index_rol['rol_u']<>2){ ?> 
        <td colspan="2">Accion 
<!-- nuevo boton -->
 
        <button class="icon-btn add-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
        <div class="add-icon"></div>
    <div class="btn-txt">Nuevo Grupo</div>
</button>
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop" aria-labelledby="staticBackdropLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="staticBackdropLabel">NUEVO GRUPO </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in" placeholder="" name="insert_ficha"   >
<label for="floatingInput_in">FICHA</label>
</div>
<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_in" placeholder="maximo de aulas 20"  pattern="^(1[0-9]|20|[1-9])$" name="insert_aula"  title="maximo de aulas 20"  required>
<label for="floatingPassword_in">NUMERO DE AULA</label>
</div>
<div class="form-floating mb-3">
  <select class="form-select" id="floatingSelect_in" aria-label="Floating label select example" name="insert_profe_g">
    <?php  $mostar_profesor_1 = $conexion_jardin-> prepare('SELECT u.* , p.* from profesor as p inner join usuarios as u on u.ID_usuario  = p.ID_profesor ;') ;
    $mostar_profesor_1 -> execute();
    while ($profe_1 = $mostar_profesor_1->fetch(pdo::FETCH_ASSOC)){
      echo "<option value=\"{$profe_1['ID_tabla_p']}\">{$profe_1['nombre_u'] } - materia  : {$profe_1['materia'] }  </option>";
    }
    ?> 
  </select>
  <label for="floatingSelect_in">SELECIONAR PROFESOR</label>
</div>

<div class="form-floating mb-3">
  <select class="form-select" id="floatingSelect_in_nivel" aria-label="Floating label select example" name="insert_nivel_g">
    <?php  $mostar_nivel_1 = $conexion_jardin-> prepare('SELECT * FROM  nivel_educ  ;') ;
    $mostar_nivel_1 -> execute();
    while ($nivel_1 = $mostar_nivel_1->fetch(pdo::FETCH_ASSOC)){
      echo "<option value=\"{$nivel_1['ID_nivel']}\" >{$nivel_1['nombre_nivel'] }  </option>";
    }
    ?> 
  </select>
  <label for="floatingSelect_in_nivel">SELECIONAR NIVEL EDUCATIVO</label>
</div>

<!-- <input  type="text" class="input_invisible"  name="id_grupo" value="<?php //echo $res_g['ID_g_c']; ?> " readonly> -->
<div class="d-flex justify-content-center">
<input  type="submit" name="insert_g" class="btn btn-success btn-md "  value="AGREGAR">
</div>
</form>
    </div>
  </div>
</div> <!-- fin nuevo ingreso -->     

        </td>
        <?php }else{?>
          <td colspan="2">Accion</td> 
          <?php }?>  
    </tr>

    <?php foreach($resultado_grupos as $fila_g): ?>
    <tr   class="cuerpo_form" >
    <td><?php if($fila_g['nombre_u']==null){ echo 'PENDIENTE'; }else{ echo $fila_g['nombre_u']; } ?></td>
    <td><?php if($fila_g['correo_u']==null){ echo 'PENDIENTE'; }else{ echo $fila_g['correo_u']; } ?></td>
    <td><?php if($fila_g['celular']==null){ echo 'PENDIENTE'; }else{ echo $fila_g['celular']; } ?></td>
    <td><?php if($fila_g['materia']==null){ echo 'PENDIENTE'; }else{ echo $fila_g['materia']; } ?></td>
    <td><?php if($fila_g['ficha']==null){ echo 'PENDIENTE'; }else{ echo $fila_g['ficha']; } ?></td>
    <td><?php if($fila_g['num_aula']==null){ echo 'PENDIENTE'; }else{ echo $fila_g['num_aula']; } ?></td>
    <td><?php if($fila_g['nombre_nivel']==null){ echo 'PENDIENTE'; }else{ echo $fila_g['nombre_nivel']; } ?></td>

    
     <td>  
     <?php if ($resul_index_rol['rol_u']<>2){ ?>
     <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_<?php echo $fila_g['ID_g_c']; ?>" aria-controls="staticBackdrop_nuevo">
  actualizar
</button>
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_<?php echo $fila_g['ID_g_c']; ?>" aria-labelledby="staticBackdropLabel_nuevo">
<?php   $pueba_datos_g = $conexion_jardin-> prepare("SELECT * from grupos_clases where ID_g_c = '$fila_g[ID_g_c]'; ") ;
  $pueba_datos_g->execute();
  $res_g = $pueba_datos_g ->fetch();


  ?>
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="staticBackdropLabel">CAMBIAR DATOS </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput" placeholder="" name="new_ficha"  value="<?php echo $res_g['ficha'] ?>" required>
<label for="floatingInput">FICHA</label>
</div>
<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword"  placeholder="maximo de aulas 20"  pattern="^(1[0-9]|20|[1-9])$" title="maximo de aulas 20" name="new_aula" value="<?php echo $res_g['num_aula'] ?>"  required>
<label for="floatingPassword">NUMERO DE AULA</label>
</div>
<div class="form-floating mb-3">
  <select class="form-select" id="floatingSelect" aria-label="Floating label select example" name="new_profe_g">
    <?php  $mostar_profesor = $conexion_jardin-> prepare('SELECT u.* , p.* from profesor as p inner join usuarios as u on u.ID_usuario  = p.ID_profesor ;') ;
    $mostar_profesor -> execute();

    while ($profe = $mostar_profesor->fetch(pdo::FETCH_ASSOC)){
      $selected = ($profe['ID_tabla_p'] == $fila_g['id_profesor_fk']) ? 'selected' : '';
      
      echo "<option value=\"{$profe['ID_tabla_p']}\" $selected>{$profe['nombre_u'] } - materia  : {$profe['materia'] }  </option>";
    }
    ?>
  </select>
  <label for="floatingSelect">SELECIONAR PROFESOR</label>
</div>
<div class="form-floating mb-3">
  <select class="form-select" id="floatingSelect_nivel" aria-label="Floating label select example" name="new_nivel_g">
    <?php  $mostar_nivel = $conexion_jardin-> prepare('SELECT * FROM  nivel_educ  ;') ;
    $mostar_nivel -> execute();
    while ($nivel_edu = $mostar_nivel->fetch(pdo::FETCH_ASSOC)){
      $selected_1 = ($nivel_edu['ID_nivel'] == $fila_g['ID_nivel']) ? 'selected' : '';
      echo "<option value=\"{$nivel_edu['ID_nivel']}\" $selected_1>{$nivel_edu['nombre_nivel'] }  </option>";
    }
    ?>
  </select>
  <label for="floatingSelect_nivel">SELECIONAR PROFESOR</label>
</div>
<input  type="text" class="input_invisible"  name="id_grupo" value="<?php echo $res_g['ID_g_c']; ?> " readonly>
<div class="d-flex justify-content-center">
<input  type="submit" name="update_g" class="btn btn-success btn-md "  value="ACTUALIZAR">
</div>
</form>
    </div>
  </div>
</div>
<?php } ?>
<?php if ($resul_index_rol['rol_u']<>2){ ?>
     </td>
     <!-- Eliminar grupo -->
     <td>  
      <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">
      <input  type="text" class="input_invisible"  name="id_g_dele" value="<?php echo $res_g['ID_g_c']; ?> " readonly>
        <input  type="submit" name="eliminar_g" class="btn btn-danger"  value="eliminar"> 
      </form>
     </td>
     <?php }?>
</tr>
<?php endforeach?>
</tbody>
</table>
    </div>
    </div> <!-- fn grupos  -->
    <?php }?>
  <?php  if($resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']==2){  ?>
   <div class="contenido" id="contenido-3">  <!--  niños alumnos -->
   <h1 id="my-title"> ALUMNOS DEL JARDIN</h1>

<div class="tb_scroll scrol_usuarios">
<table >

<tr class="head xd">
    <td>NOMBRE</td> 
    <td>APELLIDO</td>
    <td>FICHA</td>
    <td>N° INDENTIDAD</td>
    <td>TUTOR</td>
    <!-- <td>FECHA NACIMIENTO</td> -->
    <td>EDAD</td>
    <td >INFO - EPS</td>
    <td>Fotos </td>
    

    <td colspan="2">Accion 

<!-- nuevo boton  niños-->

<!-- fin nuevo ingreso  niños-->
     

    </td>
</tr>
<script>
   document.addEventListener('DOMContentLoaded', function() {
  // Inicia el popover con la opción de permitir HTML
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
          trigger: 'focus',
            html: true  // Esto permite que el contenido sea interpretado como HTML
        });
    });
  });
  </script>

<?php foreach($resultado_niños as $fila_n): ?>
<tr   class="cuerpo_form" >

<td><?php echo $fila_n['nombre_a']; ?></td>
<td><?php echo $fila_n['apellido_a']; ?></td>
<td><?php if ($fila_n['ficha']== null){ echo 'Pendiente';}else {echo $fila_n['ficha'];} ?></td>
<td><?php echo $fila_n['doc_identidad']; ?></td>
<td><?php echo $fila_n['nombre_u']; ?></td>
<!-- <td>/* echo $fila_n['fecha_nacimiento']; */?></td>  -->
<td><?php echo $fila_n['edad']; ?></td> 
<!-- modal para ver el pds ---------------------------------------------- -->
<td>


<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal_<?php echo $fila_n['ID_alumno']; ?>">
    Ver
</button>

<!-- Modal -->
<div class="col ajuste-col">
    <div class="modal fade z" id="exampleModal_<?php echo $fila_n['ID_alumno']; ?>" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable rotate-scale-up">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">INFORMACIÓN </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                
                    <iframe src="<?php  echo 'https://proyectosjevl.com/mundoacuarela/'.$fila_n['info_eps']; ?>" width="100%" height="400px" frameborder="0"></iframe>

                    </div>
                <div class="modal-footer">
                    Información Reservada Del jardín
                </div>
            </div>
        </div>
    </div>
</div>


</td> 
<td>
  

  <button type="button " class="btn btn-primary"
        data-bs-toggle="popover" data-bs-placement="top"
        data-bs-custom-class="custom-popover"
        data-bs-trigger="focus"
        data-bs-title="foto del niño"
        data-bs-content="<img src='<?php echo $fila_n['foto_alumno']; ?>' class='img_nino_poper' alt='Foto del niño'>">
    
  ver
</button>
</td>



 <td>  
 <?php if ($resul_index_rol['rol_u']<>2){ ?>
 
 <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_n<?php echo $fila_n['ID_alumno']; ?>" aria-controls="staticBackdrop">
Actualizar
</button>
<!-- actualizar niño -->
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_n<?php echo $fila_n['ID_alumno']; ?>" aria-labelledby="staticBackdropLabel">
<div class="offcanvas-header">

<h5 class="offcanvas-title" id="staticBackdropLabel"> ACTUALIZAR DATOS </h5>
<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">
<div>

<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in"  aria-label="First name" placeholder="" name="update_nombre_nino"  value="<?php echo $fila_n['nombre_a'] ?>"   Pattern="[a-zA-Záéíóúü ]+" title="Solo letras" required="">
<label for="floatingInput_in">NOMBRE</label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i"  aria-label="Last name" placeholder=""  name="update_apellido_nino" value="<?php echo $fila_n['apellido_a'] ?>" Pattern="[a-zA-Záéíóúü ]+" title="Solo letras " required="">
<label for="floatingPassword_i">APELLIDO</label>
</div>
<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_e"  placeholder=""  name="update_edad_nino"  value="<?php echo $fila_n['edad'] ?>" Pattern="^[1-6]$" title="solo se puede niños de 1 a 6 años" required="" >
<label for="floatingPassword_e">EDAD</label>
</div>
</div>

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_cc"  placeholder="" name="update_doc_nino"  value="<?php echo $fila_n['doc_identidad'] ?>" pattern="\d+"  title="solo numeros de documentos" required="">
<label for="floatingInput_cc">N° DOCUMENTO</label>
</div>

<div class="form-floating mb-3">
<input  type="date" class="form-control" id="floatingPassword_f"   placeholder=""  name="update_fecha_nino"  value="<?php echo $fila_n['fecha_nacimiento'] ?>"  max="<?php echo  date( 'Y-m-d'); ?>" required="">
<label for="floatingPassword_f">FECHA DE NACIMIENTO</label>
</div>
</div>

<div class="input-group mb-3">
<div class="form-floating ">
<input  type="file" class="form-control" id="floatingPassword_file" aria-label="Upload" accept="application/pdf"   placeholder=""  name="update_eps_nino"  >
<label for="floatingPassword_file">DOCUMENTO-EPS</label>
</div>
</div>

<div class="input-group mb-3">
<div class="form-group">
  <label for="formFileImage_nino" class="form-label">Foto Del Alumno</label>
  <input class="form-control" type="file" id="formFileImage_nino" name="foto_nino_update" accept="image/*" >
 
</div>
  </div>





<div class="form-floating mb-3">
<select class="form-select" id="floatingSelect_fi" aria-label="Floating label select example" name="update_ficha_nino" >
<?php  $mostar_profesor_f = $conexion_jardin-> prepare('SELECT * from grupos_clases') ;
$mostar_profesor_f -> execute();

while ($profe_f = $mostar_profesor_f->fetch(pdo::FETCH_ASSOC)){
  $selected_grupo_uptade_nino = ( $profe_f['ID_g_c']==  $fila_n['ID_grupo_fk'] )  ?  'selected' : ''  ;

  echo "<option value=\"{$profe_f['ID_g_c']}\" $selected_grupo_uptade_nino>{$profe_f['ficha'] } </option>";

}

?>

</select>
<label for="floatingSelect_fi">SELECIONAR FICHA</label>
</div>

<div class="form-floating mb-3">

<select class="form-select" id="floatingSelect_tu" aria-label="Floating label select example" name="update_tutor_nino" ">
<?php  $mostar_profesor_tu = $conexion_jardin-> prepare('SELECT * from usuarios where rol_u = 3  and activo = 1 ;') ;
$mostar_profesor_tu -> execute();


while ($profe_tu = $mostar_profesor_tu->fetch(pdo::FETCH_ASSOC)){
  $selected_tutor_uptade_nino = ( $profe_tu['ID_usuario']==  $fila_n['ID_tutor'] )  ?  'selected' : ''  ;

  echo "<option value=\"{$profe_tu['ID_usuario']}\" $selected_tutor_uptade_nino>{$profe_tu['nombre_u']} </option>";

}

?>

</select>
<label for="floatingSelect_tu">SELECIONAR TUTOR</label>
</div>

<input  type="text" class="input_invisible"  name="id_nino" value="<?php echo $fila_n['ID_alumno']; ?> " readonly>


<div class="d-flex justify-content-center">
<input  type="submit" name="update_ni" class="btn btn-success btn-md "  value="ACTUALIZAR">
</div>
</form>


</div>
</div>
 </div><!-- fin actualizar  niño-->
 <?php }else{?>
  <button class="btn btn-primary text_obser" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_n<?php echo $fila_n['ID_alumno']; ?>" aria-controls="staticBackdrop">
OBSERVACIÓN
</button>
<!-- crear observacion  -->
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_n<?php echo $fila_n['ID_alumno']; ?>" aria-labelledby="staticBackdropLabel">
<div class="offcanvas-header">

<h5 class="offcanvas-title" id="staticBackdropLabel"> CREAR OBSERVACIÓN DEL NIÑO </h5>
<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">



<form action="procesos.php" method="post">




<div class="input-group">
  <span class="input-group-text">Descripción</span>
  <textarea class="form-control" aria-label="With textarea" name="decripcion_obser"></textarea>
</div>

<input  type="text" class="input_invisible"  name="id_nino_ob" value="<?php echo $fila_n['ID_alumno']; ?> " readonly>

<div class="d-flex justify-content-center mt-3">
<input  type="submit" name="create_observa" class="btn btn-success btn-md "  value="ENVIAR">
</div>

</form>
</div>





 </div><!-- fin  creancion de observacion-->
   
  <?php }?>
 </td>

 <!-- Eliminar ninño -->
 <td>  
 <?php if ($resul_index_rol['rol_u']<>2){ ?>
  <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

  <input  type="text" class="input_invisible"  name="id_ni_dele" value="<?php echo $fila_n['ID_alumno']; ?> " readonly>

    <input  type="submit" name="eliminar_ni" class="btn btn-danger"  value="eliminar"> 
   

  </form>
  <?php }else{?>


<!-- Botón para ver observaciones -->
<button type="button" class="btn btn-success text_obser" onclick="guardarIDYMostrarModal(<?php echo $fila_n['ID_alumno']; ?>)">Ver Observaciones</button>

<!-- Modal para ver las observaciones -->
<div class="col ajuste-col">
    <div class="modal fade z" id="modal_observaciones" tabindex="1" aria-hidden="true" aria-labelledby="label-modal1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable rotate-scale-up ajuste_tamano">
            <div class="modal-content ajuste_modal_color">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <div class="my-title_2">OBSERVACIONES DEL ALUMNO</div>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="observaciones-content">
                    <!-- Aquí se cargará el contenido del modal mediante AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function guardarIDYMostrarModal(id) {
    $.ajax({
        url: 'procesos.php',
        method: 'POST',
        data: { id_alumno: id },
        success: function(response) {
            // Opcional: manejar la respuesta si es necesario

            // Cargar las observaciones en el modal
            $('#observaciones-content').load('cargar_observaciones.php', function() {
                // Abrir el modal después de cargar el contenido
                $('#modal_observaciones').modal('show');
            });
        }
    });
}
</script>




    <?php }?>
 </td>



</tr>
<?php endforeach?>
</table>

</div>



  </div>  <!--fin niños alumnos  -->

  <?php }?>
  <?php  if($resul_index_rol['rol_u']==5 or $resul_index_rol['rol_u']==4){  ?>

     <div class="contenido" id="contenido-4"><!-- profesores -->
     <h1 id="my-title"> PROFESORES Y SU MATERIA </h1>

     <div class="tb_scroll scrol_usuarios">
<table >

<tr class="head xd">
    <td>NOMBRE</td> 
    <td>APELLIDO</td>
    <td>FECHA DE NACIMIENTO</td>
    <td>CORREO</td>
    <td>MATERIA</td>
    <td>CELULAR</td>
    <td>AÑOS DE EXPERIENCIA</td>
   
    

    <td colspan="2">Accion 
<!-- nuevo boton  profe-->
    <!-- fin nuevo ingreso  profesor-->
      
    </td>
</tr>
<?php foreach($resultado_profesor as $fila_pro): ?>
<tr   class="cuerpo_form" >

<td><?php echo $fila_pro['nombre_u']; ?></td>
<td><?php echo $fila_pro['apellido_u']; ?></td>
<td><?php echo $fila_pro['fechanacimiento']; ?></td>
<td><?php echo $fila_pro['correo_u']; ?></td>
<td><?php echo $fila_pro['materia']; ?></td>
<td><?php echo $fila_pro['celular']; ?></td>
<td><?php echo $fila_pro['years_experiencia']; ?></td> 





 <td>  

 
 <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop<?php echo $fila_pro['ID_tabla_p']; ?>" aria-controls="staticBackdrop">
actualizar
</button>
<!-- actualizar profe -->
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop<?php echo $fila_pro['ID_tabla_p']; ?>" aria-labelledby="staticBackdropLabel">
<div class="offcanvas-header">

<h5 class="offcanvas-title" id="staticBackdropLabel"> ACTUALIZAR DATOS PROFESOR </h5>
<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">
<div>


<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in"   placeholder="" name="nombre_materia_update" value="<?php echo $fila_pro['materia'] ?>" required> 
<label for="floatingInput_in">MATERIA</label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i"   placeholder=""  name="cel_profe_update" pattern="^[1-9]\d{9}$" title="SOLO NUMEROS DE TELEFONO" value="<?php echo $fila_pro['celular'] ?>" required>
<label for="floatingPassword_i">TELEFONO</label>
</div>
<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_e"  placeholder=""  name="xp_profe_update" pattern="[0-9]{1,2}" value="<?php echo $fila_pro['years_experiencia'] ?>" required>
<label for="floatingPassword_e">EXPERIENCIA </label>
</div>
</div>


<div class="form-floating mb-3">

  <select class="form-select" id="floatingSelect_pro" aria-label="Floating label select example" value="<?php echo $fila_pro['ID_usuario']; ?>" name="profe_update">


<?php
    $mostar_profesor_nu = $conexion_jardin->prepare('SELECT * from usuarios where rol_u = 2 and activo = 1 ;');
    $mostar_profesor_nu->execute();

    while ($profe_nu = $mostar_profesor_nu->fetch(PDO::FETCH_ASSOC)) {
      if ($profe_nu['ID_usuario'] == $fila_pro['ID_usuario']) {
        echo "<option value=\"{$profe_nu['ID_usuario']}\" selected>{$profe_nu['nombre_u']} - {$profe_nu['apellido_u']}</option>";
      } else {
        echo "<option value=\"{$profe_nu['ID_usuario']}\">{$profe_nu['nombre_u']} - {$profe_nu['apellido_u']}</option>";
      }
    }
    ?>

</select>
<label for="floatingSelect_pro">SELECIONAR PROFESOR</label>
</div>

<input  type="text" class="input_invisible"  name="id_profe_update" value="<?php echo $fila_pro['ID_tabla_p']; ?> " readonly>


<div class="d-flex justify-content-center">
<input  type="submit" name="update_p" class="btn btn-success btn-md "  value="AGREGAR">
</div>
</form>



</div>
</div>
 </div><!-- fin actualizar  profe-->
 </td>

 <!-- Eliminar profe -->
 <td>  
  <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

  <input  type="text" class="input_invisible"  name="id_profe_dele" value="<?php echo $fila_pro['ID_tabla_p']; ?> " readonly>

    <input  type="submit" name="eliminar_profe" class="btn btn-danger"  value="eliminar"> 
   

  </form>
 </td>



</tr>
<?php endforeach?>
</table>

</div>

</div> <!-- fin profesores -->
    <?php }?>
    <?php  if($resul_index_rol['rol_u']==1){  ?>

     <div class="contenido" id="contenido-5"> <!-- inicio usuarios -->
     
     <h1 id="my-title"> USUARIOS ACEPTADOS</h1>

<div class="tb_scroll scrol_usuarios">
<table >

<tr class="head xd">
    <td>NOMBRE</td> 
    <td>APELLIDO</td>
    <td>CORREO</td>
    <!-- <td>CONTRASEÑA</td> -->
    <td>ROL</td>
    <!-- <td>ESTADO</td> -->
   
    

    <td colspan="3">Acción 
<!-- nuevo boton  usuario-->
    </td>
</tr>
<?php foreach($resultado_usuarios as $fila_user): ?>
<tr   class="cuerpo_form" >

<td><?php echo $fila_user['nombre_u']; ?></td>
<td><?php echo $fila_user['apellido_u']; ?></td>
<td><?php echo $fila_user['correo_u']; ?></td>
<!-- <td>/* echo $fila_user['Contrasena_u']; */?></td> -->
<td><?php echo $fila_user['nombre_rol']; ?></td>
<!-- <td> /* if ( $fila_user['activo']==1){
echo 'activo';
} */?></td>  -->





 <td>  

 
 <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_user<?php echo $fila_user['ID_usuario']; ?>" aria-controls="staticBackdrop<?php echo $fila_user['ID_usuario']; ?>">
Actualizar
</button>
<!-- actualizar datos usurio -->
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_user<?php echo $fila_user['ID_usuario']; ?>" aria-labelledby="staticBackdropLabel">
<div class="offcanvas-header">

<h5 class="offcanvas-title" id="staticBackdropLabel"> ACTUALIZAR DATOS USUARIO </h5>
<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">
<div>


<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in" aria-label="First name"  placeholder="" name="nombre_usuario_update" value="<?php echo $fila_user['nombre_u'] ?>"> 
<label for="floatingInput_in">Nombre</label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i" aria-label="Last name"  placeholder=""  name="apellido_usuario_update"  value="<?php echo $fila_user['apellido_u'] ?>" >
<label for="floatingPassword_i">Apellido</label>
</div>
</div>

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="email" class="form-control" id="floatingInput_co"   placeholder="" name="correo_usuario_update" value="<?php echo $fila_user['correo_u'] ?>"> 
<label for="floatingInput_co">Correo</label>
</div>

<!-- <div class="form-floating mb-3">
<input  type="password" class="form-control" id="floatingPassword_pas"   placeholder=""  name="contrasena_usuario_update" value="<?php echo $fila_user['Contrasena_u'] ?>" readonly>
<label for="floatingPassword_pas">Contraseña</label>
</div> -->
</div>


<div class="form-floating mb-3">

  <select class="form-select" id="floatingSelect_pro" aria-label="Floating label select example" value="<?php echo $fila_user['rol_u']; ?>" name="rol_usuario_update">


<?php
    $mostar_profesor_nu = $conexion_jardin->prepare('SELECT nombre_rol, id_rol FROM rol_usuario ;');

    $mostar_profesor_nu->execute();

    while ($profe_nu = $mostar_profesor_nu->fetch(PDO::FETCH_ASSOC)) {
      if ($profe_nu['id_rol'] == $fila_user['rol_u']) {
        echo "<option value=\"{$profe_nu['id_rol']}\" selected>{$profe_nu['nombre_rol']}</option>";
      } else {
        echo "<option value=\"{$profe_nu['id_rol']}\">{$profe_nu['nombre_rol']} </option>";
      }
    }
    ?>

</select>
<label for="floatingSelect_pro">SELECIONAR ROL</label>
</div>

<input  type="text" class="input_invisible"  name="id_usuario_update" value="<?php echo $fila_user['ID_usuario']; ?> " readonly>


<div class="d-flex justify-content-center">
<input  type="submit" name="update_user" class="btn btn-success btn-md "  value="ACTUALIZAR">
</div>
</form>



</div>
</div>
 </div><!-- fin actualizar  usuario-->
 </td>

 <!-- Eliminar usuario -->
 <td>  
  <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

  <input  type="text" class="input_invisible"  name="id_usuario_dele" value="<?php echo $fila_user['ID_usuario']; ?> " readonly>

    <input  type="submit" name="eliminar_user" class="btn btn-danger"  value="eliminar"> 
   

  </form>
 </td>

<!-- desactivar usuario -->
 <td>  
  <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

  <input  type="text" class="input_invisible"  name="id_desac_user" value="<?php echo $fila_user['ID_usuario']; ?> " readonly>

    <input  type="submit" name="desactivar_user" class="btn desactivar "  value="Desactivar"> 
   

  </form>
 </td>



</tr>
<?php endforeach?>
</table>

</div>

     </div> <!--fin usuarios -->
     <?php }?>


     <div class="contenido bg-info p-5 bg-opacity-50 text-dark" id="contenido-6">

<form class="row g-3" action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
 <h1 class="text-center">MIS DATOS</h1>
<div class="col-md-6" style="display: none;">
   <label for="inputNombre" class="form-label">ID</label>
   <input type="text" class="form-control" id="" name="porfile_id" value="<?php echo $resul_index_rol['ID_usuario'] ?>">
 </div>
 <div class="col-md-6">
   <label for="inputNombre" class="form-label"  >Nombre</label>
   <input type="text" class="form-control" id="" name="porfile_name" value="<?php echo $resul_index_rol['nombre_u'] ?>" Pattern="[a-zA-Záéíóúü ]+" required="">
 </div>
 <div class="col-md-6">
   <label for="inputApellido" class="form-label"  >Apellido</label>
   <input type="text" class="form-control" id="" name="porfile_lastname" value="<?php echo $resul_index_rol['apellido_u'] ?>" Pattern="[a-zA-Záéíóúü ]+" required="">
 </div>
 <div class="col-md-6">
   <label for="inputEmail4" class="form-label"  >Correo</label>
   <input type="email" class="form-control" id="" name="porfile_email" value="<?php echo $resul_index_rol['correo_u'] ?>" required="">
 </div>
 <div class="col-md-6">
   <label for="inputEmail4" class="form-label">Nueva Contraseña</label>
   <input type="password" class="form-control" id="" name="porfile_password" >
 </div>
 <div class="col-12">
   <label for="inputFecha" class="form-label"  >Fecha de nacimiento</label>
   <input type="date" class="form-control" id="" name="porfile_birthdate" value="<?php echo $resul_index_rol['fechanacimiento'] ?>" required="">
     </div>
 <div class="col-lg-4  col-sm-12 mx-auto">
   <button type="submit" class="btn btn-primary col-12" name="update_perfil">Actualizar</button>
 </div>
 <div>
  <!-- <a href="reportegrupo.php">Reporte Grupo</a> -->
 </div>
</form>
   </div>


   <!-- generar certificado rol acudiente -->
<div class="contenido" id="contenido-7">
<h1 class="text-center">CERTIFICADO</h1><!--CERTIFICADOS PDF-->
<div class=" tb_scroll scrol_usuarios">
<p class="fs-5 mt-4">Bienvenidos a los certificados del jardín infantil Mundo Acuarela</p>
<p class="fs-5">En este apartado tendrá acceso a un certificado de estudio vigente</p>
<div class="col-6 mx-auto">
   <form action="reportes.php" class="mt-5" method="POST">
      <label for="">Por favor digite el número de identidad del alumno</label>
      <input type="text" class="form-control mt-5" placeholder="N° Identificación" name="identificacion" pattern="\d+" required>
      <div class="mt-5 col-lg-6 col-sm-12 mx-auto">
         <button type="submit" class="btn btn-info col-12" name="certificado">Descargar</button>
      </div>
   </form>
</div>
</div>
</div>
  
<?php
// Obtener todos los ID_alumno y nombre asociados al tutor
$consulta_alumnos = $conexion_jardin->prepare("SELECT a.ID_alumno,a.doc_identidad, a.nombre_a, a.apellido_a
                                              FROM alumno a
                                              INNER JOIN usuarios u ON a.ID_tutor = u.ID_usuario
                                              WHERE a.ID_tutor = :id_tutor"); //CONSULTA PARA OBTENER EL ID DEL ALUMNO DE ACUERDO AL USUARIO ACUDIENTE ACUTUALMENTE INICIADO SESION
$consulta_alumnos->bindParam(':id_tutor', $id_usuario_index);
$consulta_alumnos->execute();
$resultado_alumnos = $consulta_alumnos->fetchAll();

$resultados_observaciones = [];//GUARDA TODOS LOS IDS CONCIDENTES

// Iterar sobre cada ID_alumno y obtener sus observaciones
foreach ($resultado_alumnos as $alumno) {
    $id_alumno = $alumno['ID_alumno'];
    $doc_alumno=$alumno['doc_identidad'];
    $nombre_alumno = $alumno['nombre_a'] . ' ' . $alumno['apellido_a'];

    $mostrar = $conexion_jardin->prepare("SELECT descripcion, fecha_hora_creacion 
                                          FROM observaciones 
                                          WHERE ID_nino_fk = :id_alumno");//CONSULTA PARA MOSTRAR LAS OBSERVACIONES DEL ALUMNO
    $mostrar->bindParam(':id_alumno', $id_alumno);
    $mostrar->execute();
    $observaciones = $mostrar->fetchAll();

    $resultados_observaciones[] = [
        'nombre_alumno' => $nombre_alumno,
        'observaciones' => $observaciones,
        'id' =>$id_alumno,
        'identificacion'=>$doc_alumno,
    ];
}
?>

<!-- obserbaciones de los niño rol acudientre  -->
<div class="contenido" id="contenido-8"><!--CONTENIDO 8-->
  <h2 class="text-center">OBSERVACIONES DEL ALUMNO</h2>
   <div class=" tb_scroll table-info  scrol_usuarios">
  <?php foreach ($resultados_observaciones as $alumno): ?>
    <form action="observaciones.php" method="POST">
    <h3>Observaciones de <?php echo htmlspecialchars($alumno['nombre_alumno']); ?> <button class="btn btn-success boton_observacion"  >Descargar</button></h3>
    <table class="tabla_n_o mb-4">
      <tr class="head xd">
      <!-- <th>Identificación</th> -->
        <th>Descripción</th>
        <th>Fecha</th>
        
        <!-- <th>Acciones</th> -->
      </tr>
      <?php foreach ($alumno['observaciones'] as $observacion): ?>
      <tr class="cuerpo_form">
      <!-- <td name="identificacionobservacion"><?php //echo  htmlspecialchars ($alumno['identificacion']); ?></td> -->
        <td><?php echo htmlspecialchars($observacion['descripcion']); ?></td>
        <td><?php echo htmlspecialchars($observacion['fecha_hora_creacion']); ?></td>
        
      </tr>
      <input type="text" class="input_invisible" name="observacion" value="<?php echo htmlspecialchars ($alumno['identificacion']); ?>">

      <?php endforeach; ?>
    </table>
    </form>

  <?php endforeach; ?>
</div>
   </div>
   <?php $alertage=false; ?>
<!-- matriculas rol acudiente -->
   <div class="contenido  b p-2  text-primary  " id="contenido-9" style="display:block;">
   

   
    <h2 class="text-center">MATRICULA</h2>
    <form  class="row g-3 "  action="procesos.php" method="post" enctype="multipart/form-data" >
    <div class="col-md-6" style="display: none;">
   <label for="id_n" class="form-label">ID</label>
   <input type="text" class="form-control" id="id_n" name="tutor_id" value="<?php echo $_SESSION['id_usuario']; ?>">
 </div>
 
 
 <div class="col-md-6  ">
 
   <label for="inputNombre" class="form-label"  >Nombre</label>
   <input type="text" class="form-control" id="inputNombre" name="nombre_nino_ma"  aria-label="First name"  Pattern="[a-zA-Záéíóúü ]+" title="Solo letras" required="" placeholder="sin numeros o caracteres especiales">
 </div>
 
 <div class="col-md-6">
   <label for="inputApellido" class="form-label"  >Apellido</label>
   <input type="text" class="form-control" id="inputApellido" name="apellido_nino_ma" aria-label="Last name" Pattern="[a-zA-Záéíóúü ]+" title="Solo letras" required="" placeholder="sin numeros o caracteres especiales" >
 </div>
 <div class="col-md-6">
   <label for="inputdocument" class="form-label"  >N° Documento</label>
   <input type="text" class="form-control" id="inputdocument" name="doc_nino_ma"  pattern="\d+"  title="solo numeros de documentos" required="" placeholder="sin letras ni caracteres especiales o espacios">
 </div>
 <div class="col-md-6">
   <label for="inputed" class="form-label"  >Edad </label>
   <input type="text" class="form-control" id="inputed" name="edad_nino_ma" Pattern="^[1-6]$" title="solo se puede niños de 1 a 6 años" required="" placeholder="sin letras ni caracteres especiales o espacios">
 </div>
<div class="col-md-6">
   <label for="inputFecha" class="form-label"  >Fecha De Nacimiento</label>
   <input type="date" class="form-control" id="" name="nacimiento_nino_ma" max="<?php echo  date( 'Y-m-d'); ?>" required="" >
     </div>

      <div class="col-md-6  mb-3">       
  <label for="formFile" class="form-label">CERTIFICADO EPS</label>
 
  <input class="form-control" type="file" id="formFile" name="file" id="file" accept="application/pdf" required="" >
</div> 
  
  <div class="col-sm-12 col-md-11">
<div class="form-group_ajust">
  <label for="formFileImage" class="form-label">Foto Del Alumno</label>
  <input class="form-control" type="file" id="formFileImage" name="fileimage" accept="image/*" required="" onchange="cambiarFoto(event, 'img_n')">
  <img class="avatar" src="img/base.png" alt="avatar" id="img_n">
</div>
  </div>



<div class="col-lg-4  col-sm-12 mx-auto">
<button type="submit" class="btn btn_matricula mt-2 col-12 text-light" name="matricula_rol_3">Enviar</button>
</div>

    </form>
</div>

<?php  if($alertage==true){  ?>
<!-- alerta para las sesiones -->
 <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute;" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>El usuario debe ser mayor de 18 años</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Le recomendamos verificar su información </p>
</div>
</div>
<?php  }?>

<div class="contenido" id="contenido-10"><!--CONTENIDO  carnet -->
  <h2 class="text-center">Carnet Estudiantil</h2>
   <div class=" tb_scroll table-info  scrol_usuarios ">
    <div class="centro_carnert">
<?php   $sql_carnet = $conexion_jardin->prepare(" SELECT a.nombre_a, a.apellido_a , a.foto_alumno ,a.ID_alumno , a.doc_identidad , g.ficha, a.ID_tutor , acu.celular,acu.emergencia_cel ,acu.direccion FROM alumno as a INNER JOIN usuarios as u on u.ID_usuario = a.ID_tutor 
INNER JOIN grupos_clases as g on g.ID_g_c = a.ID_grupo_fk 
INNER JOIN acudientes as acu on acu.ID_usuario_fk = u.ID_usuario WHERE a.ID_tutor= $_SESSION[id_usuario] ;");   
$sql_carnet->execute();
$alumno_carnet = $sql_carnet->fetchAll();

foreach( $alumno_carnet as $carnet ){

?>

<div class=" card mb-3  fondo_card border-success">

  <div class="row  ">
    <div class="col-md-4 ajuto_carnert ">
      <img src="<?php echo 'https://proyectosjevl.com/mundoacuarela/'.$carnet['foto_alumno'];?>" class="img-fluid " alt="sin foto momneto">
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title title_config_card">Jardiin Infantil Mundo Acuarela</h5>
        <p class="card-text "> Nombre :  <?php echo ' '. $carnet['nombre_a'] . '  ' . $carnet['apellido_a'];  ?></p>
        <p class="card-text"> N° Documento T.I. :  <?php echo ' '. $carnet['doc_identidad'] ; ?></p>
        <p class="card-text"> Ficha :  <?php echo ' '. $carnet['ficha'] ; ?></p>
        <p class="card-text"> Celular :  <?php echo ' '. $carnet['celular'] ; ?></p>
        <p class="card-text"> Celular Emergencia  :  <?php echo ' '. $carnet['emergencia_cel'] ; ?></p>
        <p class="card-text"> Direcion  :  <?php echo ' '. $carnet['direccion'] ; ?></p>


   

        <!-- <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p> -->
      </div>
      <div class="card-footer text-body-secondary">
  <form action="carnet.php" method="POST"  class="ajust_carnet"> 
  <input type="text" class="input_invisible"  name="id_carnet"  value="<?php echo  $carnet['ID_alumno']  ?>" >
  <button class="btn btn-success  "  >Descargar</button>
</form>
  </div>
    </div>
  
  </div>
  
</div>



<?php }?>

   </div>

   </div>
</div>



 </div>   <!--contenido de las tablas  main  -->

 </div><!-- container_index -->

 <script src=" https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- <script  src="bootstrap-5.3.3-dist/js/bootstrap.min.js"></script>  -->
<script  src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script> 

<script src="js_J_I/index_usuarios.js"></script> 


</body>
</html>