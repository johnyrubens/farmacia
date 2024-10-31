<?php
require('fpdf/fpdf.php');
include 'db_config.php';

// Obter o ID da receita passada por GET
$receita_id = $_GET['id'];

// Buscar os dados da receita no banco de dados
$sql_receita = "SELECT r.*, p.nome AS prescritor_nome, p.crm, p.assinatura 
                FROM receitas r 
                JOIN prescritores p ON r.prescritor_id = p.id 
                WHERE r.id = $receita_id";
$result_receita = $conn->query($sql_receita);
$receita = $result_receita->fetch_assoc();

// Buscar os medicamentos da receita
$sql_medicamentos = "SELECT pr.nome, mr.quantidade 
                     FROM medicamentos_receita mr 
                     JOIN produtos pr ON mr.produto_id = pr.id 
                     WHERE mr.receita_id = $receita_id";
$result_medicamentos = $conn->query($sql_medicamentos);
$medicamentos = [];
while ($row = $result_medicamentos->fetch_assoc()) {
    $medicamentos[] = $row;
}

class PDF extends FPDF
{
    // Cabeçalho
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(190, 10, 'Receita Medica', 1, 1, 'C');
        $this->Ln(10);
    }

    // Rodapé
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'NANO FARMACOS', 0, 0, 'C');
    }
}

// Criar PDF
$pdf = new PDF();
$pdf->AddPage();

// Informações do paciente
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'Informacoes do Paciente:', 1, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(190, 10, 'Paciente: ' . $receita['paciente_nome'], 0, 1);
$pdf->Cell(190, 10, 'CPF: ' . $receita['cpf'], 0, 1);
$pdf->Cell(190, 10, 'Telefone: ' . $receita['telefone'], 0, 1);
$pdf->Cell(190, 10, 'Endereco: ' . $receita['rua'] . ', ' . $receita['numero'] . ' - ' . $receita['complemento'] . ', ' . $receita['cidade'] . '/' . $receita['uf'] . ' - ' . $receita['cep'], 0, 1);

// Medicamentos
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'Medicamentos Prescritos:', 1, 1, 'L');
$pdf->SetFont('Arial', '', 12);
foreach ($medicamentos as $medicamento) {
    $pdf->Cell(190, 10, 'Medicamento: ' . $medicamento['nome'] . ' - Quantidade: ' . $medicamento['quantidade'], 0, 1);
}

// Data de geração da receita
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(190, 10, 'Data de Geracao: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

// Assinatura do médico
$pdf->Ln(20);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(190, 10, 'Assinatura do Medico:', 0, 1, 'C');
if (!empty($receita['assinatura'])) {
    $pdf->Image($receita['assinatura'], 80, $pdf->GetY(), 50); // Assinatura do médico
}
$pdf->Ln(20);
$pdf->Cell(190, 10, 'CRM: ' . $receita['crm'], 0, 1, 'C');

// Geração do PDF
$pdf->Output();
?>
