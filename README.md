# Task Management API - Backend Developer Challenge 🚀

Esta é uma API RESTful completa desenvolvida em **Laravel 11+** e **PHP 8.3+** para o gerenciamento de tarefas e comentários. Este sistema permite criar, editar, excluir e visualizar tarefas, além de gerenciá-las através de status, adicionar comentários e possuir camadas de proteção.

O foco primordial deste projeto foi aplicar boas práticas de desenvolvimento de software, padrões de projeto estritos, o uso consistente do **GitFlow** e a excelência na cobertura do código.

---

## 🛠️ Requisitos Técnicos Atingidos

- ✅ **Framework:** Laravel
- ✅ **Comandos Git:** Uso intenso do GitFlow para desenvolvimento (separando `main`, `develop` e os ramos de `feature/`).
- ✅ **API Restful:** Controllers e roteamentos semânticos isolados de front-end.
- ✅ **Filtros e Status:** Funcionalidade real para alteração e verificação (Ex: `?status=pending`).
- ✅ **Autenticação e Segurança:** Implementado com Laravel Sanctum e Laravel Policies.
- ✅ **Diferencial (Testes):** Cbertura de testes de ponta-a-ponta via pacote [**Pest**](https://pestphp.com/).
- ✅ **Diferencial (Docker):** Totalmente englobado no [Laravel Sail](https://laravel.com/docs/sail).
- ✅ **Diferencial (Migrations e Seeders):** Tudo foi estruturado via terminal com base de mentirinha (Faker).

---

## 🚀 Como Rodar o Projeto

Como o projeto é construído em cima do **Laravel Sail**, para iniciar o projeto não é necessário ter PHP, Composer ou Node na sua máquina real, apenas o **Docker** configurado.

### 1. Clonar este repositório
```bash
git clone https://github.com/SEU_USUARIO/NOME_DO_REPO.git
cd task-manager
```

### 2. Configurar o ambiente Local (.env)
Copie o arquivo padrão de exemplos de variáveis para criar as variáveis definitivas do servidor.
```bash
cp .env.example .env
```
*(O Laravel Sail possui credenciais Default que não exigem manutenções pesadas).*

### 3. Subir os Contêineres (Docker)
Agora inicie os serviços do banco de dados e servidor. O Sail vai provisionar as bibliotecas necessárias.
```bash
./vendor/bin/sail up -d
```
*(Caso não possua a pasta `vendor`, utilize uma imagem rápida fornecida pelo Laravel para instalar dependências via devcontainer)*:
```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php84-composer:latest composer install --ignore-platform-reqs
```

### 4. Preparar o Banco e Popular Dados (Seeders)
Depois dos contêineres estarem no ar (status Running), basta injetar nossas configurações ao banco. O comando a seguir roda as *Migrations* já injetando dados *Fake*.
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

### 5. (Opcional) Executar Suíte de Testes
Caso deseje comprovar a estabilidade da API e suas travas lógicas, execute de dentro do contêiner o **Pest**:
```bash
./vendor/bin/sail test
```

A partir do momento em que subiu o Servidor Sail, basta bater nos *endpoints* via URL `http://localhost/api/tasks`. **Para ter os dados iniciais o Seeder configura o usuário padrão `test@example.com` (Senha: `password`).**

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
