<?php
// Incluindo arquivos necessários
require_once('globals.php');
require_once('db.php');
require_once('models/User.php');
require_once('models/Message.php');
require_once('dao/UserDAO.php');

// Instanciando objetos necessários
$message = new Message($BASE_URL);
$userDao = new UserDAO($conn, $BASE_URL);

// Obtendo o tipo de requisição
$type = filter_input(INPUT_POST, "type");

// Verificando o tipo de requisição
if ($type === "update") {
    // Verificando o token de autenticação do usuário
    $userData = $userDao->verifyToken();

    // Obtendo dados do formulário
    $name = filter_input(INPUT_POST, "name");
    $last_name = filter_input(INPUT_POST, "last_name");
    $email = filter_input(INPUT_POST, "email");
    $bio = filter_input(INPUT_POST, "bio");

    // Validando comprimento do nome e sobrenome
    if (strlen($name) > 50 || strlen($last_name) > 50) {
        handleValidationFailure("O nome e o sobrenome não devem exceder 50 caracteres cada.");
    }

    // Validando comprimento da bio
    if (strlen($bio) > 521) {
        handleValidationFailure("A bio não deve exceder 521 caracteres.");
    }

    // Atualizando dados do usuário apenas se estiverem presentes no formulário
    if (!empty($name)) {
        $userData->name = $name;
    }
    if (!empty($last_name)) {
        $userData->last_name = $last_name;
    }
    if (!empty($email)) {
        $userData->email = $email;
    }
    if (!empty($bio)) {
        $userData->bio = $bio;
    }

    // Verificando e processando upload de imagem
    if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
        handleImageUpload($userDao, $userData);
    }

    // Atualizando usuário no banco de dados
    $userDao->update($userData);
} elseif ($type === "changepassword") {
    // Verificando token de autenticação e id do usuário
    $userData = $userDao->verifyToken();
    $id = $userData->id;

    // Obtendo dados do formulário
    $currentpassword = filter_input(INPUT_POST, "currentpassword");
    $password = filter_input(INPUT_POST, "password");
    $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

    // Verificando se a senha atual está correta
    if ($userDao->verifyPassword($id, $currentpassword)) {
        // Validando nova senha
        if (!isValidPassword($password, $confirmpassword)) {
            exit(); // Encerra o script se a validação falhar
        }

        // Criando objeto usuário e atualizando senha no banco de dados
        $user = new User();
        $finalPassword = $user->generatePassword($password);
        $user->password = $finalPassword;
        $user->id = $id;

        $userDao->changePassword($user);
    } else {
        $message->setMessage("Senha atual incorreta!", "error", "back");
    }
} elseif ($type === 'deleteaccount') {
    // Verificar token de autenticação e obter dados do usuário
    $userData = $userDao->verifyToken(true);

    // Verificar se a senha atual está correta usando password_verify
    $currentPassword = filter_input(INPUT_POST, "currentpassword");
    if (password_verify($currentPassword, $userData->password)) {
        // Tentar excluir a conta do usuário
        $result = $userDao->deleteUser($userData->id);

        // Verificar o resultado da exclusão
        if ($result) {
            // Exibir mensagem de sucesso e encerrar o script
            echo "<script>alert('Conta excluída com sucesso!')</script>";
            exit();
        } else {
            // Exibir mensagem de erro ao excluir conta e encerrar o script
            $message->setMessage("Erro ao excluir a conta. Por favor, tente novamente.", "error", "back");
            exit();
        }
    } else {
        // Exibir mensagem de senha incorreta e encerrar o script
        $message->setMessage("Senha incorreta. Por favor, tente novamente.", "error", "back");
        exit();
    }
} else {
    $message->setMessage("Informações inválidas!", "error", "index.php");
}

// Funções

function handleValidationFailure($errorMessage)
{
    global $message;
    $message->setMessage($errorMessage, "error", "back");
    exit();
}

function handleImageUpload($userDao, $userData)
{
    global $message;

    $image = $_FILES["image"];
    $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
    $jpgArray = ["image/jpeg", "image/jpg"];

    // Verificando tipo de imagem
    if (!in_array($image["type"], $imageTypes)) {
        handleValidationFailure("Tipo inválido de imagem, insira png ou jpg!");
    }

    // Criando imagem a partir do arquivo
    $imageFile = createImageFile($image, $jpgArray);

    // Verificando se a imagem é válida
    if (!$imageFile) {
        handleValidationFailure("Tipo inválido de imagem, insira png ou jpg!");
    }

    // Gerando nome único para a imagem e salvando no diretório
    $imageName = generateUniqueImageName();
    saveImageFile($imageFile, $imageName);

    // Atualizando dados do usuário com o nome da imagem
    $userData->image = $imageName;
}

function createImageFile($image, $jpgArray)
{
    return in_array($image["type"], $jpgArray) ? imagecreatefromjpeg($image["tmp_name"]) : imagecreatefrompng($image["tmp_name"]);
}

function generateUniqueImageName()
{
    $user = new User(); // Substitua pela classe correta se necessário
    return $user->imageGenerateName();
}

function saveImageFile($imageFile, $imageName)
{
    imagejpeg($imageFile, "./img/users/" . $imageName, 100);
}

function isValidPassword($password, $confirmpassword)
{
    global $message;

    // Verificando comprimento e padrões da senha
    if (strlen($password) < 6 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $message->setMessage("A senha deve ter no mínimo 6 caracteres e deve conter letras e números.", "error", "back");
        return false;
    }

    // Verificando se as senhas coincidem
    if ($password !== $confirmpassword) {
        $message->setMessage("As senhas não batem!", "error", "back");
        return false;
    }

    return true;
}
