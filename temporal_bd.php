<?php 
include('conexion_j_i.php');
session_start();

// cambiar base de datos d

$valor = 1;
$sql = $conexion_jardin->prepare("ALTER TABLE grupos_clases DROP FOREIGN KEY grupos_clases_ibfk_1;

");


$sql_g = $conexion_jardin->prepare("ALTER TABLE grupos_clases
ADD 
CONSTRAINT grupos_clases_ibfk_1
FOREIGN KEY (id_profesor_fk)
REFERENCES profesor(ID_tabla_p)
ON  DELETE SET NULL


");
if ($valor ==1 ){
$sql->execute();
$sql_g->execute();

echo 'se ejecuto slq ';
$sql_2 = $conexion_jardin->prepare("UPDATE `atencion_cliente` SET `bloqueo`='no'; ");

}else{
    echo 'fallo';
}
?>