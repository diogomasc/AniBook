<?php
// Incluindo arquivos necessários e configurações
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once('globals.php');
require_once('db.php');
require_once('models/User.php');
require_once('models/Message.php');
require_once('dao/UserDAO.php');

require 'vendor/autoload.php'; // Carregar o autoloader do PHPMailer

// Instanciando objetos necessários
$message = new message($BASE_URL); // Objeto para mensagens ao usuário
$userDao = new UserDAO($conn, $BASE_URL); // Objeto DAO para acesso aos dados do usuário

// Obtendo o tipo de requisição (registro ou login)
$type = filter_input(INPUT_POST, "type");

function registerUser($email, $name, $lastname, $password, $confpassword, $userDao, $message)
{
    if (!validateRegistrationData($name, $lastname, $email, $password, $confpassword, $message)) {
        return;
    }

    if ($userDao->findByEmail($email) !== false) {
        $message->setMessage("Email já cadastrado", "error", "back");
        return;
    }

    $user = createUser($name, $lastname, $email, $password);
    $userDao->create($user, true);
    sendWelcomeEmail($email, $name, $lastname);

    $message->setMessage("Usuário registrado com sucesso", "success", "index.php");
}

function validateRegistrationData($name, $lastname, $email, $password, $confpassword, $message)
{
    if (!$name || !$lastname || !$email || !$password || !$confpassword) {
        $message->setMessage("Todos os campos são obrigatórios", "error", "back");
        return false;
    }
    if (strlen($name) > 50) {
        $message->setMessage("O nome não deve exceder 50 caracteres", "error", "back");
        return false;
    }
    if (strlen($lastname) > 50) {
        $message->setMessage("O sobrenome não deve exceder 50 caracteres", "error", "back");
        return false;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message->setMessage("Por favor, insira um email válido. Formato: algumacoisa@algumacoisa.com", "error", "back");
        return false;
    }
    if (strlen($password) < 6 || !preg_match("/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/", $password)) {
        $message->setMessage("A senha deve ter no mínimo 6 caracteres e conter letras e números", "error", "back");
        return false;
    }
    if ($password !== $confpassword) {
        $message->setMessage("Senhas não conferem", "error", "back");
        return false;
    }
    return true;
}

function createUser($name, $lastname, $email, $password)
{
    $user = new User();
    $user->name = $name;
    $user->lastname = $lastname;
    $user->email = $email;
    $user->password = $user->generatePassword($password);
    $user->token = $user->generateToken();
    return $user;
}

function sendWelcomeEmail($email, $name, $lastname)
{
    $mail = new PHPMailer(true);
    try {
        configureSMTP($mail);
        $mail->setFrom('dmascuniv@gmail.com', 'AniBook');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Bem-vindo ao AniBook!';
        $mail->Body = createWelcomeEmailBody($name, $lastname);
        $mail->send();
    } catch (Exception $e) {
        echo "<br>Mensagem não enviada. Erro: {$mail->ErrorInfo}";
    }
}

function configureSMTP($mail)
{
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dmascuniv@gmail.com';
    $mail->Password = 'vnfgfnfasetmwczo';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
}

function createWelcomeEmailBody($name, $lastname)
{
    return "<div style='text-align: center;'>
                <img src='img/anibook.svg' alt='AniBook Logo' style='width: 200px;'><br>
                Olá $name $lastname,<br><br>
                Seja bem-vindo ao AniBook! Estamos felizes por ter você conosco.<br><br>
                Atenciosamente,<br>
                Equipe do AniBook
            </div>";
}

function loginUser($email, $password, $userDao, $message)
{
    if (!validateLoginData($email, $password, $message)) {
        return;
    }

    if ($userDao->authenticateUser($email, $password)) {
        $message->setMessage("Seja bem-vindo!", "success", "index.php");
    } else {
        $message->setMessage("Usuário e/ou senha incorretos", "error", "back");
    }
}

function validateLoginData($email, $password, $message)
{
    if (!$email || !$password) {
        $message->setMessage("Por favor, preencha todos os campos", "error", "back");
        return false;
    }
    return true;
}

if ($type === "register") {
    registerUser(
        filter_input(INPUT_POST, "email"),
        filter_input(INPUT_POST, "nome"),
        filter_input(INPUT_POST, "lastname"),
        filter_input(INPUT_POST, "password"),
        filter_input(INPUT_POST, "confpassword"),
        $userDao,
        $message
    );
} elseif ($type === "login") {
    loginUser(
        filter_input(INPUT_POST, "email"),
        filter_input(INPUT_POST, "password"),
        $userDao,
        $message
    );
} else {
    $message->setMessage("Informações inválidas", "error", "index.php");
}
