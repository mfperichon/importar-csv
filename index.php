<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Importar archivo csv a una Base de Datos usando PHP</title>
    <meta name="description" content="Importar archivo csv a una Base de Datos usando PHP">
    <meta name="keywords" content="ejemplo, codigo, importar, archivos, csv, database, base de datos, PHP">

    <!-- Favicons -->
    <link href="favicon.ico" rel="icon">
    <link href="apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Hojas de estilo CSS -->
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/misestilos.css">

    <!-- Incluir Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <!-- Incluir jQuery JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
</head>
<body>
<div class="content-wrapper">
    <section class="container-md">
        <h3>
            Actualización de Farmacias
            <small class="text-body-secondary">proceso automático para actualización de cartillas</small>
        </h3>
    </section>

    <?php
        require_once('conectar.php'); // Archivo con los datos de conexion a la base de datos

        $mensaje="";
        $insertados = 0;
        $actualizados = 0;
        // Recibo el estado de la operacion en las variables de SESSION
        // Segun el resultado, se genera un alerta diferente (elemento 'alert' de Bootstrap)
        if (isset($_SESSION['message']) && $_SESSION['message'])
        {
            $resultado = $_SESSION['message'];
            switch ($resultado) {
                case 'success':
                    $insertados = $_SESSION['insertados'];
                    $actualizados = $_SESSION['actualizados'];
                    $mensaje="<div class='container-lg'>
                    <div class='alert alert-success alert-dismissible fade show' role='alert' id='myAlert'>
                        El archivo se importó con éxtito en la Base de Datos!<br>Insertados: $insertados registros<br>Actualizados: $actualizados registros
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                </div>";
                    unset($_SESSION['insertados']);
                    unset($_SESSION['actualizados']);
                    break;
                case 'update':
                    $insertados = $_SESSION['insertados'];
                    $actualizados = $_SESSION['actualizados'];
                    $mensaje="<div class='container-lg'>
                    <div class='alert alert-info alert-dismissible fade show' role='alert' id='myAlert'>
                        Se actualizaron los registros del archivo con éxtito!<br>Insertados: $insertados registros<br>Actualizados: $actualizados registros
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                </div>";
                    unset($_SESSION['insertados']);
                    unset($_SESSION['actualizados']);
                    break;
                case 'error':
                    $mensaje="<div class='container-lg'>
                    <div class='alert alert-danger alert-dismissible fade show' role='alert' id='myAlert'>
                        Ocurrió un ERRROR y el archivo no se pudo importar a la base de datos!
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                </div>";
                    break;
                case 'invalid_file':
                    $mensaje="<div class='container-lg'>
                    <div class='alert alert-warning alert-dismissible fade show' role='alert' id='myAlert'>
                        El archivo que intentó importar tiene un formato INVÁLIDO
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>
                </div>";
                    break;
                default:
                    $mensaje="";
            }
            unset($_SESSION['message']);
        }

        // Aca se muestra el mensaje de alerta segun el resultado de la operacion
        echo $mensaje; 
    ?>

    <section class="content">
        <div class="container">            
            <div class="panel panel-default">
                <div class="panel-body">
                    <br>
                    <div class="row">
                        <form action="import.php" method="post" enctype="multipart/form-data" id="import_form">
                            <div class="col-6">
                                <label for="file" class="form-label">Importar archivo csv</label>
                                <input type="file" name="file" class="form-control"/>
                            </div>
                            <div class="col-6 p-3">
                                <input type="submit" class="btn btn-primary" name="import_data" id="import_data" value="IMPORTAR">
                                <span id="cargando" style="visibility: hidden;"><img src='img/loading.gif' width='20px' height='20px'/> Procesando Archivo ...</span>
                            </div>
                        </form>
                    </div>
                    <br>
                    <div class="row">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Provincia</th>
                                    <th>Localidad</th>
                                    <th>Nombre</th>
                                    <th>Domicilio</th>
                                    <th>Telefono</th>
                                    <th>Codigo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM farmacias ORDER BY id_farmacia, provincia, localidad, codigo LIMIT 10";
                                $resultset = mysqli_query($conexion, $sql) or die("database error:" . mysqli_error($conexion));
                                if (mysqli_num_rows($resultset)) {
                                    while ($rows = mysqli_fetch_assoc($resultset)) {
                                ?>
                                    <tr>
                                        <td><?php echo $rows['id_farmacia']; ?></td>
                                        <td><?php echo $rows['provincia']; ?></td>
                                        <td><?php echo $rows['localidad']; ?></td>
                                        <td><?php echo $rows['nombre']; ?></td>
                                        <td><?php echo $rows['domicilio']; ?></td>
                                        <td><?php echo $rows['telefono']; ?></td>
                                        <td><?php echo $rows['codigo']; ?></td>
                                    </tr>
                                <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="7">No se encontraron farmacias.....</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // **** Mostrar el gif de prograso de carga para que se vea alguna actividad
    // **** cuando el archivo tiene muchos registros y demora un poco la subida
    // **** Al hacer click en el boton, se muestra el div que esta oculto
    import_data.onclick = temuestro;
    function temuestro() {
        document.getElementById("cargando").style.visibility = "visible";
    }
</script>

<script type="text/javascript">
    // **** Funcion setTimeOut para ocultar las notificaciones de Bootstrap
    // **** Pasados los 4 segundos, se oculta el elemento alert con id=myAlert
    setTimeout(function () {
        // Cerrar la notificacion 'alert' de Bootstrap
        $('#myAlert').alert('close');
    }, 4000);
</script>

</body>
</html>