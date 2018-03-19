<?php

/**
 * Example 02: custom Header and Footer
 */

$loader = require __DIR__ . '/../vendor/autoload.php';


/**
 * @see https://tcpdf.org/examples/example_003/
 */
class MyCustomPdf extends \TCPDI
{
	/**
	 * Page header
	 */
    public function Header()
	{
		// Position at 5 mm from top
		$this->SetY(5);

        // Set font
        $this->SetFont('helvetica', 'B', 20);

        // Title
        $this->Cell(0, 15, '<< Example header >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    /**
	 * Page footer
	 */
    public function Footer()
	{
        // Position at 15 mm from bottom
        $this->SetY(-15);

        // Set font
        $this->SetFont('helvetica', 'I', 8);

        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}


$obj = new MyCustomPdf();

$pdf = new andreaventuri\PdfMerger\PdfMerger($obj);

$pdf->addPDF(__DIR__.'/pdf/one.pdf', '1,3,4')
	->addPDF(__DIR__.'/pdf/two.pdf', '1-2')
	->addPDF(__DIR__.'/pdf/three.pdf')
	->merge('file', __DIR__.'/output02.pdf');