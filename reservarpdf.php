<?php
//header('Cache-Control: no cache'); //no cache
//session_cache_limiter('private_no_expire'); // works
//session_cache_limiter('public'); // works too
session_start();
require('fpdf.php');
date_default_timezone_set('America/Monterrey');
$fecha = date("d-m-Y");
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        //$this->Image('logo.png',10,8,33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(50, 10, 'Reservar en linea', 1, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->Cell(200, 10, 'La Central Estacionamiento Publico S.A de C.V.', 0, 1, 'C');
        $this->Ln(20);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        //$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 16);
//cuerpo del archivo pdf
$pdf->Cell(90, 10, 'Placas', 1, 0, 'C', 0);
$pdf->Cell(90, 10, $_SESSION['placas'], 0, 1, 'C');
$pdf->Cell(90, 10, 'Fecha', 1, 0, 'C', 0);
$pdf->Cell(90, 10, $fecha, 0, 1, 'C');
$pdf->Cell(90, 10, 'Dueno', 1, 0, 'C', 0);
$pdf->Cell(90, 10, $_SESSION['nombredue'], 0, 1, 'C');
$pdf->Cell(90, 10, 'Num de Cajon', 1, 0, 'C', 0);
$pdf->Cell(90, 10, $_SESSION['cajon'], 0, 1, 'C');
$pdf->Cell(90, 10, 'Marca', 1, 0, 'C', 0);
$pdf->Cell(90, 10, $_SESSION['marca'], 0, 1, 'C');
$pdf->Cell(90, 10, 'Modelo', 1, 0, 'C', 0);
$pdf->Cell(90, 10, $_SESSION['modelo'], 0, 1, 'C');
$pdf->Cell(90, 10, 'Color', 1, 0, 'C', 0);
$pdf->Cell(90, 10, $_SESSION['color'], 0, 1, 'C');
$pdf->Cell(90, 10, 'Tamano', 1, 0, 'C', 0);
$pdf->Cell(90, 10, $_SESSION['tamaño'], 0, 1, 'C');
$pdf->Ln(20);
$pdf->Ln(20);
$pdf->Ln(20);
$pdf->Cell(190, 10, 'PRESENTE ESTE TICKET ', 0, 1, 'C');
$pdf->Cell(190, 10, 'AL MOMENTO DE INGRESAR SU VEHICULO...', 0, 1, 'C');

$pdf->Output();
