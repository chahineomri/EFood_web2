<?php

namespace App\Data;

use Dompdf\Dompdf;
use Dompdf\Options;

class pdfService
{
    private $domPdf;

    public function __construct()
    {
        $this->domPdf = new Dompdf();
        $pdfOptions = new Options();
        //$pdfOptions->set('defaultFont', 'Garamond');
        $pdfOptions->set('isRemoteEnabled', true);
        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html){
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("facture.pdf",[
            'Attachement'=>true
        ]);
    }
}