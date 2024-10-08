<?php
ob_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/vnd.icon" href="img/LogoLibros.png">
    <title>Reportes</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
            margin: 20px;
        }
        .logo{
            width: 80px;
            height: 70px;
        }
        /* Incluye aquí más estilos CSS necesarios */
    </style>
</head>
<body>
<?php
$validacion = 1 ;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['identificacion'])) {
$identificacion = $_POST['identificacion'];

if ($identificacion == null || $identificacion == ' ') {
    $validacion = 2 ; 
header("location:index_usuarios.php?certi_v=" . $validacion);
}
include ('conexion_j_i.php');
$sql = $conexion_jardin->prepare("SELECT * from alumno where doc_identidad = :identificacion");
$sql->bindParam(':identificacion', $identificacion, PDO::PARAM_STR);
$sql->execute();
$lista = $sql->fetchAll(PDO::FETCH_ASSOC);
if (!$lista) {
    $validacion = 2 ; 
header("location:index_usuarios.php?certi_v=" . $validacion);
}
}
?>
<!-- <img class="logo" src="http://<?php // echo $_SERVER['HTTP_HOST']?>/mundoacuarela/img/LogoLibros.png" alt=""> cambio para poder usar imagenes  -->

 <img class="logo" src="https://proyectosjevl.com/mundoacuarela/img/LogoLibros.png" alt=""> <!--//cambio para poder usar imagenes  en el server -->

 <div class="menu">
 <h1 class="text-center">JARDIN INFANTIL MUNDO ACUARELA</h1>

 </div>
 <div class="">
 <h2>CERTIFICADO DE ESTUDIO</h2>
 </div>
 <div class="texto">
 <p style="text-align: justify;">
<?php foreach($lista as $fila_n): ?>    
A quien corresponda: <br>

Se hace constar por medio del presente certificado que el(a) niño(a) <?php echo $fila_n['nombre_a']; ?>, con número de identificación <?php echo $fila_n['doc_identidad']; ?>, se encuentra actualmente matriculado y asistiendo regularmente a clases en el Jardín Infantil Mundo Acuarela. <br>
</p>
 </div>
 <div class="texto">
    <p>
 Detalles del Estudiante: <br>
Nombre Completo: <?php echo $fila_n['nombre_a'] . ' ' . $fila_n['apellido_a']; ?> <br>
Fecha de Nacimiento: <?php echo $fila_n['fecha_nacimiento']; ?> <br>
</p>
 </div>
 <div class="texto">
    <p>
 Nombre del Jardín: Jardín Infantil Mundo Acuarela <br>
Dirección: En algun lugar del mundo <br>
Teléfono: 123 456 789 09 <br>
Correo Electrónico: mundoacuarela@gmail.com<br>
</p>
 </div>
 <div class="texto">
    <p>
 El estudiante ha estado asistiendo a nuestras actividades educativas y recreativas de manera continua <br>
 Este certificado se expide a solicitud del interesado para los fines que estime convenientes. <br>
 En Ibagué-Tolima, a <?php echo date('d/m/Y'); ?>.
 </p>
 </div>
 <div class="texto">
<p>
Firma: <br>
Rosa Elena Martinez <br>
Director/a <br>
Jardín Infantil Mundo Acuarela
</p>
 </div>
 <?php endforeach; ?>
 
</html>

<?php
$html = ob_get_clean();
// echo $html;
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
$dompdf->stream("reporte.pdf", array("Attachment" => true)); // true se descarga al dar el link y no se abre -->
?>
