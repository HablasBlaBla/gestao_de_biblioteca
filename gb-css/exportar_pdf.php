<?php
//require('fpdf/fpdf.php');
require 'conn.php';

// Consultar histórico
$sql = "SELECT e.id, l.titulo AS livro, a.nome AS aluno, e.data_emprestimo, e.data_devolucao, e.estado
        FROM emprestimos e
        JOIN livros l ON e.livro_id = l.id
        JOIN alunos a ON e.aluno_id = a.id";
$result = $conn->query($sql);

// Criação do PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'fpdf/font/DejaVu.php');  // Caminho correto para o arquivo .php de fonte
$pdf->SetFont('DejaVu', '', 12);  // Usando a fonte DejaVu para garantir o uso da fonte adicionada.

// Adicionar conteúdo ao PDF
$pdf->Cell(200, 10, 'Histórico de Empréstimos', 0, 1, 'C');
$pdf->Ln();

// Cabeçalhos da tabela
$pdf->Cell(40, 10, 'Livro');
$pdf->Cell(40, 10, 'Aluno');
$pdf->Cell(40, 10, 'Data de Empréstimo');
$pdf->Cell(40, 10, 'Data de Devolução');
$pdf->Ln();

// Adicionar dados do banco de dados
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['livro']);
    $pdf->Cell(40, 10, $row['aluno']);
    $pdf->Cell(40, 10, date('d/m/Y', strtotime($row['data_emprestimo'])));
    $pdf->Cell(40, 10, $row['data_devolucao'] ? date('d/m/Y', strtotime($row['data_devolucao'])) : 'Não devolvido');
    $pdf->Ln();
}

// Gerar o PDF
$pdf->Output('D', 'historico_emprestimos.pdf');
?>
