<?php
require('fpdf/fpdf.php');
include 'koneksi.php';

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'LAPORAN BUKU TAMU TATA USAHA',0,1,'C');
        $this->SetFont('Arial','',10);
        $this->Cell(0,6,'Tanggal Cetak: '.date('d-m-Y'),0,1,'C');
        $this->Ln(5);

        // Header tabel
        $this->SetFont('Arial','B',9);
        $this->Cell(8,8,'No',1);
        $this->Cell(30,8,'Nama',1);
        $this->Cell(28,8,'Identitas',1);
        $this->Cell(40,8,'Keperluan',1);
        $this->Cell(25,8,'Check In',1);
        $this->Cell(25,8,'Check Out',1);
        $this->Cell(25,8,'Status',1);
        $this->Ln();
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Halaman '.$this->PageNo(),0,0,'C');
    }

    function Row($data)
{
    $nb = 0;
    foreach ($data as $txt) {
        $nb = max($nb, $this->NbLines(40, $txt));
    }
    $h = 8 * $nb;

    if ($this->GetY() + $h > $this->PageBreakTrigger) {
        $this->AddPage();
    }

    foreach ($data as $i => $txt) {
        $w = [8, 30, 28, 40, 25, 25, 25][$i];
        $x = $this->GetX();
        $y = $this->GetY();

        $this->Rect($x, $y, $w, $h);
        $this->MultiCell($w, 8, $txt, 0, 'L');
        $this->SetXY($x + $w, $y);
    }

    $this->Ln($h);
}

function NbLines($w, $txt)
{
    $cw = &$this->CurrentFont['cw'];
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if ($nb > 0 && $s[$nb - 1] == "\n") $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while ($i < $nb) {
        $c = $s[$i];
        if ($c == "\n") {
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
            continue;
        }
        if ($c == ' ') $sep = $i;
        $l += $cw[$c];
        if ($l > $wmax) {
            if ($sep == -1) {
                if ($i == $j) $i++;
            } else {
                $i = $sep + 1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $nl++;
        } else {
            $i++;
        }
    }
    return $nl;
}

}

$pdf = new PDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);
$pdf->SetFont('Arial','',9);

// Query data tamu
$query = mysqli_query($koneksi, "
    SELECT 
        nama_lengkap,
        no_id,
        institusi,
        keperluan,
        waktu_checkin,
        waktu_checkout,
        status_aktif
    FROM data_tamu
    ORDER BY waktu_checkin DESC
");

$no = 1;
while ($data = mysqli_fetch_assoc($query)) {

    $status = ($data['status_aktif'] == 'aktif') ? 'Sedang Aktif' : 'Selesai';

    $pdf->Cell(8,8,$no++,1);
    $pdf->Cell(30,8,$data['nama_lengkap'],1);
    $pdf->Cell(28,8,$data['no_id'],1);
    $pdf->Cell(40,8,$data['keperluan'],1);

    $pdf->Cell(
        25,8,
        date('d/m/Y H:i', strtotime($data['waktu_checkin'])),
        1
    );

    $pdf->Cell(
        25,8,
        $data['waktu_checkout']
            ? date('d/m/Y H:i', strtotime($data['waktu_checkout']))
            : '-',
        1
    );

    $pdf->Cell(25,8,$status,1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output('I','Laporan_Buku_Tamu.pdf');
