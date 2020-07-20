<?php

header( 'Content-Type: application/json' );

// if ( 
// 	!array_key_exists('HTTP_X_HASH', $_SERVER) || 
// 	!array_key_exists('HTTP_X_TIMESTAMP', $_SERVER) || 
// 	!array_key_exists('HTTP_X_UID', $_SERVER)  
// 	) {
// 		header( 'Status-Code: 403' );
	
// 		echo json_encode(
// 			[
// 				'error' => "No autorizado",
// 			]
// 		);
// 		die;
// 	}
// list($hash, $uid, $timestamp) = [
//     $_SERVER['HTTP_X_HASH'],
//     $_SERVER['HTTP_X_TIMESTAMP'],
//     $_SERVER['HTTP_X_UID']
// ];

// $secret = 'Sh!! No se lo cuentes a nadie!';

// $newHash = sha1($uid.$timestamp.$secret);

// if ( $newHash !== $hash ) {
// 	header( 'Status-Code: 403' );
	
// 		echo json_encode(
// 			[
// 				'error' => "No autorizado. Hash esperado: $newHash, hash recibido: $hash",
// 			]
// 		);
		
// 		die;
// }


if (!array_key_exists('HTTP_X_TOKEN', $_SERVER)) {
    die;
}

$url = 'http://localhost:8001';

$ch = curl_init($url);

curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        "X-Token: {$_SERVER['HTTP_X_TOKEN']}"
    ]
);

curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);

$ret = curl_exec($ch);

if ($ret !== 'true') {
    
    die;
}
// EJEMPLO DE COMO USAR AUTENTICACION BASICA MEDIANTE HTTP (NO RECOMENDADO, POCO SEGURA, LOS DATOS QUEDAN EXPUESTOS)
// $user = array_key_exists('PHP_AUTH_USER', $_SERVER) ? $_SERVER['PHP_AUTH_USER'] : '';
// $pwd = array_key_exists('PHP_AUTH_PW', $_SERVER) ? $_SERVER['PHP_AUTH_PW'] : '';

// if ($user !== 'oscar' || $pwd !== '1234') {
//     die;
// }
//se definen los recursos disponibles
$allowedResourceTypes = [
    'books',
    'authors',
    'genres'  
];

//se valida que el recurso este disponible
$resourceType = $_GET['resource_type'];

if (!in_array( $resourceType, $allowedResourceTypes)) {
    header( 'Status-Code: 400' );
	echo json_encode(
		[
			'error' => "Resource type '$resourceType' is un unkown",
		]
	);
	
	die;
}

//Definir recursos
$books = [
    1 => [
        'title' => 'La melancolia de los feos',
        'id_autor' => 2,
        'id_genero' => 2
    ],
    2 => [
        'title' => 'Satanás',
        'id_autor' => 1,
        'id_genero' => 1
    ],
    3 => [
        'title' => 'Lady Masacre',
        'id_autor' => 2,
        'id_genero' => 2
    ]
];



header('Content-type: application/json');

//Levantamos id del recurso buscado 
$resourceId = array_key_exists('resource_id', $_GET) ? $_GET['resource_id'] : '';
//Generamos respuesta cuando el pedido sea correcto 
switch (strtoupper($_SERVER['REQUEST_METHOD'])) {
    case 'GET':
        if ( "books" !== $resourceType ) {
			header( 'Status-Code: 404' );
			echo json_encode(
				['error' => $resourceType.' not yet implemented :(']
			);

			die;
		}

		if ( !empty( $resourceId ) ) {
			if ( array_key_exists( $resourceId, $books ) ) {
				echo json_encode($books[ $resourceId ]);
			} else {
				header( 'Status-Code: 404' );
				echo json_encode(
					['error' => 'Book '.$resourceId.' not found :(']
				);
			}
		} else {
			echo json_encode($books);
		}

		die;
        break;

    case 'POST':
        
        $json = file_get_contents('php://input');
        $books[] = json_decode($json, true);
        // echo array_keys($books)[count($books) -1];
        echo json_encode ($books);

        break;

    case 'PUT':
        
        if (!empty($resourceId) && array_key_exists($resourceId, $books)) {
            //Tomamos la entrada de texto
            $json = file_get_contents('php://input');
            //Se transforma el JSON recibido a un elemento nuevo
            $books[$resourceId] = json_decode($json, true);
            //Mostramos arreglo modificado
            echo json_encode($books);
        }

        break;

    case 'DELETE':
        
        if (!empty($resourceId) && array_key_exists($resourceId, $books)){
            unset($books[$resourceId]);
        }
        echo json_encode($books);
        break;
}