<?php
// Incluindo cabeçalho e arquivos necessários
require_once("templates/header.php");
require_once("models/Movie.php");
require_once("dao/MovieDAO.php");
require_once("dao/ReviewDAO.php");
require_once("models/User.php");
require_once('dao/UserDAO.php');
require_once("get_youtube_video_id.php");

// Obtendo o ID do filme da requisição
$id = filter_input(INPUT_GET, "id");

$movie;

// Inicializando objetos DAO
$movieDao = new MovieDAO($conn, $BASE_URL);
$reviewDao = new ReviewDAO($conn, $BASE_URL);

// Verificando se o ID está vazio
if (empty($id)) {
  // Se estiver vazio, exibir mensagem de erro e redirecionar para a página inicial
  $message->setMessage("O anime não foi encontrado!", "error", "index.php");
} else {
  // Buscando o filme pelo ID
  $movie = $movieDao->findById($id);

  if (!$movie) {
    // Se o filme não for encontrado, exibir mensagem de erro e redirecionar para a página inicial
    $message->setMessage("O anime não foi encontrado!", "error", "index.php");
  }
}

// Verificando se há imagem de capa do filme
if ($movie->image == "") {
  $movie->image = "movie_cover.jpg";
}

$userOwnsMovie = false;

// Verificando se o usuário está logado e se é o proprietário do filme
if (!empty($userData)) {
  if ($userData->id === $movie->users_id) {
    $userOwnsMovie = true;
  }

  // Verificando se o usuário já avaliou o filme
  $alreadyReviewed = $reviewDao->hasAlreadyReviewed($id, $userData->id);
}

// Obtendo avaliações do filme
$movieReviews = $reviewDao->getMoviesReview($movie->id);
?>

<style>
.responsive-iframe-container {
    position: relative;
    overflow: hidden;
    padding-top: 56.25%; /* Aspect Ratio (height / width * 100%) */
    margin-top: 20px;
    margin-bottom: 20px;
    border-radius: 10px;
}

.responsive-iframe-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
</style>

<div id="main-container" class="container-fluid">
  <div class="row">
    <!-- Detalhes do filme -->
    <div class="offset-md-1 col-md-6 movie-container">
      <h1 class="page-title" style="overflow-wrap: break-word;"><?= $movie->title ?></h1>
      <p class="movie-details">
        <span>Duração: <?= empty(trim($movie->length)) ? 'Não informado' : $movie->length ?></span>
        <span class="pipe"></span>
        <span><?= $movie->category ?></span>
        <span class="pipe"></span>
        <span>Ano: <?= $movie->ano == 0 ? 'Não informado' : $movie->ano ?></span>
        <span class="pipe"></span>
        <span><i class="fas fa-star"></i> <?= $movie->rating ?></span>
      </p>
      <!-- Exibindo trailer, se disponível -->
      <!-- Exibindo trailer, se disponível -->
<?php if (!empty($movie->trailer)) : ?>
    <?php
    // Extrai o ID do vídeo do link do YouTube
    $video_id = get_youtube_video_id($movie->trailer);

    // Constrói o link de incorporação
    $embed_link = "https://www.youtube.com/embed/" . $video_id;
    ?>
    <div class="responsive-iframe-container">
      <iframe src="<?= $embed_link ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
<?php else : ?>
    <p>Não foi adicionado trailer.</p>
<?php endif; ?>
      <!-- Descrição do filme -->
      <p style=" overflow-wrap: break-word;"><?= $movie->description ?></p>
    </div>
    <!-- Imagem do filme e informações de envio -->
    <div class="col-md-4 movie-container send-from">
      <div class="movie-image-container" style="background-image: url('<?= $BASE_URL ?>img/movies/<?= $movie->image ?>')"></div>
      <p class="movie-details" style="display: inline-block; width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 10px;">Enviado por:
        <a href="<?= $BASE_URL ?>profile.php?id=<?= $movie->users_id ?> ">
          <?php
          // Obtendo informações do usuário que enviou o filme
          $userDAO = new UserDAO($conn, $BASE_URL);
          $user = $userDAO->findById($movie->users_id);
          if ($user) {
            echo $user->getFullName($user);
          } else {
            echo "Usuário não encontrado";
          }
          ?>
        </a>
      </p>
    </div>
    <!-- Avaliações do filme -->
    <div class="offset-md-1 col-md-10" id="reviews-container">
      <h3 id="reviews-title">Avaliações:</h3>
      <!-- Formulário de avaliação, se o usuário estiver logado e não for o proprietário do filme -->
      <?php if (!empty($userData) && !$userOwnsMovie && !$alreadyReviewed) : ?>
        <div class="col-md-12" id="review-form-container">
          <h4>O que acha dessa obra? Faça seu com comentário!</h4>
          <p class="page-description">Preencha o formulário com a nota e comentário sobre o anime</p>
          <form action="<?= $BASE_URL ?>review_process.php" id="review-form" method="POST">
            <input type="hidden" name="type" value="create">
            <input type="hidden" name="movies_id" value="<?= $movie->id ?>">
            <div class="form-group">
              <label for="rating">Nota:</label>
              <select name="rating" id="rating" class="form-control">
                <option value="">Selecione</option>
                <!-- Opções de avaliação -->
                <option value="10">10</option>
                <option value="9">9</option>
                <option value="8">8</option>
                <option value="7">7</option>
                <option value="6">6</option>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
              </select>
            </div>
            <div class="form-group">
              <label for="review">Seu comentário:</label>
              <textarea name="review" id="review" rows="3" class="form-control" placeholder="O que você achou do anime?"></textarea>
            </div>
            <input type="submit" class="btn card-btn" value="Enviar comentário">
          </form>
        </div>
      <?php endif; ?>
      <!-- Mensagem se não houver comentários -->
      <?php if (count($movieReviews) == 0) : ?>
        <?php if ($userOwnsMovie) : ?>
          <!--  Mensagem se o usuário for o proprietário do filme -->
          <p class="empty-list" style="margin-top: 20px; margin-bottom: 20px;">Ainda não há comentários na sua postagem! Não desanime 'u'</p>
        <?php elseif (!empty($userData)) : ?>
          <!-- Mensagem se o usuário estiver logado mas ainda não comentou -->
          <p class="empty-list" style="margin-top: 20px; margin-bottom: 20px;">Ainda não há comentários para esta obra...<br>Seja o primeiro a comentar!</p>
        <?php else : ?>
          <!-- Mensagem se o usuário não estiver logado (for um visitante) -->
          <p class="empty-list" style="margin-top: 20px; margin-bottom: 20px;">Ainda não há comentários para esta obra... <br>Crie uma conta para poder comentar!</p>
        <?php endif; ?>
      <?php endif; ?>
      <!-- Listagem de comentários -->
      <?php foreach ($movieReviews as $review) : ?>
        <?php require("templates/user_review.php"); ?>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php
// Incluindo rodapé
require_once("templates/footer.php");
?>
<script>
  // Validação do formulário de avaliação
  const reviewForm = document.getElementById("review-form");
  const ratingSelect = document.getElementById("rating");
  const reviewTextarea = document.getElementById("review");

  reviewForm.addEventListener("submit", function(event) {
    if (ratingSelect.value === "" || reviewTextarea.value === "") {
      event.preventDefault();
      alert("Você precisa preencher a nota e o comentário!");
    }
  });

  // Confirmação antes de enviar o formulário
  reviewForm.addEventListener("submit", function(event) {
    if (ratingSelect.value !== "" && reviewTextarea.value !== "") {
      if (!confirm("Deseja enviar o comentário?")) {
        event.preventDefault();
      }
    }
  });

  // Exibir mensagem de sucesso, se houver
  const urlParams = new URLSearchParams(window.location.search);
  const success = urlParams.get('success');

  if (success) {
    alert("Comentário enviado com sucesso!");
  }
</script>