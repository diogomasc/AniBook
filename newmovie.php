<?php
// newmovie.php

// Incluindo cabeçalho e arquivos necessários
require_once("templates/header.php");
require_once("models/User.php");
require_once("dao/UserDAO.php");
require_once("category_movies.php");

// Inicialização de objetos
$user = new User();
$userDao = new UserDao($conn, $BASE_URL);

// Verificação do token do usuário
// Verifica se o token do usuário é válido, permitindo o acesso apenas a usuários autenticados.
$userData = $userDao->verifyToken(true);
?>

<div id="main-container" class="container-fluid">
  <div class="col-md-6 offset-md-3 new-movie-container">
    <h1 class="page-title">Adicionar Anime</h1>
    <p class="page-description">Adicione sua crítica e compartilhe com o mundo!</p>
    <form action="<?= $BASE_URL ?>movie_process.php" id="add-movie-form" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="type" value="create">
      <!-- Formulário para adicionar um novo anime -->
      <div class="form-group">
        <label for="title">Título:</label>
        <input type="text" class="form-control" id="title" name="title" placeholder="Digite o título do seu anime">
        <span id="title_contator"></span>
      </div>
      <!-- Campo para adicionar imagem -->
      <div class="form-group">
        <label for="image">Imagem:</label>
        <input type="file" class="form-control-file" name="image" id="image">
      </div>
      <!-- Campo para adicionar a duração do anime -->
      <div class="form-group">
        <label for="length">Duração:</label>
        <input type="text" class="form-control" id="length" name="length" placeholder="Digite a duração do anime">
      </div>
      <!-- Campo para selecionar a categoria do anime -->
      <div class="form-group">
        <label for="category">Categoria:</label>
        <select name="category" id="category" class="form-control">
          <option value="">Selecione</option>
          <?php foreach (MovieCategory::getCategories() as $category) : ?>
            <option value="<?= $category ?>">
              <?= $category ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <!-- Campo para adicionar o ano do anime -->
      <div class="form-group">
        <label for="ano">Ano:</label>
        <input type="number" class="form-control" id="ano" name="ano" placeholder="Digite o ano do anime">
      </div>
      <!-- Campo para adicionar o link do trailer -->
      <div class="form-group">
        <label for="trailer">Trailer:</label>
        <input type="text" class="form-control" id="trailer" name="trailer" placeholder="Insira o link do trailer">
      </div>
      <!-- Campo para adicionar uma descrição do anime -->
      <div class="form-group">
        <label for="description">Descrição:</label>
        <textarea name="description" id="description" rows="5" class="form-control" style="min-height: 120px; max-height: 450px;" placeholder="Descreva o anime..."></textarea>
        <span id="description_contator"></span>
      </div>
      <!-- Botão para enviar o formulário -->
      <input type="submit" class="btn card-btn" value="Adicionar">
    </form>
  </div>
</div>

<script>
  // Event listeners para os campos do formulário
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

    // Limita o número de caracteres a 100
    if (nameCharacters > 100) {
      nameText = nameText.substring(0, 100);
      titleInput.value = nameText;
      nameCharacters = 100;
    }

    // Atualiza o contador de caracteres
    titleCounterSpan.innerText = `${nameCharacters}/100`;

    // Muda a cor do contador se exceder 100 caracteres
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

    // Limita o número de caracteres a 1024
    if (caracteres > 1024) {
      texto = texto.substring(0, 1024);
      descriptionTextarea.value = texto;
      caracteres = 1024;
    }

    // Atualiza o contador de caracteres
    descriptionCounterSpan.innerText = `${caracteres}/${1024}`;

    // Muda a cor do contador dependendo do número de caracteres
    if (caracteres >= 1024) {
      descriptionCounterSpan.style.color = "red";
    } else if (caracteres < 960) {
      descriptionCounterSpan.style.color = "gray";
    } else {
      descriptionCounterSpan.style.color = "orange";
    }
  });

  // Verifica se os campos obrigatórios foram preenchidos antes de enviar o formulário
  const form = document.getElementById("add-movie-form");

  form.addEventListener("submit", function(event) {
    const title = document.getElementById("title").value;
    const description = document.getElementById("description").value;
    const category = document.getElementById("category").value;

    // Impede o envio do formulário se os campos obrigatórios estiverem vazios
    if (title.trim() === "" || description.trim() === "" || category.trim() === "") {
      event.preventDefault();
      alert("Você precisa adicionar pelo menos: título, descrição e categoria!");
    }
  });
</script>

<?php
// Incluindo rodapé
require_once("templates/footer.php");
?>