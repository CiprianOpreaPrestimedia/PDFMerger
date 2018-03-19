# PDFMerger for PHP (version 5.3 to 7.2)

PDFMerger forked from myokyawhtun/PDFMerger

New features:
* PSR-4 support
* Namespaces support
* Possibility to print header and/or footer on the merged pdf

## PHP 7 Compatible

I tested with PHP 7.2.3 on my develpment machine and it works well.

## Support of PDF 1.5 and PDF 1.6

FPDF and FPDI libraries replaced by TCPDF with TCPDI extension and parser.

### Example Usage
```php
$loader = require __DIR__ . '/../vendor/autoload.php';

$pdf = new andreaventuri\PdfMerger\PdfMerger;

$pdf->addPDF('samplepdfs/one.pdf', '1, 3, 4');
$pdf->addPDF('samplepdfs/two.pdf', '1-2');
$pdf->addPDF('samplepdfs/three.pdf');


$pdf->merge('file', 'samplepdfs/TEST2.pdf'); // generate the file

$pdf->merge('download', 'samplepdfs/test.pdf'); // force download

// REPLACE 'file' WITH 'browser', 'download', 'string', or 'file' for output options
```