<?php

ini_set('display_errors', 0); // Desativar a exibição de erros no navegador
error_reporting(0);

session_start();
require('fpdf.php');


if (!isset($_SESSION['receita_id'])) {
    die("Erro: ID da receita não especificado.");
}

$receita_id = $_SESSION['receita_id'];

// Conexão com o banco de dados
$servername = "193.203.175.98"; // Para a Hostinger, geralmente é "localhost"
$username = "u704604173_mateusligoski1"; // Use o nome de usuário que você configurou
$password = "a7@9&s>N4"; // A senha que você configurou
$dbname = "u704604173_farmacia"; // O nome da base de dados (inclua o prefixo, se houver)

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta para obter os detalhes do paciente e prescritor
$sql = "SELECT r.cid, r.data_prescricao, c.nome AS cliente_nome, c.cpf, c.telefone, c.rua, c.numero, c.complemento, c.cidade, c.uf, c.cep, 
        pr.nome AS prescritor_nome, pr.assinatura AS assinatura_path, pr.crm AS prescritor_crm
        FROM receitas r
        INNER JOIN clientes c ON r.cliente_id = c.id
        INNER JOIN prescritores pr ON r.prescritor_id = pr.id
        WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receita_id);
$stmt->execute();
$receita = $stmt->get_result()->fetch_assoc();

// Consulta para obter medicamentos da receita
$sql_medicamentos = "SELECT m.nome, m.tipo, m.concentracao, mr.posologia, mr.quantidade, m.valor
                     FROM medicamentos_receita mr 
                     INNER JOIN produtos m ON mr.produto_id = m.id 
                     WHERE mr.receita_id = ?";
$stmt_medicamentos = $conn->prepare($sql_medicamentos);
$stmt_medicamentos->bind_param("i", $receita_id);
$stmt_medicamentos->execute();
$medicamentos_result = $stmt_medicamentos->get_result();


// Criação do PDF com estilização
class PDF extends FPDF {
    function RoundedCell($w, $h, $txt, $border = 0, $ln = 0, $align = '', $fill = false) {
        $this->SetDrawColor(169, 169, 169); // Cinza escuro
        $this->SetLineWidth(0.3);
        $this->SetFillColor(245, 245, 245); // Cinza claro
        $this->Cell($w, $h, $txt, $border, $ln, $align, $fill);
    }
}

$pdf = new PDF();
$pdf->AddPage();


// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetFillColor(169, 169, 169); // Cinza escuro para o cabeçalho
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 15, utf8_decode('Receita Médica'), 0, 1, 'C', true);

// Dados do Paciente e Prescritor
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 10, 'Dados do Paciente e Prescritor:', 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 10); // Negrito para rótulos
$pdf->RoundedCell(25, 8, 'Paciente:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(70, 8, utf8_decode($receita['cliente_nome']), 1, 0, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->RoundedCell(25, 8, 'CPF:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(60, 8, $receita['cpf'], 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->RoundedCell(25, 8, 'Telefone:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(155, 8, $receita['telefone'], 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->RoundedCell(25, 8, 'Logradouro:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(70, 8, utf8_decode($receita['rua'] . ', ' . $receita['numero'] . ' ' . $receita['complemento']), 1, 0, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->RoundedCell(25, 8, 'Cidade/UF:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(60, 8, utf8_decode($receita['cidade'] . ' - ' . $receita['uf']), 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->RoundedCell(25, 8, 'CEP:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(70, 8, $receita['cep'], 1, 0, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->RoundedCell(25, 8, 'Prescritor:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(60, 8, utf8_decode($receita['prescritor_nome']), 1, 1, 'L', true);

$pdf->SetFont('Arial', 'B', 10);
$pdf->RoundedCell(25, 8, 'CID:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(70, 8, $receita['cid'], 1, 0, 'L', true);
$pdf->SetFont('Arial', 'B', 10);
$pdf->RoundedCell(25, 8, 'Data:', 1, 0, 'L', true);
$pdf->SetFont('Arial', '', 10);
$pdf->RoundedCell(60, 8, date('d/m/Y', strtotime($receita['data_prescricao'])), 1, 1, 'L', true);

// Medicamentos Prescritos
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Medicamentos Prescritos:', 0, 1, 'L');

$pdf->SetFont('Arial', '', 10);
$total_valor = 0;
while ($medicamento = $medicamentos_result->fetch_assoc()) {
    $pdf->SetFillColor(220, 235, 255);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->RoundedCell(0, 8, 'Medicamento: ' . utf8_decode($medicamento['nome'] . ' - ' . $medicamento['tipo'] . ' - ' . $medicamento['concentracao']), 1, 1, 'L', true);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->RoundedCell(0, 8, 'Posologia: ' . utf8_decode($medicamento['posologia']), 1, 1, 'L', true);
    $pdf->RoundedCell(40, 8, 'Quantidade: ' . $medicamento['quantidade'], 1, 0, 'L', true);
    
    // Calcular o valor total do medicamento
    $valor_total_medicamento = $medicamento['valor'] * $medicamento['quantidade'];
    $pdf->RoundedCell(0, 8, 'Valor: R$ ' . number_format($valor_total_medicamento, 2, ',', '.'), 1, 1, 'L', true);
    $total_valor += $valor_total_medicamento;
}

// Total dos Medicamentos
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Valor Total dos Medicamentos: R$ ' . number_format($total_valor, 2, ',', '.'), 0, 1, 'R');

// Assinatura do Prescritor
$pdf->Ln(10);

// Centralizando a imagem da assinatura
$xCenter = ($pdf->GetPageWidth() - 40) / 2; // 40 é a largura da imagem
$pdf->Image($receita['assinatura_path'], $xCenter, $pdf->GetY(), 40, 20);

// Adicionando linha para assinatura
$pdf->Ln(25);
$xLine = ($pdf->GetPageWidth() - 60) / 2; // 60 é o comprimento da linha
$pdf->SetX($xLine);
$pdf->Cell(60, 0, '', 'T', 1, 'C'); // Linha horizontal

// Nome e CRM do prescritor
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, utf8_decode($receita['prescritor_nome']), 0, 1, 'C');
$pdf->Cell(0, 8, 'CRM: ' . $receita['prescritor_crm'], 0, 1, 'C');

$pdf->Output();

?>
