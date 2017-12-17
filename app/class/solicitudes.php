<?php
class Solicitudes {

 function __construct() {

  $con = mysql_connect(SERVER, USER, PASS);  
  mysql_select_db(DATABASE);

 }

 public function guardarSolicitud($remite, $destinatario){

  $response = array("success" => "0");

  $sql  ="INSERT INTO solicitudes (remite, destinatario) VALUES ('" . stripslashes($remite) . "', '" . stripslashes($destinatario) . "');";
  $result = mysql_query($sql);

  if(mysql_affected_rows() > 0)
     $response["success"] = "1";  

  return json_encode($response);

 }

 public function borrarSolicitud($id){

  $response = array("success" => "0");

  $sql = "DELETE FROM solicitudes WHERE id=".stripslashes($id).";";
  mysql_query($sql);

  if(mysql_affected_rows() > 0)
     $response["success"] = "1";  

  return json_encode($response);

 }

 public function getSolicitud($id){

  $response = array("success" => 0);

  $sql = "SELECT id,remite,destinatario FROM solicitudes WHERE id = '".stripslashes($id)."';";
  $result = mysql_query($sql);

  if(mysql_num_rows($result) > 0){
     $data = mysql_fetch_array($result);
     return $data;
  }else 
     return false;  

 }

 public function getNumeroSolicitudes ($usuarioLocal){
    // array for JSON response
    $response = array();

    // get all products from products table
    $result = mysql_query("SELECT id FROM solicitudes WHERE destinatario = '".stripslashes($usuarioLocal)."'" ) or die(mysql_error());

    if(mysql_num_rows($result) > 0){
        // success
        $response["success"] = 1;
        $response["nSolicitudes"] = mysql_num_rows($result);
    }else{
        // no products found
        $response["success"] = 0;
        $response["nSolicitudes"] = "";
    }

    // echoing JSON response
    echo json_encode($response);
 }

 public function getAllSolicitudes($usuarioLocal){

    // array for JSON response
    $response = array();

    // get all products from products table
    $result = mysql_query("SELECT id,remite,destinatario FROM solicitudes WHERE remite = '".stripslashes($usuarioLocal)."' OR destinatario = '".stripslashes($usuarioLocal)."'" ) or die(mysql_error());

    // check for empty result
    if(mysql_num_rows($result) > 0){

        require_once 'users.php';
        $user = new users();

        // looping through all results
        // products node
        $response["solicitudes"] = array();

        while($row = mysql_fetch_array($result)){
            // temp user array
            $solicitud = array();
            $solicitud["id"] = $row["id"];
            $solicitud["remite"] = $row["remite"];
            $solicitud["remiteName"] = $user -> getUserByUnid($row["remite"]);
            $solicitud["destinatario"] = $row["destinatario"];
            $solicitud["destinatarioName"] = $user -> getUserByUnid($row["destinatario"]);

            // push single product into final response array
            array_push($response["solicitudes"], $solicitud);
        }

        // success
        $response["success"] = 1;

        // echoing JSON response
        echo json_encode($response);

    }else{
        // no products found
        $response["success"] = 0;
        $response["message"] = "No se encontraron solicitudes";

        // echo no users JSON
        echo json_encode($response);
    }

 }//----FIN FUNCION getAllSolicitudes()

 public function getAllSolicitudesRec($usuarioLocal){

    // array for JSON response
    $response = array();

    // get all products from products table
    $result = mysql_query("SELECT id,remite,destinatario FROM solicitudes WHERE destinatario = '".stripslashes($usuarioLocal)."'" ) or die(mysql_error());

    // check for empty result
    if(mysql_num_rows($result) > 0){

        require_once 'users.php';
        $user = new users();

        // looping through all results
        // products node
        $response["solicitudes"] = array();

        while($row = mysql_fetch_array($result)){
            // temp user array
            $solicitud = array();
            $solicitud["id"] = $row["id"];
            $solicitud["remite"] = $row["remite"];
            $solicitud["remiteName"] = $user -> getUserByUnid($row["remite"]);
            $solicitud["destinatario"] = $row["destinatario"];
            $solicitud["destinatarioName"] = $user -> getUserByUnid($row["destinatario"]);

            // push single product into final response array
            array_push($response["solicitudes"], $solicitud);
        }

        // success
        $response["success"] = 1;

        // echoing JSON response
        echo json_encode($response);

    }else{
        // no products found
        $response["success"] = 0;
        $response["message"] = "No se encontraron solicitudes";

        // echo no users JSON
        echo json_encode($response);
    }

 }//----FIN FUNCION getAllSolicitudesRec()

 public function getValidaSolicitud($remite, $destinatario){
  
    $sql = "SELECT id FROM solicitudes WHERE (remite = '".stripslashes($remite)."' AND destinatario='".stripslashes($destinatario)."')
     OR (remite = '".stripslashes($destinatario)."' AND destinatario='".stripslashes($remite)."');";
    $result = mysql_query($sql);
    if(mysql_num_rows($result) > 0){
       $data = mysql_fetch_array($result);
       return $data;
    }else 
       return false; 

 }

}
?>