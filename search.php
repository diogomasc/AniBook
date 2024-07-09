<?php
// Incluindo cabeçalho e MovieDAO
require_once("templates/header.php");
require_once("dao/MovieDAO.php");

// Instanciando MovieDAO
$movieDao = new MovieDAO($conn, $BASE_URL);

// Obtendo parâmetro de busca
$q = filter_input(INPUT_GET, "q");

// Verificando se há uma consulta válida
if ($q !== null && $q !== "") {
  // Verificando se a consulta é numérica
  if (is_numeric($q)) {
    // Buscando filmes pelo ano
    $movies = $movieDao->findByAno($q);
    $searchType = "ano";
  } else {
    // Buscando filmes pelo título
    $movies = $movieDao->findByTitle($q);
    $searchType = "título";
  }
} else {
  // Inicializando array de filmes e tipo de busca
  $movies = [];
  $searchType = null;
}
?>
<div id="main-container" class="container-fluid">
  <?php if ($searchType !== null) : ?>
    <!-- Exibindo título da busca -->
    <h2 class="section-title" id="search-title">
      <?php if ($searchType === "ano") : ?>
        Você está buscando pelo ano: <span id="search-result"><?= $q ?></span>
      <?php else : ?>
        Você está buscando por <span id="search-result"><?= $q ?></span>
      <?php endif; ?>
    </h2>
    <!-- Exibindo descrição da busca -->
    <p class="section-description">Resultados de busca retornados com base na sua pesquisa.</p>
    <!-- Exibindo container de filmes -->
    <div class="movies-container">
      <?php foreach ($movies as $movie) : ?>
        <!-- Incluindo cartão de filme -->
        <?php require("templates/movie_card.php"); ?>
      <?php endforeach; ?>
      <!-- Exibindo mensagem se não houver filmes -->
      <?php if (count($movies) === 0) : ?>
        <p class="empty-list">Não há animes para esta busca, <a href="<?= $BASE_URL ?>" class="back-link">voltar</a>.</p>
      <?php endif; ?>
    </div>
  <?php else : ?>
    <!-- Exibindo título e descrição quando não há busca -->
    <h2 class="section-title" id="search-title">:(</h2>
    <p class="section-description">Digite o nome do anime ou o ano de lançamento para realizar a busca!</p>
    <!-- Exibindo mensagem se não houver busca -->
    <p class="empty-list">Não há o que buscar, <a href="<?= $BASE_URL ?>" class="back-link">voltar</a>.</p>
  <?php endif; ?>
</div>
<?php
// Incluindo rodapé
require_once("templates/footer.php");
?>
