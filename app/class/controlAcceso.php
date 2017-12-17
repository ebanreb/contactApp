<?php
class controlAcceso {

 private $accD = array('contact' => true, 'solicitudes' => true, 'cnf' => true);
 private $accDs = array('allusers' => true, 'search' => true, 'enviarsolicitud' => true, 'getSolicitudes' => true,'getSolicitudesRec' => true, 
 	                    'numeroSolicitudes' => true, 'borrarsolicitud' => true, 'aceptarsolicitud' => true,
 	                    'submitmsg' => true, 'getMensajes' => true, 'login' => false, 'register' => false, 'logout' => true,
 	                    'verificacion' => true, 'cnf' => true, 'foto' => true, 'getNumMensajes' => true, 'recPass' => false, 
 	                    'passByCod' => false, 'bajaCuenta' => true);
 private $redireccion = ROOT;

 function __construct() {

  $con = mysql_connect(SERVER, USER, PASS);  
  mysql_select_db(DATABASE);

 }

 function validaAcceso($seccion){
    
    if( ($this -> accD[$seccion] == true && !isset($_SESSION['contactUser'])) 
       OR ($seccion == 'inicio' && isset($_SESSION['contactUser'])) OR $this -> accD[$seccion] == false){
         header("Location: " . $this -> redireccion);
         exit;
    }
 }

 function validaAccServ($servicio){

     return ($this -> accDs[$servicio] && !isset($_SESSION['contactUser'])) ? false : $servicio ;

 }

}
?>