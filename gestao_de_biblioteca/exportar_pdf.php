<?php
require('fpdf/fpdf.php');
require 'conn.php';

// Consultar histórico
$sql = "SELECT e.id, l.titulo AS livro, a.nome AS aluno, e.data_emprestimo, e.data_devolucao, e.estado_livro
        FROM emprestimos e
        JOIN livros l ON e.livro_id = l.id
        JOIN alunos a ON e.aluno_id = a.id";
$result = $conn->query($sql);

// Criação do PDF
$pdf = new FPDF();
$pdf->AddPage();

// Se você já tem o arquivo gerado corretamente (DejaVuLGCSans.php), use a seguinte linha:
$pdf->AddFont('DejaVu', '', 'fpdf/font/DejaVu.php');  // Caminho correto para o arquivo .php de fonte

$pdf->SetFont('DejaVu', '', 12);  // Usando a fonte DejaVu para garantir o uso da fonte adicionada.

// Adicionar conteúdo ao PDF
$pdf->Cell(200, 10, 'Histórico de Empréstimos', 0, 1, 'C');
$pdf->Ln();

// Aqui você começa a adicionar os dados do seu banco de dados
// Exemplo fictício de tabela
$pdf->Cell(40, 10, 'Livro');
$pdf->Cell(40, 10, 'Aluno');
$pdf->Cell(40, 10, 'Data de Empréstimo');
$pdf->Ln();
$pdf->Cell(40, 10, 'Exemplo de Livro');
$pdf->Cell(40, 10, 'Aluno Teste');
$pdf->Cell(40, 10, '01/01/2022');
$pdf->Ln();

// Gerar o PDF
$pdf->Output('D', 'historico_emprestimos.pdf');

