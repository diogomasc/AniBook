<?php
// Incluindo o cabeçalho da página
include_once('templates/header.php');

// Requisitando os arquivos necessários
require_once('dao/UserDAO.php');
require_once('dao/MovieDAO.php');
require_once('models/User.php');

// Instanciando objetos e obtendo dados do usuário logado
$user = new User();
$userDao = new UserDAO($conn, $BASE_URL);
$movieDao = new MovieDAO($conn, $BASE_URL);
$userData = $userDao->verifyToken(true);

// Obtendo os filmes do usuário
$userMovies = $movieDao->getMoviesByUserId($userData->id);
?>

<style>
   /* Estilos para telas menores que 990px */
   @media (max-width: 990px) {
      .table-movie-title {
         width: 300px;
      }
   }

   /* Estilos para telas menores que 768px */
   @media (max-width: 768px) {
      .table-movie-title {
         width: 200px;
      }
   }

   /* Estilos para telas menores que 450px */
   @media (max-width: 450px) {
      .table-movie-title {
         width: 100px;
      }
   }
</style>

<div id="main-container" class="container-fluid">
   <div class="col-md-12">
      <!-- Título e descrição da seção -->
      <h2 class="section-title">Dashboard</h2>
      <p class="section-description">Adicione ou atualize as informações dos animes que você enviou</p>
      <!-- Botão para adicionar novo filme -->
      <div id="add-movie-container">
         <a href="<?= $BASE_URL ?>newmovie.php" class="btn card-btn">
            <i class="bi bi-cloud-plus"></i> Adicionar Anime
         </a>
      </div>
      <!-- Tabela de filmes do usuário -->
      <div id="movies-dashboard" class="table-responsive">
         <table class="table">
            <thead>
               <tr>
                  <th scope="col">#</th>
                  <th scope="col">Título</th>
                  <th scope="col">Nota</th>
                  <th scope="col" class="actions-column">Ações</th>
               </tr>
            </thead>
            <tbody>
               <!-- Loop através dos filmes do usuário -->
               <?php foreach ($userMovies as $movie) : ?>
                  <tr>
                     <td scope="row"><?= $movie->id ?></td>
                     <td>
                        <a href="<?= $BASE_URL ?>movie.php?id=<?= $movie->id ?>" class="table-movie-title" style="display: inline-block; width: 420px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                           <?= $movie->title ?>
                        </a>
                     </td>
                     <td>
                        <i class="bi bi-star"><?= $movie->rating ?></i>
                     </td>
                     <td class="actions-column">
                        <!-- Link para editar o filme -->
                        <a href="<?= $BASE_URL ?>editmovie.php?id=<?= $movie->id ?>" class="edit-btn">
                           <i class="bi bi-pencil"></i> Editar
                        </a>
                        <!-- Formulário para deletar o filme -->
                        <form action="<?= $BASE_URL ?>movie_process.php" method="POST" style="display: inline;">
                           <input type="hidden" name="type" value="delete">
                           <input type="hidden" name="id" value="<?= $movie->id ?>">
                           <button type="submit" class="delete-btn">
                              <i class="bi bi-file-earmark-x"></i> Deletar
                           </button>
                        </form>
                     </td>
                  </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
      </div>
   </div>
</div>

<?php
// Incluindo o rodapé da página
include_once('templates/footer.php');
?>

<!-- Script para confirmar a exclusão de filmes -->
<script>
   document.querySelectorAll('.delete-btn').forEach(button => {
      button.addEventListener('click', (e) => {
         e.preventDefault();
         if (confirm('Tem certeza que deseja deletar este anime?')) {
            e.target.closest('form').submit();
         }
      });
   });
</script>