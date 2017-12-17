<?php
class Mensajes
{
 
 public $con;
 
 function __construct() {
  $this->con = new mysqli();
  $this->con->connect(SERVER, USER, PASS,DATABASE,PORT);
        //Si sucede algún error la función muere e imprimir el error
        if($this->con->connect_error){
            die("Error en la conexion : ".$this->con->connect_errno.
                                      "-".$this->con->connect_error);
        }  
  
 }
 
 public function guardarMensaje($id_remite,$id_destinatario,$contenido){

    try {
        $time = time();
        $response = array("success" => "0");
        $sql  ="INSERT INTO rec_mensajes (id_remite, id_destinatario, contenido, fecha) 
        VALUES (?, ?, ?, ?); ";

          /* Le damos los parámetros (símbolos ‘?’),
             * pueden ser de tipo ‘i’ = integer
             *                    ‘d’ = double
             *                    ‘s’ = string
             *                    ‘b’ = BLOB
             */
        $sentencia = $this->con->prepare($sql);
        $sentencia->bind_param("sssi",$id_remite, $id_destinatario, $contenido, $time);
        
        if($sentencia->execute()){
                $sentencia->close();
                $response["success"] = "1";  
                return json_encode($response);
            }
            //Sino surgió algún error y retornamos una cadena de error.
            else{
                $sentencia->close();
                $response["success"] = "0";  
                return json_encode($response);
            }

    } catch (Exception $e) {
      //echo $e;
      $this->con->close();
      $response["success"] = "0";  
      return json_encode($response);
    }

 }

  public function getAllMensajes($id_remite,$id_destinatario,$gmt,$numreg = null){
    // array for JSON response
  $response = array();
    
  try{
            $limit = ($numreg != null) ? $numreg : 50 ;
           /*$sql  = "SELECT id_remite,id_destinatario,contenido,fecha FROM rec_mensajes 
                    WHERE (id_remite = ? AND id_destinatario = ?) OR (id_remite = ? AND id_destinatario = ?) ORDER BY fecha ASC";*/
            $sql  = "SELECT tmp.id_remite,tmp.id_destinatario,tmp.contenido,tmp.fecha FROM 
                      (SELECT id_remite,id_destinatario,contenido,fecha FROM rec_mensajes 
                       WHERE (id_remite = ? AND id_destinatario = ?) OR (id_remite = ? AND id_destinatario = ?) 
                              ORDER BY fecha DESC LIMIT ?) tmp
                     ORDER BY tmp.fecha ASC";
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("ssssi",$id_remite,$id_destinatario,$id_destinatario,$id_remite,$limit);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($id_remite,$id_destinatario,$contenido,$fecha);
            //Preguntamos si retorno algo, método feth()
            if ($sentencia->num_rows > 0) {
                
                require_once 'funciones.php';

                $response["mensajes"] = array();
                while($sentencia->fetch()){
                    //$sentencia->close();
                    $mensaje = array();
                    $mensaje["id_remite"] = $id_remite;
                    $mensaje["id_destinatario"] = $id_destinatario;
                    $mensaje["contenido"] = $contenido;
                    $mensaje["fecha"] = mifechagmt($fecha,$gmt);

                    // push single product into final response array
                    array_push($response["mensajes"], $mensaje);
                }
                $response["success"] = 1;
            }else{
                $response["success"] = 0;
                $response["message"] = "No se encontraron mensajes";
            }
            echo json_encode($response);
  }catch(Exception $e){
            //echo $e;
            $this->con->close();
            $sentencia->close();
            $response["success"] = 0;
            $response["message"] = "error busqueda de mensajes";
            echo json_encode($response);
  }

 }//----FIN FUNCION getAllUsers()


 public function getVisto($remite=null,$destinatario=null,$sw=null){
    
  $response = array();
  $visto = null;

  try{
           $sql  = "SELECT id_remite FROM rec_mensajes 
                    WHERE (id_remite = ? AND id_destinatario = ?) AND visto = 0;";
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("ss",$remite,$destinatario);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($id_remite);
            //Preguntamos si retorno algo, método feth()
            $visto = ($sentencia->num_rows > 0) ? $sentencia->num_rows : '' ;
            if($sw == null)
               return $visto;
            else{
               $response["success"] = 1;
               $response["visto"] = $visto;
               echo json_encode($response);
            }

  }catch(Exception $e){
            //echo $e;
            $this->con->close();
            $sentencia->close();
            if($sw == null)
               return '';
            else{
               $response["success"] = 0;
               $response["visto"] = '';
               echo json_encode($response);
            }
  }

 }//FIN getVisto()

 public function setVisto($remite=null,$destinatario=null){

     try{
             $sql  ="UPDATE rec_mensajes set visto = ? WHERE  id_remite = ? AND id_destinatario = ? AND visto = 0; ";
                    $valor = 1;
                    $sentencia = $this->con->prepare($sql);
                    $sentencia->bind_param("iss",$valor, $remite,$destinatario);
                     
                    if($sentencia->execute()){
                      $sentencia->close();
                      return true;
                    }else{
                      return false;
                    }

    }catch(Exception $e){
              //echo $e;
              $this->con->close();
              $sentencia->close();
              return false;
    }

 }


 function __destruct(){
    $this->con->close();
 }

}

?>