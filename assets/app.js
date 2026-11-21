// Espera a página carregar antes de procurar os elementos.
document.addEventListener('DOMContentLoaded', function () {
    // Abre e fecha o menu em telas pequenas.
    const botaoMenu = document.querySelector('.menu-toggle');
    const menu = document.querySelector('.nav');

    if (botaoMenu && menu) {
        botaoMenu.addEventListener('click', function () {
            menu.classList.toggle('open');
        });
    }

    // Pede confirmação antes de excluir um registro.
    const formularios = document.querySelectorAll('[data-confirm]');

    formularios.forEach(function (formulario) {
        formulario.addEventListener('submit', function (evento) {
            const confirmou = confirm(formulario.dataset.confirm);

            if (!confirmou) {
                evento.preventDefault();
            }
        });
    });

    // Filtra as linhas da tabela enquanto o usuário digita.
    const campoBusca = document.querySelector('[data-table-search]');

    if (campoBusca) {
        campoBusca.addEventListener('input', function () {
            const pesquisa = campoBusca.value.toLowerCase();
            const linhas = document.querySelectorAll('.data-table tbody tr');

            linhas.forEach(function (linha) {
                const texto = linha.innerText.toLowerCase();
                linha.hidden = !texto.includes(pesquisa);
            });
        });
    }

    // Deixa a placa em letras maiúsculas e remove caracteres inválidos.
    const campoPlaca = document.querySelector('input[name="placa"]');

    if (campoPlaca) {
        campoPlaca.addEventListener('input', function () {
            campoPlaca.value = campoPlaca.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .slice(0, 7);
        });
    }
});
