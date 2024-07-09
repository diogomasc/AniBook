<?php
// Importa as classes necessárias para envio de emails
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Inclui o cabeçalho da página e os arquivos necessários
require_once('templates/header.php');
require_once('dao/UserDAO.php');
require_once('models/User.php');

// Carrega a classe do Composer para enviar emails
require 'vendor/autoload.php';

// Inicializa objetos necessários e verifica se o usuário está logado
$user = new User();
$userDao = new UserDAO($conn, $BASE_URL);
$userData = $userDao->verifyToken(true);
$fullName = $user->getFullName($userData);

// Define uma imagem padrão se o usuário não possuir uma
if ($userData->image == "") {
   $userData->image = "user.png";
}
?>

<!-- Conteúdo da página -->
<div id="main-container" class="container-fluid">
   <div class="col-md-12">
      <!-- Formulário para atualização de dados do usuário -->
      <div id="update-data-container">
         <form action="<?= $BASE_URL ?>user_process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="type" value="update">
            <div class="row">
               <div class="col-md-6 offset-md-3">
                  <!-- Informações do usuário -->
                  <h1 style=" overflow-wrap: break-word;"><?= $fullName ?></h1>
                  <p class="page-description">Altere seus dados no formulário abaixo:</p>
                  <div id="profile-image-container" class="profile-image" style="background-image: url('<?= empty($userData->image) ? $BASE_URL . "img/users/user.png" : $BASE_URL . "img/users/" . $userData->image ?>')"></div>
                  <!-- Campo para selecionar uma nova foto de perfil -->
                  <div class="form-group">
                     <label for="image">Foto:</label>
                     <input type="file" class="form-control-file" name="image">
                  </div>
                  <!-- Campos para editar nome, sobrenome e email -->
                  <div class="form-group">
                     <label for="name">Nome:</label>
                     <input type="text" class="form-control" name="name" id="name" placeholder="Digite seu nome" value="<?= $userData->name ?>">
                     <span id="name_contator"></span>
                  </div>
                  <div class="form-group">
                     <label for="last_name">Sobrenome:</label>
                     <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Digite seu sobrenome" value="<?= $userData->last_name ?>" maxlength="100">
                     <span id="last_name_contator"></span>
                  </div>
                  <div class="form-group">
                     <label for="email">Email:</label>
                     <input type="text" readonly class="form-control disabled" name="email" id="email" placeholder="Digite seu email" value="<?= $userData->email ?>">
                  </div>
                  <!-- Campo para editar a biografia -->
                  <div class="form-group">
                     <label for="bio">Sobre você:</label>
                     <textarea name="bio" id="bio" class="form-control" rows="5" placeholder="Fale um pouco sobre você" maxlength="512"><?= $userData->bio ?></textarea>
                     <span id="contator"></span>
                  </div>
                  <!-- Botão para atualizar os dados -->
                  <input type="submit" class="btn card-btn" value="Atualizar">
               </div>
            </div>
         </form>
      </div>


      <!-- Formulário para atualização de senha -->
      <div id="change-password-container" class="row">
         <div class="col-md-6 offset-md-3">
            <h2>Atualizar senha:</h2>
            <p class="page-description">Digite a nova senha e confirme para atualizar sua senha:</p>
            <form action="<?= $BASE_URL ?>user_process.php" method="POST">
               <input type="hidden" name="type" value="changepassword">
               <!-- Campos para digitar a senha atual, nova senha e confirmação de senha -->
               <div class="form-group">
                  <label for="currentpassword">Senha Atual:</label>
                  <input type="password" class="form-control" name="currentpassword" id="currentpassword" placeholder="Digite sua senha atual" required>
               </div>
               <div class="form-group">
                  <label for="password">Nova Senha:</label>
                  <input type="password" class="form-control" name="password" id="password" placeholder="Digite sua nova senha" required>
               </div>
               <div class="form-group">
                  <label for="confirmpassword">Confirmação de Senha:</label>
                  <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="Confirme sua nova senha" required>
               </div>
               <!-- Botão para atualizar a senha -->
               <input type="submit" class="btn card-btn" value="Atualizar">
            </form>
         </div>
      </div>
      <!-- Formulário para deletar a conta -->
      <div id="delete-account-container" class="row" style="margin-top: 30px">
         <div class="col-md-6 offset-md-3">
            <h2>Deletar Conta:</h2>
            <p class="page-description">Ao deletar sua conta, todos os dados serão perdidos permanentemente.</p>
            <form action="<?= $BASE_URL ?>user_process.php" method="POST">
               <input type="hidden" name="type" value="deleteaccount">
               <!-- Campo para digitar a senha atual para confirmar a exclusão da conta -->
               <div class="form-group">
                  <label for="currentpassword">Digite sua senha para confirmar:</label>
                  <input type="password" class="form-control" name="currentpassword" id="currentpassword" placeholder="Digite sua senha atual" required>
               </div>
               <!-- Botão para deletar a conta -->
               <input type="submit" class="btn card-btn-delete" value="Deletar Conta">
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Script para validação de formulários -->
<script>
   // Adiciona event listeners para os campos do formulário
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

   // Contador de caracteres para o campo de nome
   const nameInput = document.getElementById("name");
   const nameCounterSpan = document.getElementById("name_contator");

   nameInput.addEventListener("input", function() {
      let nameText = nameInput.value;
      let nameCharacters = nameText.length;

      if (nameCharacters > 50) {
         nameText = nameText.substring(0, 50);
         nameInput.value = nameText;
         nameCharacters = 50;
      }

      nameCounterSpan.innerText = `${nameCharacters}/50`;

      if (nameCharacters >= 50) {
         nameCounterSpan.style.color = "red";
      } else {
         nameCounterSpan.style.color = "gray";
      }
   });

   // Contador de caracteres para o campo de sobrenome
   const lastNameInput = document.getElementById("last_name");
   const lastNameCounterSpan = document.getElementById("last_name_contator");

   lastNameInput.addEventListener("input", function() {
      let nameText = lastNameInput.value;
      let nameCharacters = nameText.length;

      if (nameCharacters > 50) {
         nameText = nameText.substring(0, 50);
         lastNameInput.value = nameText;
         nameCharacters = 50;
      }

      lastNameCounterSpan.innerText = `${nameCharacters}/50`;

      if (nameCharacters >= 50) {
         lastNameCounterSpan.style.color = "red";
      } else {
         lastNameCounterSpan.style.color = "gray";
      }
   });

   // Contador de caracteres para o campo de biografia
   const bioTextarea = document.getElementById("bio");
   const counterSpan = document.getElementById("contator");

   bioTextarea.addEventListener("input", function() {
      let texto = bioTextarea.value;
      let caracteres = texto.length;

      if (caracteres > 512) {
         texto = texto.substring(0, 512);
         bioTextarea.value = texto;
         caracteres = 512;
      }

      counterSpan.innerText = `${caracteres}/${512}`;

      if (caracteres >= 512) {
         counterSpan.style.color = "red";
      } else if (caracteres < 470) {
         counterSpan.style.color = "gray";
      } else {
         counterSpan.style.color = "orange";
      }
   });

   // Validação do formulário de atualização de dados
   document.getElementById('update-data-container').addEventListener('submit', function(event) {
      const name = document.getElementById('name').value;
      const lastName = document.getElementById('last_name').value;
      const bio = document.getElementById('bio').value;
      const image = document.querySelector('input[type="file"]').files[0];

      if (name === '<?= $userData->name ?>' && lastName === '<?= $userData->last_name ?>' && bio === '<?= $userData->bio ?>' && (!image || image.name === '<?= $userData->image ?>')) {
         alert("Nada foi alterado, não há o que atualizar");
         event.preventDefault();
      } else {
         const confirmUpdate = confirm("Tem certeza que deseja atualizar seus dados?");
         if (!confirmUpdate) {
            event.preventDefault();
         }
      }
   });

   // Validação do formulário de atualização de senha
   document.getElementById('change-password-container').addEventListener('submit', function(event) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirmpassword').value;

      if (password !== confirmPassword) {
         alert("As senhas não coincidem!");
         event.preventDefault();
      } else if (password.length < 6 || !password.match(/[A-Za-z]/) || !password.match(/[0-9]/)) {
         alert("A senha deve ter no mínimo 6 caracteres e deve conter letras e números.");
         event.preventDefault();
      } else {
         const confirmChange = confirm("Tem certeza que deseja alterar sua senha?");
         if (!confirmChange) {
            event.preventDefault();
         }
      }
   });

   // Validação do formulário de exclusão de conta
   document.getElementById('delete-account-container').addEventListener('submit', function(event) {
      const confirmDelete = confirm("Tem certeza que deseja deletar sua conta?");

      if (!confirmDelete) {
         event.preventDefault();
      } else {
         alert("Sua conta será permanentemente excluída. Esta ação não pode ser desfeita!");
      }
   });
</script>

<?php
// Inclui o rodapé da página
require_once('templates/footer.php');
?>