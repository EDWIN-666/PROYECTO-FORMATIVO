<?php 
include ('conexion_j_i.php');

$fecha_hora_actual = date('Y-m-d H:i:s');

$validacion = 2 ;

if(isset($_POST['consulta'])){

  $nombre = $_POST['nombre'];
  $apellido = $_POST['apellido'];
  $correo = $_POST['correo'];
  $mensaje = $_POST['mensaje'];

  $sql_bloqueo = $conexion_jardin->prepare("SELECT bloqueo FROM atencion_cliente WHERE correo_a_cl = '$correo' AND  bloqueo = 'si';");
  $sql_bloqueo->execute();
 
   if(!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $nombre) or  !preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $apellido)  ){

   header("location:formulario_atencion.php?v=". $validacion);
    // echo 'error de letras';

  }elseif(!preg_match('/^[a-zA-ZáéíóúüñÑ0-9¿?., ]+$/',$mensaje)){
    header("location:formulario_atencion.php?v=". $validacion);
    // echo 'eror en mensaje';

  }elseif(!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/' , $correo)){
    header("location:formulario_atencion.php?v=". $validacion);
 // echo 'error correo';
  }elseif ($sql_bloqueo->rowCount()>0) {
    echo "<script>
    window.onload = function() {
        alert_bloqueo();
    };
</script> ";

    
  }else{
    $pendiente = 'pendiente';   
    $sql= $conexion_jardin->prepare("INSERT into atencion_cliente(nombre_a_cl,apellido_a_cl,correo_a_cl,consuta_a_cl,date_consulta_a_cl,estado_consulta,bloqueo)
    VALUES(:nomb ,:apel,:corre,:mens,:datet,:status_m,'no' )");

    $sql->bindParam(':nomb', $nombre, PDO::PARAM_STR);
    $sql->bindParam(':apel', $apellido, PDO::PARAM_STR);
    $sql->bindParam(':corre', $correo, PDO::PARAM_STR);
    $sql->bindParam(':mens', $mensaje, PDO::PARAM_STR);
    $sql->bindParam(':datet', $fecha_hora_actual, PDO::PARAM_STR);
    $sql->bindParam(':status_m', $pendiente, PDO::PARAM_STR);


    if($sql->execute()==true){
      $validacion=1;
      header("location:formulario_atencion.php?v=". $validacion);


    }else{
      echo 'no sirvio consulta';
    }
   

  }


}


$result = $_GET['v'] ?? 3 ;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/vnd.icon" href="img/LogoLibros.png">
    <link rel="stylesheet" href="css_J_I/estilo_fomulario_aten.css">
    
    <title>Formulario</title>
</head>
<body>
<!-- mensaje de enviado  enviado -->

<?php  if($result==1){ ?>

<div class="toast-container position-fixed bottom-0 end-0 p-3 ">
  <div id="liveToast" class="toast ajuste_toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header   ">
      <img src="img/LogoLibros.png" class="rounded me-2 logo" alt="...">
      <strong class="me-auto  color_title">Jardin Mundo Acuarela</strong>
 
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body ">
     <span class="color_title" >Formulario Enviado, Espere Su Respuesta</span> 
    </div>
  </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
      const toastLiveExample = document.getElementById('liveToast');
      const toast 
 = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
      toast.show();
    });
  </script>
<?php }elseif($result==2){?>
  <div class="toast-container position-fixed bottom-0 end-0 p-3 " ">
  <div id="liveToast" class="toast ajuste_toast_red" role="alert" aria-live="assertive" aria-atomic="true"  >
    <div class="toast-header   ">
      <img src="img/LogoLibros.png" class="rounded me-2 logo" alt="...">
      <strong class="me-auto  color_title">Jardin Mundo Acuarela</strong>
 
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body ">
     <span class="" style="color: red;">ERROR AL ENVIAR INTENTELO OTRA VEZ.. SIGUIENDO EL TIPO DE DATOS INDICADO

     </span> 
    </div>
  </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
      const toastLiveExample = document.getElementById('liveToast');
      const toast 
 = bootstrap.Toast.getOrCreateInstance(toastLiveExample);
      toast.show();
    });
  </script>
  <?php }?>

<div class="contain_titulo">
<h1 class="titulo">¿Tienes Alguna Pregunta? ¡Escríbenos!</h1>
</div>
<div class=" scroll">

  <form class="row g-3 formulario" action="<?php $_SERVER["PHP_SELF"]; ?>" method="POST">
<div class="responsive_input">
    <div class="mb-3 col-sm-12 col-9 color_title">  <label for="nombre" class="form-label">Nombre</label>
      <input type="text" class="form-control" id="nombre" name="nombre" placeholder=""  Pattern="[a-zA-ZáéíóúüñÑ ]+" title="Solo letras" required>   

    </div>

    <div class="mb-3 col-sm-12 col-9 color_title">  <label for="apellido" class="form-label">Apellido</label>
      <input type="text" class="form-control" id="apellido" name="apellido" placeholder=""  Pattern="[a-zA-Záéíóúü ]+" title="Solo letras" required>   

    </div>

    <div class="mb-3 col-9 col-sm-12 color_title"> 
      <label for="myInput" class="form-label"   >Correo Electronico</label>
      <input type="email" class="form-control" id="myInput" data-bs-toggle="popover" 
      data-bs-trigger="focus" data-bs-placement="top" data-bs-title="Aviso" 
      
      data-bs-content="Si Ya Estas Registrado, Usa Ese Mismo Correo En Este Campo" 
 name="correo" placeholder="ejemplo@gmail.com" required>
 

    </div>

    <div class="mb-3  
 col-9 col-sm-12 color_title">          <label for="exampleFormControlTextarea1" class="form-label">Mensaje O Pregunta</label>
      <textarea class="form-control" id="exampleFormControlTextarea1" name="mensaje" rows="3"  Pattern="[a-zA-ZáéíóúüñÑ?¿,. ]+" title="Solo letras sin tildes" required></textarea>
    </div>
</div>
    <button type="submit" name="consulta" class="btn btn-success" > Enviar</button>
  </form>

</div>
<script>

    document.addEventListener('DOMContentLoaded', function() { 
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

 // Obtén la instancia del popover que quieres manejar
 const popoverTriggerEl = document.querySelector('[data-bs-toggle="popover"]');
    const popoverInstance = bootstrap.Popover.getInstance(popoverTriggerEl);

    popoverTriggerEl.addEventListener('shown.bs.popover', () => {
        setTimeout(() => {
            popoverInstance.hide();  // Aquí usas la instancia del popover, no el elemento HTML
        }, 7000);
    });
});


function alert_bloqueo(){
  Swal.fire({
        title: '! ADVERTENCIA !',
        text: " El correo  ingresado a sido bloquedao, intente con otro.. ",
        icon: 'warning',
      
       

    });
}
  
</script>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Incluye los scripts de Bootstrap -->
<script src="./bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>