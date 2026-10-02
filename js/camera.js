const botaoCamera = document.getElementById("testarcamera");
const areaCamera = document.getElementById("areadacamera");

const video = document.getElementById("videoponto");
const botaoFoto = document.getElementById("tirarfoto");

const fotopega = document.getElementById("pegafoto");
const preview = document.getElementById("previewfoto");

const botaoConfirmar = document.getElementById("confirmarponto");
const botaoCancelar = document.getElementById("cancelarponto");
const botaoVoltar = document.getElementById("voltarcamera");

let modoCamera = "teste";

let streamCamera = null;

async function abrirCamera(modo = "teste") {

    modoCamera = modo;

    try {

        if (streamCamera) {
            streamCamera.getTracks().forEach(track => track.stop());
        }

        streamCamera = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: "user"
            },
            audio: false
        });

        video.srcObject = streamCamera;

        areaCamera.hidden = false;

        video.hidden = false;
        botaoFoto.hidden = false;

        preview.hidden = true;
        botaoConfirmar.hidden = true;
        botaoCancelar.hidden = modo !== "registro";
        botaoVoltar.hidden = modo !== "teste";

        await video.play();

        return true;

    } catch (erro) {

        fechaCamera();
        alert("Não foi possível abrir a câmera.");

        return false;

    }

};

function fechaCamera() {

    if (streamCamera) {
        streamCamera.getTracks().forEach(track => track.stop());
        streamCamera = null;
    }

    video.pause();
    video.srcObject = null;

    video.hidden = true;
    botaoFoto.hidden = true;
    preview.hidden = true;
    botaoConfirmar.hidden = true;

    botaoCancelar.hidden = true;
    botaoVoltar.hidden = true;
    areaCamera.hidden = true;

    preview.removeAttribute("src");
    fotopega.width = 0;
    fotopega.height = 0;

}


botaoCamera.addEventListener("click", function () {
    abrirCamera("teste");
});


botaoFoto.addEventListener("click", function () {

    if (!streamCamera || !video.videoWidth || !video.videoHeight) {
        alert("Aguarde a câmera carregar.");
        return;
    }

    fotopega.width = video.videoWidth;
    fotopega.height = video.videoHeight;

    const contexto = fotopega.getContext("2d");


    contexto.drawImage(video, 0, 0, fotopega.width, fotopega.height);

    preview.src = fotopega.toDataURL("image/jpeg", 0.85);

    streamCamera.getTracks().forEach(track => track.stop());

    streamCamera = null;

    video.pause();
    video.srcObject = null;
    video.hidden = true;
    botaoFoto.hidden = true;

    preview.hidden = false;
    botaoConfirmar.hidden = modoCamera !== "registro";

});

botaoCancelar.addEventListener("click", function () {

    fechaCamera();

    window.location.href = "dashboard.php";

});

botaoVoltar.addEventListener("click", function () {

    fechaCamera();

});

botaoConfirmar.addEventListener("click", function () {

    botaoConfirmar.disabled = true;

    fotopega.toBlob(async function (imagem) {

        if (!imagem) {
            alert("erro ao registrar foto.");
            botaoConfirmar.disabled = false;
            return;
        }

        const formdoponto = document.getElementById("formponto");

        const dados = new FormData(formdoponto);

        dados.append("imagem", imagem, "foto_ponto.jpg");

        try {
            const resposta = await fetch(formdoponto.action, {
                method: "POST",
                body: dados
            });

            const mensagemEspera = await resposta.text();

            if (!resposta.ok) {
                throw new Error(mensagem);
            }
            window.location.href = "dashboard.php";
        } catch(erro) {
            console.error("erro ao registrar ponto: ", erro);
            alert("não foi possível registrar o ponto: " + erro.message);

            botaoConfirmar.disabled = false;
        }

    }, "image/jpeg", 0.85);

});

