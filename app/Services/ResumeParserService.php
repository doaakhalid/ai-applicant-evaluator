<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class ResumeParserService
{
    
    public function parse(string $filePath): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        return $pdf->getText();
    }
}
