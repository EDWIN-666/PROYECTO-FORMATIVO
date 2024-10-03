<?php
session_start();
require 'conexion_j_i.php'; 


$id_nino = $_SESSION['id_nino_ajax'] ?? null;

if ($id_nino) {
    $result_buscar_observacion = null;
    $sql_buscar_observacion = $conexion_jardin->prepare("SELECT * FROM observaciones WHERE id_nino_fk = :id_nino ORDER BY fecha_hora_creacion ;");
    $sql_buscar_observacion->bindParam(':id_nino', $id_nino, PDO::PARAM_INT);
    if ($sql_buscar_observacion->execute()) {
        $result_buscar_observacion = $sql_buscar_observacion->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>
    <!DOCTYPE html>
<html  lang="es" >
    <head> 
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/vnd.icon" href="IMG/LogoLibros.png">
    <!-- <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link  rel="stylesheet"  href="css_J_I/estilo_usuarios.css">
    
    <title>JARDIN INFANTIL </title>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Chewy&family=Handlee&family=Lobster&display=swap');
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
/*   
        body{
          background-attachment: fixed;
          overflow-x: hidden;
        }
         table{
          width: 300px;
         }
         .main{
          height: 550px; 
         
  }*/
}
body{
  font-family: 'Handlee',cursive;
}
.xd{
  background: rgb(255, 213, 250);
}
    </style>
</head>

    <div class="tb_scroll ajsute_tb_modal_ob">
        <table>
            <tr class="xd">
                <td>DESCRIPCIÓN</td>
                <td>FECHA</td>
                <td colspan="2">Acción</td>
            </tr>
            <?php foreach ($result_buscar_observacion as $observacion_n) : ?>
                <tr class="cuerpo_form">
                    <td><?php echo htmlspecialchars($observacion_n['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($observacion_n['fecha_hora_creacion']); ?></td>
                    <td>
                        <button class="btn btn-success" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop_<?php echo htmlspecialchars($observacion_n['id_observacion']); ?>" aria-controls="staticBackdrop_nuevo">Actualizar</button>
                        <div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop_<?php echo htmlspecialchars($observacion_n['id_observacion']); ?>" aria-labelledby="staticBackdropLabel_nuevo">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="staticBackdropLabel">CAMBIAR OBSERVACIÓN</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form action="procesos.php" method="post">
                                    <div class="input-group">
                                        <span class="input-group-text">Descripción</span>
                                        <textarea class="form-control" aria-label="With textarea" name="decripcion_obser_update"><?php echo htmlspecialchars($observacion_n['descripcion']); ?></textarea>
                                    </div>
                                    <input type="hidden" name="id_observacion" value="<?php echo $observacion_n['id_observacion']; ?>">
                                    <div class="d-flex justify-content-center mt-3">
                                        <input type="submit" name="update_observa" class="btn btn-success btn-md" value="ACTUALIZAR">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                    <td>
                        <form action="procesos.php" method="post">
                            <input type="hidden" name="id_observacion_dele" value="<?php echo $observacion_n['id_observacion']; ?>">
                            <input type="submit" class="btn btn-danger btn-md" name="eliminar_observacion" value="ELIMINAR">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
} else {
    echo 'No se encontró el ID del niño.';
}
?>

<script src=" https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script  src="bootstrap-5.3.3-dist/js/bootstrap.min.js"></script> 
<script  src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script> 

<script src="js_J_I/index_usuarios.js"></script> 


</body>
</html>
