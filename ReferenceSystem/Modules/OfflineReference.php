<?php


namespace ReferenceSystem\Modules;

require_once __DIR__.'/../vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;


//OfflineReference::generate("test.pdf");


class OfflineReference
{
    public static function generate($filename)
    {
        $html2pdf = new Html2Pdf('P', 'A4', 'ru', true, 'UTF-8', array(15, 5, 15, 5));
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $sch = ReferenceSystem::GetReferenceScheme();
        $html2pdf->setDefaultFont('dejavusans');
        $str = "";
        for ($i = 0; $i<count($sch); $i++)
        {
            $str .= '<page>' . "\r\n";
            self::printVertex($sch[$i], $str, 1);
            $str .= '</page>' . "\r\n";
        }
        $html2pdf->writeHTML($str);
        $html2pdf->output("$filename.pdf");
    }


    private static function printVertex($Vertex, &$str, $depth)
    {
        $str .= "<h$depth>".$Vertex->Article->getCaption() . "</h$depth><br>\r\n";
        $str .= $Vertex->Article->getContent() . "<br>\r\n";
        for($i = 0; $i<count($Vertex->Children); $i++)
        {
            self::printVertex($Vertex->Children[$i], $str, $depth+1);
        }
    }
}