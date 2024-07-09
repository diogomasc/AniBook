<?php
// Incluindo cabeçalho e arquivos necessários
require_once("templates/header.php");
require_once("models/User.php");
require_once("dao/UserDAO.php");
require_once("dao/MovieDAO.php");

// Inicialização de objetos DAO
$user = new User();
$userDao = new UserDAO($conn, $BASE_URL);
$movieDao = new MovieDAO($conn, $BASE_URL);

// Obtendo o ID do usuário da requisição
$id = filter_input(INPUT_GET, "id");

// Verificando se o ID está vazio
if (empty($id)) {
  // Se o ID estiver vazio, verificar se o usuário está logado
  if (!empty($userData)) {
    $id = $userData->id;
  } else {
    // Se não estiver logado, exibir mensagem de erro e redirecionar para a página inicial
    $message->setMessage("Usuário não encontrado!", "error", "index.php");
  }
} else {
  // Se o ID não estiver vazio, procurar o usuário pelo ID
  $userData = $userDao->findById($id);
  if (!$userData) {
    // Se o usuário não for encontrado, exibir mensagem de erro e redirecionar para a página inicial
    $message->setMessage("Usuário não encontrado!", "error", "index.php");
  }
}

// Obtendo o nome completo do usuário
$fullName = $user->getFullName($userData);

// Verificando se o usuário possui uma imagem de perfil
if ($userData->image == "") {
  $userData->image = "user.png";
}

// Obtendo os filmes enviados pelo usuário
$userMovies = $movieDao->getMoviesByUserId($id);
?>

<div id="main-container" class="container-fluid">
  <div class="col-md-8 offset-md-2">
    <div class="row profile-container">
      <div class="col-md-12 about-container">
        <h1 class="page-title" style="overflow-wrap: break-word;"><?= $fullName ?></h1>
        <div id="profile-image-container" class="profile-image" style="background-image: url('<?= empty($userData->image) ? $BASE_URL . "img/users/user.png" : $BASE_URL . "img/users/" . $userData->image ?>')"></div>

        <h3 class="about-title">Sobre:</h3>
        <?php if (!empty($userData->bio)) : ?>
          <!-- Exibindo biografia do usuário -->
          <p class="profile-description" style="overflow-wrap: break-word;"><?= $userData->bio ?></p>
        <?php else : ?>
          <!-- Exibindo mensagem se o usuário não tiver biografia -->
          <p class="profile-description">O usuário ainda não escreveu nada aqui...</p>
        <?php endif; ?>
      </div>
      <div class="col-md-12 added-movies-container">
        <h3>Animes que enviou:</h3>
        <div class="movies-container row">
          <?php foreach ($userMovies as $movie) : ?>
            <div class="col-sm-12 col-md-6 col-lg-4" style="margin-bottom: 25px;">
              <!-- Incluindo cartão de filme -->
              <?php require("templates/movie_card.php"); ?>
            </div>
          <?php endforeach; ?>
          <?php if (count($userMovies) === 0) : ?>
            <!-- Exibindo mensagem se o usuário não tiver enviado nenhum filme -->
            <p class="empty-list">O usuário ainda não enviou animes.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// Incluindo rodapé
require_once("templates/footer.php");
?>