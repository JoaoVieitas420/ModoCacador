// =====================================================================
// Modo Caçador — comportamentos da interface
// =====================================================================

// Botões "Mostrar" / "Ocultar" palavra-passe
document.querySelectorAll('[data-mostrar]').forEach((botao) => {
  botao.addEventListener('click', () => {
    const campo = document.getElementById(botao.dataset.mostrar);
    const mostrar = campo.type === 'password';
    campo.type = mostrar ? 'text' : 'password';
    botao.setAttribute('aria-pressed', String(mostrar));
    botao.setAttribute('aria-label', mostrar ? 'Ocultar palavra-passe' : 'Mostrar palavra-passe');
    botao.querySelector('span').textContent = mostrar ? 'Ocultar' : 'Mostrar';
  });
});

// Diálogo de confirmação de eliminação (substitui o confirm() do browser).
// Cada botão de eliminar indica, em atributos data-*, o URL (POST) e os textos do diálogo.
const dialogo = document.getElementById('dialogo-eliminar');
if (dialogo) {
  const form = document.getElementById('dialogo-form');
  let origem = null;

  document.querySelectorAll('[data-eliminar]').forEach((botao) => {
    botao.addEventListener('click', () => {
      origem = botao;
      form.action = botao.dataset.eliminar;
      document.getElementById('dialogo-titulo').textContent = botao.dataset.titulo || 'Eliminar?';
      document.getElementById('dialogo-item').textContent = botao.dataset.item || '';
      const consequencia = document.getElementById('dialogo-consequencia');
      consequencia.textContent = botao.dataset.consequencia || '';
      consequencia.hidden = !botao.dataset.consequencia;
      document.getElementById('dialogo-confirmar').textContent = botao.dataset.confirmar || 'Eliminar';
      dialogo.showModal();
    });
  });

  dialogo.querySelector('[data-fechar]').addEventListener('click', () => dialogo.close());
  // devolve o foco ao botão que abriu o diálogo
  dialogo.addEventListener('close', () => { if (origem) origem.focus(); });
}

// Galeria do artigo: as miniaturas trocam a imagem principal
const principal = document.getElementById('galeria-principal');
if (principal) {
  const miniaturas = document.querySelectorAll('[data-galeria]');
  miniaturas.forEach((botao) => {
    botao.addEventListener('click', () => {
      principal.src = botao.dataset.galeria;
      miniaturas.forEach((b) => b.setAttribute('aria-pressed', String(b === botao)));
    });
  });
}
