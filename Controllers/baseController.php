<?php namespace APP\Controllers;

require_once __DIR__."/../Config/Constantes.php";                     //Inclusión de las constantes y funciones globales
require_once __DIR__."/../Autoload.php";                              //Inclusión de archivo para Autoload de las clases

\APP\Autoload::run();                                                                                 //Arranca Autoload

                                                                                                         //Declaraciones
use APP\Models\Punto;
use APP\Models\Viaje;
use APP\Models\Cliente;
use APP\Models\CodigoPostal;
use APP\Models\Empleado;
use App\Utils\Log;

$_GET   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);                                        //Limpia entrada
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$action = $_POST["action"];
unset($_POST["action"]);

if ($action == "codigosPostales_de_Estado_y_DelegacionMunicipio"){
$miModelo = new CodigoPostal();
$miModelo->set("estado", $_POST["estado"]);
$miModelo->set("municipio", $_POST["municipio"]);
echo json_encode(CodigosPostales::buscarRetornaSoloCodigosPostales($miModelo));
}

elseif ($action == "empleadoLogin"){

    $miModelo = new Empleado();
    $miModelo->set( "userName", $_POST["userName"]);
    echo json_encode(Empleados::login($miModelo, $_POST["password"]));

}
elseif ($action == "empleadosTodos"){
    echo json_encode(Empleados::todosArrelo());
}
elseif ($action == "empleadoAgregar"){

    $miModelo = new Empleado();

    foreach ($_POST as $key => $value) {
        $miModelo->set($key, $value);
    }

    echo json_encode(Empleados::agregar($miModelo));

}
elseif ($action == "empleadoEditar"){

    $miModelo = new Empleado();

    foreach ($_POST as $key => $value) {
        $miModelo->set($key, $value);
    }

    echo json_encode(Empleados::editar($miModelo));

}
elseif ($action == "empleadoContraseña"){

    $miModelo = new Empleado();
    foreach ($_POST as $key => $value) {
        $miModelo->set($key, $value);
    }

    echo json_encode(Empleados::editarContraseña($miModelo));

}

elseif ($action == "clientesTodos"){
    echo json_encode(Clientes::todosArrelo());
}
elseif ($action == "clienteAgregar"){

    $miModelo = new Cliente();

    foreach ($_POST as $key => $value) {
        $miModelo->set($key, $value);
    }

    echo json_encode(Clientes::agregar($miModelo));

}
elseif ($action == "clienteEditar"){

    $miModelo = new Cliente();

    foreach ($_POST as $key => $value) {
        $miModelo->set($key, $value);
    }

    echo json_encode(Clientes::editar($miModelo));

}

elseif ($action == "todosViajesClientesPuntos"){

    $salida = Viajes::viajesClientesArrelo();

    if ($salida["success"]){
        $viajes = array();
        foreach ($salida["todos"] as $viaje){
            $viaje["puntos"] = array();
            $viajes[$viaje["idViaje"]] = $viaje;
        }

        $salida = Puntos::todosArrelo();
        if ($salida["success"]){
            foreach ($salida["todos"] as $punto){
                array_push($viajes[$punto["idViaje"]]["puntos"],$punto);
            }
            $salida["todos"] = array();
            foreach ($viajes as $viaje){
                array_push($salida["todos"],$viaje);
            }
        }

    }

    echo json_encode($salida);
}
elseif ($action == "viajeAgregar"){

    $miViaje = new Viaje();
    $miPunto = new Punto();

    $miViaje->set("idViaje",$_POST["idViaje"]);
    $miViaje->set("destinoEstado",$_POST["destinoEstado"]);
    $miViaje->set("destinoLugar",$_POST["destinoLugar"]);
    $miViaje->set("kilometros",$_POST["kilometros"]);
    $miViaje->set("idCliente",$_POST["idCliente"]);

    $salida = Viajes::agregar($miViaje);

    if ($salida["success"]){

        $miPunto->set("idViaje",$salida["lastId"]);
        foreach ($_POST["puntos"] as $key => $value) {
            $miPunto->set("fecha",$value["fecha"]);
            $miPunto->set("hora",$value["hora"]);
            $miPunto->set("estadoDireccion",$value["estadoDireccion"]);
            $miPunto->set("delegacionMunicipioDireccion",$value["delegacionMunicipioDireccion"]);
            $miPunto->set("codigoPostalDireccion",$value["codigoPostalDireccion"]);
            $miPunto->set("coloniaDireccion",$value["coloniaDireccion"]);
            $miPunto->set("calleNumeroDireccion",$value["calleNumeroDireccion"]);
            $miPunto->set("descripcionDireccion",$value["descripcionDireccion"]);
            $salida = Puntos::agregar($miPunto);
            if (!$salida["success"]){
                echo json_encode($salida);
            }
        }
        echo json_encode($salida);

    }else{
        echo json_encode($salida);
    }



}
elseif ($action == "viajeEditar"){

    $miViaje = new Viaje();
    $miPunto = new Punto();

    $miViaje->set("idViaje",$_POST["idViaje"]);
    $miViaje->set("destinoEstado",$_POST["destinoEstado"]);
    $miViaje->set("destinoLugar",$_POST["destinoLugar"]);
    $miViaje->set("kilometros",$_POST["kilometros"]);
    $miViaje->set("idCliente",$_POST["idCliente"]);

    $salida = Viajes::editar($miViaje);

    if ($salida["success"]){

        $miPunto->set("idViaje",$miViaje->get("idViaje"));

        $salida = Puntos::eliminarPorID($miPunto);

        if(!$salida["success"]){
            echo json_encode($salida);
        }

        foreach ($_POST["puntos"] as $key => $value) {
            $miPunto->set("fecha",$value["fecha"]);
            $miPunto->set("hora",$value["hora"]);
            $miPunto->set("estadoDireccion",$value["estadoDireccion"]);
            $miPunto->set("delegacionMunicipioDireccion",$value["delegacionMunicipioDireccion"]);
            $miPunto->set("codigoPostalDireccion",$value["codigoPostalDireccion"]);
            $miPunto->set("coloniaDireccion",$value["coloniaDireccion"]);
            $miPunto->set("calleNumeroDireccion",$value["calleNumeroDireccion"]);
            $miPunto->set("descripcionDireccion",$value["descripcionDireccion"]);
            $salida = Puntos::agregar($miPunto);
            if (!$salida["success"]){
                echo json_encode($salida);
            }
        }
        echo json_encode($salida);

    }else{
        echo json_encode($salida);
    }

}




else{

    $salida["success"] = false;
    $salida["error"] = "Controlador desconocido.";

    echo json_encode($salida);
}

/*
COMENTARIOS GENERALES:

    Este controlador se encarga de tomar el post, MAPEAR EL POST CON LOS OBJETOS, llamar al controlador especifico y
    formatear respuesta
*/