<?php 
include('conexion_j_i.php');
session_start();



if (!isset( $_SESSION['id_usuario'])){ 
  header("location:index_sesion.php");
}

$id_usuario_index = $_SESSION['id_usuario'];
 $consul_rol_usuario_index = $conexion_jardin -> prepare("SELECT usuarios.correo_u , rol_usuario.* , usuarios.rol_u from usuarios inner join rol_usuario on usuarios.rol_u = rol_usuario.id_rol  where  usuarios.ID_usuario= '$id_usuario_index' and usuarios.activo = 1 ;");
 $consul_rol_usuario_index ->execute();
 $resul_index_rol = $consul_rol_usuario_index->fetch();

// mensajes 

if($resul_index_rol['rol_u']==4 or $resul_index_rol['rol_u']== 5 or $resul_index_rol['rol_u']== 6  or $resul_index_rol['rol_u']== 1 ){  

$mensaje_receptor = $conexion_jardin->prepare("SELECT atc.*, u.rol_u, ra.nombre_rol
FROM atencion_cliente AS atc
LEFT JOIN usuarios AS u ON u.ID_usuario = atc.ID_receptor
LEFT JOIN rol_usuario AS ra ON ra.id_rol = u.rol_u
ORDER BY CASE WHEN atc.estado_consulta = 'pendiente' THEN 0 ELSE 1 END;");
$mensaje_receptor->execute();
$result_receptor = $mensaje_receptor->fetchAll(); 
}else{

$sql_notificacion_leida = $conexion_jardin->prepare("UPDATE atencion_cliente SET lectura = 'leido' WHERE correo_a_cl =  '$resul_index_rol[correo_u]';");
$sql_notificacion_leida->execute();

$mensaje_receptor = $conexion_jardin->prepare("SELECT atc.*, u.rol_u, ra.nombre_rol
FROM atencion_cliente AS atc
LEFT JOIN usuarios AS u ON u.ID_usuario = atc.ID_receptor
LEFT JOIN rol_usuario AS ra ON ra.id_rol = u.rol_u WHERE atc.estado_consulta = 'respondido'  and atc.correo_a_cl = '$resul_index_rol[correo_u] '; ");
  $mensaje_receptor->execute();
  $result_receptor = $mensaje_receptor->fetchAll();
}

$validacion = $_GET['env'] ?? 3 ;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/vnd.icon" href="./img/LogoLibros.png">
    <link rel="stylesheet" href="css_J_I/sti_mensajes.css">
    
    <title>Mensajes</title>
</head>
<body>
<style> 
body{
    background: url(img/Diseño\ sin\ título.png);
}
</style>
<?php  if ($validacion==1) {?>
<div class="espacio_alert">

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
<symbol id="check-circle-fill" viewBox="0 0 16 16">
  <path fill="green" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</symbol>
</svg>
<div class="col-9 mx-auto">
<div class="alert alert-success  ajuste_color_alerta  fade show alert-dismissible" role="alert">
<h4 class="alert-heading">  	<svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"  style="width: 20px; height: 20px;" ><use xlink:href="#check-circle-fill"/></svg>¡ACCION EXITOSA !</h4>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
<hr>
<p class="mb-0"> <strong>Puede Continuar Con Normalidad </strong> </p>

</div>
</div>

</div>
<?php } if ($validacion==2){?>
  <div class="espacio_alert">

  <div class="col-9 mx-auto">
<div class="alert alert-danger   ajuste_color_alerta fade show alert-dismissible"  role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>Sucedio Un Error Al Enviar El Mensaje </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Le recomendamos verificar la  información no se permite carateres especiales como () x_ "" '' etc..   </p>
</div>
</div>
</div >
  <?php }   ?>
 

 <h1><a href="index_usuarios.php"><i class="bi bi-arrow-90deg-left"  ></i></a> MENSAJERIA</h1>
<div class="container_index mt-3">
  
<div class="main ">
  
  <div class=" tb_scroll scrol_usuarios ">

<?php if( $resul_index_rol['rol_u']== 1 || $resul_index_rol['rol_u']==4 || $resul_index_rol['rol_u']== 5 || $resul_index_rol['rol_u']== 6   ){  ?>  
<table >
    <tr class="xd">
        <td>NOMBRE</td>
        <td>APELLIDO</td>
        <td>CORREO</td>
        <td class="t_r">CONSULTA </td>   
        <td>FECHA ENVIO </td>
        <td class="t_r">RESPUESTA</td>
        <td>RECEPTOR</td>
        <td>FECHA RESPUESTA</td>
        <td>ESTADO</td>
        <td colspan="3" >ACCION</td>

    </tr>
<?php  foreach ($result_receptor as  $contenido ): ?>
<tr class="cuerpo_form fila">
  <td ><?php echo $contenido['nombre_a_cl']; ?></td>
  <td ><?php echo $contenido['apellido_a_cl']; ?></td>
  <td><?php echo $contenido['correo_a_cl']; ?></td>
  <td ><?php echo $contenido['consuta_a_cl']; ?></td>
  <td ><?php echo $contenido['date_consulta_a_cl']; ?></td>
  <td><?php echo $contenido['respuesta_a_cl']; ?></td>
  <td ><?php echo $contenido['nombre_rol']; ?></td>
  <td ><?php echo $contenido['date_respuesta_a_cl']; ?></td>
  <td ><?php echo $contenido['estado_consulta']; ?></td>

<td class="fila"><button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_<?php echo $contenido['ID_cunsulta']; ?>" aria-controls="staticBackdrop_nuevo">
  Responder
</button>
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_<?php  echo $contenido['ID_cunsulta']; ?>" aria-labelledby="staticBackdropLabel_nuevo">
<div class="offcanvas-header">
<h5 class="offcanvas-title" id="staticBackdropLabel">RESPONDER PREGUNTA </h5>
<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">
  <form action="mail.php" method="post" > 
<div class="mb-3  
 col-9 col-sm-12 color_title">          <label for="exampleFormControlTextarea1" class="form-label">ESCRIBE TU RESPUESTA..</label>
      <textarea class="form-control ajuste_textarea" id="exampleFormControlTextarea1" name="respuesta" rows="3"  Pattern="[a-zA-Záéíóúü ]+" title="Solo letras" required style="border: gray solid 2px;"></textarea>
    </div>
    <input type="hidden" name="id_consut" value="<?php echo $contenido['ID_cunsulta']; ?>" >
    <div class="ajus_enviar">

    <button type="submit" name="enviar_mail"  class="btn btn-success  " > ENVIAR</button>
  </div>
    </form>
</div>
</div>
</td>
<td><button class="btn btn-danger" onclick="eliminar_mensaje(<?php echo "'" . $contenido['ID_cunsulta'] . "', '" . str_replace("'", "\'", $contenido['correo_a_cl']) . "'"; ?>)">Eliminar</button>
</td>
<td>  
  <?php if ($contenido['bloqueo']== 'no' ){ ?>
    <button class="btn btn-danger ajust_bloquear" onclick="bloquear_emisor(<?php echo "'" . $contenido['correo_a_cl'] . "'"; ?>)">Bloquear</button>

 <?php }else{ ?>
  <a class="btn btn-primary ajuste_desbloq" href="bloque_correo_ms.php?desbloq=<?php echo  $contenido['correo_a_cl'] ;?> "  >Desbloquear</a>

  <?php } ?>

</td>
</tr>

<?php endforeach ?>


</table>

 <?php }else { ?> 
  <table >
    <tr class="xd">
        <td>NOMBRE</td>
        <td>APELLIDO</td>
        <td>CORREO</td>
        <td>CONSULTA </td>   
        <td>FECHA ENVIO </td>
        <td>RESPUESTA</td>
        <td>RECEPTOR</td>
        <td>FECHA RESPUESTA</td>
        <td>ESTADO</td>
        <td colspan="2">ACCION</td>

    </tr>
<?php  foreach ($result_receptor as  $contenido ): ?>
<tr class="cuerpo_form fila">
  <td ><?php echo $contenido['nombre_a_cl']; ?></td>
  <td ><?php echo $contenido['apellido_a_cl']; ?></td>
  <td><?php echo $contenido['correo_a_cl']; ?></td>
  <td ><?php echo $contenido['consuta_a_cl']; ?></td>
  <td ><?php echo $contenido['date_consulta_a_cl']; ?></td>
  <td><?php echo $contenido['respuesta_a_cl']; ?></td>
  <td ><?php echo $contenido['nombre_rol']; ?></td>
  <td ><?php echo $contenido['date_respuesta_a_cl']; ?></td>
  <td ><?php echo $contenido['estado_consulta']; ?></td>

<td class="fila">
</td>
</tr>

<?php endforeach ?>


</table>
 <?php  } ?> <!--fin if rol -->
</div>
</div>
</div>




<script  src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script> 

<script>
  const filas = document.querySelectorAll('.fila');

filas.forEach(fila => {
  fila.addEventListener('click', () => {
    filas.forEach(fila => fila.classList.remove('seleccionada')); // Quitar la clase 'seleccionada' de todas las filas
    fila.classList.add('seleccionada'); // Agregar la clase 'seleccionada' a la fila clicada
  });
});


function eliminar_mensaje(idConsulta , correo) {
    Swal.fire({
        title: '¿ELIMINAR ?',
        text: "¿Seguro que deseas eliminar este mensaje del correo  " + correo + " ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí eliminar',
        cancelButtonText: 'Cancelar'   

    }).then((result) => {
        if (result.isConfirmed)   
 {
           
            window.location.href = "delete_mensaje.php?del=" + idConsulta;
        }
    });
}

function bloquear_emisor(correo){
  Swal.fire({
        title: '¿ BLOQUEAR ?',
        text: "¿Seguro que deseas BLOQUEAR  este  correo  :  " + correo + ", no podra enviar mas mensajes  ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí Bloquear',
        cancelButtonText: 'Cancelar'   

    }).then((result_bloq) => {
        if (result_bloq.isConfirmed)   
 {
           
            window.location.href = "bloque_correo_ms.php?bloq=" +  correo;
        }
    });

}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>