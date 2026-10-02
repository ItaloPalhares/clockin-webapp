const botaoregistrar = document.getElementById("registrarponto");

const form = document.getElementById("formponto");

const statusPonto = document.getElementById("statusponto");

botaoregistrar.addEventListener("click", function() {

    statusPonto.textContent = "Obtendo localização...";
    botaoregistrar.disabled = true;

    navigator.geolocation.getCurrentPosition(

        function(posicao) {

            document.getElementById("latitude").value =
                posicao.coords.latitude;

            document.getElementById("longitude").value =
                posicao.coords.longitude;

            statusPonto.textContent = "localização obtida!";

            abrirCamera("registro").then(function(abriu){

                if(!abriu){
                    botaoregistrar.disabled = false;
                    statusPonto.textContent = "Não foi possível acessar a câmera"
                }

            });
        },

        function(erro) {

            statusPonto.textContent =
                "Falha ao obter sua localização: " + erro.message;

            botaoregistrar.disabled = false;
        },

        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    );

});