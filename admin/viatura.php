<?php
/**
 * Admin - Gestão de Viatura (Adicionar/Editar)
 * Se id=0 ou não definido: Adicionar nova viatura
 * Se id>0: Editar viatura existente
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

$error = '';
$carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEditing = $carId > 0;
$pageTitle = $isEditing ? 'Editar Viatura' : 'Adicionar Viatura';

// Capturar página de retorno
$returnUrl = isset($_GET['return']) ? $_GET['return'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php');
// Sanitizar URL de retorno para evitar redirecionamentos externos
if (!empty($returnUrl) && strpos($returnUrl, 'http') === 0) {
    $parsedUrl = parse_url($returnUrl);
    $returnUrl = $parsedUrl['path'] ?? 'dashboard.php';
    if (!empty($parsedUrl['query'])) {
        $returnUrl .= '?' . $parsedUrl['query'];
    }
}

// Dados da viatura (vazios para adicionar, preenchidos para editar)
$car = [
    'marca' => '',
    'modelo' => '',
    'versao' => '',
    'preco' => '',
    'ano' => '',
    'quilometros' => '',
    'combustivel' => '',
    'descricao' => '',
    'imagem_path' => '',
    'garantia' => 1,
    'iva_incluido' => 1,
    'matricula' => '',
    // Campos técnicos
    'potencia' => '',
    'cilindrada' => '',
    'transmissao' => '',
    'tracao' => '',
    'portas' => 5,
    'lugares' => 5,
    'cor' => '',
    'cor_interior' => '',
    'consumo_medio' => '',
    'emissoes_co2' => '',
    'segmento' => ''
];

// Se estamos a editar, buscar os dados existentes
if ($isEditing) {
    try {
        $cars = dbQuery("SELECT * FROM cars WHERE id = :id LIMIT 1", [':id' => $carId]);
    } catch (Exception $e) {
        header('Location: dashboard.php?error=' . urlencode('Erro ao buscar viatura.'));
        exit;
    }
    
    if (empty($cars)) {
        header('Location: dashboard.php?error=' . urlencode('Viatura não encontrada.'));
        exit;
    }
    
    $car = $cars[0];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $versao = trim($_POST['versao'] ?? '');
    $preco = (float)($_POST['preco'] ?? '0');
    $ano = (int)($_POST['ano'] ?? 0);
    $quilometros = (int)str_replace([' ', '.'], '', $_POST['quilometros'] ?? '0');
    $combustivel = trim($_POST['combustivel'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $garantia = isset($_POST['garantia']) ? 1 : 0;
    $iva_incluido = isset($_POST['iva_incluido']) ? 1 : 0;
    $matricula = strtoupper(trim($_POST['matricula'] ?? ''));
    
    // Campos técnicos
    $potencia = !empty($_POST['potencia']) ? (int)$_POST['potencia'] : null;
    $cilindrada = !empty($_POST['cilindrada']) ? (int)$_POST['cilindrada'] : null;
    $transmissao = trim($_POST['transmissao'] ?? '');
    $tracao = trim($_POST['tracao'] ?? '');
    $portas = !empty($_POST['portas']) ? (int)$_POST['portas'] : null;
    $lugares = !empty($_POST['lugares']) ? (int)$_POST['lugares'] : null;
    $cor = trim($_POST['cor'] ?? '');
    $cor_interior = trim($_POST['cor_interior'] ?? '');
    $consumo_medio = !empty($_POST['consumo_medio']) ? (float)$_POST['consumo_medio'] : null;
    $emissoes_co2 = !empty($_POST['emissoes_co2']) ? (int)$_POST['emissoes_co2'] : null;
    $segmento = trim($_POST['segmento'] ?? '');
    
    if (empty($marca) || empty($modelo) || $preco <= 0 || $ano <= 0) {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        $imagemPath = $isEditing ? $car['imagem_path'] : '';
        $imagensExtra = [];
        
        // ==========================================
        // MODO EDIÇÃO: Processar imagens removidas e nova principal
        // ==========================================
        if ($isEditing) {
            // Processar imagens removidas
            if (!empty($_POST['removed_images'])) {
                $removedImages = explode(',', $_POST['removed_images']);
                foreach ($removedImages as $imgPath) {
                    $imgPath = trim($imgPath);
                    if (!empty($imgPath)) {
                        $filePath = __DIR__ . '/../uploads/' . $imgPath;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        dbExecute("DELETE FROM car_images WHERE car_id = :car_id AND imagem_path = :imagem_path", 
                            [':car_id' => $carId, ':imagem_path' => $imgPath]);
                        if ($imgPath === $car['imagem_path']) {
                            $imagemPath = '';
                        }
                    }
                }
            }
            
            // Processar alteração da imagem principal
            if (!empty($_POST['nova_imagem_principal'])) {
                $imagemPath = trim($_POST['nova_imagem_principal']);
            }
        }
        
        // ==========================================
        // Processar novas imagens (comum para adicionar e editar)
        // ==========================================
        $fileInputName = $isEditing ? 'novas_imagens' : 'imagens';
        if (isset($_FILES[$fileInputName]) && !empty($_FILES[$fileInputName]['name'][0])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            $totalFiles = count($_FILES[$fileInputName]['name']);
            $principalIndex = isset($_POST['imagem_principal_index']) ? (int)$_POST['imagem_principal_index'] : 0;
            
            if ($principalIndex >= $totalFiles) {
                $principalIndex = 0;
            }
            
            for ($i = 0; $i < $totalFiles; $i++) {
                if ($_FILES[$fileInputName]['error'][$i] === UPLOAD_ERR_OK) {
                    $fileType = mime_content_type($_FILES[$fileInputName]['tmp_name'][$i]);
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        $error = 'Tipo de imagem inválido. Use JPEG, PNG ou WebP.';
                        break;
                    }
                    
                    if ($_FILES[$fileInputName]['size'][$i] > $maxSize) {
                        $error = 'Uma das imagens tem mais de 5MB.';
                        break;
                    }
                    
                    $ext = pathinfo($_FILES[$fileInputName]['name'][$i], PATHINFO_EXTENSION);
                    $novoNome = uniqid('car_') . '.' . strtolower($ext);
                    $uploadPath = __DIR__ . '/../uploads/' . $novoNome;
                    
                    if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'][$i], $uploadPath)) {
                        // Se não tem imagem principal ou é a primeira e estamos a adicionar
                        if (!$isEditing && $i === $principalIndex) {
                            $imagemPath = $novoNome;
                        } elseif ($isEditing && empty($imagemPath) && $i === 0) {
                            $imagemPath = $novoNome;
                        }
                        $imagensExtra[] = $novoNome;
                    }
                }
            }
        }
        
        // ==========================================
        // Guardar na base de dados
        // ==========================================
        if (empty($error)) {
            try {
                if ($isEditing) {
                    // UPDATE
                    dbExecute(
                        "UPDATE cars SET marca = :marca, modelo = :modelo, versao = :versao, preco = :preco, 
                         ano = :ano, quilometros = :quilometros, combustivel = :combustivel, 
                         descricao = :descricao, imagem_path = :imagem_path, garantia = :garantia, 
                         iva_incluido = :iva_incluido, matricula = :matricula,
                         potencia = :potencia, cilindrada = :cilindrada, transmissao = :transmissao,
                         tracao = :tracao, portas = :portas, lugares = :lugares, cor = :cor,
                         cor_interior = :cor_interior, consumo_medio = :consumo_medio,
                         emissoes_co2 = :emissoes_co2, segmento = :segmento
                         WHERE id = :id",
                        [
                            ':marca' => $marca,
                            ':modelo' => $modelo,
                            ':versao' => $versao,
                            ':preco' => $preco,
                            ':ano' => $ano,
                            ':quilometros' => $quilometros,
                            ':combustivel' => $combustivel,
                            ':descricao' => $descricao,
                            ':imagem_path' => $imagemPath,
                            ':garantia' => $garantia,
                            ':iva_incluido' => $iva_incluido,
                            ':matricula' => $matricula,
                            ':potencia' => $potencia,
                            ':cilindrada' => $cilindrada,
                            ':transmissao' => $transmissao,
                            ':tracao' => $tracao,
                            ':portas' => $portas,
                            ':lugares' => $lugares,
                            ':cor' => $cor,
                            ':cor_interior' => $cor_interior,
                            ':consumo_medio' => $consumo_medio,
                            ':emissoes_co2' => $emissoes_co2,
                            ':segmento' => $segmento,
                            ':id' => $carId
                        ]
                    );
                    
                    // Inserir novas imagens se houver
                    if (!empty($imagensExtra)) {
                        $maxOrdem = dbQuery("SELECT MAX(ordem) as max_ordem FROM car_images WHERE car_id = :car_id", 
                            [':car_id' => $carId]);
                        $ordem = ($maxOrdem[0]['max_ordem'] ?? 0) + 1;
                        
                        foreach ($imagensExtra as $imgPath) {
                            dbExecute(
                                "INSERT INTO car_images (car_id, imagem_path, ordem) VALUES (:car_id, :imagem_path, :ordem)",
                                [':car_id' => $carId, ':imagem_path' => $imgPath, ':ordem' => $ordem]
                            );
                            $ordem++;
                        }
                    }
                    
                    // Redirecionar para a página anterior
                    $redirectUrl = basename($returnUrl) === basename($_SERVER['PHP_SELF']) ? 'dashboard.php' : $returnUrl;
                    header('Location: ' . $redirectUrl . (strpos($redirectUrl, '?') !== false ? '&' : '?') . 'success=updated');
                    exit;
                } else {
                    // INSERT
                    dbExecute(
                        "INSERT INTO cars (marca, modelo, versao, preco, ano, quilometros, combustivel, descricao, imagem_path, garantia, iva_incluido, matricula,
                         potencia, cilindrada, transmissao, tracao, portas, lugares, cor, cor_interior, consumo_medio, emissoes_co2, segmento) 
                         VALUES (:marca, :modelo, :versao, :preco, :ano, :quilometros, :combustivel, :descricao, :imagem_path, :garantia, :iva_incluido, :matricula,
                         :potencia, :cilindrada, :transmissao, :tracao, :portas, :lugares, :cor, :cor_interior, :consumo_medio, :emissoes_co2, :segmento)",
                        [
                            ':marca' => $marca,
                            ':modelo' => $modelo,
                            ':versao' => $versao,
                            ':preco' => $preco,
                            ':ano' => $ano,
                            ':quilometros' => $quilometros,
                            ':combustivel' => $combustivel,
                            ':descricao' => $descricao,
                            ':imagem_path' => $imagemPath,
                            ':garantia' => $garantia,
                            ':iva_incluido' => $iva_incluido,
                            ':matricula' => $matricula,
                            ':potencia' => $potencia,
                            ':cilindrada' => $cilindrada,
                            ':transmissao' => $transmissao,
                            ':tracao' => $tracao,
                            ':portas' => $portas,
                            ':lugares' => $lugares,
                            ':cor' => $cor,
                            ':cor_interior' => $cor_interior,
                            ':consumo_medio' => $consumo_medio,
                            ':emissoes_co2' => $emissoes_co2,
                            ':segmento' => $segmento
                        ]
                    );
                    
                    $pdo = getDbConnection();
                    $newCarId = $pdo->lastInsertId();
                    
                    // Inserir imagens
                    foreach ($imagensExtra as $ordem => $imgPath) {
                        dbExecute(
                            "INSERT INTO car_images (car_id, imagem_path, ordem) VALUES (:car_id, :imagem_path, :ordem)",
                            [':car_id' => $newCarId, ':imagem_path' => $imgPath, ':ordem' => $ordem]
                        );
                    }
                    
                    // Redirecionar para a página anterior ou dashboard
                    $redirectUrl = basename($returnUrl) === basename($_SERVER['PHP_SELF']) ? 'dashboard.php' : $returnUrl;
                    header('Location: ' . $redirectUrl . (strpos($redirectUrl, '?') !== false ? '&' : '?') . 'success=added');
                    exit;
                }
            } catch (Exception $e) {
                $error = 'Erro ao guardar: ' . $e->getMessage();
            }
        }
    }
}

$anoAtual = (int)date('Y');
require_once __DIR__ . '/includes/header.php';
?>

<style>
    /* Forçar largura consistente apenas nesta página */
    .admin-content {
        max-width: 1000px !important;
        width: 100% !important;
        margin: 0 auto !important;
    }
    .page-header {
        text-align: center !important;
    }
    .admin-card {
        width: 100% !important;
        max-width: 1000px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title"><?php echo $isEditing ? 'Editar Viatura' : 'Adicionar Viatura'; ?></h1>
    <p class="page-subtitle">
        <?php if ($isEditing): ?>
            <?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?>
        <?php else: ?>
            Preencha os dados da nova viatura
        <?php endif; ?>
    </p>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Marca *</label>
                <input type="text" name="marca" class="form-control" required
                       value="<?php echo htmlspecialchars($car['marca']); ?>"
                       placeholder="Ex: BMW">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Modelo *</label>
                <input type="text" name="modelo" class="form-control" required
                       value="<?php echo htmlspecialchars($car['modelo']); ?>"
                       placeholder="Ex: Série 3">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Versão</label>
                <input type="text" name="versao" class="form-control"
                       value="<?php echo htmlspecialchars($car['versao']); ?>"
                       placeholder="Ex: 320d M Sport">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Preço (€) *</label>
                <input type="number" name="preco" class="form-control" required
                       value="<?php echo $car['preco'] ? (int)$car['preco'] : ''; ?>" 
                       placeholder="Ex: 25000" min="0" step="1">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Matrícula (Interno)</label>
                <input type="text" name="matricula" class="form-control"
                       value="<?php echo htmlspecialchars($car['matricula'] ?? ''); ?>"
                       placeholder="Ex: 00-AA-00">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Ano *</label>
                <select name="ano" class="form-select" required>
                    <option value="">Selecionar...</option>
                    <?php for ($a = $anoAtual + 1; $a >= $anoAtual - 30; $a--): ?>
                        <option value="<?php echo $a; ?>" <?php echo $car['ano'] == $a ? 'selected' : ''; ?>>
                            <?php echo $a; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Quilómetros *</label>
                <input type="number" name="quilometros" class="form-control" required
                       value="<?php echo $car['quilometros']; ?>"
                       placeholder="Ex: 50000" min="0">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Combustível *</label>
                <select name="combustivel" class="form-select" required>
                    <option value="">Selecionar...</option>
                    <?php 
                    $combustiveis = ['Gasolina', 'Diesel', 'Híbrido', 'Elétrico', 'GPL'];
                    foreach ($combustiveis as $c): 
                    ?>
                        <option value="<?php echo $c; ?>" <?php echo $car['combustivel'] === $c ? 'selected' : ''; ?>>
                            <?php echo $c; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <!-- Switches Garantia e IVA -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <div style="padding: 1rem 1.25rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-weight: 500; margin-bottom: 0.25rem;">
                            <i class="bi bi-shield-check me-1 text-success"></i>
                            Garantia Incluída
                        </div>
                        <small class="text-muted">A viatura inclui garantia de stand</small>
                    </div>
                    <div class="form-check form-switch mb-0" style="padding-left: 0;">
                        <input class="form-check-input" type="checkbox" role="switch" name="garantia" id="garantia" 
                               <?php echo $car['garantia'] ? 'checked' : ''; ?> style="width: 3rem; height: 1.5rem; margin-left: 0; cursor: pointer;">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div style="padding: 1rem 1.25rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-weight: 500; margin-bottom: 0.25rem;">
                            <i class="bi bi-receipt me-1 text-primary"></i>
                            IVA Incluído
                        </div>
                        <small class="text-muted">O preço já inclui IVA</small>
                    </div>
                    <div class="form-check form-switch mb-0" style="padding-left: 0;">
                        <input class="form-check-input" type="checkbox" role="switch" name="iva_incluido" id="iva_incluido" 
                               <?php echo $car['iva_incluido'] ? 'checked' : ''; ?> style="width: 3rem; height: 1.5rem; margin-left: 0; cursor: pointer;">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" rows="4"
                      placeholder="Detalhes adicionais sobre a viatura..."><?php echo htmlspecialchars($car['descricao']); ?></textarea>
        </div>
        
        <!-- Características Técnicas -->
        <div class="mb-4">
            <h5 class="mb-3" style="color: var(--text-dark); font-weight: 600; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">
                <i class="bi bi-gear-fill me-2"></i>Características Técnicas
            </h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Potência (cv)</label>
                    <input type="number" name="potencia" class="form-control"
                           value="<?php echo htmlspecialchars($car['potencia'] ?? ''); ?>"
                           placeholder="Ex: 150" min="0">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Cilindrada (cm³)</label>
                    <input type="number" name="cilindrada" class="form-control"
                           value="<?php echo htmlspecialchars($car['cilindrada'] ?? ''); ?>"
                           placeholder="Ex: 1998" min="0">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Transmissão</label>
                    <select name="transmissao" class="form-select">
                        <option value="">Selecionar...</option>
                        <?php 
                        $transmissoes = ['Manual', 'Automática', 'Semi-automática'];
                        foreach ($transmissoes as $t): 
                        ?>
                            <option value="<?php echo $t; ?>" <?php echo ($car['transmissao'] ?? '') === $t ? 'selected' : ''; ?>>
                                <?php echo $t; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tração</label>
                    <select name="tracao" class="form-select">
                        <option value="">Selecionar...</option>
                        <?php 
                        $tracoes = ['Frente', 'Trás', '4x4'];
                        foreach ($tracoes as $tr): 
                        ?>
                            <option value="<?php echo $tr; ?>" <?php echo ($car['tracao'] ?? '') === $tr ? 'selected' : ''; ?>>
                                <?php echo $tr; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Configuração -->
        <div class="mb-4">
            <h5 class="mb-3" style="color: var(--text-dark); font-weight: 600; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">
                <i class="bi bi-sliders me-2"></i>Configuração
            </h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Portas</label>
                    <select name="portas" class="form-select">
                        <option value="">Selecionar...</option>
                        <?php 
                        for ($p = 2; $p <= 5; $p++): 
                        ?>
                            <option value="<?php echo $p; ?>" <?php echo ($car['portas'] ?? '') == $p ? 'selected' : ''; ?>>
                                <?php echo $p; ?> portas
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Lugares</label>
                    <select name="lugares" class="form-select">
                        <option value="">Selecionar...</option>
                        <?php 
                        $lugaresOpcoes = [2, 4, 5, 7, 9];
                        foreach ($lugaresOpcoes as $l): 
                        ?>
                            <option value="<?php echo $l; ?>" <?php echo ($car['lugares'] ?? '') == $l ? 'selected' : ''; ?>>
                                <?php echo $l; ?> lugares
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Segmento</label>
                    <select name="segmento" class="form-select">
                        <option value="">Selecionar...</option>
                        <?php 
                        $segmentos = ['Citadino', 'Berlina', 'SUV', 'Monovolume', 'Desportivo', 'Comercial', 'Carrinha'];
                        foreach ($segmentos as $s): 
                        ?>
                            <option value="<?php echo $s; ?>" <?php echo ($car['segmento'] ?? '') === $s ? 'selected' : ''; ?>>
                                <?php echo $s; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Estética -->
        <div class="mb-4">
            <h5 class="mb-3" style="color: var(--text-dark); font-weight: 600; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">
                <i class="bi bi-palette-fill me-2"></i>Estética
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cor Exterior</label>
                    <input type="text" name="cor" class="form-control"
                           value="<?php echo htmlspecialchars($car['cor'] ?? ''); ?>"
                           placeholder="Ex: Preto Metalizado">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cor Interior</label>
                    <input type="text" name="cor_interior" class="form-control"
                           value="<?php echo htmlspecialchars($car['cor_interior'] ?? ''); ?>"
                           placeholder="Ex: Preto">
                </div>
            </div>
        </div>
        
        <!-- Eficiência -->
        <div class="mb-4">
            <h5 class="mb-3" style="color: var(--text-dark); font-weight: 600; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem;">
                <i class="bi bi-speedometer me-2"></i>Eficiência
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Consumo Médio (L/100km)</label>
                    <input type="number" name="consumo_medio" class="form-control" step="0.1"
                           value="<?php echo htmlspecialchars($car['consumo_medio'] ?? ''); ?>"
                           placeholder="Ex: 5.5" min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Emissões CO2 (g/km)</label>
                    <input type="number" name="emissoes_co2" class="form-control"
                           value="<?php echo htmlspecialchars($car['emissoes_co2'] ?? ''); ?>"
                           placeholder="Ex: 120" min="0">
                </div>
            </div>
        </div>
        
        <div class="mb-4">
            <label class="form-label">
                <i class="bi bi-images me-1"></i>
                Imagens da Viatura
                <span id="imageCount" class="badge bg-secondary ms-2">0 imagens</span>
            </label>
            
            <?php if ($isEditing): ?>
                <?php 
                // Buscar imagens existentes
                $existingImages = dbQuery("SELECT * FROM car_images WHERE car_id = :car_id ORDER BY ordem", [':car_id' => $carId]);
                ?>
                
                <!-- Imagens existentes -->
                <?php if (!empty($existingImages) || !empty($car['imagem_path'])): ?>
                <div class="mb-3">
                    <small class="text-muted d-block mb-2">
                        <i class="bi bi-check-circle text-success me-1"></i>
                        Imagens guardadas (clique na ⭐ para definir como principal):
                    </small>
                    <div id="existingImages" class="image-preview-grid">
                        <?php 
                        $allImages = [];
                        if (!empty($car['imagem_path'])) {
                            $allImages[] = ['imagem_path' => $car['imagem_path'], 'id' => 0, 'is_main' => true];
                        }
                        foreach ($existingImages as $img) {
                            if ($img['imagem_path'] !== $car['imagem_path']) {
                                $allImages[] = $img;
                            }
                        }
                        foreach ($allImages as $index => $img): 
                            $imgPath = '../uploads/' . htmlspecialchars($img['imagem_path']);
                            $isMain = isset($img['is_main']) && $img['is_main'];
                        ?>
                        <div class="preview-item existing-image <?php echo $isMain ? 'is-principal' : ''; ?>" data-path="<?php echo htmlspecialchars($img['imagem_path']); ?>">
                            <img src="<?php echo $imgPath; ?>" alt="Imagem existente">
                            <?php if ($isMain): ?>
                                <span class="badge-principal"><i class="bi bi-star-fill me-1"></i>Principal</span>
                            <?php endif; ?>
                            <span class="badge-order">#<?php echo $index + 1; ?></span>
                            <button type="button" class="btn-set-principal" onclick="setExistingAsPrincipal(this)" title="Definir como principal">
                                <i class="bi bi-star<?php echo $isMain ? '-fill' : ''; ?>"></i>
                            </button>
                            <button type="button" class="btn-remove" onclick="removeExistingImage(this)" title="Remover">
                                <i class="bi bi-x"></i>
                            </button>
                            <button type="button" class="btn-view" onclick="openFullscreen('<?php echo $imgPath; ?>')" title="Ver em ecrã inteiro">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Input oculto para rastrear imagens removidas -->
                <input type="hidden" name="removed_images" id="removedImages" value="">
                <!-- Input para nova imagem principal -->
                <input type="hidden" name="nova_imagem_principal" id="novaImagemPrincipal" value="">
            <?php endif; ?>
            
            <!-- Drag & Drop Zone -->
            <div id="dropZone" class="drop-zone">
                <div class="drop-zone-content">
                    <i class="bi bi-cloud-arrow-up" style="font-size: 3rem; color: var(--primary-color);"></i>
                    <p class="mb-2 mt-3"><strong><?php echo $isEditing ? 'Arraste novas imagens para aqui' : 'Arraste as imagens para aqui'; ?></strong></p>
                    <p class="text-muted mb-3">ou</p>
                    <label for="<?php echo $isEditing ? 'novas_imagens' : 'imagens'; ?>" class="btn btn-primary">
                        <i class="bi bi-folder2-open me-1"></i>
                        <?php echo $isEditing ? 'Adicionar Imagens' : 'Selecionar Imagens'; ?>
                    </label>
                    <?php if ($isEditing): ?>
                        <input type="file" name="novas_imagens[]" id="novas_imagens" class="d-none" 
                               accept="image/jpeg,image/png,image/webp" multiple>
                    <?php else: ?>
                        <input type="file" name="imagens[]" id="imagens" class="d-none" 
                               accept="image/jpeg,image/png,image/webp" multiple>
                    <?php endif; ?>
                </div>
            </div>
            
            <small class="text-muted d-block mt-2">
                <i class="bi bi-info-circle me-1"></i>
                <?php if ($isEditing): ?>
                    Adicione quantas imagens quiser. Formatos: JPEG, PNG, WebP. Máximo: 5MB cada.
                <?php else: ?>
                    Selecione quantas imagens quiser. A primeira será a imagem principal.
                    Arraste para reordenar. Formatos: JPEG, PNG, WebP. Máximo: 5MB cada.
                <?php endif; ?>
            </small>
            
            <!-- Preview Grid -->
            <div id="imagePreview" class="image-preview-grid mt-3"></div>
            
            <?php if (!$isEditing): ?>
                <!-- Input para indicar qual é a imagem principal -->
                <input type="hidden" name="imagem_principal_index" id="imagemPrincipalIndex" value="0">
            <?php endif; ?>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i>
                <?php echo $isEditing ? 'Guardar Alterações' : 'Guardar Viatura'; ?>
            </button>
            <a href="dashboard.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>
                Cancelar
            </a>
        </div>
    </form>
</div>

<style>
.drop-zone {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(248,250,252,0.9));
    transition: all 0.3s ease;
    cursor: pointer;
}

.drop-zone:hover,
.drop-zone.drag-over {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, rgba(var(--primary-rgb, 220,38,38), 0.05), rgba(255,255,255,0.95));
    transform: scale(1.01);
}

.drop-zone.drag-over {
    box-shadow: 0 0 20px rgba(var(--primary-rgb, 220,38,38), 0.2);
}

.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 15px;
}

.preview-item {
    position: relative;
    aspect-ratio: 4/3;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: grab;
    transition: all 0.2s ease;
    background: #f8fafc;
}

.preview-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.preview-item.dragging {
    opacity: 0.5;
    cursor: grabbing;
}

.preview-item.drag-over-item {
    border: 2px solid var(--primary-color);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-item .badge-principal {
    position: absolute;
    top: 8px;
    left: 8px;
    background: linear-gradient(135deg, var(--primary-color), #f87171);
    color: white;
    font-size: 10px;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.preview-item .badge-order {
    position: absolute;
    bottom: 8px;
    left: 8px;
    background: rgba(0,0,0,0.6);
    color: white;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 4px;
}

.preview-item .btn-remove {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.preview-item:hover .btn-remove {
    opacity: 1;
}

.preview-item .btn-remove:hover {
    background: #dc2626;
    transform: scale(1.1);
}

.preview-item .btn-set-principal {
    position: absolute;
    top: 8px;
    right: 42px;
    background: rgba(100, 100, 100, 0.8);
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    z-index: 10;
}

.preview-item:hover .btn-set-principal {
    opacity: 1;
}

.preview-item .btn-set-principal:hover {
    background: rgba(251, 191, 36, 1);
    transform: scale(1.15);
    color: #1a1a2e;
}

.preview-item.is-principal {
    border: 3px solid var(--primary-color);
    box-shadow: 0 0 15px rgba(var(--primary-rgb, 220,38,38), 0.3);
}

.preview-item.is-principal .btn-set-principal {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: #1a1a2e;
}

.preview-item .file-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: white;
    font-size: 10px;
    padding: 20px 8px 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.preview-item.removing {
    animation: fadeOut 0.3s ease forwards;
}



/* Modal de Ecrã Inteiro */
.image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 50px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.9);
    align-items: center;
    justify-content: center;
}

.modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90vh;
    object-fit: contain;
    animation: zoomIn 0.3s;
}

@keyframes zoomIn {
    from {transform:scale(0)}
    to {transform:scale(1)}
}

.close-modal {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
    cursor: pointer;
    z-index: 10000;
}

.close-modal:hover,
.close-modal:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* Botão de Ver */
.preview-item .btn-view {
    position: absolute;
    top: 8px;
    right: 76px; /* Ajustado para ficar ao lado do botão remover/estrela */
    background: rgba(59, 130, 246, 0.9);
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    z-index: 10;
}

.preview-item:hover .btn-view {
    opacity: 1;
}

.preview-item .btn-view:hover {
    background: #2563eb;
    transform: scale(1.1);
}
</style>

<!-- Modal Structure -->
<div id="imageModal" class="image-modal" onclick="closeFullscreen()">
    <span class="close-modal">&times;</span>
    <img class="modal-content" id="fullImage">
</div>

<script>
function openFullscreen(src) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("fullImage");
    modal.style.display = "flex";
    modalImg.src = src;
    document.body.style.overflow = "hidden"; // Previne scroll no body
}

function closeFullscreen() {
    const modal = document.getElementById("imageModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
}

// Fechar com ESC
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeFullscreen();
    }
});
</script>

<script>
const isEditing = <?php echo $isEditing ? 'true' : 'false'; ?>;

<?php if ($isEditing): ?>
// ==========================================
// Funções para modo EDIÇÃO
// ==========================================
let removedImages = [];

function removeExistingImage(btn) {
    const item = btn.closest('.preview-item');
    const path = item.dataset.path;
    
    removedImages.push(path);
    document.getElementById('removedImages').value = removedImages.join(',');
    
    item.classList.add('removing');
    setTimeout(() => item.remove(), 300);
    
    updateImageCount();
}

function setExistingAsPrincipal(btn) {
    const item = btn.closest('.preview-item');
    const path = item.dataset.path;
    
    document.querySelectorAll('#existingImages .preview-item').forEach(el => {
        el.classList.remove('is-principal');
        const badge = el.querySelector('.badge-principal');
        if (badge) badge.remove();
        const starBtn = el.querySelector('.btn-set-principal i');
        if (starBtn) starBtn.className = 'bi bi-star';
    });
    
    item.classList.add('is-principal');
    
    if (!item.querySelector('.badge-principal')) {
        const badge = document.createElement('span');
        badge.className = 'badge-principal';
        badge.innerHTML = '<i class="bi bi-star-fill me-1"></i>Principal';
        item.appendChild(badge);
    }
    
    const starBtn = item.querySelector('.btn-set-principal i');
    if (starBtn) starBtn.className = 'bi bi-star-fill';
    
    document.getElementById('novaImagemPrincipal').value = path;
}
<?php endif; ?>

function updateImageCount() {
    const existingEl = document.getElementById('existingImages');
    const existing = existingEl ? existingEl.querySelectorAll('.preview-item:not(.removing)').length : 0;
    const newImages = ImageUploader.files.length;
    const total = existing + newImages;
    
    const badge = document.getElementById('imageCount');
    badge.textContent = `${total} imagen${total !== 1 ? 's' : ''}`;
    badge.className = `badge ${total > 0 ? 'bg-success' : 'bg-secondary'} ms-2`;
}

// ==========================================
// Gerenciador de imagens (comum)
// ==========================================
const ImageUploader = {
    files: [],
    principalIndex: 0,
    preview: document.getElementById('imagePreview'),
    dropZone: document.getElementById('dropZone'),
    input: document.getElementById(isEditing ? 'novas_imagens' : 'imagens'),
    principalInput: document.getElementById('imagemPrincipalIndex'),
    
    init() {
        this.bindEvents();
        updateImageCount();
    },
    
    bindEvents() {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
            this.dropZone.addEventListener(event, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        
        ['dragenter', 'dragover'].forEach(event => {
            this.dropZone.addEventListener(event, () => {
                this.dropZone.classList.add('drag-over');
            });
        });
        
        ['dragleave', 'drop'].forEach(event => {
            this.dropZone.addEventListener(event, () => {
                this.dropZone.classList.remove('drag-over');
            });
        });
        
        this.dropZone.addEventListener('drop', (e) => {
            const droppedFiles = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
            this.addFiles(droppedFiles);
        });
        
        this.dropZone.addEventListener('click', (e) => {
            if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'LABEL') {
                this.input.click();
            }
        });
        
        this.input.addEventListener('change', (e) => {
            this.addFiles(Array.from(e.target.files));
        });
    },
    
    addFiles(newFiles) {
        const maxSize = 5 * 1024 * 1024;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        
        newFiles.forEach(file => {
            if (!allowedTypes.includes(file.type)) {
                alert(`Ficheiro "${file.name}" ignorado: tipo inválido. Use JPEG, PNG ou WebP.`);
                return;
            }
            if (file.size > maxSize) {
                alert(`Ficheiro "${file.name}" ignorado: excede 5MB.`);
                return;
            }
            this.files.push(file);
        });
        
        this.renderPreviews();
        this.updateFileInput();
        updateImageCount();
    },
    
    removeFile(index) {
        this.files.splice(index, 1);
        if (!isEditing) {
            if (index < this.principalIndex) {
                this.principalIndex--;
            } else if (index === this.principalIndex) {
                this.principalIndex = 0;
            }
            if (this.principalIndex >= this.files.length) {
                this.principalIndex = Math.max(0, this.files.length - 1);
            }
        }
        this.renderPreviews();
        this.updateFileInput();
        updateImageCount();
    },
    
    setPrincipal(index) {
        this.principalIndex = index;
        if (this.principalInput) {
            this.principalInput.value = index;
        }
        this.renderPreviews();
    },
    
    renderPreviews() {
        this.preview.innerHTML = '';
        
        this.files.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.draggable = true;
            div.dataset.index = index;
            
            const isPrincipal = !isEditing && index === this.principalIndex;
            if (isPrincipal) {
                div.classList.add('is-principal');
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                const sizeMB = (file.size / 1024 / 1024).toFixed(1);
                
                if (isEditing) {
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <span class="badge-order" style="background: rgba(34, 197, 94, 0.8);">Novo #${index + 1}</span>
                        <button type="button" class="btn-remove" onclick="ImageUploader.removeFile(${index})" title="Remover">
                            <i class="bi bi-x"></i>
                        </button>
                        <button type="button" class="btn-view" onclick="openFullscreen('${e.target.result}')" title="Ver em ecrã inteiro" style="right: 42px;">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <div class="file-info">${file.name} (${sizeMB}MB)</div>
                    `;
                } else {
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        ${isPrincipal ? '<span class="badge-principal"><i class="bi bi-star-fill me-1"></i>Principal</span>' : ''}
                        <span class="badge-order">#${index + 1}</span>
                        <button type="button" class="btn-set-principal" onclick="ImageUploader.setPrincipal(${index})" title="Definir como principal">
                            <i class="bi bi-star${isPrincipal ? '-fill' : ''}"></i>
                        </button>
                        <button type="button" class="btn-remove" onclick="ImageUploader.removeFile(${index})" title="Remover">
                            <i class="bi bi-x"></i>
                        </button>
                        <button type="button" class="btn-view" onclick="openFullscreen('${e.target.result}')" title="Ver em ecrã inteiro">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <div class="file-info">${file.name} (${sizeMB}MB)</div>
                    `;
                }
            };
            reader.readAsDataURL(file);
            
            // Drag events for reordering (only in add mode)
            if (!isEditing) {
                div.addEventListener('dragstart', (e) => {
                    div.classList.add('dragging');
                    e.dataTransfer.setData('text/plain', index);
                });
                
                div.addEventListener('dragend', () => {
                    div.classList.remove('dragging');
                    document.querySelectorAll('.preview-item').forEach(item => {
                        item.classList.remove('drag-over-item');
                    });
                });
                
                div.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    const dragging = document.querySelector('.dragging');
                    if (dragging !== div) {
                        div.classList.add('drag-over-item');
                    }
                });
                
                div.addEventListener('dragleave', () => {
                    div.classList.remove('drag-over-item');
                });
                
                div.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const fromIndex = parseInt(e.dataTransfer.getData('text/plain'));
                    const toIndex = parseInt(div.dataset.index);
                    
                    if (fromIndex !== toIndex) {
                        const [movedFile] = this.files.splice(fromIndex, 1);
                        this.files.splice(toIndex, 0, movedFile);
                        this.renderPreviews();
                        this.updateFileInput();
                    }
                });
            }
            
            this.preview.appendChild(div);
        });
    },
    
    updateFileInput() {
        const dt = new DataTransfer();
        this.files.forEach(file => dt.items.add(file));
        this.input.files = dt.files;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    ImageUploader.init();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
