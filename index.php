<?php

require_once("models/Message.php");
require_once("templates/header.php");
require_once("dao/MovieDAO.php");

// Inicialização do objeto MovieDAO
$movieDao = new MovieDAO($conn, $BASE_URL);

// Obtendo todas as categorias
$categories = $movieDao->getAllCategories();

// Obtendo os últimos filmes adicionados
$latestMovies = $movieDao->getLatestMovies();
?>

<div id="main-container" class="container-fluid">

    <!-- Navbar de categorias -->
    <div id="navbar-categories">
        <?php foreach ($categories as $category) : ?>
            <a href="#<?php echo $category; ?>"><?php echo $category; ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Seção de Novos Animes -->
    <h2 class="section-title">Novos Animes</h2>
    <p class="section-description">Veja os comentários dos últimos animes adicionados no AniBook!</p>
    <div id="anime-container" class="movies-container">
        <?php foreach ($latestMovies as $movie) : ?>
            <?php require("templates/movie_card.php"); ?>
        <?php endforeach; ?>
        <?php if (count($latestMovies) === 0) : ?>
            <p class="empty-list">Ainda não há animes cadastrados!</p>
        <?php endif; ?>
    </div>

    <!-- Link de volta para o topo -->
    <a href="#main-navbar" class="back-to-top">
        <i class="fas fa-arrow-up"></i> Voltar para o topo
    </a>

    <br><br>

    <!-- Loop através das categorias -->
    <?php foreach ($categories as $category) : ?>
        <?php $movies = $movieDao->getMoviesByCategory($category); ?>
        <h2 class="section-title" id="<?php echo $category; ?>"><?php echo $category; ?></h2>
        <p class="section-description">Veja os melhores animes de <?php echo $category; ?></p>
        <div class="movies-container">
            <?php foreach ($movies as $movie) : ?>
                <?php require("templates/movie_card.php"); ?>
            <?php endforeach; ?>
            <?php if (count($movies) === 0) : ?>
                <p class="empty-list">Ainda não há animes de <?php echo $category; ?> cadastrados!</p>
            <?php endif; ?>
        </div>
        <!-- Link de volta para o topo -->
        <a href="#main-navbar" class="back-to-top">
            <i class="fas fa-arrow-up"></i> Voltar para o topo
        </a>

        <br>

    <?php endforeach; ?>

</div>

<?php require_once("templates/footer.php"); ?>