<?php
ob_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/vnd.icon" href="img/LogoLibros.png">
    <title>INFORMES</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Chewy&family=Handlee&family=Lobster&display=swap');
       
       
        body {
            font-family: 'Handlee',cursive;
          
        }
      
        .text-center {
            text-align: center;
        }
        .menu{
            margin-top: 70px;
        }
        p{
            line-height: 1.6;
        }
        .texto{
            width: 700px;
            margin: 20px;
        }
        .logo{
            position: absolute;
            width: 80px;
            height: 70px;
            z-index: -1 !important;
            opacity: 0.5 ;
            left: -20px;
            top: -35px;
        }
        /* Incluye aquí más estilos CSS necesarios */
         /* Estilo básico para la tabla */
table {
    
    width: 700px;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 18px;
    text-align: left;
    border-spacing: 2px;    
    margin-top: 10px !important;
    table-layout: fixed; /* Hace que todas las columnas tengan el mismo ancho */
    word-wrap: break-word;
   
}



/* Estilo para el encabezado de la tabla */
table thead tr {
 
    background-color: #f2f2f2;
    color: #333;
    text-align: left;
    font-weight: bold;
   
}

table thead td {
    padding: 12px;
    border-bottom: 2px solid rgb(255, 213, 250)  !important;
    
}


/* Estilo para el cuerpo de la tabla */
table tbody tr {
   
    border-bottom: 3px solid rgb(255, 213, 250) ;
    border-right: 3px solid rgb(255, 213, 250);
    border-left: 3px solid rgb(255, 213, 250);
    background:  #bcc8ff ;
}

table tbody td {
  
    padding: 12px;
    border-right: 3px solid rgb(255, 213, 250) !important ;
}




        .xd{
            background: rgb(255, 213, 250) !important;
        }
       /* .espacioimg{
            margin-top: 10px; */
            /* border: 3px  solid red;
        } */
        .grafico{
            
            width: 690px;
            height: 360px;
            background-size: cover;
            /* position: absolute; */
            
        }

    </style>
</head>
<body>
<?php
include ('conexion_j_i.php');

$validacion =1;
if($_SERVER['REQUEST_METHOD'] === 'POST') {

$name_archivo = $_POST['name_archivo'];

if(!preg_match('/^[a-zA-ZáéíóúüñÑ0-9¿?.,_]+$/',$name_archivo)){
    $validacion = 2;
    header("location:informes.php?v=". $validacion);
    // echo 'eror en mensaje';

  }

  $sql_slecionado = $_POST['sql_informe'];


$sql = $conexion_jardin->prepare($sql_slecionado);


 $chart_image = $_POST['chart_image'];

 $tipo_tabla = $_POST['sql_tipo_informe'];

 $rol_usus = $_POST['sql_rol_usu'] ?? null ;

$sql->execute();
$lista = $sql->fetchAll(PDO::FETCH_ASSOC);
if (!$lista) {
    
    echo "No se encontró un alumno con ese número de identificación.";
}

}else{
    echo 'no recibio nada por post
    ';
}







?>
<!-- <img class="logo" src="http://<?php // echo $_SERVER['HTTP_HOST']?>/mundoacuarela/img/LogoLibros.png" alt=""> cambio para poder usar imagenes  -->

 <img class="logo" src="https://proyectosjevl.com/mundoacuarela/img/LogoLibros.png" alt=""> <!--//cambio para poder usar imagenes  en el server -->

 <h1 class="text-center">JARDIN INFANTIL MUNDO ACUARELA</h1>

 <?php 
 switch ($tipo_tabla) {
    case 'ninos_por_grupos':
        echo "<div class='texto'><h3> TODOS LOS NIÑOS DE UNA  FICHA </h3> Dependiendo de los filtos establecidos anteriormente </div>" ;

        break;

    case 'obcervaciones_ninos':
            echo "<div class='texto'><h3> OBSERVACIONES DEL NIÑO  </h3> Dependiendo de los filtos establecidos anteriormente </div>" ;
            break;
    case 'usuarios_x_rol':
                echo "<div class='texto'><h3> TODOS LOS USUARIOS   </h3> Dependiendo de los filtos establecidos anteriormente </div>" ;
                break;
    default:
        # code...
        break;
 }
 ?>
<div class="texto">
    <table>
<?php 
 switch ($tipo_tabla){
    case 'ninos_por_grupos':  ?>
     <thead>

            <tr class="xd">
                
                <th>NOMBRE</th>
                <th>APELLIDO</th>
                <th>DOCUMENTO</th>
                <th>EDAD</th>
                <th>N° AULA</th>
                <th>FICHA</th>
            </tr>
    </thead>   
    <tbody> 
            <?php foreach ($lista as $fila_n) { ?>
            <tr>
                <td><?php echo $fila_n['nombre_a']; ?></td>
                <td><?php echo $fila_n['apellido_a']; ?></td>
                <td><?php echo $fila_n['doc_identidad']; ?></td>
                <td><?php echo $fila_n['edad']; ?></td>
                <td><?php echo $fila_n['num_aula']; ?></td>
                <td><?php echo $fila_n['ficha']; ?></td>
            </tr>
            <?php }  
            echo ' </tbody>';
            break ;?>
        <?php  case 'obcervaciones_ninos': ?>
            <thead>
            <tr class="xd">
                <th>DESCRIPCIÓN</th>
                <th>FECHA</th>
                <th colspan="2">NIÑO</th>
            </tr>
        </thead>
        <tbody> 
            <?php foreach ($lista as $fila_n) { ?>
            <tr>
                <td><?php echo $fila_n['descripcion']; ?></td>
                <td><?php echo $fila_n['fecha_hora_creacion']; ?></td>
                <td><?php echo $fila_n['nombre_a']; ?></td>
                <td><?php echo $fila_n['apellido_a']; ?></td>
            </tr>
            <?php } 
             echo ' </tbody>';  
            break ;  ?>


    <?php  case 'usuarios_x_rol': ?>

        <thead>

            <tr class="xd">
            <td>NOMBRE</td>
        <td>APELLIDO</td>
        <td>CORRE</td>
         <?php 
         if($rol_usus== '2'){
          echo ' <td>MATERIA</td>
           <td>CELULAR</td>
            <td>AÑOS DE EXPERIENCIA</td>';

         }elseif($rol_usus== '3'){
          echo ' <td>CELUAR</td>
          <td>DIRRECION</td>
           <td>CELULAR EMG</td>';
         }elseif($rol_usus== '4' OR $rol_usus == '5 'OR $rol_usus== '6'){

         }else{
          echo ' <td>ROL</td>
          ';

          
         }

    ?>
            </tr>
    </thead>   
    <tbody> 
            <?php foreach ($lista as $fila_n) { ?>
            <tr>
            <td ><?php echo $fila_n['nombre_u'];  ?></td>
  <td ><?php echo $fila_n['apellido_u'];  ?></td>
  <td ><?php echo $fila_n['correo_u'];  ?></td>
  <?php 
         if($rol_usus== '2'){
          echo "<td >{$fila_n['celular']}  </td>
         <td >{$fila_n['materia']}  </td>
           <td >{$fila_n['years_experiencia']}  </td>";
         }elseif($rol_usus== '3'){
      echo "<td >{$fila_n['celular']}  </td>
           <td >{$fila_n['direccion']}  </td>
           <td >$fila_n[emergencia_cel]  </td>";
         }elseif($rol_usus== '4' OR $rol_usus== '5'OR  $rol_usus== '6'){

         }else{
          echo " <td >{$fila_n['nombre_rol']}  </td>
         " ;
         }

    ?>
            </tr>
            <?php } echo ' </tbody>';  
        break ;  ?>
        <?php } ?>
    </table>
</div>




<div class="espacioimg">
  <h1>Reporte Estadístico</h1>
    <img src="<?php echo $chart_image ; ?>" class="grafico" alt="Gráfica estadística">
    </div>
</body>
</html>




<?php



$html = ob_get_clean();

//  require_once '../mundoacuarela/libreria/dompdf/autoload.inc.php';
 require_once '../JARDINMUNDOACUARELA/libreria/dompdf/autoload.inc.php';



use Dompdf\Dompdf;

$dompdf = new Dompdf();
$options = $dompdf->getOptions();
$options->set('isHtml5ParserEnabled', true);
$options->set(array('isRemoteEnabled' => true));
$dompdf->setOptions($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('letter');
$dompdf->render();
$dompdf->stream("$name_archivo.pdf", array("Attachment" => true));
?>'