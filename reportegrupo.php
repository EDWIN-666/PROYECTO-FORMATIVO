<?php
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/vnd.icon" href="IMG/LogoLibros.png">
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
        /* Incluye aquí más estilos CSS necesarios */
    </style>
</head>
<body>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['identificacion'])) {
    $identificacion = $_POST['identificacion'];

    include ('conexion_j_i.php');

    // Primer consulta para obtener el ID_alumno
    $consultaodid = $conexion_jardin->prepare("SELECT ID_alumno FROM alumno WHERE doc_identidad = 1120123");
    $consultaodid->bindParam(':identificacion', $identificacion, PDO::PARAM_STR);
    $consultaodid->execute();

    // Verificar si la consulta devolvió algún resultado
    if ($consultaodid->rowCount() > 0) {
        // Obtener el ID_alumno
        $resultado = $consultaodid->fetch(PDO::FETCH_ASSOC);
        $id_alumno = $resultado['ID_alumno'];

        $sql = $conexion_jardin->prepare("SELECT grupos_clases.ID_g_c, alumno.ID_grupo_fk, observaciones.*
        FROM grupos_clases 
        LEFT JOIN alumno ON alumno.ID_grupo_f` = grupos_clases.ID_g_c 
        LEFT JOIN observaciones ON observaciones.id_nino_fk = alumno.ID_alumno
        ");
        $sql->bindParam(':id_alumno', $id_alumno, PDO::PARAM_INT);
        $sql->execute();
        $lista = $sql->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $lista = [];
        echo "No se encontró ningún alumno con esa identificación.";
    }
} else {
    $lista = [];
    echo "No se recibió una identificación válida.";
}
?>

<div class="menu">
    <h1 class="text-center">JARDIN INFANTIL MUNDO ACUARELA</h1>
</div>
<div>
    <h2>REPORTES DE ESTUDIO</h2>
</div>
<div class="texto">
    <p style="text-align: justify;">
    <?php foreach($lista as $fila_n): ?>    
    A quien corresponda: <br>
    Se hace constar por medio del presente certificado que el(a) niño(a) <?php echo $fila_n['nombre_a']; ?>, con número de identificación <?php echo $fila_n['doc_identidad']; ?>, se encuentra actualmente matriculado y asistiendo regularmente a clases en el Jardín Infantil Mundo Acuarela. <br>
    </p>
</div>
<div>
    <table>
        <thead>
            <tr>
                <td>N° Observación</td>
                <td>Descripción</td>
                <td>Fecha</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach($lista as $fila_o): ?>
            <tr>
                <td><?php echo $fila_o['id_observacion'] ?></td>
                <td><?php echo $fila_o['descripcion'] ?></td>
                <td><?php echo $fila_o['fecha_hora_creacion'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
</body>
</html>

<?php
$html = ob_get_clean();
require_once '../JARDINMUNDOACUARELA/libreria/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('letter');
$dompdf->render();
$dompdf->stream("reporte.pdf", array("Attachment" => false));
?>
