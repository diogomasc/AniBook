<?php

require_once("globals.php");
require_once("db.php");
require_once("models/Movie.php");
require_once("models/Review.php");
require_once("models/Message.php");
require_once("dao/UserDAO.php");
require_once("dao/MovieDAO.php");
require_once("dao/ReviewDAO.php");

// Inicialização de objetos DAO e Message
$message = new Message($BASE_URL);
$userDao = new UserDAO($conn, $BASE_URL);
$movieDao = new MovieDAO($conn, $BASE_URL);
$reviewDao = new ReviewDAO($conn, $BASE_URL);

// Obtendo o tipo de requisição
$type = filter_input(INPUT_POST, "type");

// Verificando se o usuário está autenticado
$userData = $userDao->verifyToken();

if ($type === "create") {
  handleReviewCreation($userData, $movieDao, $reviewDao, $message);
} else {
  $message->setMessage("Tipo de requisição inválido!", "error", "index.php");
}

function handleReviewCreation($userData, $movieDao, $reviewDao, $message)
{
  // Obtendo os dados da avaliação do formulário
  $rating = filter_input(INPUT_POST, "rating");
  $review = filter_input(INPUT_POST, "review");
  $movies_id = filter_input(INPUT_POST, "movies_id");

  // Verificando se todos os campos necessários estão preenchidos
  if (empty($rating) || empty($review) || empty($movies_id)) {
    $message->setMessage("Você precisa inserir a nota e o comentário!", "error", "back");
    return;
  }

  // Verificando se o filme existe
  $movieData = $movieDao->findById($movies_id);
  if (!$movieData) {
    $message->setMessage("Filme não encontrado!", "error", "index.php");
    return;
  }

  // Criando o objeto de avaliação
  $reviewObject = new Review();
  $reviewObject->rating = $rating;
  $reviewObject->review = $review;
  $reviewObject->movies_id = $movies_id;
  $reviewObject->users_id = $userData->id;

  // Salvando a avaliação no banco de dados
  $reviewDao->create($reviewObject);
}
