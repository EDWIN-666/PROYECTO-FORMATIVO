<?php 
include ('conexion_j_i.php');
session_start();
$error_alerta = false;
$ejecucion_insert = false;
$alertamenor=false;
$errorpolitica=false;
$error_cuenta = false ;
$error_cu_activo= false ;
$error_registro= false ;

if (isset($_POST['registrar'])) {
  $nombre_reg = $_POST['nombre_reg'];
  $apellido_reg = $_POST['apellido_reg'];
  $correo_reg = $_POST['correo_reg'];
  $contra_reg = $_POST['contra_reg'];
  $error = politica_contra($contra_reg);
  $fechanacimiento=$_POST['fechanacimiento'];
  if (empty($error)) {
      $contrasena_hash = password_hash($contra_reg, PASSWORD_BCRYPT);

      // Verificar si el correo ya existe
      $revisar_correo = $conexion_jardin->prepare('SELECT * FROM usuarios WHERE correo_u = :correo_reg;');
      $revisar_correo->bindParam(':correo_reg', $correo_reg, PDO::PARAM_STR);
      $revisar_correo->execute();
      $fechaactual= new DateTime();
      $fechatime = new DateTime($fechanacimiento);
      $edad =$fechaactual->diff($fechatime)->y;
      if ($revisar_correo->rowCount() > 0) {
          $error_alerta = "El correo ya está registrado.";

      }elseif($edad < 18 || $edad>90){
        // echo "El usuario debe ser mayor de 18 años";
        $alertamenor = true;
      
      }elseif(!preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $nombre_reg) ||  !preg_match('/^[a-zA-ZáéíóúüÑñ][a-zA-ZáéíóúüÑñ\s]*$/', $apellido_reg) 
      || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/' , $correo_reg )){
        $error_registro=true;
        

      } else {
          // Insertar registro con rol_u como NULL
          $insertar_regisrtro = $conexion_jardin->prepare('INSERT INTO usuarios (nombre_u, apellido_u, correo_u, Contrasena_u, rol_u,fechanacimiento) VALUES (:nombre_reg, :apellido_reg, :correo_reg, :contra_reg, NULL,:fechanacimiento);');
          $insertar_regisrtro->bindParam(':nombre_reg', $nombre_reg, PDO::PARAM_STR);
          $insertar_regisrtro->bindParam(':apellido_reg', $apellido_reg, PDO::PARAM_STR);
          $insertar_regisrtro->bindParam(':correo_reg', $correo_reg, PDO::PARAM_STR);
          $insertar_regisrtro->bindParam(':contra_reg', $contrasena_hash, PDO::PARAM_STR);
          $insertar_regisrtro->bindParam(':fechanacimiento',$fechanacimiento,PDO::PARAM_STR);
          $insertar_regisrtro->execute();
          $ejecucion_insert = true;
      }
  } else {
    $errorpolitica = true;
  }
}

function politica_contra($contraseña) {
  $errores = [];

  if (strlen($contraseña) < 6) {
      $errores[] = "La contraseña debe tener al menos 6 caracteres.";
  }
  if (!preg_match('/[A-Z]/', $contraseña)) {
      $errores[] = "La contraseña debe contener al menos una letra mayúscula.";
  }
  if (!preg_match('/[a-z]/', $contraseña)) {
      $errores[] = "La contraseña debe contener al menos una letra minúscula.";
  }
  if (!preg_match('/[0-9]/', $contraseña)) {
      $errores[] = "La contraseña debe contener al menos un número.";
  }
  return $errores;
}
if (isset($_POST['sesion'])) {
  $correo_sesion = $_POST['correo'];
  $contra_sesion = $_POST['contra'];
  $verificar_cuenta = $conexion_jardin->prepare('SELECT * FROM usuarios WHERE correo_u = :correo_sesion');
  $verificar_cuenta->bindParam(':correo_sesion', $correo_sesion, PDO::PARAM_STR);
  $verificar_cuenta->execute();
  $resultado_ver = $verificar_cuenta->fetch();
  if ($verificar_cuenta->rowCount() > 0) {
      $contrasena_encriptada = $resultado_ver['Contrasena_u'];
      if (password_verify($contra_sesion, $contrasena_encriptada)) {
          if ($resultado_ver['activo'] == 1) {
              $_SESSION['id_usuario'] = $resultado_ver['ID_usuario'];
              header("location:index_usuarios.php");
          } else {
              $error_cu_activo = true;
          }
      } else {
          $error_cuenta = true;
      }
  } else {
      $error_cuenta = true;
  }
}
// olvido contraseña sql  buscar y actulicar//////////////////////////////////////////////////////////////////
$correo_admin = false;
$correo_encontrado = false;
$correo_noencontrado = false;
$errorver = false;

if (isset($_POST['buscar_correo'])) {
    $fechaver = $_POST['nacver'];
    $correo_buscar = $_POST['CORREO_B'];
    $_SESSION['correo_buscar'] = $correo_buscar;

    // Preparar la consulta para verificar si el usuario es admin
    $consulta_buscar = $conexion_jardin->prepare('SELECT * FROM usuarios WHERE correo_u = :correo AND rol_u = 1');
    $consulta_buscar->bindParam(':correo', $correo_buscar, PDO::PARAM_STR);
    $consulta_buscar->execute();

    if ($consulta_buscar->rowCount() > 0) {
        $correo_admin = true;
    } else {
        // Preparar la consulta para verificar si el correo existe
        $exiscorreo = $conexion_jardin->prepare('SELECT * FROM usuarios WHERE correo_u = :correo AND activo = 1');
        $exiscorreo->bindParam(':correo', $correo_buscar, PDO::PARAM_STR);
        $exiscorreo->execute();

        if ($exiscorreo->rowCount() > 0) {
            // Preparar la consulta para verificar si la fecha de nacimiento coincide
            $correover = $conexion_jardin->prepare('SELECT * FROM usuarios WHERE correo_u = :correo AND fechanacimiento = :nacver');
            $correover->bindParam(':correo', $correo_buscar, PDO::PARAM_STR);
            $correover->bindParam(':nacver', $fechaver, PDO::PARAM_STR);
            $correover->execute();

            if ($correover->rowCount() > 0) {
                $correo_encontrado = true;
            } else {
                $errorver = true; // La fecha de nacimiento no coincide
            }
        } else {
            $correo_noencontrado = true; // El correo no existe
        }
    }
}
$update_contra = false;
$errorpoliticacam = false;
if (isset($_POST['contra_form'])) {
    $contra_nueva = $_POST['contra_nueva'];
    $correo_guia_b = $_SESSION['correo_buscar'];
    $error = politica_contra($contra_nueva);
    if (empty($error)){
      $update_buscar = $conexion_jardin->prepare("UPDATE usuarios SET Contrasena_u = :contra WHERE correo_u = :correo");
      $contrasena_encriptada = password_hash($contra_nueva, PASSWORD_BCRYPT);
      $update_buscar->bindParam(':contra', $contrasena_encriptada, PDO::PARAM_STR);
      $update_buscar->bindParam(':correo', $correo_guia_b, PDO::PARAM_STR);
      $update_buscar->execute();
      if ($update_buscar->rowCount() > 0) {
          $update_contra = true;
      }
    }else{
      $errorpoliticacam = true;
    }

}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/vnd.icon" href="img/LogoLibros.png">
        <link href="./bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link  rel="stylesheet"  href="css_J_I/estilo_sesion.css">
    <title> Inicio sesion </title>
    <style>
      
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
        }
    </style>
</head>
<body>
<div class=" container_4">
  <!-- <div class="d-flex align-items-center"> -->
<div class="main   col-xl-6  col-md-7   col-9">  	
		<input type="checkbox" id="chk" aria-hidden="true">
			<div class="login">
				<form class="form "  action="<?php $_SERVER['PHP_SELF'] ?>"  method="post" >
					<label for="chk" aria-hidden="true"  class="inicio texto">INICIAR SESION 🔻</label>
					<input class="input" type="email" name="correo" placeholder="CORREO" required="">
					<input class="input" type="password" name="contra" placeholder="CONTRASEÑA" required="">
					<button  type="submit"   name="sesion"  class="btn btn-success "> ENTRAR</button>
				</form>
<!-- olvido su contraseña -->
<div class="d-flex justify-content-center col-12">
        <button class="btn btn-primary col-lg-4 col-sm-6" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop" aria-controls="staticBackdrop">
    <div class="btn-txt"> ¿Olvido Su Contraseña...? </div>
</button>
<a href="index.php" class="btn btn-warning ms-2 col-lg-4 col-sm-6">Regresar</a>
</div>
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop" aria-labelledby="staticBackdropLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="staticBackdropLabel">BUSCAR CORREO </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">
<div class=" mb-3">
<label for="floatingInput" class="label_bus">Ingrese correo </label>
<input type="email" placeholder="ejemplo@gmail.com" class="form-control mb-2" id="floatingInput" name="CORREO_B" required>

<label for="floatingInput_fecha" class="label_bus">Fecha Nacimiento (la usada al momento de resgistrase) </label>
<input type="date" placeholder="Fecha de Nacimiento" class="form-control" id="floatingInput_fecha" name="nacver" required>
</div>
<div class="d-flex justify-content-center">
<input  type="submit" name="buscar_correo" class="btn btn-success btn-md "  value="BUSCAR">
</div>
</form>
    </div>
  </div>
</div> <!-- fin nuevo ingreso -->
			</div>
<!-- formulario de registro  ----------------------------------------------------------------->
      <div class="register">
				<form class="form row"  action="<?php $_SERVER['PHP_SELF'] ?>"  method="post" >
					<label for="chk" aria-hidden="true" class="textor">REGISTRARSE🔺</label>
					<div class="input-group  ">
  <span class="input-group-text  col-12 col-sm-4 textop" >NOMBRE Y APELLIDO</span>
  <input type="text" aria-label="First name" name="nombre_reg" placeholder="Nombre" class="form-control  col-sm-6  col-sm-4 " Pattern="[a-zA-Záéíóúü ]+" required="">
  <input type="text" aria-label="Last name" name="apellido_reg" placeholder="Apellido" class="form-control  col-sm-6  col-md-4 " Pattern="[a-zA-Záéíóúü ]+"  required="">
</div>
					<!-- <input class="input" type="text" name="nombre_res" placeholder="NOMBRE" required=""> -->
					<div class="input-group  ">
  <span class="input-group-text col-12 col-sm-4  textop" >CORREO Y CONTRASEÑA</span>
  <input type="email" aria-label="First name" name="correo_reg" placeholder="Correo" class="form-control col-sm-6  col-sm-4  "  required="">
  <input type="password" aria-label="Last name" name="contra_reg" placeholder="Contraseña" class="form-control  col-sm-6  col-sm-4 " required="">
</div>
<div class="input-group  ">
  <span class="input-group-text col-12 col-sm-4  textop" >FECHA DE NACIMIENTO </span>
  <input type="date" aria-label="First name" name="fechanacimiento" placeholder="Fecha de Nacimiento" class="form-control col-sm-6  col-sm-4  "  required="">
</div>

<?php
            if (isset($error) && !empty($error)) {
                echo '<div class="alert alert-danger fade show alert-dismissible ajuste_errores" role="alert">   
  <h4 class="alert-heading">¡Error!</h4>
                
                        <ul>';
                foreach ($error as $errores) {
                    echo '<li>' . $errores . '</li>';
                }
                echo '  </ul>
                  <button type="button" class="btn-close ajuste_close_error" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>';
            }
            ?>
					<button  type="submit"  name="registrar" class="btn btn-success">CREAR</button>
				</form>
			</div>
	<!-- </div> -->
  </div>
  <!-- alerta por si ya esta registrado -->
  <?php  if($error_alerta==true){   ?>
				<div class="alert alert-danger  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p>El correo <strong><?php echo $correo_reg; ?></strong> Ya está registrado.</p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Por favor, intente con otro correo electrónico.</p>
</div>

  <!--                           ALERTAS                                          GUIAS        -->
<?php  }elseif($ejecucion_insert==true){ ?>
	<!-- alerta por si la ejecion del insert es  verdadera  -->
	<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="check-circle-fill" viewBox="0 0 16 16">
    <path fill="green" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
	</svg>
<div class="alert alert-success  ajuste_color_alerta  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">  	<svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"  style="width: 20px; height: 20px;" ><use xlink:href="#check-circle-fill"/></svg>¡ REGISTRO EXITOSO !</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Por favor, espere la activación de su cuenta </p>
</div>
<?php  }elseif($errorver==true ){  ?>
<!-- alerta para las sesiones -->
<div class="alert alert-danger  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>La Fecha de nacimiento no coincide con su correo</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Por favor,  verifique su información </p>
</div>
<?php  }elseif($error_cuenta==true ){  ?>
<!-- alerta para las sesiones -->
<div class="alert alert-danger  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>Su  correo  o  contraseña es incorrecta . </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Por favor,  verifique su información </p>
</div>
<?php  }elseif($errorpoliticacam==true ){  ?>
<!-- alerta para las sesiones -->
<div class="alert alert-danger mt-5 fade show alert-dismissible" role="alert">
<h4 class="alert-heading">¡Error al actualizar contraseña!</h4>
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
</div>
<?php  }elseif($errorpolitica==true ){  ?>
<!-- alerta para las sesiones -->
<div class="alert alert-danger  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>La contraseña no cumple con las politicas de seguridad</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Por Favor vuelva a intentarlo</p>
</div>
<?php  }elseif($alertamenor==true ){  ?>
<!-- alerta para las sesiones -->
<div class="alert alert-danger  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>El usuario debe ser mayor de 18 años y menor a 90 años </strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Le recomendamos verificar su información </p>
</div>
<?php }  elseif($error_cu_activo==true){ ?>
  <!-- alerta cuenta de inactiva  -->
  <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="info-fill" viewBox="0 0 16 16">
    <path  fill="blue" d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  </svg>
  <div class="alert alert-info   fade show alert-dismissible" role="alert">
  <h4 class="alert-heading"> <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Info:" style="width: 20px; height: 20px;" ><use xlink:href="#info-fill"/></svg>¡ SU CUENTA ESTA INACTIVA !</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Por favor, comunique se con el administrador o director </p>
</div>
<?php  }elseif($correo_admin==true ){  ?>
<div class="alert alert-danger  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>El rol de su cuenta es Administrador</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">No se puede cambiar la contraseña </p>
</div>
<?php  }elseif($correo_noencontrado==true ){  ?>
<!-- alerta para las sesiones -->
<div class="alert alert-danger  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>El correo no existe o la cuenta esta inactiva</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Por favor,  verifique su información </p>
</div>
 <?php  }elseif($correo_encontrado==true ){  ?>
	<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="check-circle-fill" viewBox="0 0 16 16">
    <path fill="green" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
	</svg>
<div class="alert alert-success  ajuste_color_alerta  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">  	<svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"  style="width: 20px; height: 20px;" ><use xlink:href="#check-circle-fill"/></svg>¡ CORREO ENCONTRADO !</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">
  <form class="form-floating" action="<?php $_SERVER['PHP_SELF'] ?>"  method="post" >
  <input type="password" class="form-control" id="floatingInputValue" placeholder=""  name="contra_nueva">
  <label for="floatingInputValue"  style="font-size: 18px;"> Nueva contraseña</label>
  <div class="d-flex justify-content-center mt-2">
  <input type="submit" name="contra_form" class="btn btn-success" aria-label="Close" value="ACTUALIZAR">
  </div>
</form>  
</p>


</div>
<?php  }elseif($update_contra==true ){  ?>
  	<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="check-circle-fill" viewBox="0 0 16 16">
    <path fill="green" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
	</svg>
<div class="alert alert-success  ajuste_color_alerta  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">  	<svg class="bi flex-shrink-0 me-2" role="img" aria-label="Success:"  style="width: 20px; height: 20px;" ><use xlink:href="#check-circle-fill"/></svg>¡ CAMBIO DE CONTRASEÑA EXITO!</h4>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Su contraseña se ha actualizado correctamente</p>
</div>
  <?php }?>
 <?php  if($error_registro==true ){  ?>
  <div class="alert alert-danger  fade show alert-dismissible" role="alert">
  <h4 class="alert-heading">¡Error!</h4>
  <p> <strong>Se enviaron mal los datos no se adminten espacios en blanco o caracteres espaciales diferentes de los indicados en cada campo</strong> </p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  <hr>
  <p class="mb-0">Le recomendamos verificar su información </p>
</div>
<?php  }  ?>


</div> <!-- container_4 -->   
<script  src="bootstrap-5.3.3-dist/js/bootstrap.min.js"> </script>
</body>
</html>
