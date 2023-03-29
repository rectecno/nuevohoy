<?php

    $txtID=(isset($_POST['txtID']))?$_POST['txtID']:""; 
    $txtCategoria=(isset($_POST['txtCategoria']))?$_POST['txtCategoria']:"";
    $txtNombre=(isset($_POST['txtNombre']))?$_POST['txtNombre']:"";
    $txtDescripcion=(isset($_POST['txtDescripcion']))?$_POST['txtDescripcion']:"";
    $txtAcciones=(isset($_POST['txtAcciones']))?$_POST['txtAcciones']:"";
    $txtFotografia=(isset($_FILES['txtFotografia']["name"]))?$_FILES['txtFotografia']:"";

    $accion=(isset($_POST['accion']))?$_POST['accion']:""; 

    $error=array();

    $accionAgregar="";
    $accionModificar=$accionEliminar=$accionCancelar="disabled";
    $mostrarModal=false;

    include ("../conexion/conexion.php");

    switch($accion){
        case "btnAgregar":

            if($txtCategoria==""){
                $error['Categoria']="Escribe la categoria";
            }
            if($txtNombre==""){
                $error['Nombre']="Escribe el Nombre";
            }
            if($txtDescripcion==""){
                $error['Descripcion']="Escribe la descripcion";
            }
            if($txtAcciones==""){
                $error['Acciones']="Escribe la accion";
            }

            if(count($error)>0){
                $mostrarModal=true;
                break;
            }

            $sentencia=$pdo->prepare("INSERT INTO articulos(Categoria,Nombre,Descripcion,Acciones,Fotografia)
            VALUES (:Categoria,:Nombre,:Descripcion,:Acciones,:Fotografia)");

            $sentencia->bindParam(':Categoria',$txtCategoria);
            $sentencia->bindParam(':Nombre',$txtNombre);
            $sentencia->bindParam(':Descripcion',$txtDescripcion);
            $sentencia->bindParam(':Acciones',$txtAcciones);

            $Fecha= new DateTime();
            $nombreArchivo=($txtFotografia!="")?$Fecha->getTimestamp()."_".$_FILES["txtFotografia"]["name"]:"imagen.jpg";

            $tmpFotografia= $_FILES["txtFotografia"]["tmp_name"];

            if($tmpFotografia!=""){
                move_uploaded_file($tmpFotografia,"../Imagenes/".$nombreArchivo);
            }

            $sentencia->bindParam(':Fotografia',$nombreArchivo);
            $sentencia->execute();

            header('Location: index.php');

        break;
//Hasta aqui todo correcto
        case "btnModificar":

            $sentencia=$pdo->prepare("UPDATE articulos SET 
            Categoria=:Categoria,
            Nombre=:Nombre,
            Descripcion=:Descripcion,
            Acciones=:Acciones WHERE id=:id");

            $sentencia->bindParam(':Categoria',$txtCategoria);
            $sentencia->bindParam(':Nombre',$txtNombre);
            $sentencia->bindParam(':Descripcion',$txtDescripcion);
            $sentencia->bindParam(':Acciones',$txtAcciones);
            
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();

            $Fecha= new DateTime();
            $nombreArchivo=($txtFotografia!="")?$Fecha->getTimestamp()."_".$_FILES["txtFotografia"]["name"]:"imagen.jpg";

            $tmpFotografia= $_FILES["txtFotografia"]["tmp_name"];

            if($tmpFotografia!=""){
                move_uploaded_file($tmpFotografia,"../Imagenes/".$nombreArchivo);

                $sentencia=$pdo->prepare("SELECT Fotografia FROM articulos WHERE id=:id");
                $sentencia->bindParam(':id',$txtID);
                $sentencia->execute();
                $articulo=$sentencia->fetch(PDO::FETCH_LAZY);
                print_r($articulo);

            if(isset($articulo["Fotografia"])){
                if(file_exists("../Imagenes/".$articulo["Fotografia"])){
                    if($articulo['Fotografia']!="imagen.jpg"){
                        unlink("../Imagenes/".$articulo["Fotografia"]);
                    }
                }
            }

                $sentencia=$pdo->prepare("UPDATE articulos SET 
                Fotografia=:Fotografia WHERE id=:id");
                $sentencia->bindParam(':Fotografia',$nombreArchivo);
                $sentencia->bindParam(':id',$txtID);
                $sentencia->execute();
            }

            header('Location: index.php');

            /*echo $txtID;
            echo "Presionaste btnModificar";*/
        break;
//Hasta aqui todo correcto
        case "btnEliminar":
            //BORRADO DE FOTOGRAFIA
            $sentencia=$pdo->prepare("SELECT Fotografia FROM articulos WHERE id=:id");
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $articulo=$sentencia->fetch(PDO::FETCH_LAZY);
            print_r($articulo);

            if(isset($articulo["Fotografia"])&&($item['Fotografia']!="imagen.jpg")){
                if(file_exists("../Imagenes/".$articulo["Fotografia"])){
                    unlink("../Imagenes/".$articulo["Fotografia"]);
                }
            }
            //BORRADO DE REGISTRO
            $sentencia=$pdo->prepare(" DELETE FROM articulos WHERE id=:id");

            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            header('Location: index.php');

            /*echo $txtID;
            echo "Presionaste btnEliminar";*/
        break;
//Hasta aqui todo correcto
        case "btnCancelar":
            header('Location: index.php');

        break;

        case "Seleccionar":
            $accionAgregar="disabled";
            $accionModificar=$accionEliminar=$accionCancelar="";
            $mostrarModal=true;

            $sentencia=$pdo->prepare("SELECT * FROM articulos WHERE id=:id");
            $sentencia->bindParam(':id',$txtID);
            $sentencia->execute();
            $articulo=$sentencia->fetch(PDO::FETCH_LAZY);

            $txtCategoria=$articulo['Categoria'];
            $txtNombre=$articulo['Nombre'];
            $txtDescripcion=$articulo['Descripcion'];
            $txtAcciones=$articulo['Acciones'];
            $txtFotografia=$articulo['Fotografia'];
        break;
    }
//Esta parte correcto
    $sentencia= $pdo->prepare("SELECT * FROM `articulos` WHERE 1");
    $sentencia->execute();
    $listaArticulos=$sentencia->fetchAll(PDO::FETCH_ASSOC);

    /*print_r($listaArticulos);*/
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <form action="" method="post" enctype="multipart/form-data">

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Articulo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <input type="hidden" required name="txtID" value="<?php echo $txtID; ?>" placeholder="" id="txtID" require="">
                            
                            <div class="form-group col-md-12">

                                
                                

                                <label for="">Selecciona la categoria deseada:</label>
                                <select class="form-control <?php echo (isset($error['Categoria']))?"is-invalid":""; ?>" name="txtCategoria" value="<?php echo $txtCategoria; ?>" placeholder="" id="txtCategoria" require="">
                                    <!-- Opciones de la lista -->
                                    <option value="Asiento adaptable">Asiento adaptable</option>
                                    <option value="Baño y aseo">Baño y aseo</option> 
                                    <option value="Dispositivos de comunicacion">Dispositivos de comunicacion</option>
                                    <option value="Acceso">Acceso</option>
                                    <option value="Silla de ruedas electricas">Silla de ruedas electricas</option>
                                    <option value="Recreacion">Recreacion</option>
                                    <option value="Standers">Standers</option>
                                    <option value="Equipos de terapia">Equipos de terapia</option>
                                    <option value="Ayuda para caminar">Ayuda para caminar</option>
                                    <option value="Silla de ruedas - Carriolas">Silla de ruedas - Carriolas</option>
                                </select>
                                <div class="invalid-feedback">
                                    <?php echo (isset($error['Categoria']))?$error['Categoria']:""; ?>
                                </div>
                            <br>
                            </div>
                            
                            <div class="form-group col-md-12">
                                <label for="">Nombre:</label>
                                <input type="text" class="form-control <?php echo (isset($error['Nombre']))?"is-invalid":""; ?>" name="txtNombre"  value="<?php echo $txtNombre; ?>" placeholder="" id="txtNombre" require="">
                                <div class="invalid-feedback">
                                    <?php echo (isset($error['Nombre']))?$error['Nombre']:""; ?>
                                </div>
                            <br>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="">Descripcion:</label>
                                <input type="text" class="form-control <?php echo (isset($error['Descripcion']))?"is-invalid":""; ?>" name="txtDescripcion" value="<?php echo $txtDescripcion; ?>" placeholder="" id="txtDescripcion" require="">
                                <div class="invalid-feedback">
                                    <?php echo (isset($error['Descripcion']))?$error['Descripcion']:""; ?>
                                </div>
                            <br>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="">Acciones:</label>
                                <input type="text" class="form-control <?php echo (isset($error['Acciones']))?"is-invalid":""; ?>" name="txtAcciones" value="<?php echo $txtAcciones; ?>" placeholder="" id="txtAcciones" require="">
                                <div class="invalid-feedback">
                                    <?php echo (isset($error['Acciones']))?$error['Acciones']:""; ?>
                                </div>
                            <br>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="">Fotografia:</label>
                                <?php if($txtFotografia!="") { ?>
                                    <br/>
                                    <img class="img-thumbnail rounded mx-auto d-block" width="100px" src="../Imagenes/<?php echo $txtFotografia;?>" />
                                    <br/>
                                    <br/>
                                    <?php } ?>
                                <input type="file" class="form-control" accept="image/*" name="txtFotografia" value="<?php echo $txtFotografia; ?>" placeholder="" id="txtFotografia" require="">
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button value="btnAgregar" <?php echo $accionAgregar;?> class="btn btn-success" type="submit" name="accion">Agregar</button>
                        <button value="btnModificar" <?php echo $accionModificar;?> class="btn btn-warning" type="submit" name="accion">Modificar</button>
                        <button value="btnEliminar" onclick="return Confirmar('¿Realmente deseas borrar?');" <?php echo $accionEliminar;?> class="btn btn-danger" type="submit" name="accion">Eliminar</button>
                        <button value="btnCancelar" <?php echo $accionCancelar;?> class="btn btn-primary" type="submit" name="accion">Cancelar</button>
                    </div>
                    </div>
                </div>
            </div>
            <br/>
            <br/>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                Agregar registro +
            </button>
            <br/>
            <br/>
        </form>

<!--ESTA PARTE CORRECTO-->
        <div class="row">
            <table class="table table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Categoria</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>Acciones</th>
                        <th>Fotografia</th>
                        <th>Opciones</th>
                    </tr>
                </thead>

                <?php foreach ($listaArticulos as $articulo){ ?>
                    <tr>
                        <td><?php echo $articulo['Categoria']; ?></td>
                        <td><?php echo $articulo['Nombre']; ?></td>
                        <td><?php echo $articulo['Descripcion']; ?></td>
                        <td><?php echo $articulo['Acciones']; ?></td>
                        <td><img class="img-thumbnail" width="100px" src="../Imagenes/<?php echo $articulo['Fotografia'];?>"/></td>

                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="txtID" value="<?php echo $articulo['ID']; ?>">
                                


                                <input type="submit" value="Seleccionar" class="btn btn-info" name="accion">
                                <button value="btnEliminar" onclick="return Confirmar('¿Realmente deseas borrar?');" type="submit" class="btn btn-danger" name="accion">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?> 
            </table>
        </div>

        <?php if($mostrarModal){?>
            <script>
                $('#exampleModal').modal('show');
            </script>
        <?php } ?> 

        <script>
            function Confirmar(Mensaje){
                return (confirm(Mensaje))?true:false;
            }
        </script>
    </div>

</body>
</html>