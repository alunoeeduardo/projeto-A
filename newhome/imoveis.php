<?php
// imoveis.php
session_start();
require_once 'config/funcoes.php';

$funcoes = new Funcoes();

// Obter filtros
$filtros = [];
if(isset($_GET['tipo'])) $filtros['tipo'] = $_GET['tipo'];
if(isset($_GET['cidade'])) $filtros['cidade'] = $_GET['cidade'];
if(isset($_GET['valor_max'])) $filtros['valor_max'] = $_GET['valor_max'];
if(isset($_GET['quartos'])) $filtros['quartos'] = $_GET['quartos'];

// Listar imóveis com filtros
$imoveis = $funcoes->listarImoveis($filtros);

// Tipos de imóveis para filtro
$tipos_imoveis = [
    'casa' => 'Casa',
    'apartamento' => 'Apartamento',
    'sobrado' => 'Sobrado',
    'kitnet' => 'Kitnet',
    'terreno' => 'Terreno'
];
?>

<?php include 'includes/header.php'; ?>

<main class="imoveis-section">
    <div class="container">
        <h1 class="section-title">Imóveis Disponíveis</h1>
        <p class="section-subtitle">Encontre o imóvel perfeito para você</p>
        
        <!-- Filtros -->
        <div class="filtros">
            <form id="filterForm" method="GET">
                <div class="filtros-grid">
                    <div class="filtro-group">
                        <label for="tipo">Tipo</label>
                        <select id="tipo" name="tipo">
                            <option value="">Todos os tipos</option>
                            <?php foreach($tipos_imoveis as $valor => $nome): ?>
                            <option value="<?php echo $valor; ?>" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == $valor) ? 'selected' : ''; ?>>
                                <?php echo $nome; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filtro-group">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" value="<?php echo $_GET['cidade'] ?? ''; ?>" placeholder="Qual cidade?">
                    </div>
                    
                    <div class="filtro-group">
                        <label for="valor_max">Valor Máximo (R$)</label>
                        <input type="number" id="valor_max" name="valor_max" value="<?php echo $_GET['valor_max'] ?? ''; ?>" placeholder="Ex: 500000">
                    </div>
                    
                    <div class="filtro-group">
                        <label for="quartos">Mín. Quartos</label>
                        <select id="quartos" name="quartos">
                            <option value="">Qualquer</option>
                            <option value="1" <?php echo (isset($_GET['quartos']) && $_GET['quartos'] == '1') ? 'selected' : ''; ?>>1+</option>
                            <option value="2" <?php echo (isset($_GET['quartos']) && $_GET['quartos'] == '2') ? 'selected' : ''; ?>>2+</option>
                            <option value="3" <?php echo (isset($_GET['quartos']) && $_GET['quartos'] == '3') ? 'selected' : ''; ?>>3+</option>
                            <option value="4" <?php echo (isset($_GET['quartos']) && $_GET['quartos'] == '4') ? 'selected' : ''; ?>>4+</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="imoveis.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Contador de resultados -->
        <div style="margin-bottom: 20px; color: var(--cor-texto-secundario);">
            <i class="fas fa-home"></i> 
            <?php echo count($imoveis); ?> imóveis encontrados
        </div>
        
        <!-- Grid de Imóveis -->
        <?php if(count($imoveis) > 0): ?>
        <div class="imoveis-grid">
            <?php foreach($imoveis as $imovel): ?>
            <div class="imovel-card animate-on-scroll">
                <div class="imovel-image">
                    <div class="imovel-tag"><?php echo ucfirst($imovel['tipo']); ?></div>
                    <button class="favorite-btn" onclick="toggleFavorite(<?php echo $imovel['id']; ?>)" 
                            data-property-id="<?php echo $imovel['id']; ?>"
                            style="position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.9); border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer;">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                <div class="imovel-content">
                    <div class="imovel-price">
                        R$ <?php echo number_format($imovel['valor'], 2, ',', '.'); ?>
                        <?php if($imovel['status'] == 'alugado'): ?>
                            <span style="font-size: 14px; color: var(--cor-erro);">(Alugado)</span>
                        <?php elseif($imovel['status'] == 'vendido'): ?>
                            <span style="font-size: 14px; color: var(--cor-erro);">(Vendido)</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="imovel-title"><?php echo htmlspecialchars($imovel['titulo']); ?></h3>
                    <div class="imovel-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($imovel['cidade']); ?> - <?php echo htmlspecialchars($imovel['estado']); ?>
                    </div>
                    
                    <div class="imovel-features">
                        <div class="imovel-feature">
                            <i class="fas fa-bed"></i>
                            <span><?php echo $imovel['quartos']; ?> Quartos</span>
                        </div>
                        <div class="imovel-feature">
                            <i class="fas fa-bath"></i>
                            <span><?php echo $imovel['banheiros']; ?> Banheiros</span>
                        </div>
                        <div class="imovel-feature">
                            <i class="fas fa-car"></i>
                            <span><?php echo $imovel['vagas']; ?> Vagas</span>
                        </div>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <a href="#" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                        
                        <?php if(isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] == 'cliente'): ?>
                        <button onclick="agendarVisita(<?php echo $imovel['id']; ?>)" 
                                class="btn btn-outline" 
                                style="width: 100%; margin-top: 10px;">
                            <i class="fas fa-calendar"></i> Agendar Visita
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info" style="text-align: center; padding: 40px;">
            <i class="fas fa-info-circle" style="font-size: 48px; margin-bottom: 20px;"></i>
            <h3>Nenhum imóvel encontrado</h3>
            <p>Tente ajustar os filtros para encontrar mais opções.</p>
            <a href="imoveis.php" class="btn btn-primary" style="margin-top: 20px;">
                <i class="fas fa-redo"></i> Limpar Filtros
            </a>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Função para agendar visita
function agendarVisita(imovelId) {
    if(!confirm('Deseja agendar uma visita para este imóvel?')) return;
    
    // Aqui você implementaria o agendamento real
    alert('Funcionalidade de agendamento em desenvolvimento!\nImóvel ID: ' + imovelId);
    
    // Exemplo de implementação futura:
    // fetch('api/agendar_visita.php', {
    //     method: 'POST',
    //     body: JSON.stringify({ imovel_id: imovelId })
    // })
    // .then(response => response.json())
    // .then(data => {
    //     if(data.success) {
    //         showAlert('Visita agendada com sucesso!', 'success');
    //     } else {
    //         showAlert('Erro ao agendar visita: ' + data.message, 'danger');
    //     }
    // });
}

// Inicializar favoritos
document.addEventListener('DOMContentLoaded', function() {
    updateFavoriteButtons();
});
</script>

<?php include 'includes/footer.php'; ?>