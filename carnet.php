<?php
ob_start();




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/vnd.icon" href="img/LogoLibros.png">

    <title>carnet</title>
    <style>
        
     
        .espacio_carnet{
            border-radius: 10%;
            width: 300px;
            height: 500px;
            border:  green solid 2px;
           position: absolute;
           left: 50%;
           top:30%;
           transform: translate(-50%,-50%);
            text-align: center;
            background-image: url('https://proyectosjevl.com/mundoacuarela/img/FondoMenu.jpg');
            background-size: cover;
           

        }
        .img-fluid{
            margin-top: 20px;
          width: 200px;
          height: 200px;
          /* border: red solid 3px; */
          border-radius: 10%;
        
          -webkit-box-shadow: 9px 11px 85px 7px rgba(0,0,0,0.75);
-moz-box-shadow: 9px 11px 85px 7px rgba(0,0,0,0.75);
box-shadow: 9px 11px 85px 7px rgba(0,0,0,0.75);
        }
        .logo{
            width: 80px;
            height: 70px;
            opacity: 0.6;
        }

        .card-title {
            font-size: medium;

        }
       
    </style>
</head>
<body>

<?php
include ('conexion_j_i.php');

$id = $_POST['id_carnet']; 
$sql = $conexion_jardin->prepare("SELECT a.nombre_a ,a.apellido_a , a.doc_identidad ,a.foto_alumno , g.ficha , u.ID_usuario, u.nombre_u , acu.celular , acu.emergencia_cel , acu.direccion FROM alumno as a 
INNER JOIN grupos_clases as g on g.ID_g_c = a.ID_grupo_fk 
 INNER JOIN usuarios as u on u.ID_usuario = a.ID_tutor 
 INNER JOIN acudientes as acu on acu.ID_usuario_fk = u.ID_usuario WHERE a.ID_alumno = $id;");

 $sql->execute();

 $alumno_carnet = $sql->fetchAll(PDO::FETCH_ASSOC);


foreach( $alumno_carnet as $carnet ){

?>


<img class="logo" src="https://proyectosjevl.com/mundoacuarela/img/LogoLibros.png" alt=""> <!--//cambio para poder usar imagenes  en el server -->


     <div class="espacio_carnet">
<div class="img">
  <img class="img-fluid" src="https:// <?php   echo $_SERVER['HTTP_HOST'] . '/mundoacuarela/' .  $carnet['foto_alumno'] ?>" alt="">
 
</div>
   <div class="texto">
        <h5 class="card-title ">Jardiin Infantil Mundo Acuarela</h5>
        <p class="card-text "> Nombre :  <?php echo ' '. $carnet['nombre_a'] . '  ' . $carnet['apellido_a'];  ?></p>
        <p class="card-text"> N° Documento T.I. :  <?php echo ' '. $carnet['doc_identidad'] ; ?></p>
        <p class="card-text"> Ficha :  <?php echo ' '. $carnet['ficha'] ; ?></p>
        <p class="card-text"> Celular :  <?php echo ' '. $carnet['celular'] ; ?></p>
        <p class="card-text"> Celular Emergencia  :  <?php echo ' '. $carnet['emergencia_cel'] ; ?></p>
        <p class="card-text"> Direcion  :  <?php echo ' '. $carnet['direccion'] ; ?></p>


        </div>
</div>

<?php }?>

   




</body>
</html>

<?php
$html = ob_get_clean();

 require_once '../mundoacuarela/libreria/dompdf/autoload.inc.php';
//require_once '../JARDINMUNDOACUARELA/libreria/dompdf/autoload.inc.php';



use Dompdf\Dompdf;

$dompdf = new Dompdf();
$options = $dompdf->getOptions();
$options->set(array('isRemoteEnabled' => true));
$dompdf->setOptions($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('letter');
$dompdf->render();
$dompdf->stream("carnet.pdf", array("Attachment" => true));
?>