<?php

    require("./bibliotecas/phpMailer/DSNConfigurator.php");
    require("./bibliotecas/phpMailer/Exception.php");
    //require("./bibliotecas/phpMailer/OAuth.php");
    //require("./bibliotecas/phpMailer/OAuthTokenProvider.php");
    require("./bibliotecas/phpMailer/PHPMailer.php");
    require("./bibliotecas/phpMailer/POP3.php");
    require("./bibliotecas/phpMailer/SMTP.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    class Mesangem{
        private $para = null;
        private $assunto = null;
        private $mensagem = null;
        public $status  = array("codStatus" => null, "descStatus" => "");

        public function __get($attr){
            return $this->$attr;
        }
        public function __set($attr, $value){
            $this->$attr = $value;
        }

        public function __mensagemValida(){

            if(empty($this->para) || empty($this->assunto) || empty($this->mensagem)){
                return false;
                
            }else {
                return true;
            }
        }
    }

    $mensagem = new Mesangem();

    $mensagem->__set("para",$_POST["para"]);
    $mensagem->__set("assunto",$_POST["assunto"]);
    $mensagem->__set("mensagem",$_POST["mensagem"]);

    if(!$mensagem->__mensagemValida()){
        header("Location: index.php?erro");
        die();
    }

    $mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = false;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'email@valido.com';                     //SMTP username
    $mail->Password   = 'senha';                               //SMTP password
    $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('email@remetente.com', 'Remetente');
    $mail->addAddress($_POST["para"]);     //Add a recipient

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $_POST["assunto"];
    $mail->Body    = $_POST["mensagem"];
    $mail->AltBody = "Client não suporta essa mensagem";

    $mail->send();
    $mensagem->status["codStatus"] = 1;
    $mensagem->status["descStatus"] = "Envio realizado com sucesso!";

} catch (Exception $e) {
    $mensagem->status["codStatus"] = 2;
    $mensagem->status["descStatus"] = "Não foi possível realizar o envio do email. <br> ERROR: " . $mail->ErrorInfo;
}

//Feed back visual
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Enivar Email</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <?php
    if($mensagem->status["codStatus"] == 1) { ?>

        <div class="py-3 text-center">
            <img class="d-block mx-auto mb-2" src="../appEnviarEmail/imagens/logo.png" alt="" width="72" height="72">
            <h2>Send Mail</h2>
            <p class="lead">Seu app de envio de e-mails particular!</p>
        </div>

        <div class="container bg-white">
            <h1 class="text-success">Sucesso</h1>
            <p><?=$mensagem->status["descStatus"]?></p>
            <a href="index.php" class="btn btn-outline-success mt-3"> Conluir</a>
        </div>
    <?php } ?>

    <?php

    if($mensagem->status["codStatus"] == 2) { ?>
        <div class="py-3 text-center">
            <img class="d-block mx-auto mb-2" src="../appEnviarEmail/imagens/logo.png" alt="" width="72" height="72">
            <h2>Send Mail</h2>
            <p class="lead">Seu app de envio de e-mails particular!</p>
        </div>

        <div class="container">
            <h1 class="text-danger">Envio não realizado</h1>
            <p><?=$mensagem->status["descStatus"]?></p>
            <a href="index.php" class="btn btn-outline-danger mt-3"> Fechar</a>
        </div>
    <?php } ?>

</body>
</html>