<?php
ob_start();
include ('conexion_j_i.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/vnd.icon" href="img/LogoLibros.png">
    <link rel="stylesheet" href="CSS/bootstrap.css">

    <title>Reportes observaciones
    </title>
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
        /* Estilo básico para la tabla */
.table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 18px;
    text-align: left;
}

/* Estilo para el encabezado de la tabla */
.table thead tr {
    background-color: #f2f2f2;
    color: #333;
    text-align: left;
    font-weight: bold;
}

.table thead td {
    padding: 12px;
    border-bottom: 2px solid #ddd;
}

/* Estilo para el cuerpo de la tabla */
.table tbody tr {
    border-bottom: 1px solid #ddd;
}

.table tbody td {
    padding: 12px;
}

/* Alternar color de fondo para las filas del cuerpo de la tabla */
.table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Agregar un efecto hover a las filas */
.table tbody tr:hover {
    background-color: #f1f1f1;
}

        /* Incluye aquí más estilos CSS necesarios */
    </style>
</head>
<body>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['observacion'])) {
    $identificacion = $_POST['observacion'];
    $consulta_observaciones=$conexion_jardin->prepare("SELECT ID_alumno, nombre_a, apellido_a from alumno where doc_identidad =:doc_alumno");
    $consulta_observaciones->bindParam(':doc_alumno',$identificacion);
    $consulta_observaciones->execute();
    $resultado_observacion=$consulta_observaciones->fetchAll();
    foreach ($resultado_observacion as $resultado_observaciones){
        $nombre_alumno =$resultado_observaciones['nombre_a'].' '.$resultado_observaciones['apellido_a'];
        if ($consulta_observaciones->rowCount() > 0) {
            $idalumno=$resultado_observaciones['ID_alumno'];
            $mostrar_observaciones=$conexion_jardin->prepare("SELECT descripcion,fecha_hora_creacion FROM observaciones WHERE ID_nino_fk = :idalumno");
            $mostrar_observaciones->bindParam(':idalumno',$idalumno);
            $mostrar_observaciones->execute();
            $lista_observaciones = $mostrar_observaciones->fetchAll();
            $resultados[]=[
                'nombre'=>$nombre_alumno,
                'observaciones'=>$lista_observaciones,
            ];
            // echo "se encontraron alumnos en el registro ".' id alumno '.$idalumno;
        } else {
            echo "No se encontró ningún alumno con esa identificación.";
        }
    }

} else {
    echo "No se recibió una identificación válida.";
    // echo 'documento' . $identificacion;
}
?>

<div class="menu">
    <h1 class="text-center">JARDIN INFANTIL MUNDO ACUARELA</h1>
</div>
<div>
    <h2>REPORTES DE SEGUMIENTO ESTUDIANTIL</h2>

</div>
<div class="texto">
    <p style="text-align: justify;">
    <?php foreach($resultados as $fila_n): ?>    
    A quien corresponda: <br>
    En el presente documento se manifiesta las observaciones sobre el alumno: <?php echo $fila_n['nombre']; ?>, con número de identificación <?php echo $identificacion ?>, referentes a su comportamiento y disiplina dentro del Jardin. <br>
    </p>
</div>
<div>
    <table class="table">
        <thead>
            <tr>
                <td>Descripción</td>
                <td>Fecha</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($fila_n['observaciones'] as $fila_o): ?>
            <tr>
                <td><?php echo $fila_o['descripcion'] ?></td>
                <td><?php echo $fila_o['fecha_hora_creacion'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>
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
 El estudiante puede presentar observaciones positivas o negatovas<br>
 Estas observaciones se expide a solicitud del interesado para los fines que estime convenientes. <br>
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
</body>
</html>

<?php
$html = ob_get_clean();
require_once '../mundoacuarela/libreria/dompdf/autoload.inc.php';


use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('letter');
$dompdf->render();
$dompdf->stream("Observaciones.pdf", array("Attachment" => true));

?>
