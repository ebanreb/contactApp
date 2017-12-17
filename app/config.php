<?php
/*
*Configuracion de la base de datos
-----------------------------------*/
//database server
define('SERVER', $OPENSHIFT_MYSQL_DB_HOST);
//database port
define('PORT', $OPENSHIFT_MYSQL_DB_PORT);
//database login name
define('USER', "adminQCadZQk");
//database login password
define('PASS', "VsGQn4mk4bI7");
//database name
define('DATABASE', "appcontact");
/*Fin configuracion de la base de datos
-----------------------------------*/
define('DOCUMENT_ROOT',$_SERVER["DOCUMENT_ROOT"]."/");
define('ROOT',"http://".$_SERVER['SERVER_NAME']."/");
?>