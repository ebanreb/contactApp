<?php
class Users
{
 
 public $con;
 
 function __construct() {
  $this->con = new mysqli();
  $this->con->connect(SERVER, USER, PASS,DATABASE);
        //Si sucede algún error la función muere e imprimir el error
        if($this->con->connect_error){
            die("Error en la conexion : ".$this->con->connect_errno.
                                      "-".$this->con->connect_error);
        }  
  
 }
 
 public function saveUser($name, $email, $password){

    try {

        //$uuid = uniqid('', true);
        $uuid = $this -> getToken(8);
        //$verificationCode = md5($uuid);
        $verificationCode = $this -> getToken(6);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $sql  ="INSERT INTO users (unique_id, name, email, encrypted_password, salt, created_at, codigo_verificacion) 
        VALUES (?, ?, ?, ?, ?, NOW(), ?); ";

          /* Le damos los parámetros (símbolos ‘?’),
             * pueden ser de tipo ‘i’ = integer
             *                    ‘d’ = double
             *                    ‘s’ = string
             *                    ‘b’ = BLOB
             */
        $sentencia = $this->con->prepare($sql);
        $sentencia->bind_param("ssssss",$uuid, $name, $email, $encrypted_password, $salt, $verificationCode);
        
        if($sentencia->execute()){
                $sentencia->close();
                $sEmail = $this -> enviarEmailVerificacion($email,$verificationCode);
                return $sEmail;
            }
            //Sino surgió algún error y retornamos una cadena de error.
            else{
                $sentencia->close();
                return false;
            }

    } catch (Exception $e) {
      //echo $e;
      $this->con->close();
      return false;
    }

 }

 /*Solicitud de cambio/recuperación de password*/
 public function recDePassword($uid, $email){

    try {
        $time = time()+(12*3600);
        $codigo = $this -> getToken(6);
        $sql  ="INSERT INTO rt_password (unique_id, codigo, tiempo) VALUES (?, ?, ?); ";

          /* Le damos los parámetros (símbolos ‘?’),
             * pueden ser de tipo ‘i’ = integer
             *                    ‘d’ = double
             *                    ‘s’ = string
             *                    ‘b’ = BLOB
             */
        $sentencia = $this->con->prepare($sql);
        $sentencia->bind_param("ssi",$uid, $codigo, $time);
        
        if($sentencia->execute()){
                $sentencia->close();
                $sEmail = $this -> enviarEmailRtPassword($email,$codigo);
                return $sEmail;
            }
            //Sino surgió algún error y retornamos una cadena de error.
            else{
                $sentencia->close(); 
                return false;
            }

    } catch (Exception $e) {
      //echo $e;
      $this->con->close();
      return false;
    }

 }

 /*Verificación de que el codigo de cambio/recuperación de password es correcto*/
 public function verificacionCodigoNewPassword($uid,$codigo){
        try{
            $sql = "SELECT codigo, tiempo FROM rt_password WHERE unique_id=?;";
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("s",$uid);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($codigo_verificacion, $tiempo);
            //Preguntamos si retorno algo, método feth()
            if($sentencia->fetch()){
                $sentencia->close();
                //Retornamos ese algo, referenciando la variable de bind_result()
                $tm = time();
                if( ($codigo_verificacion == $codigo) && $tiempo >= $tm) {
                   return true;
                }else
                   return false;
                
            }else{
                $sentencia->close();
                return false;
            }
        }catch(Exception $e){
                  //echo $e;
                  $this->con->close();
                  $sentencia->close();
                  return false;
        }
 }
 
 /*Modificación/Recuperación del password de usuario*/
 public function changePassword($uid,$password){
     try {
        
        $sql  = "UPDATE users SET encrypted_password = ?,salt = ? WHERE unique_id = ?;";

        if($password != null){
          $hash = $this->hashSSHA($password);
          $encrypted_password = $hash["encrypted"]; // encrypted password
          $salt = $hash["salt"]; // salt
        }

          /* Le damos los parámetros (símbolos ‘?’),
             * pueden ser de tipo ‘i’ = integer
             *                    ‘d’ = double
             *                    ‘s’ = string
             *                    ‘b’ = BLOB
             */
        $sentencia = $this->con->prepare($sql);
        $sentencia->bind_param("sss",$encrypted_password, $salt, $uid); 
        
        if($sentencia->execute()){
                $sentencia->close();
                $sql  = "DELETE FROM rt_password WHERE unique_id = ?;";
                $sentencia = $this->con->prepare($sql);
                $sentencia->bind_param("s",$uid);
                $sentencia->execute();
                $sentencia->close();
                return true;
        }
        //Sino surgió algún error y retornamos una cadena de error.
        else{
                $sentencia->close();
                return false;
        }

    } catch (Exception $e) {
      //echo $e;
      $this->con->close();
      return false;
    }
 }

 public function modUser($uuid, $name=null, $email=null, $password=null){

    try {
        $response = array("success" => "0");
        $sw = 0;
        
        $sql  = "UPDATE users SET name = ?";
        $where = " WHERE unique_id = ?;";

        if($email != null){
          $sql .= ",email = ?";
          $sw += 1;
        }

        if($password != null){
          $hash = $this->hashSSHA($password);
          $encrypted_password = $hash["encrypted"]; // encrypted password
          $salt = $hash["salt"]; // salt
          $sql .= " ,encrypted_password = ?,salt = ?";
          $sw += 2;
        }

        $sql = $sql.$where;
        
          /* Le damos los parámetros (símbolos ‘?’),
             * pueden ser de tipo ‘i’ = integer
             *                    ‘d’ = double
             *                    ‘s’ = string
             *                    ‘b’ = BLOB
             */
        $sentencia = $this->con->prepare($sql);
        //$sentencia->bind_param("sssss",$name, $email, $encrypted_password, $salt, $uuid);
        switch ($sw) {
          case 0:
            $sentencia->bind_param("ss",$name, $uuid);
            break;
          case 1:
            $sentencia->bind_param("sss",$name, $email, $uuid);
            break;
          case 2:
            $sentencia->bind_param("ssss",$name, $encrypted_password, $salt, $uuid);
            break;
          case 3:
            $sentencia->bind_param("sssss",$name, $email, $encrypted_password, $salt, $uuid);
            break;
        }
               
        
        if($sentencia->execute()){
                $sentencia->close();
                $response["success"] = "1";  
                return true;
            }
            //Sino surgió algún error y retornamos una cadena de error.
            else{
                $sentencia->close();
                $response["success"] = "0";  
                return false;
            }

    } catch (Exception $e) {
      //echo $e;
      $this->con->close();
      $response["success"] = "0";  
      return false;
    }

 }

 /*Baja, metodo logico, de cuenta de usuario registrado*/
 public function bajaCuenta($uid){
   
   try {
        
        $sql  = "UPDATE users SET verificado = -1 WHERE unique_id = ?;";

          /* Le damos los parámetros (símbolos ‘?’),
             * pueden ser de tipo ‘i’ = integer
             *                    ‘d’ = double
             *                    ‘s’ = string
             *                    ‘b’ = BLOB
             */
        $sentencia = $this->con->prepare($sql);
        $sentencia->bind_param("s",$uid); 
        
        if($sentencia->execute()){
                $sentencia->close();
                return true;
        }
        //Sino surgió algún error y retornamos una cadena de error.
        else{
                $sentencia->close();
                return false;
        }

    } catch (Exception $e) {
      //echo $e;
      $this->con->close();
      return false;
    }

 }
  
 public function search($nameSearch,$logUser){
    // array for JSON response
  $response = array();
    
  try{
            $param = "%{$nameSearch}%";
            $sql = 'SELECT unique_id, name FROM users WHERE name LIKE ? AND name <> ? AND verificado!=-1';
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("ss",$param,$logUser);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($unique_id, $name);
            //Preguntamos si retorno algo, método feth()
            if ($sentencia->num_rows > 0) {
                $response["usuarios"] = array();
                while($sentencia->fetch()){
                    //$sentencia->close();
                    $usuario = array();
                    $usuario["uuid"] = $unique_id;
                    $usuario["name"] = $name;

                    // push single product into final response array
                    array_push($response["usuarios"], $usuario);
                }
                $response["success"] = 1;
            }else{
                $response["success"] = 0;
                $response["message"] = "No users found";
            }
            echo json_encode($response);
  }catch(Exception $e){
            //echo $e;
            $this->con->close();
            $sentencia->close();
            $response["success"] = 0;
            $response["message"] = "No se encontraron usuarios";
            echo json_encode($response);
  }

 }//----FIN FUNCION getAllUsers()

 /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
        try{
            $response = array("success" => 0);
  
            $sql = "SELECT unique_id, name, email, encrypted_password, salt, created_at,updated_at,verificado FROM users WHERE email=? AND verificado!=-1;";
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("s",$email);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($uuid, $name, $email, $encrypted_password, $salt, $created_at,$updated_at,$verificado);
            //Preguntamos si retorno algo, método feth()
            if($sentencia->fetch()){
                $sentencia->close();
                //Retornamos ese algo, referenciando la variable de bind_result()
                $data['uuid'] = $uuid;
                $data['name'] = $name;
                $data['email'] = $email;
                $data['encrypted_password'] = $encrypted_password;
                $data['salt'] = $salt;
                $data['created_at'] = $created_at;
                $data['updated_at'] = $updated_at;
                $data['verificado'] = $verificado;
                
                $hash = $this->checkhashSSHA($salt, $password);
                // check for password equality
                if ($encrypted_password == $hash) {
                    // user authentication details are correct
                    return $data;
                }
                
            }else{
                $sentencia->close();
                return false;
            }
        }catch(Exception $e){
                  //echo $e;
                  $this->con->close();
                  $sentencia->close();
                  return false;
        }
    }

    public function verficacionDeCuenta($email, $codVr){
        try{
            $response = array("success" => 0);
  
            $sql = "SELECT codigo_verificacion FROM users WHERE email=?;";
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("s",$email);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($codigo_verificacion);
            //Preguntamos si retorno algo, método feth()
            if($sentencia->fetch()){
                $sentencia->close();
                //Retornamos ese algo, referenciando la variable de bind_result()

                if($codigo_verificacion == $codVr){
                  $sql  ="UPDATE users set verificado = ? WHERE  email = ?; ";
                  $valor = 1;
                  $sentencia = $this->con->prepare($sql);
                  $sentencia->bind_param("is",$valor, $email);
                   
                  if($sentencia->execute()){
                      $sentencia->close();
                      return true;
                  }else
                      return false;
                  
                }else
                   return false;
                
            }else{
                $sentencia->close();
                return false;
            }
        }catch(Exception $e){
                  //echo $e;
                  $this->con->close();
                  $sentencia->close();
                  return false;
        }
    }  

    public function getUserByUnid($unid) {
        try{
            $response = array("success" => 0);
  
            $sql = "SELECT name FROM users WHERE unique_id=? AND verificado!=-1;";
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("s",$unid);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($name);
            //Preguntamos si retorno algo, método feth()
            if($sentencia->fetch()){
                $sentencia->close();
                //Retornamos ese algo, referenciando la variable de bind_result()
                
                return $name;
                
            }else{
                $sentencia->close();
                return false;
            }
        }catch(Exception $e){
                  //echo $e;
                  $this->con->close();
                  $sentencia->close();
                  return false;
        }
    }

  public function getAllUsers($usuarioLocal){
    // array for JSON response
  $response = array();
    
  try{
          /*$sql  = "SELECT unique_id,name FROM users 
                           WHERE unique_id <> ?
                           AND (unique_id IN (SELECT usuario_contacto FROM contactos WHERE id_usuario=?) 
                                OR unique_id IN (SELECT id_usuario FROM contactos WHERE usuario_contacto=?)) ORDER BY name ASC";*/
           /*
           SELECT id_destinatario,
SUM(CASE WHEN visto = 0 THEN 1 ELSE 0 END) AS "mensajes_pendientes"
    FROM rec_mensajes
    WHERE id_remite = '53da8356809803.09732729'
    GROUP BY id_destinatario
           */

           /*$sql  = "SELECT users.unique_id,users.name FROM users INNER JOIN contactos on ((contactos.id_usuario=users.unique_id)
            OR (contactos.usuario_contacto=users.unique_id)) AND ((contactos.id_usuario=?) OR (contactos.usuario_contacto=?))
             AND users.unique_id<>? ORDER BY users.name ASC;";*/

             /*
               SELECT rec_mensajes.* FROM 
(SELECT MAX(fecha) AS fecha 
         FROM rec_mensajes 
         WHERE '53da8356809803.09732729' IN (id_remite,id_destinatario)
         GROUP BY IF ('53da8356809803.09732729' = id_remite,id_destinatario,id_remite)) AS latest
LEFT JOIN rec_mensajes ON latest.fecha = rec_mensajes.fecha AND '53da8356809803.09732729' IN (rec_mensajes.id_remite, rec_mensajes.id_destinatario)
GROUP BY IF ('53da8356809803.09732729' = rec_mensajes.id_remite,rec_mensajes.id_destinatario,rec_mensajes.id_remite)
             */

            /*$sql="SELECT users.unique_id as unique_id,users.name as name,CASE WHEN mensajes.mensajes_pendientes IS NULL THEN 0 ELSE mensajes.mensajes_pendientes END as visto 
            FROM users 
            INNER JOIN contactos on ((contactos.id_usuario=users.unique_id) OR (contactos.usuario_contacto=users.unique_id)) AND ((contactos.id_usuario=?) OR (contactos.usuario_contacto=?)) 
            LEFT JOIN (SELECT id_remite,SUM(CASE WHEN visto = 0 THEN 1 ELSE 0 END) AS mensajes_pendientes 
                       FROM rec_mensajes 
                       WHERE id_destinatario = ? 
                       GROUP BY id_remite) as mensajes on ((contactos.id_usuario = mensajes.id_remite) OR (contactos.usuario_contacto = mensajes.id_remite)) 
            WHERE users.unique_id<>? 
            ORDER BY users.name ASC ;";*/

            $sql="SELECT lstContactos.*, lastMensaje.contenido, lastMensaje.id_remite FROM  
(SELECT users.unique_id as unique_id,users.name as name,CASE WHEN mensajes.mensajes_pendientes IS NULL THEN 0 ELSE mensajes.mensajes_pendientes END as visto 
            FROM users 
            INNER JOIN contactos on ((contactos.id_usuario=users.unique_id) OR (contactos.usuario_contacto=users.unique_id)) AND ((contactos.id_usuario=?) OR (contactos.usuario_contacto=?)) 
            LEFT JOIN (SELECT id_remite,SUM(CASE WHEN visto = 0 THEN 1 ELSE 0 END) AS mensajes_pendientes 
                       FROM rec_mensajes 
                       WHERE id_destinatario = ?
                       GROUP BY id_remite) as mensajes on ((contactos.id_usuario = mensajes.id_remite) OR (contactos.usuario_contacto = mensajes.id_remite)) 
            WHERE users.unique_id<>?  AND users.verificado!=-1) as lstContactos
LEFT JOIN
(SELECT rec_mensajes.* FROM 
(SELECT MAX(fecha) AS fecha 
         FROM rec_mensajes 
         WHERE ? IN (id_remite,id_destinatario)
         GROUP BY IF (? = id_remite,id_destinatario,id_remite)) AS latest
LEFT JOIN rec_mensajes ON latest.fecha = rec_mensajes.fecha AND ? IN (rec_mensajes.id_remite, rec_mensajes.id_destinatario)
GROUP BY IF (? = rec_mensajes.id_remite,rec_mensajes.id_destinatario,rec_mensajes.id_remite)) as lastMensaje
             ON ((lstContactos.unique_id = lastMensaje.id_remite) OR (lstContactos.unique_id = lastMensaje.id_destinatario)) ORDER BY lstContactos.name ASC;";
  
            $sentencia = $this->con->prepare($sql);
            //$sentencia->bind_param("sss",$usuarioLocal,$usuarioLocal,$usuarioLocal);
            $sentencia->bind_param("ssssssss",$usuarioLocal,$usuarioLocal,$usuarioLocal,$usuarioLocal,$usuarioLocal,$usuarioLocal,$usuarioLocal,$usuarioLocal);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            //$sentencia->bind_result($uuid,$name);
            $sentencia->bind_result($uuid,$name,$visto,$contenido,$remite);
            //Preguntamos si retorno algo, método feth()
            if ($sentencia->num_rows > 0) {
                //require_once 'mensajes.php';
                //$mensajes = new Mensajes();
                $response["usuarios"] = array();
                while($sentencia->fetch()){
                    //$sentencia->close();
                    $usuario = array();
                    $usuario["uuid"] = $uuid;
                    $usuario["name"] = $name;
                    //$usuario["visto"] = $mensajes -> getVisto($uuid,$usuarioLocal);
                    $usuario["visto"] = ($visto > 0) ? $visto : '' ;
                    $usuario["foto"] = (file_exists(DOCUMENT_ROOT."images/contact/min/".$uuid.".jpg")) ? 1 : 0 ;
                    $usuario["ltsMensaje"] = $contenido;
                    $usuario["ltsRemite"] = $remite;

                    // push single product into final response array
                    array_push($response["usuarios"], $usuario);
                }
                $response["success"] = 1;
            }else{
                $response["success"] = 0;
                $response["message"] = "No se encontraron usuarios";
            }
            echo json_encode($response);
  }catch(Exception $e){
            //echo $e;
            $this->con->close();
            $sentencia->close();
            $response["success"] = 0;
            $response["message"] = "No se encontraron usuarios";
            echo json_encode($response);
  }

 }//----FIN FUNCION getAllUsers()
 
    /**
     * Check user is existed or not by email
     */
    public function isUserExisted($email) {

        try{
            $response = array("success" => 0);
  
            $sql = "SELECT unique_id,email FROM users WHERE email=? AND verificado!=-1;";
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("s",$email);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($uuid, $email);
            //Preguntamos si retorno algo, método feth()
            if($sentencia->fetch()){
                $sentencia->close();
                //return true;
                return $uuid;
            }else{
                $sentencia->close();
                return false;
            }
        }catch(Exception $e){
                  //echo $e;
                  $this->con->close();
                  $sentencia->close();
                  return false;
        }
    }

    /**
     * Check user is existed or not by name
     */
    public function isUserExistedByName($name) {

        try{
            $response = array("success" => 0);
  
            $sql = "SELECT name FROM users WHERE name=? AND verificado!=-1;";
  
            $sentencia = $this->con->prepare($sql);
            $sentencia->bind_param("s",$name);
            $sentencia->execute();

            // Si es una consulta de select almacenamos el resultado con el método store_result() del objeto $stmt.
            $sentencia->store_result();
           
            // Número de filas obtenidas, si fuera necesario.
            // $numfilas=$stmt->num_rows;
            
            $sentencia->bind_result($name);
            //Preguntamos si retorno algo, método feth()
            if($sentencia->fetch()){
                $sentencia->close();
                return true;
            }else{
                $sentencia->close();
                return false;
            }
        }catch(Exception $e){
                  //echo $e;
                  $this->con->close();
                  $sentencia->close();
                  return false;
        }
    }

    public function validaCampos($nombre=null, $email=null, $confEmail=null, $password=null, $confPassword=null){
        $reg_email = '/^[_a-zA-Z-]+[._a-zA-Z0-9-]+@[a-zA-Z0-9-]+\.[.a-zA-Z0-9]+/i';
        $mensaje="";
        
        if($nombre!=null){
          if(empty($nombre))
          {
            $mensaje.="El campo nombre es obligatorio<br/>";
          }
        }
        
        if($email!=null){
          if(!preg_match($reg_email, $email)){
            $mensaje.="el email no es valido<br/>";
          }elseif($email != $confEmail){
            $mensaje.="los emails no coinciden<br/>";
          }
        }
        
        if($password!=null){
          if(empty($password)){
            $mensaje.="el password es obligatorio<br/>";
          }elseif($password != $confPassword){
            $mensaje.="los Passwords deben coincidir <br/>";  
          }
        }
       
       return $mensaje;
    }

    public function enviarEmailVerificacion($email,$verificationCode){
         $htmlStr = "";
         $htmlStr .= "Hola " . $email . ",<br /><br />";
                 
         $htmlStr .= "La primera vez que te logues se te pedira que introduzcas el siguiente codigo para activar tu cuenta.<br /><br /><br />";
         $htmlStr .= "{$verificationCode}<br /><br /><br />";
                 
         $htmlStr .= "Muchas gracias,<br />";
         $htmlStr .= "<a href='http://xanela.hol.es/contact/' target='_blank'>Contact!</a><br />";
                 
         
         $name = "Contact!";
         $email_sender = "ifritfire2003@hotmail.com";
         $subject = "Codigo de verificación de cuenta";
         $recipient_email = $email;
 
         $headers  = "MIME-Version: 1.0rn";
         $headers .= "Content-type: text/html; charset=utf8";
         $headers .= "From: {$name} <{$email_sender}> n";
 
         $body = $htmlStr;
 
                // send email using the mail function, you can also use php mailer library if you want
                //if( $mail($recipient_email, $subject, $body, $headers) ){
               if( $this->SendMAIL($recipient_email, $subject, $body, $body,$email_sender,$name) ){
                     
                    $sql  ="UPDATE users set verificacion_envio = ? WHERE  email = ?; ";
                    $valor = 1;
                    $sentencia = $this->con->prepare($sql);
                    $sentencia->bind_param("is",$valor, $email);
                     
                    if($sentencia->execute())
                        $sentencia->close();

                    return true;
                     
                }else{
                    //die("Sending failed.");
                   return false;
                }
    }

    public function enviarEmailRtPassword($email,$codigo){
         $htmlStr = "";
         $htmlStr .= "Hola " . $email . ",<br /><br />";
                 
         $htmlStr .= "Recibimos una solicitud de cambio de contraseña. Para confirmar tu nueva contraseña introduce el siguiente codigo en el formulario de cambio de contraseña: .<br /><br />";
         $htmlStr .= "{$codigo}<br /><br />";
         $htmlStr .= "El codigo sera valido por 12h.<br/><br/>";
         
         $htmlStr .= "Por favor, ignora este mensaje en el caso que no nos hayas enviado un cambio de contraseña de tu cuenta.<br/>";
                 
         $htmlStr .= "Muchas gracias,<br /><br />";
         $htmlStr .= "<a href='http://xanela.hol.es/contact/' target='_blank'>Contact!</a><br />";
                 
         
         $name = "Contact!";
         $email_sender = "ifritfire2003@hotmail.com";
         $subject = "Recuperar Contraseña";
         $recipient_email = $email;
 
         /*$headers  = "MIME-Version: 1.0rn";
         $headers .= "Content-type: text/html; charset=utf8";
         $headers .= "From: {$name} <{$email_sender}> n";*/
 
         $body = $htmlStr;
 
                // send email using the mail function, you can also use php mailer library if you want
               //if(mail($recipient_email, $subject, $body, $headers) ){
               if( $this->SendMAIL($recipient_email, $subject, $body, $body,$email_sender,$name) ){
                  return true;
               }else{
                  //die("Sending failed.");
                  return false;
               }
    }

    function SendMAIL($para,$subject,$body,$altbody,$mailFROM,$mailNameCompany){
        require "phpmailer/class.phpmailer.php";
        $mail = new phpmailer(true);
        $mail->PluginDir = "phpmailer/";
        
        //$mail->Mailer = "smtp";
        $mail->Host = "smtp.mailgun.org"; # Editar el Host smtp
        $mail->IsSMTP(); // telling the class to use SMTP
        //$mail->SMTPDebug = 2; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls"; // sets the prefix to the servier
        $mail->Username = "postmaster@sandbox5a250db59b544a199f9aa63d3b54eb6f.mailgun.org"; # editar el usuario
        $mail->Password = "5448f1d4274ec991afa171b336259b23"; # Editar el password
        $mail->Port = 587;
        
        $mail->From = $mailFROM;
        $mail->FromName = $mailNameCompany;
        $mail->Subject = $subject;
        $email = $para;
        $body = $body;

         
        $mail->Body = $body;
        $mail->AltBody = $altbody;
        $mail->IsHTML(true);
        $mail->CharSet = "UTF-8";
        $mail->Timeout=20;
        $mail->AddAddress($email);
        $exito = $mail->Send();
        $intentos=1; 
        /*while((!$exito)&&($intentos<5)&&($mail->ErrorInfo!="SMTP Error: Data not accepted")){
          sleep(5);
          $exito = $mail->Send();
          $intentos=$intentos+1;                
        }
         
        if ($mail->ErrorInfo=="SMTP Error: Data not accepted") {
               $exito=true;
        }*/

        while ((!$exito) && ($intentos < 5)) {
          sleep(5);
          //echo $mail->ErrorInfo;
          $exito = $mail->Send();
          $intentos=$intentos+1;  
          
        }
         
            
        /*if(!$exito){
          echo "Problemas enviando correo electrónico a ".$valor;
          echo "<br/>".$mail->ErrorInfo;  
           }
           else
           {
          echo "Mensaje enviado correctamente";
           }*/ 

        return $exito;
    }
 
    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
 
    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    }

    public function crypto_rand_secure($min, $max) { 
       $range = $max - $min; 
       if ($range < 0) 
         return $min; // not so random... 
       $log = log($range, 2); 
       $bytes = (int) ($log / 8) + 1; // length in bytes 
       $bits = (int) $log + 1; // length in bits 
       $filter = (int) (1 << $bits) - 1; // set all lower bits to 1 
       do { 
           $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes))); 
           $rnd = $rnd & $filter; // discard irrelevant bits 
        } while ($rnd >= $range); 
        return $min + $rnd;
    } 

    public function getToken($length){ 
      $token = ""; 
      $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
      $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz"; 
      $codeAlphabet.= "0123456789"; 
      for($i=0;$i<$length;$i++){ 
        $token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))]; 
      } 
      return $token; 
    }


 function __destruct(){
    $this->con->close();
 }

}

?>