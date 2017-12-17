<?php

class Contactos {

 

 function __construct() {

  $con = mysql_connect(SERVER, USER, PASS,"", PORT);  
  mysql_select_db(DATABASE);

 }

 

 public function guardarContacto($id_usuario, $usuario_contacto){

  $response = array("success" => "0");

  $sql  ="INSERT INTO contactos (id_usuario, usuario_contacto) VALUES ('" . stripslashes($id_usuario) . "', '" . stripslashes($usuario_contacto) . "');";

  

  $result = mysql_query($sql);

    if(mysql_affected_rows() > 0){
       $response["success"] = "1";  
       return $response;
    }else{
       return false;
    }

 }



 public function borrarContacto($id){

  $response = array("success" => "0");

  $sql  ="DELETE FROM contactos WHERE id=".stripslashes($id).";";

  

  $result = mysql_query($sql);

  if(mysql_affected_rows() > 0)

   $response["success"] = "1";  

  

  return json_encode($response);

 }

 

 public function getContactos($id){

  $response = array("success" => 0);

  

  $sql = "SELECT id,id_usuario, usuario_contacto FROM contactos WHERE id = '".stripslashes($id)."';";

  $result = mysql_query($sql);

  if(mysql_num_rows($result) > 0){

     $data = mysql_fetch_array($result);

   

   return $data;

  } else 

   return false;  

 }



 public function getAllContactos($usuarioLocal){



    // array for JSON response

    $response = array();



    // get all products from products table

    $result = mysql_query("SELECT id,id_usuario FROM contactos WHERE id_usuario = '".stripslashes($usuarioLocal)."'" ) or die(mysql_error());



    // check for empty result

    if (mysql_num_rows($result) > 0) {

        // looping through all results

        // products node

        $response["contactos"] = array();



        while ($row = mysql_fetch_array($result)) {

            // temp user array

            $contacto = array();

            $contacto["id"] = $row["id"];

            $contacto["id_usuario"] = $row["id_usuario"];



            // push single product into final response array

            array_push($response["contactos"], $solicitud);

        }

        // success

        $response["success"] = 1;



        // echoing JSON response

        echo json_encode($response);

    } else {

        // no products found

        $response["success"] = 0;

        $response["message"] = "No solicitudes found";



        // echo no users JSON

        echo json_encode($response);

    }



 }//----FIN FUNCION getAllUsers()



 public function getValidaContacto($id_usuario, $usuario_contacto){

    $response = array("success" => 0);

    $sql = "SELECT id FROM contactos WHERE (id_usuario = '".stripslashes($id_usuario)."' AND usuario_contacto='".stripslashes($usuario_contacto)."') 
    OR (id_usuario = '".stripslashes($usuario_contacto)."' AND usuario_contacto='".stripslashes($id_usuario)."');";

    $result = mysql_query($sql);

    if(mysql_num_rows($result) > 0){
       $data = mysql_fetch_array($result);
       return $data;
    } else 
       return false; 

   }


}

?>