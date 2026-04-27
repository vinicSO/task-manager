# API de Gerenciamento de Tarefas 🚀

Esta é uma API RESTful completa desenvolvida em **Laravel 13** e **PHP 8.3+** para o gerenciamento de tarefas. Este sistema permite criar, editar, excluir e visualizar tarefas, além de gerenciá-las através de status, adicionar comentários e possuir camadas de proteção.

O foco primordial deste projeto foi aplicar boas práticas de desenvolvimento de software, padrões de projeto estritos, o uso consistente do **GitFlow** e a excelência na cobertura do código.

---

## 🛠️ Requisitos Técnicos Atingidos

- ✅ **Framework:** Laravel
- ✅ **API Restful:** Controllers e roteamentos semânticos isolados de front-end.
- ✅ **Filtros e Status:** Funcionalidade real para alteração e verificação (Ex: `?status=pending`).
- ✅ **Autenticação e Segurança:** Implementado com Laravel Sanctum e Laravel Policies.
- ✅ **Testes:** Cbertura de testes de ponta-a-ponta via pacote [**Pest**](https://pestphp.com/).
- ✅ **Docker:** Totalmente englobado no [Laravel Sail](https://laravel.com/docs/sail).

---

## 🚀 Como Rodar o Projeto

Para facilitar a configuração do ambiente baseado no **Laravel Sail** (Docker), centralizamos os comandos essenciais através do `Makefile` incluído na raiz do projeto. 

### 1. Clonar este repositório
```bash
git clone https://github.com/vinicSO/task-manager.git
cd task-manager
```

### 2. Instalação Completa Automatizada
Para instalar as dependências, criar o arquivo de variáveis, subir os contêineres, gerar a chave do framework e popular o banco automaticamente com dados variados em uma única chamada, basta rodar (requer Composer nativo ou via Devcontainer):
```bash
make install
```

### 3. Gerir o Servidor Diariamente
Caso já tenha instalado, basta iniciar e parar os contêineres em segundo-plano quando trabalhar no repositório:
```bash
make up    # Inicializa os containers (Sail up -d)
make down  # Encerra os processos
```

### 4. (Opcional) Executar a Suíte de Testes
Para certificar-se da integridade das validações de Requests e aprovações pelas Policies (Security) construídas com o framework **Pest**, digite:
```bash
make test
```

A partir do momento em que os envios do `make up` finalizam, a sua API estará disponível globalmente em `http://localhost/api/tasks`. **Para ter os dados iniciais do teste, o Seeder injeta no banco o usuário padrão `test@example.com` (Senha: `password`).**

---

## 💡 Decisões Técnicas Tomadas

Para atender aos pilares de alta consistência e qualidade de um desafio (Sênior/Pleno), tomei as seguintes caminhos:
1. **Repository & Service Pattern ou Fat Model?** Decidi seguir o viés "Fat Model com Thin Controllers" combinado com API Resources, já que as requisições atendiam a CRUDs isolados e simples, evitando abstrações como Interfaces extensas caso a aplicação não precise disso neste momento.
2. **Utilização Estrita de Enums:** Introduzimos o `App\Enums\TaskStatus` utilizando a flexibilidade do PHP 8, o que garante 100% que as Strings enviadas estarão contidas no escopo global e valida a request nativamente antes de chegar no controller usando a classe *Rule Enum*.
3. **Escopo via Laravel Policies (`TaskPolicy`):** Não deleguei a verificação do que "é ou não" de um usuário nos Controllers. Foi criado uma *Policy* que implementa uma barreira no nível de permissões de acesso da URL da API pelo método `Gate::authorize()`.
4. **Respostas Formatas (API Resources/Collections):** Em vez de retornar models crus de Eloquent (`return $task`), as respostas foram embrulhadas em API Resources (`return new TaskResource($task)`) ocultando IDs internos, Datas e dados de relacionamento do banco.
5. **GitFlow:** Abri a separação dos contextos das features (por exemplo, branch para `feature/seeders`, `feature/automated-tests`, etc) deixando o rastreamento individual do que foi commitado em qual época com grande clareza para o PO ou Equipe Ágil.

---

## 🔄 Melhorias que eu faria com mais tempo

Se não estivesse restrito ao cronograma do desafio, priorizaria as seguintes implantações:
- [ ] **Documentação OpenAPI/Swagger:** Adicionaria anotações como o L5-Swagger para gerar uma Interface gráfica permitindo que os desenvolvedores front-end testem a aplicação na própria página.
- [ ] **Filas (Queues) e Broadcasts:** Implementaria que os e-mails informativos sobre uma nova "Tarefa Atribuída" ou "Novo Comentário" acontecessem via Filas (*Redis*) para acelerar o tempo de resposta do API, e enviaria *WebSockets Server* (Laravel Reverb) para notificar o *frontend* instantaneamente sem recarregar e fazer pollings.
- [ ] **CI/CD Simplificado (Ex: Github Actions):** Criaria um gatilho `.yml` nas Pull Requests da `main` que forçaria a rodar a suite `sail test` e o linter `pint`.
- [ ] **Soft Deletes:** Caso houvesse necessidade de recuperar tarefas removidas por engano.

---

Espero que este desafio comprove as minhas vivências práticas e amor pela área que abracei! 🚀
