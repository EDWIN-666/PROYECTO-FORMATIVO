<?php 
include('conexion_j_i.php');
session_start();

if (!isset( $_SESSION['id_usuario'])){ 
    header("location:index_sesion.php");
  }
 
$offcavaz_pdf = '';

$sql_nino_imprimir = null ;
  $busqueda_nino_table = 0 ;
//contenido 1

$alerta_filtro1 = null ;
if(isset($_POST['ninos_x_grupos'])){
$sql_nino = "SELECT alumno.nombre_a, alumno.apellido_a, alumno.doc_identidad, alumno.edad, grupos_clases.num_aula , grupos_clases.ficha, grupos_clases.id_profesor_fk fROM alumno 
LEFT JOIN grupos_clases ON alumno.ID_grupo_fk = grupos_clases.ID_g_c 
LEFT JOIN profesor ON grupos_clases.id_profesor_fk = profesor.ID_profesor 
LEFT JOIN nivel_educ ON grupos_clases.nivel = nivel_educ.ID_nivel 
WHERE";

$sql_nino_grafica = "SELECT  COUNT(alumno.nombre_a)as cantidad, grupos_clases.ficha, grupos_clases.id_profesor_fk fROM alumno 
LEFT JOIN grupos_clases ON alumno.ID_grupo_fk = grupos_clases.ID_g_c 
LEFT JOIN profesor ON grupos_clases.id_profesor_fk = profesor.ID_profesor 
LEFT JOIN nivel_educ ON grupos_clases.nivel = nivel_educ.ID_nivel 
WHERE";

$nivel_educ = $_POST['select_contenido_1_nivel'] ?? 'Ninguno';
$aula = $_POST['select_2_contenido_1_aula'] ?? 'Ninguno';
$profe = $_POST['select_3_contenido_1_profesor'] ?? 'Ninguno';

$nivel_educ_if = 0 ;
 $aula_if = 0 ;
  $profesor_if = 0 ;
  $alerta_filtro1 = 1 ;
  if ($nivel_educ == 'Ninguno' AND $aula=='Ninguno' AND $profe == 'Ninguno'){
    $alerta_filtro1=2;
    // header("location:informes.php?f_1=".$alerta_filtro1);
    // return;

  }else{
    
if ($nivel_educ != 'Ninguno') {
  $sql_nino .= ' grupos_clases.nivel =:nivel ' ; 
  $sql_nino_grafica .= ' grupos_clases.nivel =:nivel ' ; 

  $nivel_educ_if = 1 ;
}

if ($aula != 'Ninguno') {
  $sql_nino .= ($nivel_educ_if == 1  ? 'AND' : ' ') .  ' grupos_clases.num_aula = :aula ' ;
  $sql_nino_grafica .= ($nivel_educ_if == 1  ? 'AND' : '') .  ' grupos_clases.num_aula = :aula ' ;

  $aula_if = 1 ;
}

if ($profe != 'Ninguno') {
  $sql_nino .= ($aula_if == 1 || $nivel_educ_if == 1   ? 'AND' : '') .  ' grupos_clases.id_profesor_fk = :profe ' ; 
  $sql_nino_grafica .= ($aula_if == 1 || $nivel_educ_if == 1  ? 'AND' : '') .  ' grupos_clases.id_profesor_fk = :profe ' ; 

  $profesor_if = 1 ;
}
$sql_nino .= " ORDER BY grupos_clases.ficha ;" ; 

$sql_nino_grafica .= " GROUP BY grupos_clases.ficha ;" ;

 $busqueda_nino = $conexion_jardin->prepare($sql_nino);
 $busqueda_grafico  = $conexion_jardin->prepare($sql_nino_grafica);

 $sql_nino_imprimir = $sql_nino;

if ($nivel_educ_if == 1 ) {
  $busqueda_nino->bindParam(':nivel', $nivel_educ, PDO::PARAM_INT);
  $busqueda_grafico->bindParam(':nivel', $nivel_educ, PDO::PARAM_INT);
  $sql_nino_imprimir = str_replace(':nivel', $nivel_educ, $sql_nino_imprimir);


} 
if ($aula_if == 1 ){
  $busqueda_nino->bindParam(':aula', $aula, PDO::PARAM_STR);
  $busqueda_grafico->bindParam(':aula', $aula, PDO::PARAM_STR);
  $sql_nino_imprimir = str_replace(':aula', $aula, $sql_nino_imprimir);


}
if($profesor_if==1){
  $busqueda_nino->bindParam(':profe', $profe, PDO::PARAM_INT);
  $busqueda_grafico->bindParam(':profe', $profe, PDO::PARAM_INT);
  $sql_nino_imprimir = str_replace(':profe', $profe, $sql_nino_imprimir);


}



$busqueda_nino->execute();
$busqueda_grafico->execute();
$result_busqueda_nino = $busqueda_nino->fetchAll();
$result_grafico = $busqueda_grafico->fetchAll(); 
$busqueda_nino_table = 1;

$offcavaz_pdf = 'ninos_por_grupos';




  }
}

//contenido 2 
$alerta_filtro2= null ;
$busqueda_observacion_table = null;
$sql_observacion_imprimir = null ;
if (isset($_POST['ninos_X_observaciones'])) {
$sql_observacion = "SELECT  observaciones.descripcion, observaciones.fecha_hora_creacion , alumno.nombre_a , alumno.apellido_a 
FROM observaciones
 INNER JOIN 
 alumno ON observaciones.id_nino_fk = alumno.ID_alumno 
 WHERE 
 observaciones.fecha_hora_creacion  " ;

  $sql_observacion_grafica = "SELECT 
    DATE_FORMAT(observaciones.fecha_hora_creacion, '%Y-%m') AS mes,
    COUNT(observaciones.ID_observacion) AS total_observaciones
FROM 
    observaciones
 INNER JOIN 
    alumno ON observaciones.id_nino_fk = alumno.ID_alumno
WHERE 
    observaciones.fecha_hora_creacion 
";

$nino_id_observacion =  (int) $_POST['contenido_2_nino'] ?? null;

$fecha_inicio = $_POST['fecha_inicio'] ?? null ;
$fecha_fin = $_POST['fecha_fin'] ?? null ;
$alerta_filtro2 = 1;
 
if( ($nino_id_observacion === NULL || $nino_id_observacion === 'null') ||
($fecha_inicio === null || $fecha_fin === null) ){
$alerta_filtro2 = 2 ;

}else{
 
$sql_observacion .= ' BETWEEN :inicio AND :fin AND  alumno.ID_alumno = :id';

$sql_observacion_grafica .= 'BETWEEN :inicio AND :fin  AND alumno.ID_alumno = :id  GROUP BY 
    mes
ORDER BY 
    mes; ';


 $ejeutar_obsebacion = $conexion_jardin->prepare($sql_observacion);

$fecha_inicio_2_imprimir = " ' ".$fecha_inicio . ' ' . "00:00:00"."'" ;

$fecha_fin_2_imprimir = " ' ".$fecha_fin . ' ' . "23:59:59"."'"  ;

$fecha_inicio_2 = $fecha_inicio . ' ' . '00:00:00' ;

$fecha_fin_2 = $fecha_fin . ' ' . '23:59:59' ;

$sql_observacion_imprimir_2 = "SELECT  observaciones.descripcion, observaciones.fecha_hora_creacion , alumno.nombre_a , alumno.apellido_a 
FROM observaciones
 INNER JOIN 
 alumno ON observaciones.id_nino_fk = alumno.ID_alumno 
 WHERE 
 observaciones.fecha_hora_creacion  BETWEEN  $fecha_inicio_2_imprimir AND $fecha_fin_2_imprimir AND  alumno.ID_alumno = $nino_id_observacion; " ;

// echo $sql_observacion_imprimir_2 ;

 $ejeutar_obsebacion->bindParam(':inicio', $fecha_inicio_2, PDO::PARAM_STR);
 $ejeutar_obsebacion->bindParam(':fin', $fecha_fin_2, PDO::PARAM_STR);
 $ejeutar_obsebacion->bindParam(':id', $nino_id_observacion, PDO::PARAM_INT);


 $ejeutar_obsebacion_grafica =$conexion_jardin->prepare($sql_observacion_grafica);

 $ejeutar_obsebacion_grafica->bindParam(':inicio', $fecha_inicio_2, PDO::PARAM_STR);
 $ejeutar_obsebacion_grafica->bindParam(':fin', $fecha_fin_2 , PDO::PARAM_STR);
 $ejeutar_obsebacion_grafica->bindParam(':id', $nino_id_observacion, PDO::PARAM_INT);


 

 $ejeutar_obsebacion->execute();
 $ejeutar_obsebacion_grafica->execute();
 $result_busqueda_observacion = $ejeutar_obsebacion->fetchAll();
 $result_busqueda_observacion_grafico = $ejeutar_obsebacion_grafica->fetchAll();   
 $busqueda_observacion_table = 1;

 $offcavaz_pdf = 'obcervaciones_ninos';
}


}

$alerta_filtro3 = null ;
$busqueda_usuario_rol = 0;
 if (isset($_POST['usuarios_x_rol'])) {

  $sql_usuarios = "SELECT u.nombre_u , u.apellido_u,u.correo_u,u.activo   ";

 $sql_usuarios_grafica = "SELECT r.nombre_rol, COUNT(u.nombre_u) as total_usuarios  FROM usuarios as u INNER JOIN rol_usuario as r on r.id_rol = u.rol_u
WHERE u.activo = '1' ";

$rol_usuario = $_POST['contenido_3_rol'] ?? 'Ninguno';

switch ($rol_usuario) {
  case '2':
    $sql_usuarios .= " , profesor.celular , profesor.materia , profesor.years_experiencia";

    $sql_usuarios .= " FROM usuarios as u
    iNNER JOIN rol_usuario as r on r.id_rol = u.rol_u INNER JOIN profesor  on profesor.ID_profesor = u.ID_usuario";
    $sql_usuarios .=  " 
     WHERE r.id_rol = '$rol_usuario' and  u.activo = '1' ORDER BY u.nombre_u ;";
     //grafica
     $sql_usuarios_grafica .= "AND  u.rol_u =$rol_usuario  GROUP BY u.rol_u;" ;

   
    break;
  case '3':
    $sql_usuarios .= ", acudientes.celular, acudientes.direccion , acudientes.emergencia_cel ";

    $sql_usuarios .= " FROM usuarios as u
    iNNER JOIN rol_usuario as r on r.id_rol = u.rol_u ";
    $sql_usuarios .=  " LEFT JOIN acudientes ON u.ID_usuario = acudientes.ID_usuario_fk
     WHERE r.id_rol = '$rol_usuario' and  u.activo = '1' ORDER BY u.nombre_u;";

     $sql_usuarios_grafica .= "AND  u.rol_u =$rol_usuario GROUP BY u.rol_u;" ;

  
    break;
  case '4':
  case '5':
  case '6':
    $sql_usuarios .= " FROM usuarios as u
    iNNER JOIN rol_usuario as r on r.id_rol = u.rol_u ";
    $sql_usuarios .=  " 
    WHERE r.id_rol = '$rol_usuario' and  u.activo = '1'   ORDER BY u.nombre_u ;";
    $sql_usuarios_grafica .= "AND  u.rol_u =$rol_usuario GROUP BY u.rol_u;" ;
     
   
    break;
        
  default:
  $sql_usuarios .= ", r.nombre_rol  FROM usuarios as u
    iNNER JOIN rol_usuario as r on r.id_rol = u.rol_u ";
    $sql_usuarios .=  " 
    WHERE  u.activo = '1'   ORDER BY u.nombre_u ;";

    $sql_usuarios_grafica .= " GROUP BY u.rol_u;" ;

   
    
    break;
}
$consulta_usuarios = $conexion_jardin->prepare($sql_usuarios);
$consulta_usuarios_grafica = $conexion_jardin->prepare($sql_usuarios_grafica);

$consulta_usuarios->execute();
$consulta_usuarios_grafica->execute();

$result_usuario = $consulta_usuarios->fetchAll();
$result_usuario_grafico = $consulta_usuarios_grafica->fetchAll();

$offcavaz_pdf='usuarios_x_rol';
$alerta_filtro3 = 1 ;

$busqueda_usuario_rol = 1 ;
// echo $sql_usuarios;
 }


$alerta_name_archivo = $_GET['v'] ?? 3 ;

if($alerta_name_archivo== 2 and $_SERVER['REQUEST_METHOD']=== 'POST'){
$alerta_name_archivo = 1 ;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/vnd.icon" href="../img/LogoLibros.png">
    <link rel="stylesheet" href="css_J_I/estilo_informes.css">
    
    <title>Informes</title>
</head>
<body>
  
<style> 
body{
    background: url(img/Diseño\ sin\ título.png);
}
</style>


 

 <h1><a href="index_usuarios.php"><i class="bi bi-arrow-90deg-left"  ></i></a> INFORMES</h1>
 
<div class="grupo_opciones">

<div class="form-check">
  <input class="form-check-input"   type="radio" name="opciones" id="flexRadioDefault1" value="contenido1">
  <label class="form-check-label" for="flexRadioDefault1">
  Niños por grupos
  </label>
</div>
<div class="form-check">
  <input class="form-check-input"   type="radio" name="opciones" id="flexRadioDefault2"value="contenido2">
  <label class="form-check-label" for="flexRadioDefault2">
  Observaciones por Alumno
  </label>
</div>
<div class="form-check">
  <input class="form-check-input"   type="radio" name="opciones" id="flexRadioDefault3" value="contenido3">
  <label class="form-check-label" for="flexRadioDefault3">
  Informacion De Usuarios
  </label>
</div>
</div>  
 <!-- filtor y tablas pARA los informes  -->
<div class="filtros">
<div id="contenido1" class="contenido">
<!-- grupos -->
 <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
    <div class="select-container">
      <div class="form-floating">
        <select class="form-select" id="floatingSelect" aria-label="Floating label select example" name="select_contenido_1_nivel">  
        <option selected  >Ninguno</option>
        <?php $select_contenido_1 = $conexion_jardin->prepare("SELECT  *  FROM  nivel_educ"); 
        $select_contenido_1->execute(); 

        while ( $nivel_educ = $select_contenido_1->fetch(PDO::FETCH_ASSOC)) {
          echo  "<option value=\"{$nivel_educ['ID_nivel']}\"> {$nivel_educ['nombre_nivel']} </option>";
        }
        ?>
          
        </select>
        <label for="floatingSelect">Nivel Educativo</label>
      </div>

      <div class="form-floating">
        <select class="form-select" id="floatingSelect_ficha" aria-label="Floating label select example" name="select_2_contenido_1_aula">
        <option selected  >Ninguno</option>
        <?php $select_2_contenido_1 = $conexion_jardin->prepare("SELECT DISTINCT num_aula FROM  grupos_clases ORDER BY num_aula
"); 
        $select_2_contenido_1->execute(); 

        while ( $aula = $select_2_contenido_1->fetch(PDO::FETCH_ASSOC)) {
          
          echo  "<option value=\"{$aula['num_aula']}\"> {$aula['num_aula']} </option>";
        }
        ?>
        </select>
        <label for="floatingSelect_ficha" >Numero De Aula</label>
      </div>

      <div class="form-floating">
        <select class="form-select" id="profesor" aria-label="Floating label select example" name="select_3_contenido_1_profesor">
        <option selected  >Ninguno</option>
        <?php $select_3_contenido_1 = $conexion_jardin->prepare("SELECT p.ID_tabla_p , u.nombre_u , p.materia FROM  profesor as p 
        INNER JOIN  usuarios as u  on u.ID_usuario = p.ID_profesor ;
"); 
        $select_3_contenido_1->execute(); 

        while ( $profe = $select_3_contenido_1->fetch(PDO::FETCH_ASSOC)) {
        
          echo  "<option value=\"{$profe['ID_tabla_p']}\"> {$profe['nombre_u']} , {$profe['materia']} </option>";
        }
        ?>
        </select>
        <label for="profesor">Profesor</label>
      </div>
    </div>
<div class="btn_evniar_form">
    <input type="submit" class="btn btn-primary" name="ninos_x_grupos" value="Generar">
  </div>
  </form>
    </div>  <!--fin contenido uno  -->

    <div id="contenido2" class="contenido">
<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
<div class="select-container">

<div class="form-floating">
        <select class="form-select" id="floatingSelect_ficha_2" aria-label="Floating label select example" name="contenido_2_ficha" required="">
        <option selected  >Ninguno</option>
        <?php $select_1_contenido_2 = $conexion_jardin->prepare("SELECT DISTINCT ficha FROM  grupos_clases
"); 
        $select_1_contenido_2->execute(); 

        while ( $aula_2 = $select_1_contenido_2->fetch(PDO::FETCH_ASSOC)) {
          
          echo  "<option value=\"{$aula_2['ficha']}\"> {$aula_2['ficha']} </option>";
        }
        ?>
        </select>
        <label for="floatingSelect_ficha_2" >Nombre de la ficha </label>
      </div>

      
<div class="form-floating">
        <select class="form-select" id="floatingSelect_nino" aria-label="Floating label select example" name="contenido_2_nino" required="">
       
        </select>
        <label for="floatingSelect_nino" >Nombre Del niño</label>
      </div>
    
    <div class="form-floating mb-3">
<input  type="date" class="form-control" id="floatingfecha_f"   placeholder=""  name="fecha_inicio"   required="">
<label for="floatingfecha_f">fECHA DE INICIO</label>
</div>
<div class="form-floating mb-3">
<input  type="date" class="form-control" id="floatingfecha_f_2"   placeholder=""  name="fecha_fin"    max="<?php echo  date( 'Y-m-d'); ?>" required="">
<label for="floatingfecha_f_2">fECHA DE FIN</label>
</div>
</div>

<div class="btn_evniar_form">
    <input type="submit" class="btn btn-primary" name="ninos_X_observaciones" value="Generar">
  </div>
</form>
 </div><!-- fin contenido 2 -->
 

    <div id="contenido3" class="contenido">
    <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
    <div class="select-container">
    <div class="form-floating">
        <select class="form-select" id="floatingSelect_ficha_2" aria-label="Floating label select example" name="contenido_3_rol" required="">
        <option selected  >Ninguno</option>
        <?php $select_1_contenido_3 = $conexion_jardin->prepare("SELECT *  FROM  rol_usuario WHERE id_rol > 1 ;
 "); 
        $select_1_contenido_3->execute(); 

        while ( $rol_3 = $select_1_contenido_3->fetch(PDO::FETCH_ASSOC)) {
          
          echo  "<option value=\"{$rol_3['id_rol']}\"> {$rol_3['nombre_rol']} </option>";
        }
        ?>
        </select>
        <label for="floatingSelect_ficha_2" >ROl DEL USUARIO </label>
      </div>
     
    <input type="submit" class="btn btn-primary" name="usuarios_x_rol" value="Generar">
  
    </div>
    </form>
 </div>     <!--fin  contenido 3 -->

 </div>  <!-- in filtros -->
<div class="container_index mt-3">
  
<div class="main ">
<?php if($alerta_filtro1==2 || $alerta_filtro2 == 2 || $alerta_name_archivo==2 ){?>
          <div class="col-4 mx-auto">
<div class="alert alert-danger  fade show alert-dismissible" style="position: absolute; z-index: 1086;" role="alert">
  <h4 class="alert-heading">ERROR AL  ENVIAR LOS DATOS </h4>
  <p> <strong> <?php if($alerta_name_archivo==2){echo 'TIENE QUE ELEJIR UN NOMBRE PARA EL ARCHIVO PDF SIN ESPACIOS';}else{echo 'Tiene que elejir una opcion';} ?></p>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"  style=" z-index: 1086;"></button>
  <hr>
  <p class="mb-0"> Vuelva A Intentarlo 🤙 </p>
</div>
</div>

<?php } ?>
  <div class=" tb_scroll scrol_usuarios">



<!-- opcines de informes  -->
 

<?php if ($busqueda_nino_table == 1 ){ ?>  
<table >
    <tr class="xd">
        <td>NOMBRE</td>
        <td>APELLIDO</td>
        <td>DOCUMENTO</td>   
        <td>EDAD</td>
        <td>N° AULA</td>
        <td>FICHA</td>
  


    </tr>
<?php foreach ($result_busqueda_nino as $nino_table ): ?>
<tr class="cuerpo_form fila">
  <td ><?php echo $nino_table['nombre_a'];  ?></td>
  <td ><?php echo $nino_table['apellido_a'];  ?></td>
  <td><?php echo $nino_table['doc_identidad'];  ?></td>
  <td ><?php echo $nino_table['edad']; ?></td>
  <td ><?php echo $nino_table['num_aula']; ?></td>
  <td><?php echo $nino_table['ficha'];  ?></td>
  



</tr>
<?php endforeach ?>
</table>
<div class="espacio_img">
 <div id="piechart_3d" class="grafico"></div>
 </div>
 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 <script>
   google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        
        var data = google.visualization.arrayToDataTable([
          ['FICHA', 'NIÑOS'],
          <?php 
          foreach ($result_grafico as $datos_grafico_nino) {
            echo "['" . $datos_grafico_nino['ficha'] . "', " . $datos_grafico_nino['cantidad'] . "],";
          }
          ?>
        ]);

        var options = {
          title: 'CANTIDAD DE NIÑOS POR FICHA (GRUPOS)',
          is3D: true,
          pieSliceText: 'value', // Muestra el número en el gráfico
            slices: {}, // Personalización de los segmentos si lo deseas
            tooltip: {
                showColorCode: true // Muestra el código de color en los tooltips
            },
            legend: {
                position: 'right', // Leyenda a la derecha
                textStyle: {
                    fontSize: 14 // Ajusta el tamaño de la leyenda
                }
            },
            pieSliceTextStyle: {
                fontSize: 16 // Ajusta el tamaño del texto en las porciones
            },
            chartArea: {
                width: '80%', 
                height: '80%'  // Ajusta el área del gráfico
            }

        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
        
        var chartImage = chart.getImageURI();

    // Inserta la imagen en un elemento HTML (por ejemplo, en un div oculto)
    document.getElementById('chart_image').value = chartImage;
      }


</script> 

<?php }elseif ($busqueda_observacion_table == 1 ){ ?>


<table >
    <tr class="xd">
        <td class="">DESCRIPCION</td>
        <td class="">FECHA</td>
     
       
        <td colspan="2" class="" > NIÑO</td>

    </tr>
<?php foreach ($result_busqueda_observacion as $observ_table ): ?>
<tr class="cuerpo_form fila">
  <td ><?php echo $observ_table['descripcion'];  ?></td>
  <td ><?php echo $observ_table['fecha_hora_creacion'];  ?></td>
  <td ><?php echo $observ_table['nombre_a'];  ?></td>
  <td ><?php echo $observ_table['apellido_a'];  ?></td>

</tr>
<?php endforeach ?>
</table>
<div class="espacio_img_observacion">
 <div id="observaciones_g" class="grafico"></div>
</div>
 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 <script>
   google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        
        var data = google.visualization.arrayToDataTable([
          ['MES', 'CANTIDAD'],
          <?php 
          foreach ($result_busqueda_observacion_grafico as $datos_grafico_observ) {
            echo "['" . $datos_grafico_observ['mes'] . "', " . $datos_grafico_observ['total_observaciones'] . "],";
          }
          ?>
        ]);

        var options = {
          title: 'CANTIDAD DE OBSERVACION  POR MES  ',
          is3D: true,
          pieSliceText: 'value', // Muestra el número en el gráfico
            slices: {}, // Personalización de los segmentos si lo deseas
            tooltip: {
                showColorCode: true // Muestra el código de color en los tooltips
            },
            legend: {
                position: 'right', // Leyenda a la derecha
                textStyle: {
                    fontSize: 14 // Ajusta el tamaño de la leyenda
                }
            },
            pieSliceTextStyle: {
                fontSize: 16 // Ajusta el tamaño del texto en las porciones
            },
            chartArea: {
                width: '80%', 
                height: '80%'  // Ajusta el área del gráfico
            }

        };

        var chart = new google.visualization.PieChart(document.getElementById('observaciones_g'));
        chart.draw(data, options);

        var chartImage = chart.getImageURI();

// Inserta la imagen en un elemento HTML (por ejemplo, en un div oculto)
document.getElementById('chart_image').value = chartImage;
      }


</script> 

<?php }elseif ($busqueda_usuario_rol == 1 ){ 
  ?>
  <table >
   
    <tr class="xd">
        <td class="tt">NOMBRE</td>
        <td class="tt">APELLIDO</td>
        <td>CORRE</td>
         <?php 
         if($rol_usuario== '2'){
          echo ' <td>MATERIA</td>
           <td>CELULAR</td>
            <td>AÑOS DE EXPERIENCIA</td>';

         }elseif($rol_usuario== '3'){
          echo ' <td>CELUAR</td>
          <td>DIRRECION</td>
           <td>CELULAR EMG</td>';
         }elseif($rol_usuario== '4' OR $rol_usuario== '5'OR  $rol_usuario== '6'){

         }else{
          echo ' <td>ROL</td>
          ';
         }

    ?>
       
  

    </tr>
<?php foreach ($result_usuario as $user_table ): ?>
<tr class="cuerpo_form fila">
  <td ><?php echo $user_table['nombre_u'];  ?></td>
  <td ><?php echo $user_table['apellido_u'];  ?></td>
  <td ><?php echo $user_table['correo_u'];  ?></td>
  <?php 
         if($rol_usuario== '2'){
          echo "<td >{$user_table['celular']}  </td>
         <td >{$user_table['materia']}  </td>
           <td >{$user_table['years_experiencia']}  </td>";
         }elseif($rol_usuario== '3'){
      echo "<td >{$user_table['celular']}  </td>
           <td >{$user_table['direccion']}  </td>
           <td >$user_table[emergencia_cel]  </td>";
         }elseif($rol_usuario== '4' OR $rol_usuario== '5'OR  $rol_usuario== '6'){

         }else{
          echo " <td >{$user_table['nombre_rol']}  </td>
         " ;
         }

    ?>


</tr>
<?php endforeach ?>
</table>
<div class="espacio_img_usuario">
 <div id="usuarios" class="grafico ajuste_user"></div>
</div>
 <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
 <script>
   google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        
        var data = google.visualization.arrayToDataTable([
          ['ROL', 'CANTIDAD'],
          <?php 
          foreach ($result_usuario_grafico as $datos_grafico_user) {
            echo "['" . ($datos_grafico_user['nombre_rol']== 'Administrador' ? 'Admin' : $datos_grafico_user['nombre_rol']). "', " . $datos_grafico_user['total_usuarios']    . "],";
          }
          ?>
        ]);

        var options = {
          title: 'CANTIDAD DE USUARIOS POR ROL  ',
          is3D: true,
          pieSliceText: 'value', // Muestra el número en el gráfico
            slices: {}, // Personalización de los segmentos si lo deseas
            tooltip: {
                showColorCode: true // Muestra el código de color en los tooltips
            },
            legend: {
                position: 'right', // Leyenda a la derecha
                textStyle: {
                    fontSize: 14 // Ajusta el tamaño de la leyenda
                }
            },
            pieSliceTextStyle: {
                fontSize: 16 // Ajusta el tamaño del texto en las porciones
            },
            chartArea: {
                width: '80%', 
                height: '80%'  // Ajusta el área del gráfico
            }

        };

        var chart = new google.visualization.PieChart(document.getElementById('usuarios'));
        chart.draw(data, options);

        var chartImage = chart.getImageURI();

// Inserta la imagen en un elemento HTML (por ejemplo, en un div oculto)
document.getElementById('chart_image').value = chartImage;
      }


</script>
<?php }?>



      <script type="text/javascript">

     
  
        // Seleccionamos todos los radios y los contenidos
        document.addEventListener('DOMContentLoaded', function(){

        const radios = document.querySelectorAll('input[name="opciones"]');
        const contenidos = document.querySelectorAll('.contenido');

        radios.forEach((radio) => {
            radio.addEventListener('change', (e) => {
                // Ocultamos todos los contenidos
                contenidos.forEach((contenido) => {
                    contenido.style.display = 'none';
                });

                // Mostramos el contenido correspondiente al radio seleccionado
                const seleccionado = e.target.value;
                document.getElementById(seleccionado).style.display = 'block';
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function(){

    $(document).ready(function (){
$("#floatingSelect_ficha_2").change(function(){
const ficha = $(this).val();
console.log("Valor seleccionado de ficha:", ficha); 
$.ajax({
  type: 'POST', 
  url : "obtener_ninos.php" ,
  data: {ficha_nombre: ficha},
  success:function(data){
    console.log('esto carga dato ',data);  // Esto imprimirá 
    $("#floatingSelect_nino").html(data);
  },

      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Error fetching ficha  esto:", textStatus, errorThrown);}
});
});

});
});

    </script>

<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && $alerta_filtro1 ==1 || $alerta_filtro2 == 1 || $alerta_name_archivo == 1 || $alerta_filtro3==1 ) {
//  echo 'filtro 1 -' . $alerta_filtro1 . '</br>' .'filtro 2 - ' . $alerta_filtro2 . '</br>' . 'filtro 3 nombre pdf -' . $alerta_name_archivo ;
  ?>
 <!-- metodos para pdf  -->

 <button class="btn btn-success ajuste_btn_canvas " type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_PDF_INF" aria-controls="staticBackdrop">
Descargar
</button>
<!-- actualizar datos usurio -->
<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_PDF_INF" aria-labelledby="staticBackdropLabel">
<div class="offcanvas-header">

<h5 class="offcanvas-title" id="staticBackdropLabel">CONFIGURAR PDF</h5>
<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">

<form id="chartForm" action="pdf_informes.php" method="post">

  <?php     switch ($offcavaz_pdf) {
    case 'ninos_por_grupos':
      echo" <textarea  class='none'  name='sql_informe' >$sql_nino_imprimir</textarea>
       <input type='hidden'  name='sql_tipo_informe'  value='$offcavaz_pdf'>"  ;

      break;
    case 'obcervaciones_ninos' :
      echo  "<textarea  class='none'  name='sql_informe' >$sql_observacion_imprimir_2 </textarea>
      <input type='hidden'  name='sql_tipo_informe' value='$offcavaz_pdf' >" ;
      break;
      case 'usuarios_x_rol':
        echo  "<textarea  class='none'  name='sql_informe' >$sql_usuarios</textarea>
        <input type='hidden'  name='sql_tipo_informe' value='$offcavaz_pdf' >
        <input type='hidden'  name='sql_rol_usu' value='$rol_usuario' >" ;

        break;
    default:
    
      break;
  }?>
 <div class="col-md-12  ">
 
 <label for="inputNombre" class="form-label"  >Nombre que va a tener el archivo</label>
 <input type="text" class="form-control" id="inputNombre" name="name_archivo"   Pattern="[a-zA-Záéíóúü0-9¿?.,_ ]+" title="SIN ESPACIOS"  required="">
</div>
     <input type="hidden" id="chart_image" name="chart_image">
     <div class="col-lg-4  col-sm-4 mx-auto mt-3">
    <button type="submit" class="btn btn-success">Generar PDF</button>
     </div>
</form>
</div>
 </div>  
 
 <?php  } ?>
 </div><!--fin contenido -->
</div>
</div>



<script src=" https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script  src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>