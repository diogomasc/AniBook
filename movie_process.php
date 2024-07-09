<?php

// Incluindo arquivos necessários
require_once("globals.php");
require_once("db.php");
require_once("models/Movie.php");
require_once("models/Message.php");
require_once("dao/UserDAO.php");
require_once("dao/MovieDAO.php");

// Instanciando objeto de mensagem e objetos DAO
$message = new Message($BASE_URL);
$userDao = new UserDAO($conn, $BASE_URL);
$movieDao = new MovieDAO($conn, $BASE_URL);

// Obtendo o tipo de ação a ser realizada (criação, atualização ou exclusão)
$type = filter_input(INPUT_POST, "type");

// Verificando o token do usuário
$userData = $userDao->verifyToken();

// Verificando o tipo de ação e chamando a função correspondente
if ($type === "create") {
  handleMovieCreation($userData->id, $movieDao, $message);
} else if ($type === "delete") {
  handleMovieDeletion($userData->id, $movieDao, $message);
} else if ($type === "update") {
  handleMovieUpdate($userData->id, $movieDao, $message);
} else {
  // Exibindo mensagem de erro se o tipo de ação for desconhecido
  $message->setMessage("Informações inválidas!", "error", "index.php");
}

// Função para validar os dados do filme
function isValidMovieData($title, $description, $category)
{
  global $message;

  if (strlen($title) > 100) {
    $message->setMessage("O título do anime não deve exceder 100 caracteres!", "error", "back");
    return false;
  }
  if (strlen($description) > 512) {
    $message->setMessage("A descrição do anime não deve exceder 512 caracteres!", "error", "back");
    return false;
  }
  if (empty($title) || empty($description) || empty($category)) {
    $message->setMessage("Você precisa adicionar pelo menos: título, descrição e categoria!", "error", "back");
    return false;
  }
  return true;
}

// Função para criar uma instância de filme
function createMovieInstance($title, $description, $trailer, $category, $length, $ano, $userId)
{
  $movie = new Movie();
  $movie->title = $title;
  $movie->description = $description;
  $movie->trailer = $trailer;
  $movie->category = $category;
  $movie->length = $length;
  $movie->ano = $ano;
  $movie->users_id = $userId;
  return $movie;
}

// Função para verificar se uma imagem foi enviada
function isImageUploaded($image)
{
  return isset($image) && !empty($image["tmp_name"]);
}

// Função para processar a imagem do filme
function processMovieImage($movie, $image)
{
  global $message;

  $allowedImageTypes = ["image/jpeg", "image/jpg", "image/png"];
  $jpgImageTypes = ["image/jpeg", "image/jpg"];

  if (!in_array($image["type"], $allowedImageTypes)) {
    $message->setMessage("Tipo inválido de imagem, insira png ou jpg!", "error", "back");
    return;
  }

  $imageFile = createImageFile($image, $jpgImageTypes);
  $imageName = $movie->imageGenerateName();

  saveImageFile($imageFile, $imageName);
  $movie->image = $imageName;
}

// Função para criar um arquivo de imagem
function createImageFile($image, $jpgImageTypes)
{
  if (in_array($image["type"], $jpgImageTypes)) {
    return imagecreatefromjpeg($image["tmp_name"]);
  } else {
    return imagecreatefrompng($image["tmp_name"]);
  }
}

// Função para salvar o arquivo de imagem
function saveImageFile($imageFile, $imageName)
{
  $imagePath = "./img/movies/" . $imageName;
  imagejpeg($imageFile, $imagePath, 100);
}

// Função para tratar a criação de filmes
function handleMovieCreation($userId, $movieDao, $message)
{
  $title = filter_input(INPUT_POST, "title");
  $description = filter_input(INPUT_POST, "description");
  $trailer = filter_input(INPUT_POST, "trailer");
  $category = filter_input(INPUT_POST, "category");
  $length = filter_input(INPUT_POST, "length");
  $ano = filter_input(INPUT_POST, "ano");

  if (!isValidMovieData($title, $description, $category)) {
    return;
  }

  $movie = createMovieInstance($title, $description, $trailer, $category, $length, $ano, $userId);

  if (isImageUploaded($_FILES["image"])) {
    processMovieImage($movie, $_FILES["image"]);
  }

  $movieDao->create($movie);
}

// Função para verificar se o usuário é o proprietário do filme
function isMovieOwner($movie, $userId)
{
  return $movie->users_id === $userId;
}

// Função para exibir mensagens de erro
function displayErrorMessage($message, $errorMessage)
{
  $message->setMessage($errorMessage, "error", "index.php");
}

// Função para tratar a exclusão de filmes
function handleMovieDeletion($userId, $movieDao, $message)
{
  $movieId = filter_input(INPUT_POST, "id");

  $movie = $movieDao->findById($movieId);

  if ($movie) {
    if (isMovieOwner($movie, $userId)) {
      $movieDao->destroy($movie->id);
    } else {
      displayErrorMessage($message, "Informações inválidas!");
    }
  } else {
    displayErrorMessage($message, "Informações inválidas!");
  }
}

// Função para coletar os dados do filme
function collectMovieData()
{
  return (object) [
    'title' => filter_input(INPUT_POST, "title"),
    'description' => filter_input(INPUT_POST, "description"),
    'trailer' => filter_input(INPUT_POST, "trailer"),
    'category' => filter_input(INPUT_POST, "category"),
    'length' => filter_input(INPUT_POST, "length"),
    'ano' => filter_input(INPUT_POST, "ano"),
    'id' => filter_input(INPUT_POST, "id")
  ];
}

// Função para validar os dados do filme para atualização
function validateMovieData($movieData, $message)
{
  if (strlen($movieData->title) > 100) {
    displayErrorMessage($message, "O título não deve exceder 100 caracteres.");
    return false;
  }

  if (strlen($movieData->description) > 1024) {
    displayErrorMessage($message, "A descrição não deve exceder 1024 caracteres.");
    return false;
  }

  if (empty($movieData->title) || empty($movieData->description) || empty($movieData->category)) {
    displayErrorMessage($message, "Você precisa adicionar pelo menos: título, descrição e categoria!");
    return false;
  }

  return true;
}

// Função para atualizar os dados do filme
function updateMovieData($movie, $movieData, $message)
{
  $movie->title = $movieData->title;
  $movie->description = $movieData->description;
  $movie->trailer = $movieData->trailer;
  $movie->category = $movieData->category;
  $movie->length = $movieData->length;
  $movie->ano = $movieData->ano;
}

// Função para verificar se uma nova imagem foi enviada
function isNewImageUploaded()
{
  return isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"]);
}

// Função para processar a nova imagem do filme
function processNewImage($movie, $message)
{
  $image = $_FILES["image"];
  $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
  $jpgArray = ["image/jpeg", "image/jpg"];

  if (!in_array($image["type"], $imageTypes)) {
    displayErrorMessage($message, "Tipo inválido de imagem, insira png ou jpg!");
    return false;
  }

  $imageFile = createImageFile($image, $jpgArray);
  $imageName = generateUniqueImageName();
  saveImageFile($imageFile, $imageName);
  $movie->image = $imageName;

  return true;
}

// Função para gerar um nome único para a imagem
function generateUniqueImageName()
{
  $movie = new Movie();
  return $movie->imageGenerateName();
}

// Função para tratar a atualização de filmes
function handleMovieUpdate($userId, $movieDao, $message)
{
  $movieData = collectMovieData();

  if (!validateMovieData($movieData, $message)) {
    return;
  }

  $movie = $movieDao->findById($movieData->id);

  if (!$movie) {
    displayErrorMessage($message, "Informações inválidas!");
    return;
  }

  if (!isMovieOwner($movie, $userId)) {
    displayErrorMessage($message, "Informações inválidas!");
    return;
  }

  updateMovieData($movie, $movieData, $message);

  if (isNewImageUploaded()) {
    if (!processNewImage($movie, $message)) {
      return;
    }
  }

  $movieDao->update($movie);
}
