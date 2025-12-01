<?php
require_once __DIR__ . '/../vendor/fpdf/fpdf.php';
require_once __DIR__ . '/../app/Core/DB.php';
require_once __DIR__ . '/../app/Models/Cliente.php';

class PDF extends FPDF {
    function Header() {
        // Logo
        $logoPath = __DIR__ . '/../public/assets/logo2.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 8, 60); // ancho 60mm
        }

        // Encabezado centrado sin fondo
        $this->SetTextColor(0,0,0); // texto negro
        $this->SetFont('Arial','B',14);

        $anchoCelda = 100;
        $altoCelda  = 10;
        $posX = ($this->GetPageWidth() - $anchoCelda) / 2;
        $this->SetX($posX);

        $this->Cell($anchoCelda, $altoCelda, utf8_decode("Póliza de Seguro"), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial','I',10);
        $this->Cell(0,10,"Emitido el: " . date('d/m/Y'),0,1,'C');
        $this->Cell(0,10,"NexoSeguros - Documento confidencial",0,0,'C');
    }
}

// Parámetros
$tipo = $_GET['tipo'] ?? '';
$id   = $_GET['id'] ?? 0;

$pdo = DB::connect();

// Consulta según tipo
switch ($tipo) {
    case 'vida':
        $stmt = $pdo->prepare("SELECT * FROM seguro_vida WHERE id_vida = ? LIMIT 1");
        break;
    case 'auto':
        $stmt = $pdo->prepare("SELECT * FROM seguro_auto WHERE id_auto = ?");
        break;
    case 'robo':
        $stmt = $pdo->prepare("SELECT * FROM seguro_robo WHERE id_robo = ?");
        break;
    case 'incendio':
        $stmt = $pdo->prepare("SELECT * FROM seguro_incendio WHERE id_incendio = ?");
        break;
    default:
        die("Tipo de seguro inválido");
}

$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("No se encontró la póliza");
}

// Ahora sí obtenemos el perfil del cliente
$clienteModel = new Cliente();
$perfil = $clienteModel->obtenerPerfilCliente($data['id_cliente']);

// Crear PDF
$pdf = new PDF();
$pdf->AddPage();

// Sección: Datos del Cliente
$pdf->SetFillColor(220,220,220);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,utf8_decode("Datos del Cliente"),0,1,'L',true);

$pdf->Ln(5);

$pdf->SetFont('Arial','',12);
$pdf->MultiCell(0,8,utf8_decode("Nombre completo: " . $perfil['nombre'] . " " . $perfil['apellidoPaterno'] . " " . $perfil['apellidoMaterno']));
$pdf->MultiCell(0,8,utf8_decode("CURP: " . $perfil['curp']));
$pdf->MultiCell(0,8,utf8_decode("RFC: " . $perfil['rfc']));

$pdf->Cell(0,8,utf8_decode("Teléfono: " . $perfil['telefono']),0,1);
$pdf->MultiCell(0,8,utf8_decode("Dirección: " . $perfil['direccion']));
$pdf->Cell(0,8,utf8_decode("Usuario: " . $perfil['usuario']),0,1);
$pdf->Cell(0,8,utf8_decode("Correo: " . $perfil['correo']),0,1);
$pdf->Ln(10);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// Sección: Detalles de la póliza
$pdf->SetFillColor(220,220,220);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,utf8_decode("Detalles de la póliza"),0,1,'L',true);
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);
$pdf->SetFillColor(240,240,240);

$fill = false;
foreach ($data as $campo => $valor) {
    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
    $pdf->Cell(60,10,utf8_decode(ucfirst(str_replace('_',' ',$campo)).":"),1,0,'L',true);
    $pdf->Cell(0,10,utf8_decode($valor),1,1,'L',true);
    $fill = !$fill;
}


// Descargar directamente al navegador
$pdf->Output('D', "poliza_{$tipo}_{$id}.pdf");