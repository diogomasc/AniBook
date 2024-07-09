<?php

class Message
{

  private $url; // URL base para redirecionamento

  public function __construct($url)
  {
    $this->url = $url; // Define a URL base para redirecionamento ao instanciar a classe
  }

  // Método para definir uma mensagem e redirecionar para outra página
  public function setMessage($msg, $type, $redirect = "index.php")
  {
    // Define a mensagem e o tipo na sessão
    $_SESSION["msg"] = $msg;
    $_SESSION["type"] = $type;

    // Redireciona para a página especificada
    if ($redirect != "back") {
      header("Location: {$this->url}{$redirect}");
    } else {
      // Se o redirecionamento for "back", redireciona de volta para a página anterior
      header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
    exit; // Encerra o script após o redirecionamento
  }

  // Método para obter a mensagem armazenada na sessão
  public function getMessage()
  {
    // Verifica se há uma mensagem na sessão
    if (!empty($_SESSION["msg"])) {
      // Retorna a mensagem e o tipo como um array associativo
      return [
        "msg" => $_SESSION["msg"],
        "type" => $_SESSION["type"]
      ];
    } else {
      // Retorna falso se não houver mensagem na sessão
      return false;
    }
  }

  // Método para limpar a mensagem da sessão
  public function clearMessage()
  {
    // Limpa a mensagem e o tipo da sessão
    $_SESSION["msg"] = "";
    $_SESSION["type"] = "";
  }
}
