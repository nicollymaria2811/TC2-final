<?php
/**
 * API REST - Santa Terezinha Transporte
 * Endpoints para integração futura com o frontend
 */

require_once 'config.php';

// --- CORREÇÃO 1: Headers de CORS Completos ---
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Permite conexões de qualquer origem (localhost)
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Se o navegador fizer uma pergunta prévia (OPTIONS), responde que SIM e para.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verificar método da requisição
$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// --- CORREÇÃO 2: Detecção Automática da Pasta (Resolve o erro 404) ---
// Isso faz funcionar independente se a pasta chamar "SantaTerezinhaBus" ou "SantaTerezinhaBus_Antigo"
$scriptName = $_SERVER['SCRIPT_NAME']; 
$path = str_replace($scriptName, '', $path);

// Roteamento da API
switch ($method) {
    case 'GET':
        handleGetRequest($path);
        break;
    case 'POST':
        handlePostRequest($path);
        break;
    case 'PUT':
        handlePutRequest($path);
        break;
    case 'DELETE':
        handleDeleteRequest($path);
        break;
    default:
        Utils::jsonResponse(['error' => 'Método não permitido'], 405);
}

/**
 * Manipular requisições GET
 */
function handleGetRequest($path) {
    // Remove barras extras do início/fim
    $path = trim($path, '/');
    $pathParts = explode('/', $path);
    
    // Pega a primeira parte da rota (ex: 'rotas', 'avisos')
    $endpoint = $pathParts[0] ?? '';

    switch ($endpoint) {
        case 'rotas':
            getRotas();
            break;
        case 'avisos':
            getAvisos();
            break;
        case 'chamados':
            getChamados();
            break;
        case 'escalas':
            getEscalas();
            break;
        case 'veiculos':
            getVeiculos();
            break;
        case 'dashboard':
            getDashboard();
            break;
        case 'mensagens':
            getMensagens();
            break;
        case 'usuarios':
            if (isset($pathParts[1]) && $pathParts[1] === 'motoristas') {
                getMotoristas();
            } else {
                Utils::jsonResponse(['error' => 'Endpoint não encontrado'], 404);
            }
            break;
        case 'inicio-linha':
            if (isset($pathParts[1]) && $pathParts[1] === 'ativo') {
                getInicioLinhaAtivo();
            } else {
                getInicioLinha();
            }
            break;
        case 'horario-passagem':
            getHorarioPassagem($pathParts);
            break;
        default:
            Utils::jsonResponse(['error' => 'Endpoint não encontrado: ' . $endpoint], 404);
    }
}

/**
 * Manipular requisições POST
 */
function handlePostRequest($path) {
    $path = trim($path, '/');
    $pathParts = explode('/', $path);
    $endpoint = $pathParts[0] ?? '';

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    
    // Log para debug
    Utils::log('DEBUG', 'POST Request', [
        'endpoint' => $endpoint,
        'path' => $path,
        'raw_input' => $rawInput,
        'decoded_data' => $data
    ]);
    
    // Verificar se o JSON foi decodificado corretamente
    if (json_last_error() !== JSON_ERROR_NONE) {
        Utils::jsonResponse(['error' => 'JSON inválido: ' . json_last_error_msg()], 400);
        return;
    }
    
    if ($data === null && $rawInput !== '') {
        Utils::jsonResponse(['error' => 'Erro ao decodificar JSON'], 400);
        return;
    }
    
    switch ($endpoint) {
        case 'login':
            login($data);
            break;
        case 'chamados':
            criarChamado($data);
            break;
        case 'mensagens':
            enviarMensagem($data);
            break;
        case 'checklist':
            atualizarChecklist($data);
            break;
        case 'escalas':
            criarEscala($data);
            break;
        case 'avisos':
            criarAviso($data);
            break;
        case 'inicio-linha':
            criarInicioLinha($data);
            break;
        default:
            Utils::jsonResponse(['error' => 'Endpoint não encontrado'], 404);
    }
}

/**
 * Manipular requisições PUT
 */
function handlePutRequest($path) {
    $path = trim($path, '/');
    $pathParts = explode('/', $path);
    $endpoint = $pathParts[0] ?? '';

    $data = json_decode(file_get_contents('php://input'), true);
    
    switch ($endpoint) {
        case 'chamados':
            if (isset($pathParts[1])) {
                atualizarChamado($pathParts[1], $data);
            } else {
                Utils::jsonResponse(['error' => 'ID do chamado não fornecido'], 400);
            }
            break;
        case 'escalas':
            if (isset($pathParts[1])) {
                atualizarEscala($pathParts[1], $data);
            } else {
                Utils::jsonResponse(['error' => 'ID da escala não fornecido'], 400);
            }
            break;
        case 'avisos':
            if (isset($pathParts[1])) {
                atualizarAviso($pathParts[1], $data);
            } else {
                Utils::jsonResponse(['error' => 'ID do aviso não fornecido'], 400);
            }
            break;
        case 'mensagens':
            if (isset($pathParts[1])) {
                atualizarMensagem($pathParts[1], $data);
            } else {
                Utils::jsonResponse(['error' => 'ID da mensagem não fornecido'], 400);
            }
            break;
        default:
            Utils::jsonResponse(['error' => 'Endpoint não encontrado'], 404);
    }
}

/**
 * Manipular requisições DELETE
 */
function handleDeleteRequest($path) {
    $path = trim($path, '/');
    $pathParts = explode('/', $path);
    $endpoint = $pathParts[0] ?? '';
    
    switch ($endpoint) {
        case 'chamados':
            if (isset($pathParts[1])) {
                deletarChamado($pathParts[1]);
            } else {
                Utils::jsonResponse(['error' => 'ID do chamado não fornecido'], 400);
            }
            break;
        case 'avisos':
            if (isset($pathParts[1])) {
                deletarAviso($pathParts[1]);
            } else {
                Utils::jsonResponse(['error' => 'ID do aviso não fornecido'], 400);
            }
            break;
        default:
            Utils::jsonResponse(['error' => 'Endpoint não encontrado'], 404);
    }
}

/**
 * Endpoints específicos
 */

function getRotas() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM rotas WHERE ativa = 1 ORDER BY nome");
        $rotas = $stmt->fetchAll();
        
        // Buscar horários para cada rota
        foreach ($rotas as &$rota) {
            $stmt = $db->prepare("SELECT horario, dia_semana FROM horarios_rotas WHERE rota_id = ? AND ativo = 1 ORDER BY horario");
            $stmt->execute([$rota['id']]);
            $rota['horarios'] = $stmt->fetchAll();
        }
        
        Utils::jsonResponse(['success' => true, 'data' => $rotas]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar rotas: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getAvisos() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT a.*, u.nome as autor_nome 
            FROM avisos a 
            LEFT JOIN usuarios u ON a.autor_id = u.id 
            WHERE a.ativo = 1 
            ORDER BY a.data_publicacao DESC
        ");
        $avisos = $stmt->fetchAll();
        
        Utils::jsonResponse(['success' => true, 'data' => $avisos]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar avisos: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getChamados() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT c.*, u1.nome as motorista_nome, u2.nome as mecanico_nome, v.numero as veiculo_numero
            FROM chamados c
            LEFT JOIN usuarios u1 ON c.motorista_id = u1.id
            LEFT JOIN usuarios u2 ON c.mecanico_id = u2.id
            LEFT JOIN veiculos v ON c.veiculo_id = v.id
            ORDER BY c.data_abertura DESC
        ");
        $chamados = $stmt->fetchAll();
        
        Utils::jsonResponse(['success' => true, 'data' => $chamados]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar chamados: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getEscalas() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT e.*, u.nome as motorista_nome, r.nome as rota_nome, v.numero as veiculo_numero
            FROM escalas e
            LEFT JOIN usuarios u ON e.motorista_id = u.id
            LEFT JOIN rotas r ON e.rota_id = r.id
            LEFT JOIN veiculos v ON e.veiculo_id = v.id
            WHERE e.data >= CURDATE()
            ORDER BY e.data, e.horario_inicio
        ");
        $escalas = $stmt->fetchAll();
        
        Utils::jsonResponse(['success' => true, 'data' => $escalas]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar escalas: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getVeiculos() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM veiculos ORDER BY numero");
        $veiculos = $stmt->fetchAll();
        
        Utils::jsonResponse(['success' => true, 'data' => $veiculos]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar veículos: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getMotoristas() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT id, username, nome, email FROM usuarios WHERE tipo = 'motorista' AND ativo = 1 ORDER BY nome");
        $motoristas = $stmt->fetchAll();
        
        Utils::jsonResponse(['success' => true, 'data' => $motoristas]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar motoristas: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getInicioLinha() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT il.*, r.nome as rota_nome, r.duracao, r.tarifa, v.numero as veiculo_numero, u.nome as motorista_nome
            FROM inicio_linha il
            LEFT JOIN rotas r ON il.rota_id = r.id
            LEFT JOIN veiculos v ON il.veiculo_id = v.id
            LEFT JOIN usuarios u ON il.motorista_id = u.id
            WHERE il.status = 'ativa'
            ORDER BY il.horario_inicio DESC
            LIMIT 1
        ");
        $inicio = $stmt->fetch();
        
        Utils::jsonResponse(['success' => true, 'data' => $inicio ?: null]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar início de linha: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getInicioLinhaAtivo() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT il.*, r.nome as rota_nome, r.duracao, r.tarifa, r.origem, r.destino, v.numero as veiculo_numero, u.nome as motorista_nome
            FROM inicio_linha il
            LEFT JOIN rotas r ON il.rota_id = r.id
            LEFT JOIN veiculos v ON il.veiculo_id = v.id
            LEFT JOIN usuarios u ON il.motorista_id = u.id
            WHERE il.status = 'ativa'
            ORDER BY il.horario_inicio DESC
            LIMIT 1
        ");
        $inicio = $stmt->fetch();
        
        if ($inicio) {
            // Buscar horários da rota
            $stmt = $db->prepare("SELECT horario, dia_semana FROM horarios_rotas WHERE rota_id = ? AND ativo = 1 ORDER BY horario");
            $stmt->execute([$inicio['rota_id']]);
            $inicio['horarios'] = $stmt->fetchAll();
        }
        
        Utils::jsonResponse(['success' => true, 'data' => $inicio ?: null]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar início de linha ativo: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getHorarioPassagem($pathParts) {
    try {
        $rotaId = $pathParts[1] ?? null;
        $latitude = $pathParts[2] ?? null;
        $longitude = $pathParts[3] ?? null;
        
        if (!$rotaId) {
            Utils::jsonResponse(['error' => 'ID da rota é obrigatório'], 400);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Buscar início de linha ativo para esta rota
        $stmt = $db->prepare("
            SELECT il.*, r.nome as rota_nome, r.duracao, r.tarifa, r.origem, r.destino
            FROM inicio_linha il
            LEFT JOIN rotas r ON il.rota_id = r.id
            WHERE il.rota_id = ? AND il.status = 'ativa'
            ORDER BY il.horario_inicio DESC
            LIMIT 1
        ");
        $stmt->execute([$rotaId]);
        $inicioLinha = $stmt->fetch();
        
        if (!$inicioLinha) {
            Utils::jsonResponse(['success' => true, 'data' => null, 'message' => 'Nenhuma linha ativa para esta rota']);
            return;
        }
        
        // Buscar horários da rota
        $stmt = $db->prepare("SELECT horario, dia_semana FROM horarios_rotas WHERE rota_id = ? AND ativo = 1 ORDER BY horario");
        $stmt->execute([$rotaId]);
        $horarios = $stmt->fetchAll();
        
        // Calcular horário aproximado de passagem baseado no horário de início
        $horarioInicio = strtotime($inicioLinha['horario_inicio']);
        $agora = time();
        $tempoDecorrido = ($agora - $horarioInicio) / 60; // minutos desde o início
        
        // Extrair duração da rota (ex: "25 min" -> 25)
        $duracaoRota = 30; // padrão
        if (isset($inicioLinha['duracao']) && preg_match('/(\d+)/', $inicioLinha['duracao'], $matches)) {
            $duracaoRota = (int)$matches[1];
        }
        
        // Calcular progresso estimado da rota (assumindo que o ônibus está em movimento)
        // Se passou menos de 5 minutos, o ônibus ainda está no início
        // Se passou mais que a duração total, já completou a rota
        $progressoPercentual = min(100, max(0, ($tempoDecorrido / $duracaoRota) * 100));
        
        // Estimar tempo até o ponto baseado na distância e velocidade média do ônibus
        // Velocidade média urbana: ~30 km/h = 0.5 km/min
        // Se tiver latitude/longitude, calcular distância real (futuro)
        $velocidadeMediaKmMin = 0.5; // km por minuto
        $distanciaEstimada = 2.0; // km (estimativa padrão)
        
        if ($latitude && $longitude && isset($inicioLinha['latitude']) && isset($inicioLinha['longitude'])) {
            // Calcular distância real usando fórmula de Haversine
            $lat1 = deg2rad($inicioLinha['latitude']);
            $lon1 = deg2rad($inicioLinha['longitude']);
            $lat2 = deg2rad($latitude);
            $lon2 = deg2rad($longitude);
            
            $dLat = $lat2 - $lat1;
            $dLon = $lon2 - $lon1;
            
            $a = sin($dLat/2) * sin($dLat/2) + cos($lat1) * cos($lat2) * sin($dLon/2) * sin($dLon/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            $distanciaEstimada = 6371 * $c; // distância em km (raio da Terra = 6371 km)
        }
        
        $tempoEstimado = round($distanciaEstimada / $velocidadeMediaKmMin); // minutos
        $horarioPassagem = date('H:i', $horarioInicio + ($tempoEstimado * 60));
        
        // Verificar se já passou
        $tempoTotal = $horarioInicio + ($tempoEstimado * 60);
        $jaPassou = $agora > $tempoTotal;
        
        // Se já passou, calcular próximo horário baseado nos horários da rota
        $proximoHorario = null;
        if ($jaPassou && !empty($horarios)) {
            $horaAtual = (int)date('H') * 60 + (int)date('i'); // minutos desde meia-noite
            foreach ($horarios as $horario) {
                $horarioMinutos = (int)substr($horario['horario'], 0, 2) * 60 + (int)substr($horario['horario'], 3, 2);
                if ($horarioMinutos > $horaAtual) {
                    $proximoHorario = $horario['horario'];
                    break;
                }
            }
            // Se não encontrou horário hoje, pegar o primeiro de amanhã
            if (!$proximoHorario && !empty($horarios)) {
                $proximoHorario = $horarios[0]['horario'];
            }
        }
        
        Utils::jsonResponse([
            'success' => true,
            'data' => [
                'rota' => $inicioLinha,
                'horarios' => $horarios,
                'horario_inicio' => date('H:i', $horarioInicio),
                'horario_passagem_estimado' => $horarioPassagem,
                'proximo_horario' => $proximoHorario,
                'tempo_estimado_minutos' => $tempoEstimado,
                'distancia_estimada_km' => round($distanciaEstimada, 2),
                'ja_passou' => $jaPassou,
                'tempo_decorrido_minutos' => round($tempoDecorrido, 1),
                'progresso_percentual' => round($progressoPercentual, 1),
                'tarifa' => $inicioLinha['tarifa']
            ]
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao calcular horário de passagem: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function criarInicioLinha($data) {
    try {
        $required = ['motorista_id', 'escala_id', 'rota_id', 'veiculo_id'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                Utils::jsonResponse(['error' => "O campo $field é obrigatório"], 400);
                return;
            }
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Finalizar outras linhas ativas do mesmo motorista
        $stmt = $db->prepare("UPDATE inicio_linha SET status = 'finalizada', horario_fim = NOW() WHERE motorista_id = ? AND status = 'ativa'");
        $stmt->execute([$data['motorista_id']]);
        
        // Criar novo início de linha
        $stmt = $db->prepare("
            INSERT INTO inicio_linha (motorista_id, escala_id, rota_id, veiculo_id, latitude, longitude)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['motorista_id'],
            $data['escala_id'],
            $data['rota_id'],
            $data['veiculo_id'],
            $data['latitude'] ?? null,
            $data['longitude'] ?? null
        ]);
        
        $id = $db->lastInsertId();
        Utils::log('INFO', 'Início de linha criado', ['inicio_linha_id' => $id]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Linha iniciada com sucesso!',
            'id' => $id
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao criar início de linha: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro ao iniciar linha'], 500);
    }
}

function getDashboard() {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Status da frota
        $stmt = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'disponivel' THEN 1 ELSE 0 END) as disponiveis,
                SUM(CASE WHEN status = 'manutencao' THEN 1 ELSE 0 END) as em_manutencao,
                SUM(CASE WHEN status = 'fora_servico' THEN 1 ELSE 0 END) as fora_servico
            FROM veiculos
        ");
        $frota = $stmt->fetch();
        
        // Status dos motoristas
        $stmt = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN e.status = 'confirmado' AND e.data = CURDATE() THEN 1 ELSE 0 END) as dirigindo,
                COUNT(*) - SUM(CASE WHEN e.status = 'confirmado' AND e.data = CURDATE() THEN 1 ELSE 0 END) as fora_escala
            FROM usuarios u
            LEFT JOIN escalas e ON u.id = e.motorista_id
            WHERE u.tipo = 'motorista' AND u.ativo = 1
        ");
        $motoristas = $stmt->fetch();
        
        // Chamados abertos
        $stmt = $db->query("SELECT COUNT(*) as chamados_abertos FROM chamados WHERE status = 'aberto'");
        $chamadosAbertos = $stmt->fetch()['chamados_abertos'];
        
        // Mensagens pendentes
        $stmt = $db->query("SELECT COUNT(*) as mensagens_pendentes FROM mensagens_chat WHERE respondida = 0");
        $mensagensPendentes = $stmt->fetch()['mensagens_pendentes'];
        
        $dashboard = [
            'frota' => $frota,
            'motoristas' => $motoristas,
            'chamados_abertos' => $chamadosAbertos,
            'mensagens_pendentes' => $mensagensPendentes
        ];
        
        Utils::jsonResponse(['success' => true, 'data' => $dashboard]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar dados do dashboard: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function getMensagens() {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT m.*, u.nome as usuario_nome
            FROM mensagens_chat m
            LEFT JOIN usuarios u ON m.usuario_id = u.id
            ORDER BY m.data_envio DESC
            LIMIT 50
        ");
        $mensagens = $stmt->fetchAll();
        
        Utils::jsonResponse(['success' => true, 'data' => $mensagens]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao buscar mensagens: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function login($data) {
    try {
        if (!isset($data['username']) || !isset($data['password'])) {
            Utils::jsonResponse(['error' => 'Username e password são obrigatórios'], 400);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE username = ? AND ativo = 1");
        $stmt->execute([$data['username']]);
        $usuario = $stmt->fetch();
        
        if (!$usuario) {
            Utils::log('WARNING', 'Tentativa de login com usuário inexistente ou inativo', ['username' => $data['username']]);
            Utils::jsonResponse(['error' => 'Credenciais inválidas'], 401);
            return;
        }
        
        // Verificar senha: primeiro tenta com password_verify (senhas criptografadas),
        // se falhar, tenta comparação direta (senhas em texto plano)
        $senhaValida = false;
        
        // Verifica se a senha no banco parece ser um hash (começa com $2y$ ou $2a$)
        if (strpos($usuario['senha'], '$2y$') === 0 || strpos($usuario['senha'], '$2a$') === 0) {
            // Senha está criptografada, usa password_verify
            $senhaValida = password_verify($data['password'], $usuario['senha']);
        } else {
            // Senha está em texto plano, compara diretamente
            $senhaValida = ($data['password'] === $usuario['senha']);
        }
        
        if ($senhaValida) {
            $token = Utils::generateToken([
                'user_id' => $usuario['id'],
                'username' => $usuario['username'],
                'tipo' => $usuario['tipo'],
                'exp' => time() + (24 * 60 * 60) // 24 horas
            ]);
            
            Utils::log('INFO', 'Login realizado com sucesso', ['username' => $usuario['username'], 'tipo' => $usuario['tipo']]);
            
            Utils::jsonResponse([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $usuario['id'],
                    'username' => $usuario['username'],
                    'nome' => $usuario['nome'],
                    'tipo' => $usuario['tipo']
                ]
            ]);
        } else {
            Utils::log('WARNING', 'Tentativa de login com senha incorreta', ['username' => $data['username']]);
            Utils::jsonResponse(['error' => 'Credenciais inválidas'], 401);
        }
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro no login: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function criarChamado($data) {
    try {
        Utils::log('DEBUG', 'criarChamado recebeu', ['data' => $data]);
        
        $requiredFields = ['motorista_id', 'veiculo_id', 'tipo', 'descricao', 'urgencia'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                Utils::log('ERROR', 'Campo obrigatório faltando', ['field' => $field, 'data' => $data]);
                Utils::jsonResponse(['error' => "Campo obrigatório: $field"], 400);
                return;
            }
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO chamados (motorista_id, veiculo_id, tipo, descricao, urgencia) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['motorista_id'],
            $data['veiculo_id'],
            $data['tipo'],
            $data['descricao'],
            $data['urgencia']
        ]);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            Utils::log('ERROR', 'Erro ao executar INSERT', ['error' => $errorInfo]);
            Utils::jsonResponse(['error' => 'Erro ao salvar no banco: ' . $errorInfo[2]], 500);
            return;
        }
        
        $chamadoId = $db->lastInsertId();
        
        Utils::log('INFO', 'Chamado criado', ['chamado_id' => $chamadoId, 'dados' => $data]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Chamado criado com sucesso',
            'chamado_id' => $chamadoId
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao criar chamado: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function enviarMensagem($data) {
    try {
        Utils::log('DEBUG', 'enviarMensagem recebeu', ['data' => $data]);
        
        if (!isset($data['mensagem'])) {
            Utils::log('ERROR', 'Campo mensagem obrigatório faltando', ['data' => $data]);
            Utils::jsonResponse(['error' => 'mensagem é obrigatório'], 400);
            return;
        }
        
        // Se for mensagem do sistema e não tiver usuario_id, usar NULL
        // Mas a tabela exige usuario_id NOT NULL, então precisamos usar um ID válido
        // Vamos buscar um usuário gestor ou criar um usuário sistema
        $db = Database::getInstance()->getConnection();
        
        $usuarioId = $data['usuario_id'] ?? null;
        $tipo = $data['tipo'] ?? 'user';
        
        // Se for mensagem do sistema sem usuario_id, buscar um gestor ou usar o primeiro usuário
        if ($tipo === 'system' && !$usuarioId) {
            $stmt = $db->query("SELECT id FROM usuarios WHERE tipo = 'gestor' LIMIT 1");
            $gestor = $stmt->fetch();
            $usuarioId = $gestor ? $gestor['id'] : 1; // Fallback para ID 1 se não encontrar gestor
        }
        
        // Se ainda não tiver usuario_id, usar 1 como fallback
        if (!$usuarioId) {
            $usuarioId = 1;
        }
        
        $stmt = $db->prepare("
            INSERT INTO mensagens_chat (usuario_id, mensagem, tipo) 
            VALUES (?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $usuarioId,
            $data['mensagem'],
            $tipo
        ]);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            Utils::log('ERROR', 'Erro ao executar INSERT', ['error' => $errorInfo]);
            Utils::jsonResponse(['error' => 'Erro ao salvar no banco: ' . $errorInfo[2]], 500);
            return;
        }
        
        $mensagemId = $db->lastInsertId();
        
        Utils::log('INFO', 'Mensagem enviada', ['mensagem_id' => $mensagemId, 'dados' => $data]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso',
            'mensagem_id' => $mensagemId
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao enviar mensagem: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function atualizarChamado($id, $data) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $fields = [];
        $values = [];
        
        if (isset($data['status'])) {
            $fields[] = 'status = ?';
            $values[] = $data['status'];
        }
        
        if (isset($data['mecanico_id'])) {
            $fields[] = 'mecanico_id = ?';
            $values[] = $data['mecanico_id'];
        }
        
        if (isset($data['observacoes'])) {
            $fields[] = 'observacoes = ?';
            $values[] = $data['observacoes'];
        }
        
        if (isset($data['status']) && $data['status'] === 'resolvido') {
            $fields[] = 'data_resolucao = NOW()';
        }
        
        if (empty($fields)) {
            Utils::jsonResponse(['error' => 'Nenhum campo para atualizar'], 400);
            return;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE chamados SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        
        Utils::log('INFO', 'Chamado atualizado', ['chamado_id' => $id]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Chamado atualizado com sucesso'
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao atualizar chamado: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function atualizarEscala($id, $data) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $fields = [];
        $values = [];
        
        if (isset($data['status'])) {
            $fields[] = 'status = ?';
            $values[] = $data['status'];
        }
        
        if (isset($data['observacoes'])) {
            $fields[] = 'observacoes = ?';
            $values[] = $data['observacoes'];
        }
        
        if (empty($fields)) {
            Utils::jsonResponse(['error' => 'Nenhum campo para atualizar'], 400);
            return;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE escalas SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        
        Utils::log('INFO', 'Escala atualizada', ['escala_id' => $id]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Escala atualizada com sucesso'
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao atualizar escala: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function atualizarChecklist($data) {
    try {
        if (!isset($data['item_id']) || !isset($data['concluido'])) {
            Utils::jsonResponse(['error' => 'item_id e concluido são obrigatórios'], 400);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            UPDATE checklist_manutencao 
            SET concluido = ?, data_verificacao = NOW(), mecanico_id = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['concluido'] ? 1 : 0,
            $data['mecanico_id'] ?? null,
            $data['item_id']
        ]);
        
        Utils::log('INFO', 'Checklist atualizado', ['item_id' => $data['item_id']]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Checklist atualizado com sucesso'
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao atualizar checklist: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function deletarChamado($id) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM chamados WHERE id = ?");
        $stmt->execute([$id]);
        
        Utils::log('INFO', 'Chamado deletado', ['chamado_id' => $id]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Chamado deletado com sucesso'
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao deletar chamado: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function criarEscala($data) {
    try {
        Utils::log('DEBUG', 'criarEscala recebeu', ['data' => $data]);
        
        // Validação básica
        $required = ['motorista_id', 'veiculo_id', 'rota_id', 'data', 'horario_inicio', 'horario_fim'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                Utils::log('ERROR', 'Campo obrigatório faltando', ['field' => $field, 'data' => $data]);
                Utils::jsonResponse(['error' => "O campo $field é obrigatório"], 400);
                return;
            }
        }

        $db = Database::getInstance()->getConnection();
        
        // Verifica se já existe escala para este motorista neste horário (evita conflito)
        $stmt = $db->prepare("
            SELECT id FROM escalas 
            WHERE motorista_id = ? 
            AND data = ? 
            AND (
                (horario_inicio BETWEEN ? AND ?) 
                OR (horario_fim BETWEEN ? AND ?)
                OR (? BETWEEN horario_inicio AND horario_fim)
                OR (? BETWEEN horario_inicio AND horario_fim)
            )
        ");
        $stmt->execute([
            $data['motorista_id'], 
            $data['data'], 
            $data['horario_inicio'], 
            $data['horario_fim'], 
            $data['horario_inicio'], 
            $data['horario_fim'],
            $data['horario_inicio'],
            $data['horario_fim']
        ]);
        
        if ($stmt->rowCount() > 0) {
            Utils::jsonResponse(['error' => 'Motorista já possui escala neste horário'], 409);
            return;
        }

        $stmt = $db->prepare("
            INSERT INTO escalas (motorista_id, veiculo_id, rota_id, data, horario_inicio, horario_fim, turno, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pendente')
        ");
        
        $result = $stmt->execute([
            $data['motorista_id'],
            $data['veiculo_id'],
            $data['rota_id'],
            $data['data'],
            $data['horario_inicio'],
            $data['horario_fim'],
            $data['turno'] ?? 'integral'
        ]);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            Utils::log('ERROR', 'Erro ao executar INSERT', ['error' => $errorInfo]);
            Utils::jsonResponse(['error' => 'Erro ao salvar no banco: ' . $errorInfo[2]], 500);
            return;
        }
        
        $id = $db->lastInsertId();
        
        Utils::log('INFO', 'Escala criada', ['escala_id' => $id, 'dados' => $data]);
        
        Utils::jsonResponse([
            'success' => true, 
            'message' => 'Escala lançada com sucesso!',
            'id' => $id
        ]);

    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao criar escala: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro ao salvar escala: ' . $e->getMessage()], 500);
    }
}

function criarAviso($data) {
    try {
        Utils::log('DEBUG', 'criarAviso recebeu', ['data' => $data]);
        
        // Validação básica
        $required = ['titulo', 'conteudo', 'tipo', 'prioridade'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                Utils::log('ERROR', 'Campo obrigatório faltando', ['field' => $field, 'data' => $data]);
                Utils::jsonResponse(['error' => "O campo $field é obrigatório"], 400);
                return;
            }
        }

        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("
            INSERT INTO avisos (titulo, conteudo, tipo, prioridade, autor_id, data_expiracao) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $dataExpiracao = !empty($data['data_expiracao']) ? $data['data_expiracao'] : null;
        
        $result = $stmt->execute([
            $data['titulo'],
            $data['conteudo'],
            $data['tipo'],
            $data['prioridade'],
            $data['autor_id'] ?? null,
            $dataExpiracao
        ]);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            Utils::log('ERROR', 'Erro ao executar INSERT', ['error' => $errorInfo]);
            Utils::jsonResponse(['error' => 'Erro ao salvar no banco: ' . $errorInfo[2]], 500);
            return;
        }
        
        $id = $db->lastInsertId();
        
        Utils::log('INFO', 'Aviso criado', ['aviso_id' => $id, 'dados' => $data]);
        
        Utils::jsonResponse([
            'success' => true, 
            'message' => 'Aviso criado com sucesso!',
            'id' => $id
        ]);

    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao criar aviso: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro ao salvar aviso: ' . $e->getMessage()], 500);
    }
}

function atualizarAviso($id, $data) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $fields = [];
        $values = [];
        
        if (isset($data['titulo'])) {
            $fields[] = 'titulo = ?';
            $values[] = $data['titulo'];
        }
        
        if (isset($data['conteudo'])) {
            $fields[] = 'conteudo = ?';
            $values[] = $data['conteudo'];
        }
        
        if (isset($data['prioridade'])) {
            $fields[] = 'prioridade = ?';
            $values[] = $data['prioridade'];
        }
        
        if (isset($data['tipo'])) {
            $fields[] = 'tipo = ?';
            $values[] = $data['tipo'];
        }
        
        if (empty($fields)) {
            Utils::jsonResponse(['error' => 'Nenhum campo para atualizar'], 400);
            return;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE avisos SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($values);
        
        Utils::log('INFO', 'Aviso atualizado', ['aviso_id' => $id]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Aviso atualizado com sucesso!'
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao atualizar aviso: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro ao atualizar aviso'], 500);
    }
}

function deletarAviso($id) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM avisos WHERE id = ?");
        $stmt->execute([$id]);
        
        Utils::log('INFO', 'Aviso deletado', ['aviso_id' => $id]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Aviso deletado com sucesso!'
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao deletar aviso: ' . $e->getMessage());
        Utils::jsonResponse(['error' => 'Erro interno do servidor'], 500);
    }
}

function atualizarMensagem($id, $data) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $fields = [];
        $values = [];
        
        if (isset($data['respondida'])) {
            $fields[] = 'respondida = ?';
            $values[] = $data['respondida'] ? 1 : 0;
        }
        
        // Nota: A coluna 'resposta' não existe na tabela mensagens_chat
        // A resposta deve ser enviada como uma nova mensagem do sistema
        
        if (empty($fields)) {
            Utils::jsonResponse(['error' => 'Nenhum campo para atualizar'], 400);
            return;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE mensagens_chat SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $result = $stmt->execute($values);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            Utils::log('ERROR', 'Erro ao executar UPDATE', ['error' => $errorInfo]);
            Utils::jsonResponse(['error' => 'Erro ao atualizar mensagem: ' . $errorInfo[2]], 500);
            return;
        }
        
        Utils::log('INFO', 'Mensagem atualizada', ['mensagem_id' => $id, 'dados' => $data]);
        
        Utils::jsonResponse([
            'success' => true,
            'message' => 'Mensagem atualizada com sucesso!'
        ]);
    } catch (Exception $e) {
        Utils::log('ERROR', 'Erro ao atualizar mensagem: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        Utils::jsonResponse(['error' => 'Erro ao atualizar mensagem: ' . $e->getMessage()], 500);
    }
}
?>