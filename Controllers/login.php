<?php

//TODO Usar __DIR__ al principio de los require o cada que se incluye un script dentro de otro
require_once __DIR__."/../Config/Constantes.php";    //Inclusión de las constantes y funciones globales
require_once __DIR__."/../Autoload.php";        //Inclusión de archivo para Autoload de las clases

	\APP\Autoload::run();						//Arranca Autoload

	if ($_SERVER["REQUEST_METHOD"] == "POST") {	//si se llama al index a través de un POST
		$userName = $password = "";
		$userName = \APP\Config\Sanitize::sanitizeInput($_POST["userName"]);	//Se limpia la entrada TODO-Validación
		$password = \APP\Config\Sanitize::sanitizeInput($_POST["password"]);

		$user = new \APP\Models\User();			//Creación de objeto User a través de los datos
		$user->set("userName", $userName);
		$user->set("password", $password);

		if($user->check()){					//Validación de usuario existente
											//Se inicia sesión con id del empleado
			session_start();						//Revisar si hay un sesión activa actualmente
			$_SESSION["idEmpleado"] = $user->get('idEmpleado');
			
			$_SESSION["timelast"] = time(); //Se asigna la hora de la última operación 

            echo json_encode(['success' => true]);

		}else{
            echo json_encode(['success' => false]);
		}

	}

?>