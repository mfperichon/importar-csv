<?php
require_once("conectar.php");

if (isset($_POST['import_data'])) {
    // validar que es un archivo csv
    $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
            $insertados=0; //Contador para los registros insertados
            $actualizados=0; //Contador para los registros actualizados
            
            while (($comercio_registro = fgetcsv($csv_file)) !== FALSE) {

                // Convertir los acentos y caracteres especiales del nombre y domicilio
                $nombre = mb_convert_encoding($comercio_registro[1], 'UTF-8', 'ISO-8859-1'); 
                $nombre = $conexion -> real_escape_string($nombre); 
                $domicilio = mb_convert_encoding($comercio_registro[2], 'UTF-8', 'ISO-8859-1'); 
                $domicilio = $conexion -> real_escape_string($domicilio);

                // Verificar si ya existe el comercio
                $sql_query = "SELECT id_comercio, codigo FROM comercios WHERE codigo = '" . $comercio_registro[4] . "'";
                $resultset = mysqli_query($conexion, $sql_query) or die("database error:" . mysqli_error($conexion));

                // Si el comercio ya existe actualizo los datos, sino inserto el registro
                if (mysqli_num_rows($resultset)) {

                    $sql_update = "UPDATE comercios set nombre='" . $nombre . "', domicilio='"  . $domicilio . "', telefono='" . $comercio_registro[3] . "' WHERE  codigo = '" . $comercio_registro[4] . "'";
                    mysqli_query($conexion, $sql_update) or die("database error:" . mysqli_error($conexion));
                    $actualizados++;

                } else {

                    $mysql_insert = "INSERT INTO comercios (nombre, domicilio, telefono, codigo )VALUES('" .  $nombre . "', '" . $domicilio . "', '" . $comercio_registro[3] . "', '" . $comercio_registro[4] . "')";
                    mysqli_query($conexion, $mysql_insert) or die("database error:" . mysqli_error($conexion));
                    $insertados++;
                }
            }
            fclose($csv_file);

            // Almaceno los resultados en la variable de SESISON para mostrar los mensajes
            $_SESSION['message'] = "success";
            $_SESSION['insertados'] = $insertados;
            $_SESSION['actualizados'] = $actualizados;
        } else {
            $_SESSION['message'] = 'error';
        }
    } else {
        $_SESSION['message'] = 'invalid_file';
    }
}

header("Location: index.php");