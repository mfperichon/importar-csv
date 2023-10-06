<?php
require_once("conectar.php");
date_default_timezone_set('America/Argentina/Buenos_Aires');

if (isset($_POST['import_data'])) {
    // validate to check uploaded file is a valid csv file
    $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
        if (is_uploaded_file($_FILES['file']['tmp_name'])) {
            $csv_file = fopen($_FILES['file']['tmp_name'], 'r');
            //fgetcsv($csv_file);
            // get data records from csv file
            $flag=0;
            $insertados=0;
            $actualizados=0;
            while (($emp_record = fgetcsv($csv_file)) !== FALSE) {
                // Check if employee already exists with same email
                $sql_query = "SELECT id_farmacia, provincia, localidad, codigo FROM farmacias WHERE codigo = '" . $emp_record[5] . "'";
                $resultset = mysqli_query($conexion, $sql_query) or die("database error:" . mysqli_error($conexion));
                // if employee already exist then update details otherwise insert new record
                if (mysqli_num_rows($resultset)) {
                    $nombre = mb_convert_encoding($emp_record[2], 'UTF-8', 'ISO-8859-1'); // echo SI
                    $nombre = $conexion -> real_escape_string($nombre); 
                    $direccion = mb_convert_encoding($emp_record[3], 'UTF-8', 'ISO-8859-1'); // echo SI
                    $direccion = $conexion -> real_escape_string($direccion);

                    $sql_update = "UPDATE farmacias set provincia='" . $emp_record[0] . "', localidad='" . $emp_record[1] . "', nombre='" . $nombre . "', domicilio='"  . $direccion . "', telefono='" . $emp_record[4] . "' WHERE  codigo = '" . $emp_record[5] . "'";
                    mysqli_query($conexion, $sql_update) or die("database error:" . mysqli_error($conexion));
                    $flag=1;
                    $actualizados++;
                } else {
                    $nombre = mb_convert_encoding($emp_record[2], 'UTF-8', 'ISO-8859-1'); // echo SI
                    $nombre = $conexion -> real_escape_string($nombre); 
                    $direccion = mb_convert_encoding($emp_record[3], 'UTF-8', 'ISO-8859-1'); // echo SI
                    $direccion = $conexion -> real_escape_string($direccion); 
                    /*
                    $nombre1 = mb_convert_encoding($emp_record[2], 'UTF-8', 'ISO-8859-1'); // echo SI
                    echo "nombre1: ". $nombre1 ."<br>";
                    $nombre2 = $conexion -> real_escape_string($emp_record[2]); // echo NO
                    echo "nombre2: ". $nombre2 ."<br>";
                    $nombre3 = utf8_encode($emp_record[2]); // echo SI
                    echo "nombre3: ". $nombre3 ."<br>";
                    die();
                    
                    $nombre = $conexion -> real_escape_string($emp_record[2]);
                    $domicilio = $conexion -> real_escape_string($emp_record[3]);
                    */

                    $mysql_insert = "INSERT INTO farmacias (provincia, localidad, nombre, domicilio, telefono, codigo )VALUES('" . $emp_record[0] . "', '" . $emp_record[1] . "', '" .  $nombre . "', '" . $direccion . "', '" . $emp_record[4] . "', '" . $emp_record[5] . "')";
                    mysqli_query($conexion, $mysql_insert) or die("database error:" . mysqli_error($conexion));
                    $flag=0;
                    $insertados++;
                }
            }
            fclose($csv_file);
            //$import_status = '?import_status=success';            
            //$_SESSION['message'] = "success";

            if ($flag==0) {
                $_SESSION['message'] = "success";
            }
            else{
                $_SESSION['message'] = "update";
            }
            $_SESSION['insertados'] = $insertados;
            $_SESSION['actualizados'] = $actualizados;
        } else {
            //$import_status = '?import_status=error';
            $_SESSION['message'] = 'error';
        }
    } else {
        //$import_status = '?import_status=invalid_file';
        $_SESSION['message'] = 'invalid_file';
    }
}
//header("Location: index.php" . $import_status);
header("Location: index.php");