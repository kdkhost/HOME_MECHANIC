# Requirements Document

## Introduction

O **Frontend CMS** permite que o administrador do site HomeMechanic edite todo o conteúdo visível no frontend (textos, imagens, overlays, badges, estatísticas, depoimentos, CTA, etc.) diretamente pelo painel admin existente (AdminLTE 4 / Laravel 11), sem precisar alterar código-fonte. O sistema usa o modelo `Setting` já existente (com `get()`, `set()`, `setMany()`, `group()` e cache) e o `FrontendSettingsProvider` para injetar os dados nas views. Novas seções podem ser adicionadas no futuro sem modificar o backend.

---

## Glossary

- **CMS_Controller**: `App\Modules\Settings\Controllers\FrontendCmsController` — controlador responsável por exibir e salvar as configurações de conteúdo do frontend.
- **Setting**: Modelo Eloquent existente em `App\Models\Setting` com métodos `get()`, `set()`, `setMany()`, `group()` e invalidação de cache.
- **FrontendSettingsProvider**: Service Provider existente que compartilha `$siteSettings` e `$frontendContent` com todas as views frontend via `View::composer`.
- **Frontend_View**: Qualquer view Blade dentro de `resources/views/modules/frontend/` ou `resources/views/layouts/frontend.blade.php`.
- **Admin_Panel**: Painel AdminLTE 4 acessível em `/admin`, protegido por autenticação.
- **Section**: Bloco de conteúdo editável do frontend (ex: Hero, About, Testimonials, CTA).
- **Overlay**: Valor numérico de 0 a 100 que representa a opacidade (em %) de uma camada escura sobre uma imagem de fundo.
- **FileUploadHelper**: Helper existente em `App\Helpers\FileUploadHelper` que salva arquivos em `public/uploads/` e retorna o path relativo.
- **cms Group**: Grupo de configurações no modelo `Setting` com `group = 'cms'` usado exclusivamente para conteúdo do frontend.

---

## Requirements

### Requirement 1: Edição do Conteúdo da Seção Hero (Home)

**User Story:** Como administrador, quero editar os textos, imagem de fundo, overlay e badges da seção Hero da página Home, para que o conteúdo reflita a identidade atual da empresa sem precisar alterar código.

#### Acceptance Criteria

1. THE Admin_Panel SHALL exibir uma página de configurações de conteúdo frontend acessível via `/admin/settings/frontend-cms`.
2. WHEN o administrador acessa `/admin/settings/frontend-cms`, THE CMS_Controller SHALL carregar todos os valores do grupo `cms` do modelo `Setting`, preenchendo campos ausentes com valores padrão definidos no controller.
3. THE Admin_Panel SHALL apresentar campos editáveis para: título linha 1 do Hero, título linha 2 (destaque laranja), subtítulo, texto do badge, URL da imagem de fundo do Hero e valor de overlay (0–100).
4. WHEN o administrador submete o formulário da seção Hero, THE CMS_Controller SHALL validar e salvar os campos `hero_title_1`, `hero_title_2`, `hero_subtitle`, `hero_badge_text`, `hero_bg_image` e `hero_overlay` no grupo `cms` via `Setting::setMany()`.
5. WHEN um arquivo de imagem é enviado para o campo de imagem de fundo do Hero, THE CMS_Controller SHALL usar o `FileUploadHelper` para salvar o arquivo em `public/uploads/cms/` e armazenar o path relativo em `hero_bg_image`.
6. WHEN a configuração `hero_overlay` é salva, THE Frontend_View SHALL aplicar `rgba(10,10,10, {overlay/100})` como opacidade do gradiente de fundo do Hero.
7. IF o campo `hero_overlay` receber um valor fora do intervalo 0–100, THEN THE CMS_Controller SHALL retornar erro de validação e não salvar o valor.

---

### Requirement 2: Edição das Estatísticas do Hero

**User Story:** Como administrador, quero editar os três números e rótulos de estatísticas exibidos no Hero (ex: "15+ Anos de Experiência"), para manter os dados atualizados.

#### Acceptance Criteria

1. THE Admin_Panel SHALL exibir campos para três estatísticas do Hero: `hero_stat_1_num`, `hero_stat_1_label`, `hero_stat_2_num`, `hero_stat_2_label`, `hero_stat_3_num`, `hero_stat_3_label`.
2. WHEN o administrador salva as estatísticas, THE CMS_Controller SHALL persistir os seis campos no grupo `cms` via `Setting::setMany()`.
3. WHEN a Frontend_View renderiza o Hero, THE Frontend_View SHALL exibir os valores de `hero_stat_*` lidos do `$frontendContent`, usando os padrões `15+`, `Anos de Experiência`, `800+`, `Projetos Realizados`, `98%`, `Clientes Satisfeitos` quando os valores não estiverem definidos.

---

### Requirement 3: Edição da Seção About

**User Story:** Como administrador, quero editar os textos, imagem, badge de anos de experiência e os quatro itens de diferenciais da seção About, para comunicar a proposta de valor da empresa.

#### Acceptance Criteria

1. THE Admin_Panel SHALL exibir campos para: `about_label`, `about_title`, `about_title_highlight`, `about_text`, `about_image`, `about_badge_num`, `about_badge_text`.
2. THE Admin_Panel SHALL exibir campos para quatro itens de diferenciais, cada um com: `about_feature_{n}_icon` (classe Bootstrap Icon), `about_feature_{n}_title` e `about_feature_{n}_text`, onde `n` é 1, 2, 3 ou 4.
3. WHEN o administrador envia uma imagem para `about_image`, THE CMS_Controller SHALL usar o `FileUploadHelper` para salvar em `public/uploads/cms/` e armazenar o path relativo.
4. WHEN o formulário da seção About é submetido, THE CMS_Controller SHALL salvar todos os campos do grupo `cms` via `Setting::setMany()`.
5. WHEN a Frontend_View renderiza a seção About, THE Frontend_View SHALL exibir os valores de `about_*` lidos do `$frontendContent`, usando os valores padrão do template original quando não definidos.

---

### Requirement 4: Edição dos Depoimentos

**User Story:** Como administrador, quero editar os três depoimentos exibidos na Home (nome, carro, texto e iniciais do avatar), para manter os testemunhos atualizados e reais.

#### Acceptance Criteria

1. THE Admin_Panel SHALL exibir campos para três depoimentos, cada um com: `testimonial_{n}_name`, `testimonial_{n}_car`, `testimonial_{n}_text` e `testimonial_{n}_initials`, onde `n` é 1, 2 ou 3.
2. WHEN o formulário de depoimentos é submetido, THE CMS_Controller SHALL salvar os doze campos no grupo `cms` via `Setting::setMany()`.
3. WHEN a Frontend_View renderiza a seção de depoimentos, THE Frontend_View SHALL exibir os valores de `testimonial_*` lidos do `$frontendContent`, usando os depoimentos padrão do template original quando não definidos.

---

### Requirement 5: Edição da Seção CTA (Call to Action)

**User Story:** Como administrador, quero editar o título, subtítulo, imagem de fundo, overlay e o link do WhatsApp da seção CTA, para direcionar os visitantes à ação desejada.

#### Acceptance Criteria

1. THE Admin_Panel SHALL exibir campos para: `cta_title`, `cta_title_highlight`, `cta_subtitle`, `cta_bg_image` e `cta_overlay` (0–100).
2. WHEN o administrador envia uma imagem para `cta_bg_image`, THE CMS_Controller SHALL usar o `FileUploadHelper` para salvar em `public/uploads/cms/` e armazenar o path relativo.
3. WHEN o formulário da seção CTA é submetido, THE CMS_Controller SHALL salvar todos os campos no grupo `cms` via `Setting::setMany()`.
4. WHEN a Frontend_View renderiza a seção CTA, THE Frontend_View SHALL usar o número de WhatsApp de `$siteSettings['whatsapp']` (já gerenciado pelo módulo General) no link do botão WhatsApp.
5. IF o campo `cta_overlay` receber um valor fora do intervalo 0–100, THEN THE CMS_Controller SHALL retornar erro de validação e não salvar o valor.

---

### Requirement 6: Edição do Hero das Páginas Internas (Serviços, Galeria, Blog, Contato)

**User Story:** Como administrador, quero editar o título, subtítulo e imagem de fundo do hero de cada página interna, para personalizar a apresentação de cada seção do site.

#### Acceptance Criteria

1. THE Admin_Panel SHALL exibir campos para o hero de cada página interna: `{page}_hero_title`, `{page}_hero_subtitle`, `{page}_hero_bg_image` e `{page}_hero_overlay`, onde `{page}` é `services`, `gallery`, `blog` ou `contact`.
2. WHEN o administrador envia uma imagem para o hero de uma página interna, THE CMS_Controller SHALL usar o `FileUploadHelper` para salvar em `public/uploads/cms/` e armazenar o path relativo.
3. WHEN o formulário de heroes de páginas internas é submetido, THE CMS_Controller SHALL salvar todos os campos no grupo `cms` via `Setting::setMany()`.
4. WHEN a Frontend_View de uma página interna renderiza o hero, THE Frontend_View SHALL exibir os valores de `{page}_hero_*` lidos do `$frontendContent`, usando as imagens e textos padrão do template original quando não definidos.

---

### Requirement 7: Injeção de Conteúdo CMS nas Views Frontend

**User Story:** Como desenvolvedor, quero que o conteúdo CMS seja automaticamente disponibilizado em todas as views frontend sem precisar alterar os controllers de frontend, para garantir extensibilidade futura.

#### Acceptance Criteria

1. THE FrontendSettingsProvider SHALL carregar todos os valores do grupo `cms` via `Setting::group('cms')` e compartilhá-los como `$frontendContent` em todas as views `modules.frontend.*` e `layouts.frontend`.
2. WHEN o cache do modelo `Setting` é invalidado após um `setMany()`, THE FrontendSettingsProvider SHALL refletir os novos valores na próxima requisição frontend sem necessidade de reinicialização do servidor.
3. IF uma chave de `$frontendContent` não existir no banco de dados, THEN THE Frontend_View SHALL usar o valor padrão definido inline na view (fallback com operador `??`).
4. THE FrontendSettingsProvider SHALL carregar o grupo `cms` em uma única query ao banco de dados por requisição, usando o cache existente do modelo `Setting`.

---

### Requirement 8: Upload e Gerenciamento de Imagens CMS

**User Story:** Como administrador, quero fazer upload de imagens para qualquer seção do frontend diretamente pelo painel admin, para substituir as imagens padrão por fotos reais da oficina.

#### Acceptance Criteria

1. THE CMS_Controller SHALL aceitar uploads de imagem nos formatos `jpg`, `jpeg`, `png`, `webp` e `gif`, com tamanho máximo de 5 MB por arquivo.
2. WHEN um novo arquivo de imagem é enviado para uma chave que já possui imagem salva, THE CMS_Controller SHALL excluir o arquivo anterior via `FileUploadHelper::delete()` antes de salvar o novo.
3. WHEN o upload de imagem falha por tamanho ou formato inválido, THE CMS_Controller SHALL retornar mensagem de erro descritiva e não alterar o valor salvo anteriormente.
4. THE Admin_Panel SHALL exibir uma pré-visualização da imagem atual ao lado de cada campo de upload de imagem.
5. THE Admin_Panel SHALL permitir que o administrador remova uma imagem (voltando ao padrão) sem precisar fazer upload de nova imagem.

---

### Requirement 9: Organização em Abas no Painel Admin

**User Story:** Como administrador, quero que as configurações de conteúdo frontend estejam organizadas em abas por seção (Hero, About, Depoimentos, CTA, Páginas Internas), para facilitar a navegação e edição.

#### Acceptance Criteria

1. THE Admin_Panel SHALL organizar os campos de configuração CMS em abas: "Hero & Estatísticas", "Sobre Nós", "Depoimentos", "CTA", "Páginas Internas".
2. WHEN o administrador salva qualquer aba, THE CMS_Controller SHALL redirecionar de volta para a mesma aba com mensagem de sucesso ou erro.
3. THE Admin_Panel SHALL incluir um link "Conteúdo Frontend" na sidebar de configurações existente (`resources/views/modules/settings/_sidebar.blade.php`), apontando para `/admin/settings/frontend-cms`.
4. THE Admin_Panel SHALL exibir uma pré-visualização do valor de overlay como uma barra visual (0–100%) ao lado do campo numérico correspondente.

---

### Requirement 10: Extensibilidade para Novas Seções

**User Story:** Como desenvolvedor, quero que o sistema CMS seja extensível para novas seções do frontend sem modificar o backend, para que futuras implementações sejam apenas de views e chaves de configuração.

#### Acceptance Criteria

1. THE CMS_Controller SHALL aceitar e salvar qualquer conjunto de chaves prefixadas com um identificador de seção (ex: `hero_`, `about_`, `cta_`) sem necessitar de alterações no controller para novas seções.
2. WHEN uma nova seção é adicionada ao frontend, THE FrontendSettingsProvider SHALL disponibilizá-la automaticamente em `$frontendContent` via `Setting::group('cms')`, sem necessidade de código adicional no provider.
3. THE Setting model SHALL persistir novas chaves do grupo `cms` via `setMany()` sem necessidade de migrations adicionais, pois a tabela `settings` já suporta chaves arbitrárias.
