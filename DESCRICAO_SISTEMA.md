# DESCRIÇÃO DO SISTEMA DE TRANSPORTE PÚBLICO SANTA TEREZINHA

## 1. TELA DE LOGIN

A tela de login é o ponto de entrada do sistema, responsável pela autenticação de todos os usuários. Esta interface apresenta um formulário de acesso que requer três informações essenciais:

### 1.1 Funcionalidades

- **Seleção de Tipo de Usuário**: O sistema oferece quatro perfis distintos:
  - Passageiro: para usuários que utilizam o transporte público
  - Motorista: para profissionais que operam os veículos
  - Mecânico: para técnicos responsáveis pela manutenção
  - Gestor: para administradores do sistema

- **Autenticação**: O usuário deve informar seu nome de usuário (username) e senha para acessar o sistema.

- **Validação de Credenciais**: O sistema valida se o tipo de usuário selecionado corresponde ao tipo cadastrado no banco de dados, garantindo que cada perfil acesse apenas sua interface específica.

- **Redirecionamento Automático**: Após autenticação bem-sucedida, o usuário é automaticamente redirecionado para sua respectiva interface (passageiro.html, motorista.html, mecanico.html ou gestor.html).

- **Proteção de Cookie**: Implementa mecanismo de proteção InfinityFree através de cookie `__test`, garantindo que requisições à API sejam processadas corretamente.

---

## 2. TELA DO PASSAGEIRO

A interface do passageiro foi desenvolvida para fornecer informações essenciais sobre o transporte público, permitindo que os usuários consultem horários, recebam avisos e se comuniquem com a empresa.

### 2.1 Dashboard (Visão Geral)

O dashboard apresenta indicadores-chave (KPIs) em cards visuais:
- **Linhas Ativas**: Exibe a quantidade total de linhas de ônibus em operação
- **Avisos Ativos**: Mostra o número de avisos e comunicados publicados
- **Próximo Ônibus**: Indica o horário do próximo veículo disponível

### 2.2 Aba: Horários

Esta seção permite que o passageiro consulte informações detalhadas sobre as rotas disponíveis:

- **Listagem de Rotas**: Exibe todas as linhas de ônibus cadastradas no sistema
- **Informações por Rota**: Para cada rota, são apresentadas:
  - Nome e número da linha
  - Status de operação (Em operação / Interrompida)
  - Origem e destino
  - Duração estimada da viagem
  - Tarifa atual
- **Busca de Rotas**: Campo de pesquisa que permite filtrar rotas por nome ou número
- **Visualização em Cards**: Interface moderna com cards informativos para melhor experiência do usuário

### 2.3 Aba: Avisos

Central de comunicação onde a empresa publica informações importantes:

- **Listagem de Avisos**: Exibe todos os avisos publicados pela gestão
- **Informações do Aviso**: Cada aviso contém:
  - Título
  - Conteúdo completo da mensagem
  - Data de publicação
  - Nível de prioridade (alta, média, baixa)
- **Atualização em Tempo Real**: Os avisos são carregados dinamicamente do banco de dados

### 2.4 Aba: Chat

Sistema de comunicação bidirecional entre passageiros e a empresa:

- **Visualização de Mensagens**: Exibe histórico de conversas com mensagens do passageiro e respostas da empresa
- **Envio de Mensagens**: Campo de texto para que o passageiro envie dúvidas, sugestões ou reclamações
- **Identificação**: Cada mensagem mostra o nome do usuário e data/hora do envio
- **Interface de Chat Moderna**: Design similar a aplicativos de mensagens, facilitando a interação

---

## 3. TELA DO MOTORISTA

A interface do motorista foi projetada para facilitar o gerenciamento das atividades diárias, permitindo que o profissional reporte problemas, consulte sua escala e controle o início/fim das linhas.

### 3.1 Dashboard (Visão Geral)

O dashboard apresenta três indicadores principais:
- **Status da Linha**: Mostra se a linha está "Parada" ou "Em Operação"
- **Escala de Hoje**: Exibe o horário de início e a rota atribuída para o dia
- **Chamados Enviados**: Contador de problemas reportados que aguardam resposta

### 3.2 Aba: Reportar Problemas

Formulário completo para registro de problemas técnicos:

- **Tipo de Problema**: Seleção entre:
  - Problema Mecânico
  - Problema Elétrico
  - Problema com Pneus
  - Outro
- **Nível de Urgência**: Classificação em quatro níveis:
  - Baixa (🟢)
  - Média (🟡)
  - Alta (🟠)
  - Crítica (🔴)
- **Descrição Detalhada**: Campo de texto livre para descrever o problema em detalhes
- **Envio Automático**: O chamado é automaticamente associado ao veículo da escala atual do motorista
- **Notificação**: Sistema envia notificação de sucesso após envio do chamado

### 3.3 Aba: Minha Escala

Visualização completa das escalas atribuídas ao motorista:

- **Listagem de Escalas**: Exibe todas as escalas do motorista, ordenadas por data
- **Destaque para Escala de Hoje**: A escala do dia atual é destacada visualmente com borda verde e fundo diferenciado
- **Informações da Escala**: Para cada escala são exibidas:
  - Data e turno
  - Horário de início e fim
  - Linha/rota atribuída
  - Veículo designado
  - Status da escala (confirmado, pendente, etc.)
- **Conexão com Controle de Linha**: Escala de hoje possui link direto para a aba de Controle de Linha
- **Atualização Automática**: Dados são sincronizados automaticamente quando uma nova escala é cadastrada pelo gestor

### 3.4 Aba: Controle de Linha

Interface central para gerenciamento do início e fim das jornadas:

- **Status Visual da Linha**: 
  - Ícone grande indicando se a linha está parada (pausa) ou em operação (play)
  - Título e subtítulo descritivos do status atual
- **Informações da Escala Atual**: Exibe automaticamente:
  - Veículo atribuído
  - Horário da escala
  - Linha/rota a ser executada
- **Botão de Controle**: 
  - "Iniciar Linha": Aparece quando a linha está parada, permitindo iniciar a jornada
  - "Finalizar Linha": Aparece quando a linha está em operação, permitindo encerrar a jornada
- **Registro de Localização**: Ao iniciar a linha, o sistema captura automaticamente a localização GPS do motorista
- **Histórico do Dia**: Seção que registra todas as linhas iniciadas e finalizadas no dia atual, mostrando:
  - Veículo utilizado
  - Linha executada
  - Horário de início/fim
  - Status (Em Andamento / Finalizada)
- **Sincronização com Escala**: Os dados são automaticamente carregados da escala de hoje ou da próxima escala disponível
- **Atualização em Tempo Real**: Status é atualizado em todas as áreas do sistema (dashboard, controle, KPI cards) quando a linha é iniciada ou finalizada

---

## 4. TELA DO MECÂNICO

A interface do mecânico foi desenvolvida para gerenciar eficientemente os chamados de manutenção, permitindo que o técnico assuma, resolva e registre soluções aplicadas.

### 4.1 Dashboard (Visão Geral)

O dashboard apresenta três indicadores principais em cards:
- **Chamados Abertos**: Quantidade de problemas reportados aguardando atendimento
- **Chamados em Andamento**: Quantidade de problemas que estão sendo resolvidos
- **Chamados Resolvidos**: Quantidade de problemas já solucionados

### 4.2 Aba: Chamados

Painel completo de gestão de chamados técnicos:

- **Listagem de Chamados**: Exibe todos os chamados do sistema, organizados por status
- **Informações do Chamado**: Cada chamado apresenta:
  - ID do chamado
  - Nome do motorista que reportou
  - Veículo envolvido (número do ônibus)
  - Tipo de problema (mecânico, elétrico, pneus, outro)
  - Descrição detalhada do problema
  - Nível de urgência (baixa, média, alta, crítica)
  - Status atual (aberto, em_andamento, resolvido)
  - Data de abertura
  - Mecânico responsável (quando atribuído)
- **Ações Disponíveis**:
  - **Assumir Chamado**: Botão disponível para chamados com status "aberto", permitindo que o mecânico se responsabilize pelo atendimento
  - **Marcar como Resolvido**: Botão disponível para chamados "em_andamento", abrindo modal para registro da solução
- **Modal de Resolução**: Ao resolver um chamado, o mecânico pode registrar:
  - Descrição da solução aplicada
  - Peças utilizadas (opcional)
  - Custo da manutenção (opcional)
  - Observações adicionais
- **Atualização Automática**: Lista é atualizada automaticamente após cada ação (assumir/resolver)
- **Filtros Visuais**: Chamados são destacados visualmente conforme status e urgência

---

## 5. TELA DO GESTOR

A interface do gestor é a mais completa do sistema, oferecendo controle total sobre todas as operações, desde cadastros básicos até geração de relatórios gerenciais.

### 5.1 Dashboard (Visão Geral)

Painel executivo com indicadores estratégicos:
- **Chamados Abertos**: Total de problemas pendentes
- **Mensagens Pendentes**: Quantidade de mensagens de passageiros aguardando resposta
- **Status da Frota**: Gráfico de pizza mostrando distribuição de veículos (Disponíveis, Em Manutenção, Fora de Serviço)
- **Status dos Motoristas**: Gráfico de pizza mostrando distribuição (Dirigindo, Fora de Escala)

### 5.2 Aba: Dashboard

Visão consolidada de todos os indicadores do sistema com gráficos interativos e métricas em tempo real.

### 5.3 Aba: Gestão

Seção central para administração de recursos do sistema, dividida em sub-abas:

#### 5.3.1 Sub-aba: Avisos
- **Listagem de Avisos**: Exibe todos os avisos publicados
- **Criar Novo Aviso**: Botão que abre modal para cadastro com campos:
  - Título do aviso
  - Conteúdo/mensagem
  - Tipo de aviso
  - Prioridade (alta, média, baixa)
  - Data de expiração (opcional)
- **Editar Aviso**: Permite modificar avisos existentes
- **Excluir Aviso**: Remove avisos do sistema

#### 5.3.2 Sub-aba: Horários
- **Listagem de Rotas**: Exibe todas as rotas cadastradas
- **Informações por Rota**: Mostra nome, status, horários disponíveis
- **Editar Rota**: Permite modificar informações da rota
- **Alterar Status**: Permite pausar ou reativar uma rota

#### 5.3.3 Sub-aba: Escalas
- **Listagem de Escalas**: Exibe todas as escalas cadastradas
- **Criar Nova Escala**: Modal completo para cadastro com campos:
  - Motorista (seleção de lista)
  - Data da escala
  - Turno (manhã, tarde, noite)
  - Horário de início e fim
  - Rota/linha (seleção de lista)
  - Veículo (seleção de lista)
- **Editar Escala**: Permite modificar escalas existentes
- **Excluir Escala**: Remove escalas do sistema
- **Visualização em Tabela**: Interface tabular para fácil visualização de todas as escalas

### 5.4 Aba: Chamados

Gestão completa de todos os chamados do sistema:
- **Listagem Completa**: Exibe todos os chamados, independente de status
- **Informações Detalhadas**: Mostra motorista, veículo, tipo, descrição, urgência, status e mecânico responsável
- **Visão Gerencial**: Permite ao gestor acompanhar o andamento de todos os chamados

### 5.5 Aba: Mensagens

Central de comunicação com passageiros:
- **Listagem de Mensagens**: Exibe todas as mensagens enviadas pelos passageiros
- **Responder Mensagem**: Permite que o gestor responda diretamente aos passageiros
- **Marcar como Respondida**: Opção para organizar mensagens já atendidas
- **Filtro por Tipo**: Mostra apenas mensagens de usuários (passageiros)

### 5.6 Aba: Relatórios

Sistema completo de geração de relatórios gerenciais:

#### 5.6.1 Relatório Operacional
- Status da frota (total, disponíveis, em manutenção, fora de serviço)
- Status dos motoristas (total, dirigindo, fora de escala)
- Indicadores gerais (chamados abertos, mensagens pendentes)

#### 5.6.2 Relatório de Manutenção
- Listagem de todos os chamados resolvidos
- Informações sobre soluções aplicadas
- Peças utilizadas e custos (quando informados)
- Datas de resolução

#### 5.6.3 Relatório de Passageiros
- Todas as mensagens recebidas dos passageiros
- Datas de envio
- Organização cronológica

#### 5.6.4 Relatório de Chamados
- Listagem completa de todos os chamados
- Status, urgência, tipo
- Motoristas e veículos envolvidos
- Datas de abertura

---

## CARACTERÍSTICAS GERAIS DO SISTEMA

### Design e Interface
- **Design Moderno**: Interface limpa e intuitiva com paleta de cores consistente
- **Responsivo**: Adaptável a diferentes tamanhos de tela
- **Navegação por Abas**: Sistema de abas para organização do conteúdo
- **Cards Informativos**: Uso de cards para apresentação de informações
- **Ícones Font Awesome**: Uso extensivo de ícones para melhor identificação visual

### Funcionalidades Técnicas
- **API RESTful**: Comunicação com backend através de requisições HTTP
- **Autenticação JWT**: Sistema de tokens para segurança
- **LocalStorage**: Armazenamento local de dados do usuário
- **Atualização em Tempo Real**: Dados são recarregados automaticamente após ações
- **Tratamento de Erros**: Mensagens de erro claras e tratamento de exceções
- **Validação de Formulários**: Validação client-side e server-side

### Sincronização de Dados
- **Sincronização Automática**: Dados são sincronizados entre diferentes áreas do sistema
- **Busca Inteligente**: Sistema busca escalas de hoje ou próxima disponível
- **Atualização Dinâmica**: KPIs e indicadores são atualizados automaticamente

### Experiência do Usuário
- **Notificações Visuais**: Sistema de notificações toast para feedback de ações
- **Loading States**: Indicadores de carregamento durante requisições
- **Empty States**: Mensagens quando não há dados para exibir
- **Modais**: Uso de modais para ações que requerem confirmação ou entrada de dados

---

## CONCLUSÃO

O Sistema de Transporte Público Santa Terezinha oferece uma solução completa e integrada para gestão de transporte urbano, atendendo às necessidades de quatro perfis distintos de usuários. Cada interface foi cuidadosamente projetada para otimizar o fluxo de trabalho específico de cada perfil, garantindo eficiência operacional e melhor experiência tanto para os funcionários quanto para os passageiros.

