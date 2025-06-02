<?php
    function consultaarchivo($texto='',$tipo='RD',$nro=10498,$anio=2020){
        //echo $tipo.' '.$nro.'-'.$anio;
        //$tipo = 'RD';
        //$nro  = 10498;
        //$anio = 2020;
        //ini_set('memory_limit','800M');
		//ini_set('max_excution_time',1800);
		//API URL
		//Contingencia, si la extranet deja de funcionar o firewall
		$url = "http://extranet.ugel01.gob.pe/rd/prueba.php";
		//$url = "http://200.123.19.250:8060/rd/consultaarchivo.php";
		//echo $url;
		
		//inicializamos el objeto CUrl
		$ch = curl_init($url);
		//el json simulamos una petici贸n de un login
		$jsonData = array('texto'=>$texto,'tipo'=>$tipo,'nro'=>$nro,'anio'=>$anio);
		//creamos el json a partir de nuestro arreglo
		$jsonDataEncoded = json_encode($jsonData);
		//Indicamos que nuestra petici贸n sera Post
		curl_setopt($ch, CURLOPT_POST, 1);
		 //para que la peticion no imprima el resultado como un echo comun, y podamos manipularlo
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//Adjuntamos el json a nuestra petici贸n
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		//Agregamos los encabezados del contenido
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		//Ejecutamos la petici贸n
		//echo curl_exec($ch);
		$result = json_decode(curl_exec($ch));
		return $result;
		//print_r($result);
		curl_close($ch);
		//return $result;
    }

    print_r(consultaarchivo());