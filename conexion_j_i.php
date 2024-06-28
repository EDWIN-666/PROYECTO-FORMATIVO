<?php 
date_default_timezone_set('America/Bogota');

 $server_p="localhost";
$user = "root";
$password = "";
$bd = "jardin_infantil";



try{
    $conexion_jardin= new PDO('mysql:host=localhost;dbname='.$bd,$user,$password);
   
} catch(PDOException $e){
    echo 'Error_1'.$e->getMessage();

}


// $conexion_mysqli = new mysqli($server_p, $user, $password, $bd);


// if ($conexion_pa->connect_error){
//     die("fallo la conexion" . $conexion_parkin->connect_error );

// } else {
//     // echo "conexion correcta" ;

    
// }

?>
