<?php

$data = [
    'username' => 'tecadmin',
    'password' => '012345678'
];
 
$payload = json_encode($data);
 
$ch = curl_init('https://api.example.com/api/1.0/user/login');
//el resultado no es retornado
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//no ver los encabezados
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
//peticion a traves de POST
curl_setopt($ch, CURLOPT_POST, true);
//se envia los datos en JSON
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//se envia informacion con datos adicionales , longitud del string enviado 
curl_setopt($ch, CURLOPT_HTTPHEADER,
	[ 
	    'Content-Type: application/json',
	    'Content-Length: ' . strlen($payload)
	]
);
//se guarda el resultado de la consulta y se cierra la conexion 
$result = curl_exec($ch);
curl_close($ch);
