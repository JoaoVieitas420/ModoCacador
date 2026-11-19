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

// Pré-visualização das fotografias escolhidas num campo de ficheiros (novo artigo)
document.querySelectorAll('[data-pre-visualizar]').forEach((campo) => {
  const lista = document.getElementById(campo.dataset.preVisualizar);
  let urls = [];
  campo.addEventListener('change', () => {
    urls.forEach((url) => URL.revokeObjectURL(url));
    urls = [];
    lista.innerHTML = '';
    Array.from(campo.files).forEach((ficheiro, i) => {
      const url = URL.createObjectURL(ficheiro);
      urls.push(url);
      const item = document.createElement('li');
      const img = document.createElement('img');
      img.src = url;
      img.alt = 'Fotografia ' + (i + 1);
      const legenda = document.createElement('span');
      legenda.className = 'secundario';
      if (i === 0 && !('semPrincipal' in lista.dataset)) {
        const principal = document.createElement('strong');
        principal.textContent = 'Principal';
        legenda.append(principal, ' · ');
      }
      legenda.append(ficheiro.name);
      legenda.style.overflowWrap = 'anywhere';
      item.append(img, legenda);
      lista.append(item);
    });
    lista.hidden = campo.files.length === 0;
  });
});

// Menu lateral em ecrãs estreitos: abre com o botão da barra do topo,
// fecha com o botão X, o fundo escurecido, a tecla Esc ou ao alargar o ecrã
const menuAbrir = document.getElementById('menu-abrir');
if (menuAbrir) {
  const menuFechar = document.getElementById('menu-fechar');
  const menuFundo = document.getElementById('menu-fundo');
  // a mesma condição do @media do menu no main.css
  const ecraMovel = window.matchMedia('(max-width: 820px), (orientation: landscape) and (max-height: 500px)');

  const definirMenu = (aberto, devolverFoco = true) => {
    document.body.classList.toggle('menu-aberto', aberto);
    menuAbrir.setAttribute('aria-expanded', String(aberto));
    menuFundo.hidden = !aberto;
    // o conteúdo por trás deixa de receber foco/cliques enquanto o menu está aberto
    document.querySelectorAll('.barra-movel, .conteudo').forEach((zona) => { zona.inert = aberto; });
    if (aberto) {
      menuFechar.focus();
    } else if (devolverFoco) {
      menuAbrir.focus();
    }
  };

  menuAbrir.addEventListener('click', () => definirMenu(true));
  menuFechar.addEventListener('click', () => definirMenu(false));
  menuFundo.addEventListener('click', () => definirMenu(false));
  document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape' && document.body.classList.contains('menu-aberto')) {
      definirMenu(false);
    }
  });
  ecraMovel.addEventListener('change', () => {
    if (!ecraMovel.matches) definirMenu(false, false);
  });
}
