<?php
// teste_conexao.php - Arquivo para testar conexão com MySQL

echo "<h2>🔧 Teste de Conexão MySQL</h2>";
echo "<p>Este arquivo testa a conexão com o banco de dados.</p>";
echo "<hr>";

// Tentar conectar
try {
    // Tente primeiro com senha vazia (XAMPP padrão)
    $conn = new PDO("mysql:host=localhost;dbname=newhome_db", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>Conexão bem-sucedida!</strong>";
    echo "<br>Usuário: root | Senha: (vazia)";
    
    // Verificar tabelas
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if(count($tables) > 0) {
        echo "<br>📊 Tabelas encontradas: " . count($tables);
        echo "<ul>";
        foreach($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<br>⚠️ Nenhuma tabela encontrada. Execute o script SQL para criar as tabelas.";
    }
    
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>Falha na conexão 1:</strong> " . $e->getMessage();
    echo "</div>";
    
    // Tentar com senha 'root' (MAMP padrão)
    try {
        $conn = new PDO("mysql:host=localhost;dbname=newhome_db", "root", "root");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>Conexão bem-sucedida na tentativa 2!</strong>";
        echo "<br>Usuário: root | Senha: root";
        echo "</div>";
        
    } catch(PDOException $e2) {
        echo "<div style='background-color: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>Falha na conexão 2:</strong> " . $e2->getMessage();
        echo "<br><br><strong>Tente estas soluções:</strong>";
        echo "<ol>";
        echo "<li>Verifique se o MySQL está rodando (XAMPP/WAMP/MAMP)</li>";
        echo "<li>Crie o banco 'newhome_db' no phpMyAdmin</li>";
        echo "<li>Execute o script SQL para criar as tabelas</li>";
        echo "<li>Configure o arquivo config/database.php com seu usuário/senha</li>";
        echo "</ol>";
        echo "</div>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>← Voltar para a página inicial</a></p>";
?>