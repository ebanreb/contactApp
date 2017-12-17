<?php
session_start();
require_once 'config.php';
require_once 'funciones.php';
require_once 'class/controlAcceso.php';
require_once 'class/inputfilter/class.inputfilter_clean.php';
require_once 'class/ThumbLib/ThumbLib.inc.php';
require_once 'class/users.php';
require_once 'class/solicitudes.php';
require_once 'class/contactos.php';
require_once 'class/mensajes.php';

$acceso = new controlAcceso();
$tag = $acceso -> validaAccServ($_GET['tag']);
//$tag = $_GET['tag'];

switch ($tag){
 case 'login':
      $email = $_POST['email'];
      $password = $_POST['password'];
      $gmt =  $_POST['GMT'];
      $user = new Users();
      $resUser = $user -> getUserByEmailAndPassword($email, $password);
      if ($resUser != false) {
            // user found
            // echo json with success = 1
          if($resUser["verificado"]==1){
            $response["success"] = 1;
            $response["lUser"] = $resUser["uuid"];
            $response["msg"] = "Correct login!";
            $response["url"] = ROOT;
            
          }else{
            $response["success"] = 1;
            $response["msg"] = "la cuenta esta sin verificar";
            $response["url"] = ROOT;
          }

          $_SESSION['contactUser']["user"]["uid"] = $resUser["uuid"];
          $_SESSION['contactUser']["user"]["name"] = $resUser["name"];
          $_SESSION['contactUser']["user"]["email"] = $resUser["email"];
          $_SESSION['contactUser']["user"]["created_at"] = $resUser["created_at"];
          $_SESSION['contactUser']["user"]["updated_at"] = $resUser["updated_at"];
          $_SESSION['contactUser']["user"]["verificado"] = $resUser["verificado"];
          $_SESSION['contactUser']["user"]["GMT"] = $gmt;

            //$_SESSION['contactUser'] = $response;

          echo json_encode($response);

      } else {
            // user not found
            // echo json with error = 1
            $response["success"] = 0;
            $response["msg"] = "email o password incorrectos! :(";
            echo json_encode($response);
      }
  break;
 case 'recPass':
      $email = $_POST['email'];
      $user = new Users();
      $resUser = $user -> isUserExisted($email);
      if ($resUser != false) {
         
          $uuid = $resUser;
          $rtP = $user -> recDePassword($uuid, $email);
          if($rtP){
             $response["success"] = 1;
             $response["msg"] = "Se envio un email a la cuenta de correo ".$email." con las instucciones para el cambio de contraseña.";
             echo json_encode($response);
          }else{
             $response["success"] = 0;
             $response["msg"] = "No se pudo finalizar el proceso :-c , intentalo otra vez";
             echo json_encode($response);
          }
          
      } else {
            // user not found
            // echo json with error = 1
            $response["success"] = 0;
            $response["msg"] = "el email introducido no esta registrado! :(";
            echo json_encode($response);
      }
  break;
 case 'passByCod':
      $email = $_POST['email'];
      $password = $_POST['password'];
      $confPassword = $_POST['confPassword'];
      $codigo = $_POST['codigo'];
      $user = new Users();
      $resUser = $user -> isUserExisted($email);
      if ($resUser != false) {
        
        if(!empty($codigo)){
          $uuid = $resUser;
          $vlCodigo = $user -> verificacionCodigoNewPassword($uuid,$codigo);
          if($vlCodigo!=false){

             $msgError = $user -> validaCampos(null, null, null, $password, $confPassword);
             if(!empty($msgError)){
               $response["success"] = 0;
               $response["msg"] = $msgError;
               echo json_encode($response);
             }else{
               $chgPass = $user -> changePassword($uuid,$password);
               if($chgPass != false){
                  $response["success"] = 1;
                  $response["msg"] = "contraseña modificada correctamente :)";
                  echo json_encode($response);
               }else{
                  $response["success"] = 0;
                  $response["msg"] = "No se pudo modificar la contraseña :-c , intentalo otra vez";
                  echo json_encode($response);
               }

             }

          }else{
            $response["success"] = 0;
            $response["msg"] = "el codigo introducido no es valido o ha caducado! :(";
            echo json_encode($response);
          }
        }else{
          $response["success"] = 0;
          $response["msg"] = "el codigo introducido no es valido! :(";
          echo json_encode($response);
        }

      } else {
            // user not found
            // echo json with error = 1
            $response["success"] = 0;
            $response["msg"] = "el email introducido no esta registrado! :(";
            echo json_encode($response);
      }
  break;
 case 'register':
      $userRg = new Users();
        $name = $_POST['name'];
        $email = $_POST['email'];
        $confEmail = $_POST['confEmail'];
        $password = $_POST['password'];
        $confPassword = $_POST['confPassword'];
        
        $msgError = $userRg -> validaCampos($name, $email, $confEmail, $password, $confPassword);
        if(!empty($msgError)){
            $response["success"] = 0;
            $response["msg"] = $msgError;
            echo json_encode($response);
        }elseif(!isset($_POST['privacidad'])){
            $response["success"] = 0;
            $response["msg"] = "debes de leer y aceptar la política de privacidad! >:(";
            echo json_encode($response);
        }elseif($userRg->isUserExistedByName($name)){
            $response["success"] = 3;
            $response["msg"] = "Ya existe un usuario con ese nombre ¬¬";
            echo json_encode($response);
        }elseif ($userRg->isUserExisted($email)) { // check if user is already existed
            $response["success"] = 2;
            $response["msg"] = "Ya existe un usuario con esa direccion de correo ¬¬";
            echo json_encode($response);
        } else {
            $user = $userRg->saveUser($name, $email, $password);
            if ($user) {
                $response["success"] = 1;
                $response["msg"] = "Registro correcto :), se te ha enviado un email con la clave para activar tu cuenta.";
                /*$response["uid"] = $user["unique_id"];
                $response["user"]["name"] = $user["name"];
                $response["user"]["email"] = $user["email"];
                $response["user"]["created_at"] = $user["created_at"];
                $response["user"]["updated_at"] = $user["updated_at"];*/
                echo json_encode($response);
            } else {
                // user failed to store
                $response["success"] = 0;
                $response["msg"] = "No se pudo realizar el registro :-c , intentalo otra vez";
                echo json_encode($response);
            }
        }
  break;
  case 'cnf':
      $userRg = new Users();
      $msgError="";
      // Request type is Register new user
        $name = $_POST['name'];
        $email = null;
        $confEmail = null;
        $password = null;
        $confPassword = null;

        if(isset($_POST['modEmail'])){
          $email = $_POST['email'];
          $confEmail = $_POST['confEmail'];
        }
        if(isset($_POST['modPass'])){
          $password = $_POST['password'];
          $confPassword = $_POST['confPassword'];
        }

        $msgError = $userRg -> validaCampos($name, $email, $confEmail, $password, $confPassword);
        if(!empty($msgError)){
            // user failed to store
            $response["success"] = 0;
            $response["msg"] = $msgError;
            echo json_encode($response);
        }elseif($name != $_SESSION['contactUser']['user']['name'] &&  $userRg->isUserExistedByName($name)){
            $response["success"] = 3;
            $response["msg"] = "Ya existe un usuario con ese nombre ¬¬";
            echo json_encode($response);
        }elseif(isset($_POST['modEmail']) && $email != $_SESSION['contactUser']['user']['email'] && $userRg->isUserExisted($email)){
            $response["success"] = 2;
            $response["msg"] = "Ya existe un usuario con esa direccion de correo ¬¬";
            echo json_encode($response);
        } else {
            // store user
            $uuid = $_SESSION['contactUser']["user"]["uid"];
            $user = $userRg->modUser($uuid, $name, $email, $password);
            if ($user) {
                // user stored successfully
                $response["success"] = 1;
                $response["msg"] = "se modificaron los datos de cuenta correctamente :)";
                $response["name"] = $name;
                $_SESSION['contactUser']['user']['name'] = $name;
                $_SESSION['contactUser']['user']['email'] = ($email!=null) ? $email : $_SESSION['contactUser']['user']['email'];
                echo json_encode($response);
            } else {
                // user failed to store
                $response["success"] = 0;
                $response["msg"] = "No se pudo modificar los datos :-c , intentalo otra vez";
                echo json_encode($response);
            }
        }
  break;
 case 'foto':
      $error_message[0] = "Unknown problem with upload.";
      $error_message[1] = "Uploaded file too large (load_max_filesize).";
      $error_message[2] = "Uploaded file too large (MAX_FILE_SIZE).";
      $error_message[3] = "File was only partially uploaded.";
      $error_message[4] = "Choose a file to upload.";

      $upload_dir  = DOCUMENT_ROOT.'images/contact/';
      $num_files = count($_FILES['user_file']['name']);

      for ($i=0; $i < $num_files; $i++) {
          //$upload_file = $upload_dir . basename($_FILES['user_file']['name'][$i]);
          $upload_file = $upload_dir . $_SESSION['contactUser']['user']['uid'] . ".jpg";

          if (!preg_match("/(gif|jpg|jpeg|png)$/",strtolower($_FILES['user_file']['name'][$i]))) {
              $response["success"] = 0;
              $response["msg"] = "I asked for an image...";
              echo json_encode($response);
          } else {
              if (is_uploaded_file($_FILES['user_file']['tmp_name'][$i])) {
                  if (move_uploaded_file($_FILES['user_file']['tmp_name'][$i], $upload_file)) {
                      $thumb = PhpThumbFactory::create($upload_file);
                      $thumb->resize(40, 40);
                      if($thumb->save($upload_dir . "min/"  . $_SESSION['contactUser']['user']['uid'] . ".jpg", 'jpg')){
                        $response["success"] = 1;
                        $response["msg"] = "nueva foto de perfil!! XD";
                        echo json_encode($response);
                      }else{
                        unlink($upload_file);
                        $response["success"] = 0;
                        $response["msg"] = "no se pudo cambiar la foto de perfil :(";
                        echo json_encode($response);
                      }
                      
                  } else {
                      $response["success"] = 0;
                      $response["msg"] = $error_message[$_FILES['user_file']['error'][$i]];
                      echo json_encode($response);
                  }
              } else {
                  $response["success"] = 0;
                  $response["msg"] = $error_message[$_FILES['user_file']['error'][$i]];
                  echo json_encode($response);
              }    
          }
      }//FIN FOR
 break;
 case 'bajaCuenta':
    $user = new Users();
    $resUser = $user -> bajaCuenta($_SESSION['contactUser']['user']['uid']);
    if ($resUser != false) {
         
          $response["success"] = 1;
          $response["msg"] = "baja de cuenta correcta!";
          $response["url"] = ROOT;
          unset($_SESSION['contactUser']);
          echo json_encode($response);

      } else {
            // user not found
            // echo json with error = 1
            $response["success"] = 0;
            $response["msg"] = "no se pudo realizar la baja de la cuenta!, intentelo otra vez :(";
            echo json_encode($response);
      }
 break;
 case 'logout':
    unset($_SESSION['contactUser']);
    /* Redirect browser */
    header("Location:" . ROOT);
    /* Make sure that code below does not get executed when we redirect. */
    exit;
 break;
 case 'verificacion':
    $email = $_POST['email'];
    $codVr = $_POST['codVr'];
    $user = new Users();
    $resUser = $user -> verficacionDeCuenta($email, $codVr);
    if ($resUser != false) {
         
          $response["success"] = 1;
          $response["msg"] = "verificación de cuenta correcta! :)";
          $response["lUser"] = $_SESSION['contactUser']['user']['uid'];
          $response["url"] = ROOT;
          $_SESSION['contactUser']["user"]["verificado"] = 1;

          echo json_encode($response);

      } else {
            // user not found
            // echo json with error = 1
            $response["success"] = 0;
            $response["msg"] = "no se pudo realizar la verificaión!, intentelo otra vez :(";
            echo json_encode($response);
      }
 break;
 case 'allusers':
      $usuarioLocal = "";
      if(isset($_POST['usuarioLocal']) && ($_POST['usuarioLocal'] == $_SESSION['contactUser']['user']['uid']) ){
         $usuarioLocal = $_POST['usuarioLocal'];
         $user = new Users();
         echo $user -> getAllUsers($usuarioLocal);
      }else{
        $response["success"] = 0;
        $response["msg"] = "se ha producido un error inesperado... :(";
        echo json_encode($response);
      }
  break;
 case 'search':
      $userRg = new Users();
      // Request type is Register new user
        $name = $_POST['query'];
        echo $userRg -> search($name,$_SESSION["contactUser"]["user"]["name"]);
  break;
  case 'enviarsolicitud':
      $solicitud = new Solicitudes();
      $contacto = new Contactos();
      $remite = "";
      $destinatario = "";
      if(isset($_POST['remite']) && ($_POST['remite'] == $_SESSION['contactUser']['user']['uid']) ){
        $remite = $_POST['remite'];
        if(isset($_POST['destinatario']))
        $destinatario = $_POST['destinatario'];

        $dataValidaSol = $solicitud -> getValidaSolicitud($remite, $destinatario);
        $dataValidaCon = $contacto -> getValidaContacto($remite, $destinatario);
        if($dataValidaSol==false && $dataValidaCon==false){
           echo $solicitud->guardarSolicitud($remite, $destinatario);
        }else{
          $response["success"] = "2";  
          echo json_encode($response);
        }
      }else{
        $response["success"] = 0;
        $response["msg"] = "se ha producido un error inesperado... :(";
        echo json_encode($response);
      }
             
  break;
  case 'getSolicitudes':
      $usuarioLocal = "";
      if(isset($_POST['usuarioLocal']) && ($_POST['usuarioLocal'] == $_SESSION['contactUser']['user']['uid']) ){
        $usuarioLocal = $_POST['usuarioLocal'];
        $solicitud = new Solicitudes();
        echo $solicitud -> getAllSolicitudes($usuarioLocal);
      }else{
        $response["success"] = 0;
        $response["msg"] = "se ha producido un error inesperado... :(";
        echo json_encode($response);
      }
         
  break;
  case 'getSolicitudesRec':
      $usuarioLocal = "";
      if(isset($_POST['usuarioLocal']) && ($_POST['usuarioLocal'] == $_SESSION['contactUser']['user']['uid']) ){
        $usuarioLocal = $_POST['usuarioLocal'];
        $solicitud = new Solicitudes();
        echo $solicitud -> getAllSolicitudesRec($usuarioLocal);
      }else{
        $response["success"] = 0;
        $response["msg"] = "se ha producido un error inesperado... :(";
        echo json_encode($response);
      }
         
  break;
  case 'numeroSolicitudes':
      $usuarioLocal = "";
      if(isset($_POST['usuarioLocal']) && ($_POST['usuarioLocal'] == $_SESSION['contactUser']['user']['uid']) ){
         $usuarioLocal = $_POST['usuarioLocal'];
         $solicitud = new Solicitudes();
         echo $solicitud -> getNumeroSolicitudes($usuarioLocal);
      }else{
        $response["success"] = 0;
        $response["msg"] = "se ha producido un error inesperado... :(";
        echo json_encode($response);
      }
  break;
  case 'borrarsolicitud':
      $delSolicitud = null;
      if(isset($_POST['solicitud']))
         $delSolicitud = $_POST['solicitud'];
      $solicitud = new Solicitudes();
      echo $solicitud -> borrarSolicitud($delSolicitud);
  break;
  case 'aceptarsolicitud':
      $solicitudId = null;
      $res = null;
      if(isset($_POST['solicitudId']))
         $solicitudId = $_POST['solicitudId'];
      $contacto = new Contactos();
      $solicitud = new Solicitudes();
      $data = $solicitud -> getSolicitud($solicitudId);
      if($data != false){
          $res = $contacto -> guardarContacto($data['remite'], $data['destinatario']);
          if($res != false){
             echo $solicitud -> borrarSolicitud($solicitudId);
          }else{
            $response = array("success" => "0", "err" => "no tag");
            echo json_encode($response);
          } 
      }else{
        $response = array("success" => "0", "err" => "no tag");
        echo json_encode($response);
      }
  break;
  case 'submitmsg':
     $ifilter = new InputFilter(array('a'), array('href'));
     $mensaje = new Mensajes();
     $remite = "";
     $destinatario = "";
     $contenido ="";
     if(isset($_POST['remite']) && ($_POST['remite'] == $_SESSION['contactUser']['user']['uid']) ){
        $remite = $_POST['remite'];
        if(isset($_POST['destinatario']))
           $destinatario = $_POST['destinatario'];
        if(isset($_POST['contenido']))
           $contenido = $ifilter->process($_POST['contenido']);

        echo $mensaje -> guardarMensaje($remite,$destinatario,$contenido);
     }else{
        $response["success"] = 0;
        $response["msg"] = "se ha producido un error inesperado... :(";
        echo json_encode($response);
     }
  break;
  case 'getMensajes':
       $mensaje = new Mensajes();
       $remite = "";
       $destinatario = "";
       $limit = null;
       if(isset($_POST['remite']) && ($_POST['remite'] == $_SESSION['contactUser']['user']['uid']) ){
          $remite = $_POST['remite'];
           if(isset($_POST['destinatario']))
              $destinatario = $_POST['destinatario'];
           if(isset($_POST['limit']))
              $limit = $_POST['limit'];
           $gmt = $_SESSION['contactUser']["user"]["GMT"];
           
           $vVisto = $mensaje -> setVisto($destinatario,$remite);
           echo $mensaje -> getAllMensajes($remite,$destinatario,$gmt,$limit);
       }else{
        $response["success"] = 0;
        $response["msg"] = "se ha producido un error inesperado... :(";
        echo json_encode($response);
       }
  break;
  case 'getNumMensajes':
       $mensaje = new Mensajes();
       $remite = "";
       $destinatario = "";
       if(isset($_POST['destinatario']) && ($_POST['destinatario'] == $_SESSION['contactUser']['user']['uid']) ){
          $destinatario = $_POST['destinatario'];
          if(isset($_POST['remite']))
             $remite = $_POST['remite'];
       
          echo $mensaje -> getVisto($remite,$destinatario,1);
       }else{
        $response["success"] = 0;
        $response["msg"] = "se ha producido un error inesperado... :(";
        echo json_encode($response);
       }
  break;
 default:
  $response = array("success" => "0", "err" => "no tag");
  echo json_encode($response);
  break;
}
?>