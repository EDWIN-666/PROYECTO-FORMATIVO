<?php 
include ('conexion_j_i.php');

session_start();


try {
if (isset($_POST['ficha_nombre'])) {

    
    $ficha_nombre = $_POST['ficha_nombre'];
$buscar_nino = $conexion_jardin->prepare("SELECT  a.ID_alumno,a.nombre_a , a.apellido_a FROM alumno AS a  
INNER JOIN grupos_clases as g on g.ID_g_c = a.ID_grupo_fk WHERE g.ficha = '$ficha_nombre';");
$buscar_nino->execute();

    $html = "";
    while ($row = $buscar_nino->fetch(PDO::FETCH_ASSOC)) {
        $html .= "<option value=\"{$row['ID_alumno']}\">{$row['nombre_a']} - {$row['apellido_a']}</option>";
    }

    echo $html;
}
}catch (Exception $e) {
    echo json_encode(array("error" => $e->getMessage()));
}

?>
