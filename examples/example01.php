<?php

/**
 * Example 01: basic usage
 */

$loader = require __DIR__ . '/../vendor/autoload.php';

$pdf = new andreaventuri\PdfMerger\PdfMerger;

$pdf->addPDF(__DIR__.'/pdf/one.pdf', '1,3,4')
	->addPDF(__DIR__.'/pdf/two.pdf', '1-2')
	->addPDF(__DIR__.'/pdf/three.pdf')
	->merge('file', __DIR__.'/output01.pdf');
