<?php
// Desactivar toda notificación de error
//error_reporting(0);
// Mostrar todos los errores
error_reporting(-1);
// Solo errores de ejecucion
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

// le damos un mobre a la sesion.
session_name('ImportarCSV');
// incia sessiones
session_start();

//Configuracion
$conexion = new mysqli('localhost', 'root', '', 'ospapel');

//Conectar a la base de datos
if (mysqli_connect_errno()) {
    // La conexión falló. ¿Que vamos a hacer? 

    // Probemos esto:
    echo "Lo sentimos, este sitio web está experimentando problemas.<br>";

    // Algo que no se debería de hacer en un sitio público, aunque este ejemplo lo mostrará
    // de todas formas, es imprimir información relacionada con errores de MySQL -- se podría registrar
    echo "Error: Fallo al conectarse a MySQL debido a: <br>";
    echo "Num.Error: " . mysqli_connect_errno() . "<br>";
    echo "DescError: " . mysqli_connect_error() . "<br>";
    
    // Podría ser conveniente mostrar algo interesante, aunque nosotros simplemente saldremos
    exit;
}
$conexion->set_charset("utf8");
?>