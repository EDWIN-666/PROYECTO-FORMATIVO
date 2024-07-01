<?php 
include ('conexion_j_i.php');
session_start();



if (!isset( $_SESSION['id_usuario'])){  //id para poder sacar el rol 
  header("location:index_sesion.php");
}
$id_usuario_index = $_SESSION['id_usuario'];
 $consul_rol_usuario_index = $conexion_jardin -> prepare("SELECT usuarios.* , rol_usuario.* from usuarios inner join rol_usuario on usuarios.rol_u = rol_usuario.id_rol  where  usuarios.ID_usuario= '$id_usuario_index' and usuarios.activo = 1 ;");
 $consul_rol_usuario_index ->execute();
 $resul_index_rol = $consul_rol_usuario_index->fetch();

 // consulta por si el rol es  profesor, completar su perfil 
if($resul_index_rol['rol_u']==2){
  $sql_perfil_profesor = $conexion_jardin->prepare("SELECT p.* from profesor as p  WHERE p.ID_profesor =  '$id_usuario_index' ");
  $sql_perfil_profesor -> execute();
  if ($sql_perfil_profesor->rowCount()>0){
    // echo '<h1 class="text-light">profesor con perfil completo (esta en la tabla profesor )</h1>';
  }else{
    //  echo '<h1 class="text-light">profesor sin perfil completo ( NOOO esta en la tabla profesor )</h1>'; 
    echo '<script type="text/javascript">',
'localStorage.setItem("abrir_modal", "true");',
         '</script>';
         

  }

}
  

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
    $consul_grupos = $conexion_jardin->prepare("SELECT g.* , p.* ,u.* from grupos_clases as g INNER join profesor as p on g.id_profesor_fk = p.ID_tabla_p INNER join usuarios as u on u.ID_usuario = p.ID_profesor ;"); // where u.rol_u = 2
    if ($consul_grupos) {
      $consul_grupos->execute();
      $resultado_grupos = $consul_grupos -> fetchAll();

    } else {
     
      echo 'errror 1 if ';
    }
  } elseif ($resul_index_rol['rol_u'] == 2) {
    $consul_grupos = $conexion_jardin->prepare("SELECT g.* , p.* ,u.* from grupos_clases as g INNER join profesor as p on g.id_profesor_fk = p.ID_tabla_p INNER join usuarios as u on u.ID_usuario = p.ID_profesor where p.ID_profesor =' $id_usuario_index';"); // where u.rol_u = 2
  //  var_dump($id_usuario_index);
    if ($consul_grupos) {
      $consul_grupos->execute();
      $resultado_grupos = $consul_grupos -> fetchAll();

    } else {
     
      echo 'errror 2 if ';

    }
  }


if(isset($_POST['update_g'])){
  $id_g = (int) $_POST['id_grupo'];
  $ficha = $_POST['new_ficha'] ;
  $aula = (int)$_POST['new_aula'];
  $profe =  (int)$_POST['new_profe_g'];
  $update_grupo = $conexion_jardin ->prepare("UPDATE  grupos_clases set ficha=:new_ficha , num_aula = :new_aula   , id_profesor_fk = :new_profe   where  ID_g_c = '$id_g' ;");
$update_grupo -> bindParam(':new_ficha' , $ficha, pdo::PARAM_STR);
$update_grupo -> bindParam(':new_aula', $aula, pdo::PARAM_INT);
$update_grupo->bindParam(':new_profe',$profe, pdo::PARAM_INT) ;
$update_grupo->execute() ;
$actualizacion=1;//cambios
header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios
  }
  if(isset($_POST['insert_g'])){  
    $ficha = $_POST['insert_ficha'] ;
    $aula = (int)$_POST['insert_aula'];
    $profe =  (int)$_POST['insert_profe_g'];
    $insert_grupo = $conexion_jardin ->prepare("INSERT INTO  grupos_clases (ficha,num_aula,id_profesor_fk ) values (:new_ficha , :new_aula  , :new_profe ) ; ");
  $insert_grupo -> bindParam(':new_ficha' , $ficha, pdo::PARAM_STR);
  $insert_grupo -> bindParam(':new_aula', $aula, pdo::PARAM_INT);
  $insert_grupo->bindParam(':new_profe',$profe, pdo::PARAM_INT) ;
  $insert_grupo->execute() ;
  $agregacion=1;//cambios
  header("location:index_usuarios.php?ag_n=".$agregacion);//cambios
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
  $colsulta_niños = $conexion_jardin-> prepare('SELECT a.* , u.*, g.*  from alumno as a INNER join usuarios as u on u.ID_usuario = a.ID_tutor INNER join grupos_clases AS g on g.ID_g_c = a.ID_grupo_fk;');
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
if(isset($_POST['insert_n'])){
 $nombre_n = $_POST['nombre_nino'];
 $apellido_n = $_POST['apellido_nino'];
 $edad_ni = (int)$_POST['edad_nino'];
 $doc_ni = $_POST['doc_nino'];
 $fecha_n = $_POST['fecha_nino'];
 $eps_nin = $_POST['eps_nino'];
  $ficha_n = (int)$_POST['ficha_nino'] ;
  $tu =  (int)$_POST['tutor_nino'];
$insert_ninoss = $conexion_jardin ->prepare("INSERT INTO alumno
 ( ID_tutor, ID_grupo_fk , nombre_a, apellido_a, doc_identidad, fecha_nacimiento  ,edad ,info_eps) 
values (:new_tu, :new_ficha ,:new_nombre,:new_apellido, :new_documento, :new_fecha ,:new_edad, :new_eps ) ; ");
$insert_ninoss->bindParam(':new_tu',$tu, pdo::PARAM_INT) ;
$insert_ninoss -> bindParam(':new_ficha', $ficha_n, pdo::PARAM_INT);
$insert_ninoss -> bindParam(':new_nombre' , $nombre_n, pdo::PARAM_STR);
$insert_ninoss -> bindParam(':new_apellido' , $apellido_n, pdo::PARAM_STR);
$insert_ninoss -> bindParam(':new_documento' , $doc_ni, pdo::PARAM_STR);
$insert_ninoss -> bindParam(':new_fecha' , $fecha_n, pdo::PARAM_STR);
$insert_ninoss -> bindParam(':new_edad' , $edad_ni, pdo::PARAM_INT);
$insert_ninoss -> bindParam(':new_eps' , $eps_nin, pdo::PARAM_STR);
$insert_ninoss->execute() ;
$agregacion=1;//cambios
header("location:index_usuarios.php?ag_n=".$agregacion);//cambios
  }
  if(isset($_POST['update_ni'])){
     $id_nino = (int)$_POST['id_nino'];
    $nombre_n = $_POST['update_nombre_nino'];
    $apellido_n = $_POST['update_apellido_nino'];
    $edad_ni = (int)$_POST['update_edad_nino'];
    $doc_ni = $_POST['update_doc_nino'];
    $fecha_n = $_POST['update_fecha_nino'];
    $eps_nin = $_POST['update_eps_nino'];
     $ficha_n = (int)$_POST['update_ficha_nino'] ;
     $tu =  (int)$_POST['update_tutor_nino'];
   $insert_ninoss = $conexion_jardin ->prepare("UPDATE alumno set 
ID_tutor=:new_tu, ID_grupo_fk= :new_ficha , nombre_a=:new_nombre, apellido_a=:new_apellido, doc_identidad=:new_documento, fecha_nacimiento= :new_fecha  ,edad =:new_edad,info_eps=:new_eps 
where ID_alumno ='$id_nino'; ");
   $insert_ninoss->bindParam(':new_tu',$tu, pdo::PARAM_INT) ;
   $insert_ninoss -> bindParam(':new_ficha', $ficha_n, pdo::PARAM_INT);
   $insert_ninoss -> bindParam(':new_nombre' , $nombre_n, pdo::PARAM_STR);
   $insert_ninoss -> bindParam(':new_apellido' , $apellido_n, pdo::PARAM_STR);
   $insert_ninoss -> bindParam(':new_documento' , $doc_ni, pdo::PARAM_STR);
   $insert_ninoss -> bindParam(':new_fecha' , $fecha_n, pdo::PARAM_STR);
   $insert_ninoss -> bindParam(':new_edad' , $edad_ni, pdo::PARAM_INT);
   $insert_ninoss -> bindParam(':new_eps' , $eps_nin, pdo::PARAM_STR);
   $insert_ninoss->execute() ;
   $actualizacion=1;//cambios

   header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios
      }
     if(isset($_POST['eliminar_ni'])){
      $id_dele_ni = (int)$_POST['id_ni_dele'];
      $eliminar_ni = $conexion_jardin ->prepare("DELETE from  alumno  where  ID_alumno = '$id_dele_ni' ;");
      //  echo var_dump($id_ni_dele);
        $eliminar_ni ->execute();
        $eliminar_ni ->execute();//cambios
        header("location:index_usuarios.php?el_n=".$eliminacion);//cambios
        }
     //profesores

     $validacion_com_perf_prof = $_GET['vcpp'] ?? 3 ;

     $validacion_observacion_nino = $_GET['dn_v'] ?? 3 ;

 $validacion_observacion_nino_up = $_GET['dn_v_up'] ?? 3 ;


$consul_profesores = $conexion_jardin -> prepare('SELECT p.* , u.* from profesor as p INNER JOIN usuarios as u on p.ID_profesor = u.ID_usuario;');
$consul_profesores -> execute();
$resultado_profesor  = $consul_profesores-> fetchAll();
if(isset($_POST['insert_p'])){
 $materia_p = $_POST['nombre_materia_insert'];
 $cel_p = (int )$_POST['cel_profe_insert'] ;
 $edad_p = (int)$_POST['edad_profe_insert'];
 $id_p = (int) $_POST['profe_insert'];
 $insert_porfesor = $conexion_jardin->prepare('INSERT into  profesor  (id_profesor, materia, celular,years_experiencia)
 values(:id_profe ,:materia,:celular,:edad); ');
 $insert_porfesor-> bindParam(':id_profe', $id_p, pdo::PARAM_INT);
 $insert_porfesor -> bindParam(':materia' , $materia_p, pdo::PARAM_STR);
 $insert_porfesor-> bindParam(':celular', $cel_p, pdo::PARAM_INT);
 $insert_porfesor-> bindParam(':edad', $edad_p, pdo::PARAM_INT);
 $insert_porfesor ->execute();
 $agregacion=1;//cambios

 header("location:index_usuarios.php?ag_n=".$agregacion);//cambios

}
if(isset($_POST['update_p'])){
$id_profe_tabla = (int) $_POST['id_profe_update'];
  $materia_p_u = $_POST['nombre_materia_update'];
  $cel_p_u = (int) $_POST['cel_profe_update'] ;
  $edad_p_u = (int)$_POST['edad_profe_update'];
  $id_p_u = (int) $_POST['profe_update'];
//  echo var_dump($id_p_u);
  $update_porfesor = $conexion_jardin->prepare("UPDATE profesor SET  
    ID_profesor=:id_profe, materia=:materia, celular=:celular,years_experiencia=:edad
  where  ID_tabla_p = '$id_profe_tabla';");
  $update_porfesor-> bindParam(':id_profe', $id_p_u, pdo::PARAM_INT);
  $update_porfesor -> bindParam(':materia' , $materia_p_u, pdo::PARAM_STR);
  $update_porfesor-> bindParam(':celular', $cel_p_u, pdo::PARAM_INT);
  $update_porfesor-> bindParam(':edad', $edad_p_u, pdo::PARAM_INT);
  $update_porfesor ->execute();
  $actualizacion=1;//cambios

  header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios

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
  $update_user = $conexion_jardin ->prepare("UPDATE  usuarios set nombre_u= :nombre, apellido_u= :apellido, correo_u = :correo,  rol_u = :rol where ID_usuario = '$id_user'; ");
  $update_user-> bindParam(':nombre', $nombre_user, pdo::PARAM_STR);
  $update_user -> bindParam(':apellido' , $apellido_user, pdo::PARAM_STR);
  $update_user-> bindParam(':correo', $correo_user, pdo::PARAM_STR);
  // $update_user-> bindParam(':contrasena', $contra_user, pdo::PARAM_STR);
  $update_user-> bindParam(':rol', $rol_user, pdo::PARAM_INT);
  $update_user->execute();
  $actualizacion=1;//cambios

  header("location:index_usuarios.php?ac_n=".$actualizacion);//cambios

  // }
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


   if(!empty($password_p)){
    $error = politica_contra($password_p);
}
    // Calcula la edad del usuario
    $current_date = new DateTime();
    $time_date = new DateTime($birthdate_p);
    $age = $current_date->diff($time_date)->y;

   
    if ($age < 18) {
        $alertamenor = true;
    } else {
        if (empty($error)) {
            if (empty($password_p)) {
                // Actualiza el perfil sin cambiar la contraseña
                $update_porfile = $conexion_jardin->prepare("UPDATE usuarios SET nombre_u=:new_name_p, apellido_u=:new_lastname_p, correo_u=:new_email_p, fechanacimiento=:new_birthdate_p WHERE ID_usuario=:id_porfile");
                $update_porfile->bindParam(':new_name_p', $name_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_lastname_p', $lastname_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_email_p', $email_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':new_birthdate_p', $birthdate_p, PDO::PARAM_STR);
                $update_porfile->bindParam(':id_porfile', $id_porfile, PDO::PARAM_INT);
                $update_porfile->execute();
                
                $actualizacion_perfil_propio=1;
                
                header("Location: index_usuarios.php?up_per=".$actualizacion_perfil_propio);
                // echo 'se ejecuto  SISIIIIN  contraseña';

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
                
                header("Location: index_usuarios.php?up_per=".$actualizacion_perfil_propio);                // echo 'se ejecuto con contraseña';
            }
        } else {
            // echo "if del empity error ";
            // var_dump($errores); 
             $errorpolitica = true;
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

  if(isset($_POST['matricula'])){

  }
  // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  //     // Verificar si el archivo fue subido sin errores
  //     if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
  //         $uploadedFile = $_FILES['file'];
  
  //         // Verificar si el archivo es un PDF
  //         $fileType = mime_content_type($uploadedFile['tmp_name']);
  //         if ($fileType === 'application/pdf') {
  //             // Crear una carpeta para guardar los archivos si no existe
  //             $uploadDir = 'pdf/';
  //             if (!is_dir($uploadDir)) {
  //                 mkdir($uploadDir, 0755, true);
  //             }
  
  //             // Generar un nombre único para el archivo
  //             $fileName = uniqid() . '-' . basename($uploadedFile['name']);
  //             $filePath = $uploadDir . $fileName;
  
  //             // Mover el archivo a la carpeta de destino
  //             if (move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
  //                 // Obtener la URL del archivo
  //                 $fileUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $filePath;
  //                 echo "Archivo subido exitosamente. URL del archivo: <a href='$fileUrl'>$fileUrl</a>";
  //             } else {
  //                 echo "Error al mover el archivo.";
  //             }
  //         } else {
  //             echo "El archivo no es un PDF.";
  //         }
  //     } else {
  //         echo "Error al subir el archivo.";
  //     }
  // } else {
  //     echo "Método de solicitud no permitido.";
  // }
  
  $actualizacion = $_GET['ac_n'] ?? 3 ;//cambios
$eliminacion = $_GET['el_n'] ?? 3 ;//cambios
$agregacion = $_GET['ag_n'] ?? 3 ;//cambios

?>
<!DOCTYPE html>
<html  lang="es" >
    <head> 
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/vnd.icon" href="IMG/LogoLibros.png">
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
  background: url(IMG/Diseño\ sin\ título.png);
  font-family: 'Handlee',cursive;
}
.xd{
  background: rgb(255, 213, 250);
}
    </style>
</head>
<body>

<!-- alerta para las sesiones  -------------------- -->
<?php  if($errorpolitica==true ){ ?>

 <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position:absolute;" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>La contraseña no cumple con las politicas de seguridad</strong> </p>
  <?php
            if (isset($error) && !empty($error)) {
                echo '<div class="alert alert-danger" role="alert">
                        <ul>';
                foreach ($error as $errores) {
                    echo '<li>' . $errores . '</li>';
                }
                echo '  </ul>
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
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute;" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>El usuario debe ser mayor de 18 años</strong> </p>
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
<div class="alert alert-success  ajuste_color_alerta  fade show alert-dismissible" style="position: absolute; z-index: 1055;" role="alert">
  <h4 class="alert-heading">  	<svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"  style="width: 20px; height: 20px;" ><use xlink:href="#check-circle-fill"/></svg>¡ ACTUALIZACION EXITOSA !</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0"> <strong>Puede Continuar Con Normalidad </strong> </p>

</div>
  </div>

  <?php  } ?>


<?php if ( $validacion_com_perf_prof == 1){?>
  <div class="col-4 mx-auto">
<div class="alert alert-info  fade show alert-dismissible" style="position: absolute; z-index: 1055;" role="alert">
  <h4 class="alert-heading">PERFIL COMPLETADO</h4>
  <p> <strong>Puede Continuar Con Normalidad </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Cualquier Novedad Hable Con Su Superior   </p>
</div>
</div>
 <?php


}elseif( $validacion_com_perf_prof == 0  ){?>
  <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute; z-index: 1056;" role="alert">
  <h4 class="alert-heading">ERROR AL  ENVIAR LOS DATOS </h4>
  <p> <strong>Se Enviaron Datos De Forma Maliciosa  </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"  style=" z-index: 1056;"></button>
  <hr>
  <p class="mb-0"> Vuelva A Intentarlo 🤙 </p>
</div>
</div>


  <?php  echo '<script type="text/javascript">',
'localStorage.setItem("abrir_modal", "true");',
         '</script>';    }elseif($validacion_observacion_nino==0 ){?>
          <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute; z-index: 1056;" role="alert">
  <h4 class="alert-heading">ERROR AL  ENVIAR LOS DATOS </h4>
  <p> <strong>Se Enviaron Datos De Forma Inconrrecta  </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"  style=" z-index: 1056;"></button>
  <hr>
  <p class="mb-0"> Vuelva A Intentarlo 🤙 </p>
</div>
</div>

<?php }elseif($validacion_observacion_nino==1 ){?>
  <div class="col-4 mx-auto">
    <div class="alert alert-info  fade show alert-dismissible" style="position: absolute; z-index: 1055;" role="alert">
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
    <div class="alert alert-success  fade show alert-dismissible" style="position: absolute; z-index: 1055;" role="alert">
  <h4 class="alert-heading">OBSERVACIÓN ACTUALIZADA</h4>
  <p> <strong>Puede Continuar Con Normalidad </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0"></p>
</div>
</div>
  <?php }elseif($validacion_observacion_nino_up==0 ){?>
    <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute; z-index: 1056;" role="alert">
  <h4 class="alert-heading">ERROR AL  ENVIAR LOS DATOS </h4>
  <p> <strong>Se Enviaron Datos De Forma Inconrrecta  </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"  style=" z-index: 1056;"></button>
  <hr>
  <p class="mb-0"> Vuelva A Intentarlo 🤙 </p>
</div>
</div>
  <?php }elseif($validacion_observacion_nino_up==4 ){?>
  <div class="col-4 mx-auto">
    <div class="alert alert-success  fade show alert-dismissible" style="position: absolute; z-index: 1055;" role="alert">
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
<div class="alert alert-info  fade show alert-dismissible" style="position: absolute; z-index: 1055;" role="alert">
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
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute;" role="alert">
  <h4 class="alert-heading">Se ha eliminado correctamente</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" ></button>

  <!-- <p> <strong>El usuario debe ser mayor de 18 años</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Le recomendamos verificar su información </p> -->
</div>
</div>
<?php  }elseif($agregacion==1){  //cambios?>
<!-- alerta para las sesiones -->
<div class="col-4 mx-auto">
<div class="alert alert-info  fade show alert-dismissible" style="position: absolute; z-index: 1055;" role="alert">
  <h4 class="alert-heading">SE AGREGO EXITOSAMENTE</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" ></button>
  <!-- <p> <strong>Se han actualizado correctamente los datos </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Puede continuar con normalidad</p> -->
</div>
</div>
  <?php } ?>

<!-- FIN DEL  LAS ALERTAS ------------------------------------- -->


<header> 
     <h2 class="texto_header_1"> JARDIN INFANTIL MUNDO ACUARELA</h2>
     <nav>
     <div class="btn-group espacio_drop" style="margin-right: 10px;">
  <button type="button" class="btn btn-primary col-12" id="reset_b">
  <!-- <i class="bi bi-person icono"></i> -->
    <p class="texto_header"> BIENVENIDO  </p></button>
  <button type="button" class="btn btn-primary dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">
  </button>
  <ul class="dropdown-menu">
    <li><a class="dropdown-item" href="salir.php">SALIR</a></li>
    <li><a class="dropdown-item" ><p style="margin-bottom: -5px"> BIENVENIDO  <?php echo $resul_index_rol['nombre_u']," | ",$resul_index_rol['nombre_rol'];?></p></button>
</a></li>
  </ul>
</div>
     </nav>
</header>


<!-- modal  de completar perfil del profesor   --------------------------------------------------------------- -->
<div class="col ajuste-col">
    <div class="modal fade  z"  
        id="modal1" 
        tabindex="1" 
        aria-hidden="true" 
        aria-labelledby="label-modal1"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollabel rotate-scale-up">
            <div class="modal-content ajuste_modal_color">
                <div class="modal-header">
                    <h4 class="modal-tittle">

                        <div class="my-title_2"> COMPLETE SU PERFIL DE PROFESOR  </div>
                    </h4>
                </div>
                <div class="modal-body">
                   
<form action="procesos.php" method="post" >

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in"   placeholder="" name="nombre_materia_cpp" pattern="[a-zA-Z]+" title="Solo letras, sin espacios" required> 
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
    <!-- <?php //if($resul_index_rol['rol_u']==3){  ?>
    <button class="boton col-12  boton_js text_btn" id="boton-9">Matricula</button>
    <?php //} ?> -->
    <!-- <button class="boton" id="boton-7">Botón 7</button>
    <button class="boton" id="boton-8">Botón 8</button> -->
  </div>
</div>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasResponsive" aria-labelledby="offcanvasResponsiveLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasResponsiveLabel"> INFORMACIÓN </h5>
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
      <!-- <?php  //if($resul_index_rol['rol_u']==3){  ?>
        <button class=" btn btn-primary col-12 m-2 boton_js" id="boton-9"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Matricula</button>
      <?php //}?> -->
      <!-- <button class="boton" id="boton-6" aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">tutores</button> -->
    <!-- <button class="boton" id="boton-7"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Botón 7</button>
    <button class="boton" id="boton-8"aria-label="Close" data-bs-target="#offcanvasResponsive"data-bs-dismiss="offcanvas">Botón 8</button>
  --></div> 
    </div>
  </div>
</div>



  </aside>  
  <div class="espacio_whap redesfooter">   
             
              <ul class="col-12">
               
                <li class=" w">
                    <span class="iconw"></span>

                  <a href="https://wa.me/573212600725?text=Hola, necesito ayuda con algo.." target="_blank" class="titulo text-light  " style="  text-decoration: none;"><span >AYUDA  O INFORMACION ?</span></a>
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
        <td>CELUALR</td>
        <td>MATERIA</td>
        <td>FICHA</td>
        <td >N° AULA</td>
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
<input  type="number" class="form-control" id="floatingPassword_in" placeholder="" min="1" name="insert_aula" value="" >
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
    <td><?php echo $fila_g['nombre_u']; ?></td>
    <td><?php echo $fila_g['correo_u']; ?></td>
    <td><?php echo $fila_g['celular']; ?></td>
    <td><?php echo $fila_g['materia']; ?></td>
    <td><?php echo $fila_g['ficha']; ?></td>
    <td><?php echo $fila_g['num_aula']; ?></td> 
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
<input type="text" class="form-control" id="floatingInput" placeholder="" name="new_ficha"  value="<?php echo $res_g['ficha'] ?>" >
<label for="floatingInput">FICHA</label>
</div>
<div class="form-floating mb-3">
<input  type="number" class="form-control" id="floatingPassword" placeholder="" min="1" name="new_aula" value="<?php echo $res_g['num_aula'] ?>" >
<label for="floatingPassword">NUMERO DE AULA</label>
</div>
<div class="form-floating mb-3">
  <select class="form-select" id="floatingSelect" aria-label="Floating label select example" name="new_profe_g">
    <?php  $mostar_profesor = $conexion_jardin-> prepare('SELECT u.* , p.* from profesor as p inner join usuarios as u on u.ID_usuario  = p.ID_profesor ;') ;
    $mostar_profesor -> execute();
    while ($profe = $mostar_profesor->fetch(pdo::FETCH_ASSOC)){
      echo "<option value=\"{$profe['ID_tabla_p']}\">{$profe['nombre_u'] } - materia  : {$profe['materia'] }  </option>";
    }
    ?>
  </select>
  <label for="floatingSelect">SELECIONAR PROFESOR</label>
</div>
<input  type="text" class="input_invisib"  name="id_grupo" value="<?php echo $res_g['ID_g_c']; ?> " readonly>
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
    

    <td colspan="2">Accion 
    <?php if ($resul_index_rol['rol_u']<>2){ ?>
<!-- nuevo boton  niños-->
    <button class="icon-btn add-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_niño" aria-controls="staticBackdrop_niño">
    <div class="add-icon"></div>
<div class="btn-txt">Nuevo Alumno</div>
</button>

<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_niño" aria-labelledby="staticBackdropLabel_niño">
<div class="offcanvas-header">
<h5 class="offcanvas-title" id="staticBackdropLabel"> INGRESAR DATOS </h5>
<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">
<div>

<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in"  aria-label="First name" placeholder="" name="nombre_nino">
<label for="floatingInput_in">NOMBRE</label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i"  aria-label="Last name" placeholder=""  name="apellido_nino" >
<label for="floatingPassword_i">APELLIDO</label>
</div>
<div class="form-floating mb-3">
<input  type="number" class="form-control" id="floatingPassword_e"  placeholder=""  name="edad_nino" >
<label for="floatingPassword_e">EDAD</label>
</div>
</div>

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_cc"  placeholder="" name="doc_nino">
<label for="floatingInput_cc">N° DOCUMENTO</label>
</div>

<div class="form-floating mb-3">
<input  type="date" class="form-control" id="floatingPassword_f"   placeholder=""  name="fecha_nino" >
<label for="floatingPassword_f">FECHA DE NACIMIENTO</label>
</div>
</div>

<div class="input-group mb-3">
<div class="form-floating ">
<input  type="file" class="form-control" id="floatingPassword_file" aria-label="Upload" accept=".pdf"  placeholder=""  name="eps_nino" value="null" >
<label for="floatingPassword_file">DOCUMENTO-EPS</label>
</div>
</div>




<div class="form-floating mb-3">
<select class="form-select" id="floatingSelect_fi" aria-label="Floating label select example" name="ficha_nino">
<?php  $mostar_profesor_f = $conexion_jardin-> prepare('SELECT * from grupos_clases') ;
$mostar_profesor_f -> execute();

while ($profe_f = $mostar_profesor_f->fetch(pdo::FETCH_ASSOC)){
  echo "<option value=\"{$profe_f['ID_g_c']}\">{$profe_f['ficha'] } </option>";

}

?>

</select>
<label for="floatingSelect_fi">SELECIONAR FICHA</label>
</div>

<div class="form-floating mb-3">

<select class="form-select" id="floatingSelect_tu" aria-label="Floating label select example" name="tutor_nino">
<?php  $mostar_profesor_tu = $conexion_jardin-> prepare('SELECT * from usuarios where rol_u = 3  and activo = 1 ;') ;
$mostar_profesor_tu -> execute();

while ($profe_tu = $mostar_profesor_tu->fetch(pdo::FETCH_ASSOC)){
  echo "<option value=\"{$profe_tu['ID_usuario']}\">{$profe_tu['nombre_u']} </option>";

}

?>

</select>
<label for="floatingSelect_tu">SELECIONAR TUTOR</label>
</div>



<div class="d-flex justify-content-center">
<input  type="submit" name="insert_n" class="btn btn-success btn-md "  value="AGREGAR">
</div>
</form>


</div>
</div>
</div> <!-- fin nuevo ingreso  niños-->
     
<?php }?> 
    </td>
</tr>
<?php foreach($resultado_niños as $fila_n): ?>
<tr   class="cuerpo_form" >

<td><?php echo $fila_n['nombre_a']; ?></td>
<td><?php echo $fila_n['apellido_a']; ?></td>
<td><?php echo $fila_n['ficha']; ?></td>
<td><?php echo $fila_n['doc_identidad']; ?></td>
<td><?php echo $fila_n['nombre_u']; ?></td>
<!-- <td>/* echo $fila_n['fecha_nacimiento']; */?></td>  -->
<td><?php echo $fila_n['edad']; ?></td> 
<td><?php echo $fila_n['info_eps']; ?></td> 



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

<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in"  aria-label="First name" placeholder="" name="update_nombre_nino"  value="<?php echo $fila_n['nombre_a'] ?>">
<label for="floatingInput_in">NEW-NOMBRE</label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i"  aria-label="Last name" placeholder=""  name="update_apellido_nino" value="<?php echo $fila_n['apellido_a'] ?>" >
<label for="floatingPassword_i">NEW-APELLIDO</label>
</div>
<div class="form-floating mb-3">
<input  type="number" class="form-control" id="floatingPassword_e"  placeholder=""  name="update_edad_nino"  value="<?php echo $fila_n['edad'] ?>" >
<label for="floatingPassword_e">NEW-EDAD</label>
</div>
</div>

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_cc"  placeholder="" name="update_doc_nino"  value="<?php echo $fila_n['doc_identidad'] ?>">
<label for="floatingInput_cc">-NEW-N° DOCUMENTO</label>
</div>

<div class="form-floating mb-3">
<input  type="date" class="form-control" id="floatingPassword_f"   placeholder=""  name="update_fecha_nino"  value="<?php echo $fila_n['fecha_nacimiento'] ?>">
<label for="floatingPassword_f">NEW-FECHA DE NACIMIENTO</label>
</div>
</div>

<div class="input-group mb-3">
<div class="form-floating ">
<input  type="file" class="form-control" id="floatingPassword_file" aria-label="Upload" accept=".pdf"  placeholder=""  name="update_eps_nino" value="null" >
<label for="floatingPassword_file">NEW-DOCUMENTO-EPS</label>
</div>
</div>




<div class="form-floating mb-3">
<select class="form-select" id="floatingSelect_fi" aria-label="Floating label select example" name="update_ficha_nino" value="<?php echo $fila_n['ID_grupo_fk'] ?>">
<?php  $mostar_profesor_f = $conexion_jardin-> prepare('SELECT * from grupos_clases') ;
$mostar_profesor_f -> execute();

while ($profe_f = $mostar_profesor_f->fetch(pdo::FETCH_ASSOC)){
  echo "<option value=\"{$profe_f['ID_g_c']}\">{$profe_f['ficha'] } </option>";

}

?>

</select>
<label for="floatingSelect_fi">NEW-SELECIONAR FICHA</label>
</div>

<div class="form-floating mb-3">

<select class="form-select" id="floatingSelect_tu" aria-label="Floating label select example" name="update_tutor_nino"   value="<?php echo $fila_n['ID_tutor'] ?>">
<?php  $mostar_profesor_tu = $conexion_jardin-> prepare('SELECT * from usuarios where rol_u = 3  and activo = 1 ;') ;
$mostar_profesor_tu -> execute();

while ($profe_tu = $mostar_profesor_tu->fetch(pdo::FETCH_ASSOC)){
  echo "<option value=\"{$profe_tu['ID_usuario']}\">{$profe_tu['nombre_u']} </option>";

}

?>

</select>
<label for="floatingSelect_tu">NEW-SELECIONAR TUTOR</label>
</div>

<input  type="text" class="input_invisibe"  name="id_nino" value="<?php echo $fila_n['ID_alumno']; ?> " readonly>


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
    <td>CORREO</td>
    <td>MATERIA</td>
    <td>CELUALR</td>
    <td>AÑOS DE EXPERIENCIA</td>
   
    

    <td colspan="2">Accion 
<!-- nuevo boton  profe-->
    <button class="icon-btn add-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_profe" aria-controls="staticBackdrop">
    <div class="add-icon"></div>
<div class="btn-txt">Nuevo Profesor</div>
</button>

<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_profe" aria-labelledby="staticBackdropLabel">
<div class="offcanvas-header">
<h5 class="offcanvas-title" id="staticBackdropLabel"> INGRESAR DATOS DEL PROFESOR </h5>
<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">
<div>

<form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">

<div class="input-group flex-nowrap">
<div class="form-floating mb-3">
<input type="text" class="form-control" id="floatingInput_in"   placeholder="" name="nombre_materia_insert">
<label for="floatingInput_in">MATERIA</label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i"   placeholder=""  name="cel_profe_insert" pattern="[0-9]{10}" >
<label for="floatingPassword_i">TELEFONO</label>
</div>
<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_e"  placeholder=""  name="edad_profe_insert" pattern="[0-9]{2}" >
<label for="floatingPassword_e">EXPERIENCIA</label>
</div>
</div>


<div class="form-floating mb-3">

<select class="form-select" id="floatingSelect_tu" aria-label="Floating label select example" name="profe_insert">
<?php  $mostar_profesor_tu = $conexion_jardin-> prepare('SELECT * from usuarios where rol_u = 2  and activo = 1 ;') ;
$mostar_profesor_tu -> execute();

while ($profe_tu = $mostar_profesor_tu->fetch(pdo::FETCH_ASSOC)){
  echo "<option value=\"{$profe_tu['ID_usuario']}\">{$profe_tu['nombre_u']} {$profe_tu['apellido_u']}  </option>";

}

?>

</select>
<label for="floatingSelect_tu">SELECIONAR PROFESOR</label>
</div>



<div class="d-flex justify-content-center">
<input  type="submit" name="insert_p" class="btn btn-success btn-md "  value="AGREGAR">
</div>
</form>


</div>
</div>
</div> <!-- fin nuevo ingreso  profesor-->
      
    </td>
</tr>
<?php foreach($resultado_profesor as $fila_pro): ?>
<tr   class="cuerpo_form" >

<td><?php echo $fila_pro['nombre_u']; ?></td>
<td><?php echo $fila_pro['apellido_u']; ?></td>
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
<input type="text" class="form-control" id="floatingInput_in"   placeholder="" name="nombre_materia_update" value="<?php echo $fila_pro['materia'] ?>"> 
<label for="floatingInput_in">MATERIA</label>
</div>

<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_i"   placeholder=""  name="cel_profe_update" pattern="[0-9]{10}" value="<?php echo $fila_pro['celular'] ?>" >
<label for="floatingPassword_i">TELEFONO</label>
</div>
<div class="form-floating mb-3">
<input  type="text" class="form-control" id="floatingPassword_e"  placeholder=""  name="edad_profe_update" pattern="[0-9]{2}" value="<?php echo $fila_pro['years_experiencia'] ?>">
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

<input  type="text" class="input_invisibl"  name="id_profe_update" value="<?php echo $fila_pro['ID_tabla_p']; ?> " readonly>


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


     <div class="contenido bg-info p-5 bg-opacity-50 text-light" id="contenido-6">

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
 <div class="col-6">
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

<div class="contenido" id="contenido-7">
<h1 class="text-center">CERTIFICADO</h1><!--CERTIFICADOS PDF-->
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
  
<?php
// Obtener todos los ID_alumno y nombre asociados al tutor
$consulta_alumnos = $conexion_jardin->prepare("SELECT a.ID_alumno, a.nombre_a, a.apellido_a
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
    ];
}
?>

<div class="contenido" id="contenido-8"><!--CONTENIDO 8-->
  <h2 class="text-center">OBSERVACIONES DEL ALUMNO</h2>
   <div class=" tb_scroll table-info  scrol_usuarios">
  <?php foreach ($resultados_observaciones as $alumno): ?>
    <h3>Observaciones de <?php echo htmlspecialchars($alumno['nombre_alumno']); ?></h3>
   
    <table class="tabla_n_o">
      <tr class="head xd">
        <th>Descripción</th>
        <th>Fecha</th>
      </tr>
      <?php foreach ($alumno['observaciones'] as $observacion): ?>
      <tr>
        <td><?php echo htmlspecialchars($observacion['descripcion']); ?></td>
        <td><?php echo htmlspecialchars($observacion['fecha_hora_creacion']); ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
 

  <?php endforeach; ?>
</div>
   </div>
   <div class="contenido" id="contenido-9"><!--CONTENIDO 8-->
  <h2 class="text-center">MATRICULA</h2>
   <div class=" tb_scroll table-info  scrol_usuarios bg-info bg-opacity-25 rounded p-2">
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">
      <label for="">CERTIFICADO EPS</label>
      <input type="file" class="form-control" name="file" id="file" accept="application/pdf">
      <input type="submit" class="btn bt-info mt-2" value="Subir">
    </form>
</div>
   </div>

 </div>   <!--contenido de las tablas  main  -->

 </div><!-- container_index -->

 <script src=" https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script  src="bootstrap-5.3.3-dist/js/bootstrap.min.js"></script> 
<script  src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script> 

<script src="js_J_I/index_usuarios.js"></script> 


</body>
</html>