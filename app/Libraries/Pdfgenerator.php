<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdfgenerator
{
    public function generate($html, $filename='', $paper = '', $orientation = '', $stream=true)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();
        if ($stream) {
            $dompdf->stream($filename.".pdf", array("Attachment" => 0));
        } else {
            return $dompdf->output();
        }
    }
}