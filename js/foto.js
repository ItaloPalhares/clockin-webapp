const modalFoto = document.getElementById("modalfoto");

const imagemRegistro = document.getElementById("imagemregistro");

const botaoFecharFoto = document.getElementById("fecharfoto");

const botoesFoto = document.querySelectorAll(".abrir-foto");

botoesFoto.forEach(function(botao) {

    botao.addEventListener("click", function() {

        const pontoId = botao.dataset.pontoId;

        imagemRegistro.src = "foto.php?id=" + pontoId;

        modalFoto.hidden = false;

    });

});

botaoFecharFoto.addEventListener("click", function() {

    modalFoto.hidden = true;

    imagemRegistro.src = "";

});