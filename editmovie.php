<?php
// editmovie.php

// Inclui o cabeçalho da página e os arquivos necessários
require_once("templates/header.php");
require_once("models/User.php");
require_once("dao/UserDAO.php");
require_once("dao/MovieDAO.php");
require_once("category_movies.php");

// Inicializa objetos necessários e verifica se o usuário está logado
$user = new User();
$userDao = new UserDao($conn, $BASE_URL);
$userData = $userDao->verifyToken(true);
$movieDao = new MovieDAO($conn, $BASE_URL);

// Obtém o ID do filme da URL
$id = filter_input(INPUT_GET, "id");

// Verifica se o ID do filme está presente
if (empty($id)) {
  // Exibe uma mensagem de erro se o ID estiver vazio
  $message->setMessage("O anime não foi encontrado!", "error", "index.php");
} else {
  // Obtém as informações do filme pelo ID
  $movie = $movieDao->findById($id);

  // Verifica se o filme foi encontrado
  if (!$movie) {
    // Exibe uma mensagem de erro se o filme não for encontrado
    $message->setMessage("O anime não foi encontrado!", "error", "index.php");
  }
}

// Define uma imagem padrão se o filme não possuir uma
if ($movie->image == "") {
  $movie->image = "movie_cover.jpg";
}
?>
<!-- Conteúdo da página -->
<div id="main-container" class="container-fluid">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6 offset-md-1">
        <!-- Formulário para edição do filme -->
        <h1 style="overflow-wrap: break-word;"><?= $movie->title ?></h1>
        <p class="page-description">Altere os dados do anime no formulário abaixo:</p>
        <form id="edit-movie-form" action="<?= $BASE_URL ?>movie_process.php" method="POST" enctype="multipart/form-data">
          <!-- Campos ocultos para enviar o tipo de operação e o ID do filme -->
          <input type="hidden" name="type" value="update">
          <input type="hidden" name="id" value="<?= $movie->id ?>">
          <!-- Campos para editar título, imagem, duração, categoria, ano, trailer e descrição -->
          <div class="form-group">
            <label for="title">Título:</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Digite o título do anime" value="<?= $movie->title ?>">
            <span id="title_contator"></span>
          </div>
          <div class="form-group">
            <label for="image">Imagem:</label>
            <input type="file" class="form-control-file" name="image" id="image">
          </div>
          <div class="form-group">
            <label for="length">Duração:</label>
            <input type="text" class="form-control" id="length" name="length" placeholder="Digite a duração do anime" value="<?= $movie->length ?>">
          </div>
          <div class="form-group">
            <label for="category">Categoria:</label>
            <select name="category" id="category" class="form-control">
              <option value="">Selecione</option>
              <?php foreach (MovieCategory::getCategories() as $category) : ?>
                <option value="<?= $category ?>" <?= MovieCategory::isSelected($category, $movie->category) ?>>
                  <?= $category ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="ano">Ano:</label>
            <input type="number" class="form-control" id="ano" name="ano" placeholder="Digite o ano do anime" value="<?= $movie->ano ?>">
          </div>
          <div class="form-group">
            <label for="trailer">Trailer:</label>
            <input type="text" class="form-control" id="trailer" name="trailer" placeholder="Insira o link do trailer" value="<?= $movie->trailer ?>">
          </div>
          <div class="form-group">
            <label for="description">Descrição:</label>
            <textarea name="description" id="description" rows="5" class="form-control" style="min-height: 120px; max-height: 450px;" placeholder="Descreva o anime..."><?= $movie->description ?></textarea>
            <span id="description_contator"></span>
          </div>
          <!-- Botão para atualizar os dados -->
          <input type="submit" class="btn card-btn" value="Atualizar">
        </form>
      </div>
      <!-- Exibição da imagem do filme -->
      <div class="col-md-3">
        <div class="movie-image-container" style="background-image: url('<?= $BASE_URL ?>img/movies/<?= $movie->image ?>')"></div>
      </div>
    </div>
  </div>
</div>


<!-- Script para validação dos campos e confirmação de atualização -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const formFields = document.querySelectorAll("input:not([type='file']), textarea");

    formFields.forEach((field) => {
      field.addEventListener("focus", function() {
        field.style.backgroundColor = "#e8f0fe";
      });

      field.addEventListener("blur", function() {
        field.style.backgroundColor = "";
      });
    });
  });

  // Contador de caracteres para o campo de título
  const titleInput = document.getElementById("title");
  const titleCounterSpan = document.getElementById("title_contator");

  titleInput.addEventListener("input", function() {
    let nameText = titleInput.value;
    let nameCharacters = nameText.length;

    if (nameCharacters > 100) {
      nameText = nameText.substring(0, 100);
      titleInput.value = nameText;
      nameCharacters = 100;
    }

    titleCounterSpan.innerText = `${nameCharacters}/100`;

    if (nameCharacters >= 100) {
      titleCounterSpan.style.color = "red";
    } else {
      titleCounterSpan.style.color = "gray";
    }
  });

  // Contador de caracteres para o campo de descrição
  const descriptionTextarea = document.getElementById("description");
  const descriptionCounterSpan = document.getElementById("description_contator");

  descriptionTextarea.addEventListener("input", function() {
    let texto = descriptionTextarea.value;
    let caracteres = texto.length;

    if (caracteres > 1024) {
      texto = texto.substring(0, 1024);
      descriptionTextarea.value = texto;
      caracteres = 1024;
    }

    descriptionCounterSpan.innerText = `${caracteres}/${1024}`;

    if (caracteres >= 1024) {
      descriptionCounterSpan.style.color = "red";
    } else if (caracteres < 960) {
      descriptionCounterSpan.style.color = "gray";
    } else {
      descriptionCounterSpan.style.color = "orange";
    }
  });

  // Confirmação antes de enviar o formulário de atualização
  const form = document.getElementById("edit-movie-form");

  form.addEventListener("submit", function(event) {
    event.preventDefault();

    const r = confirm("Deseja realmente atualizar os dados do anime?");

    if (r === true) {
      form.submit();
    }
  });
</script>

<?php
// Inclui o rodapé da página
require_once("templates/footer.php");
?>