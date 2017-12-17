<?php
   session_start();
   include_once 'app/config.php';
   include_once 'app/class/controlAcceso.php';
   
   $uri = "";
    if(isset($_GET['seccion'])){
       $acceso = new controlAcceso();
       $acceso -> validaAcceso($_GET['seccion']);
       $uri = "app/view/".$_GET['seccion'].'.php';
    }else{
       if(isset($_SESSION['contactUser'])){
          $uri = ($_SESSION['contactUser']['user']['verificado']==1) ? "app/view/contact.php" : "app/view/verificacion.php";
       }else{
          $uri = "app/view/inicio.php";
       }
   }

?>
<!doctype html>
<html lang="es">
<head>
  <!--<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">-->
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="UTF-8">
	<meta http-equiv='cache-control' content='no-cache'>
	<meta http-equiv='expires' content='0'>
	<meta http-equiv='pragma' content='no-cache'>
	<title>Contact!</title>

  <base href="<?= ROOT; ?>" />

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="js/jquery.nicescroll.min.js"></script>
  <!-- Latest compiled and minified JavaScript -->
  <script type="text/javascript" src="js/timezone-js/src/date.js"></script>
  <script src="js/cookie.js"></script>
  <script src="js/global.js"></script>
  <?php
   if(isset($_SESSION['contactUser']) && $_SESSION['contactUser']['user']['verificado']==1){
  ?>
    <script src="http://contact-xanela.rhcloud.com/socket.io/socket.io.js"></script>
    <script type="text/javascript" src="js/jlinkify.pack.js"></script>
    <script src="js/contact.js"></script>
    <script src="js/custom.js"></script>
  <?php
  }else{
  ?>
    <script src="js/iniData.js"></script>
  <?php
  }
  ?>
  
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/estilos.css">
  <link rel="stylesheet" type="text/css" href="css/resp.css">
    
</head>
<body>

<div id="backPopAp"></div>

<div class="container-fluid">

  <?php
     include_once "app/view/cabecera.php";
     include_once $uri;
     if(!isset($_SESSION['contactUser']))
        include_once 'app/view/footer.php';
  ?>

</div>

<div id="backPopUp"></div>
<div id="popUpAviso">
  <h1><small>política de privacidad</small></h1>
  <h2><small>datos personales</small></h2>
  <p>
  De acuerdo con lo establecido en la Ley Orgánica 15/1999 de Protección de Datos de Carácter Personal (LOPD), así como en su Reglamento de Desarrollo, aprobado por Real Decreto 1720/2007, de 21 de diciembre, Contact! informa a los usuarios de que sus datos de registro serán incorporados a un fichero cuya finalidad es la gestión de usuarios en la plataforma y serán tratados con absoluta confidencialidad, no siendo cedidos a terceros, ni destinados a finalidades distintas para las que han sido solicitados.
  </p>
  <h2><small>seguridad</small></h2>
  <p>
    Contact! utiliza técnicas de seguridad de la información generalmente aceptadas en la industria, procedimientos de control de acceso y mecanismos criptográficos, todo ello con el objeto de evitar el acceso no autorizado a los datos. Para lograr estos fines, el usuario acepta que el prestador obtenga datos para efectos de la correspondiente autenticación de los controles de acceso. No obstante, el usuario debe ser consciente de que las medidas de seguridad en Internet no son inexpugnables. 
  </p>
  <h2><small>cookies</small></h2>
  <p>
    Te aviso que en este sitio se puede utilizar cookies cuando el usuario navega por las diferentes páginas del sitio. Durante el uso de nuestra página web aceptas y autorizas expresamente el uso de cookies, de acuerdo con nuestra política de privacidad.
  </P>
  <h5><strong>¿Qué son las cookies?</strong></h5>
  <p>
    Una cookie es un fichero muy pequeño que se descarga en el ordenador/smartphone/tablet del usuario al acceder a determinadas páginas web para almacenar y recuperar información sobre la navegación que se efectúa desde dicho equipo. Para conocer más información sobre las cookies, te invito a acceder al siguiente <a href="http://es.wikipedia.org/wiki/Cookie_(inform%C3%A1tica)" target="_blank">enlace</a>. 
  </p>
  <h5><strong>¿Para qué sirven las cookies?</strong></h5>
  <p>
    La utilización de las cookies tiene como finalidad exclusiva recordar las preferencias del usuario (idioma, país, inicio de sesión, características de su navegador, información de uso de nuestra Web, etc.)
  </p>
  <p>
    Recordando sus preferencias sabremos las características del ordenador que está usando y así podremos ofrecerle una mejor experiencia de navegación. Las cookies pueden ayudar a nuestro sitio Web a distinguir el navegador del usuario como visitante anterior y así guardar y recordar las preferencias que puedan haberse establecido mientras el usuario estaba navegando por el sitio, personalizar las páginas de inicio, identificar qué sectores de un sitio han sido visitados o mantener un registro de selecciones en un “carro de compra”.
  </p>
  <p>
    Asimismo pueden ser utilizadas para obtener información acerca del tráfico dentro del propio site y estimar el número de visitas realizadas.
  </p>
  <p>
    Normalmente los sitios Web utilizan las cookies para obtener información estadística sobre sus páginas Web. Tenga en cuenta que recogemos datos sobre sus movimientos y uso de nuestra Web como datos estadísticos, no personales.
  </p>
  <h5><strong>Salvaguardias de protección</strong></h5>
  <p>
    El usuario puede configurar su navegador para aceptar, o no, las cookies que recibe o para que el navegador le avise cuando un servidor quiera guardar una cookie o borrarlas de su ordenador. Puede encontrar las instrucciones en la configuración de seguridad en su navegador Web.
  </p>
</div>

</body>
</html>