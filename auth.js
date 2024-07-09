document.addEventListener("DOMContentLoaded", function() {
    // Realçar campos do formulário
    const formFields = document.querySelectorAll("input, textarea");

    formFields.forEach((field) => {
       field.addEventListener("focus", function() {
          field.style.backgroundColor = "#e8f0fe";
       });

       field.addEventListener("blur", function() {
          field.style.backgroundColor = "";
       });
    });

    // Validação do formulário de registro
    const registerForm = document.querySelector("#register-container form");

    registerForm.addEventListener("submit", function(event) {
       const email = document.getElementById("email-register").value;
       const nome = document.getElementById("nome").value;
       const lastname = document.getElementById("lastname").value;
       const password = document.getElementById("password-register").value;
       const confpassword = document.getElementById("confpassword").value;

       // Verificando se todos os campos estão preenchidos
       if (!email || !nome || !lastname || !password || !confpassword) {
          alert("Todos os campos são obrigatórios.");
          event.preventDefault();
          return;
       }

       // Verificando se o nome não excede 50 caracteres
       if (nome.length > 50) {
          alert("O nome não deve exceder 50 caracteres.");
          event.preventDefault();
          return;
       }

       // Verificando se o sobrenome não excede 50 caracteres
       if (lastname.length > 50) {
          alert("O sobrenome não deve exceder 50 caracteres.");
          event.preventDefault();
          return;
       }

       // Verificando se o email é válido
       if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
          alert("Por favor, insira um email válido. Formato: algumacoisa@algumacoisa.com");
          event.preventDefault();
          return;
       }

       // Verificando se a senha tem pelo menos 6 caracteres e contém letras e números
       if (password.length < 6 || !password.match(/[A-Za-z]/) || !password.match(/[0-9]/)) {
          alert("A senha deve ter no mínimo 6 caracteres e deve conter letras e números.");
          event.preventDefault();
          return;
       }

       // Verificando se as senhas coincidem
       if (password !== confpassword) {
          alert("Senhas não conferem.");
          event.preventDefault();
          return;
       }

       // Confirmar o registro
       const confirmRegister = confirm("Tem certeza que deseja registrar-se?");
       if (!confirmRegister) {
          event.preventDefault();
          return;
       }
    });

    // Validação do formulário de login
    const loginForm = document.querySelector("#login-container form");

    loginForm.addEventListener("submit", function(event) {
       const email = document.getElementById("email-login").value;
       const password = document.getElementById("password-login").value;

       // Verificando se todos os campos estão preenchidos
       if (!email || !password) {
          alert("Todos os campos são obrigatórios.");
          event.preventDefault();
          return;
       }
    });

    // Contador de caracteres para os campos de registro
    const emailRegister = document.getElementById("email-register");
    const emailCounterSpan = document.getElementById("email-contador");

    emailRegister.addEventListener("input", function() {
       let emailText = emailRegister.value;
       let emailCharacters = emailText.length;

       // Limita o número de caracteres a 100
       if (emailCharacters > 100) {
          emailText = emailText.substring(0, 100);
          emailRegister.value = emailText;
          emailCharacters = 100;
       }

       // Atualiza o contador de caracteres
       emailCounterSpan.innerText = `${emailCharacters}/100`;

       // Muda a cor do contador se exceder 100 caracteres
       if (emailCharacters >= 100) {
          emailCounterSpan.style.color = "red";
       } else {
          emailCounterSpan.style.color = "gray";
       }
    });

    // Contador de caracteres para o campo de nome
    const nomeInput = document.getElementById("nome");
    const nomeCounterSpan = document.getElementById("nome-contator");

    nomeInput.addEventListener("input", function() {
       let nameText = nomeInput.value;
       let nameCharacters = nameText.length;

       // Limita o número de caracteres a 50
       if (nameCharacters > 50) {
          nameText = nameText.substring(0, 50);
          nomeInput.value = nameText;
          nameCharacters = 50;
       }

       // Atualiza o contador de caracteres
       nomeCounterSpan.innerText = `${nameCharacters}/50`;

       // Muda a cor do contador se exceder 50 caracteres
       if (nameCharacters >= 50) {
          nomeCounterSpan.style.color = "red";
       } else {
          nomeCounterSpan.style.color = "gray";
       }
    });

    // Contador de caracteres para o campo de sobrenome
    const lastnameInput = document.getElementById("lastname");
    const lastnameCounterSpan = document.getElementById("lastname-contator");

    lastnameInput.addEventListener("input", function() {
       let lastnameText = lastnameInput.value;
       let lastnameCharacters = lastnameText.length;

       // Limita o número de caracteres a 50
       if (lastnameCharacters > 50) {
          lastnameText = lastnameText.substring(0, 50);
          lastnameInput.value = lastnameText;
          lastnameCharacters = 50;
       }

       // Atualiza o contador de caracteres
       lastnameCounterSpan.innerText = `${lastnameCharacters}/50`;

       // Muda a cor do contador se exceder 50 caracteres
       if (lastnameCharacters >= 50) {
          lastnameCounterSpan.style.color = "red";
       } else {
          lastnameCounterSpan.style.color = "gray";
       }
    });
