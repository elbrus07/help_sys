<?php


namespace ReferenceSystem\Modules;

require_once __DIR__.'/../vendor/autoload.php';

use Sunra\PhpSimple\HtmlDomParser;
use PhpOffice\PhpWord;





class OfflineReference
{
    public static function XHTMLTagsFix($html)
    {
        $tagsForFix = [ 'img', 'br' ];
        $parser = HtmlDomParser::str_get_html( $html );
        foreach ($tagsForFix as $tagName) {
            foreach ($parser->find($tagName) as $e) {
                if($e->outertext[mb_strlen($e->outertext)-2] != '/')
                    $e->outertext = mb_substr($e->outertext, 0, mb_strlen($e->outertext) - 1) . "/>";
            }
        }
        return $parser->save();
    }

    public static function downloadAll($filename)
    {
        $phpWord = new PhpWord\PhpWord();
        PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord->addParagraphStyle('Heading2', array('alignment' => 'center'));
        $sch = ReferenceSystem::GetReferenceScheme();
        $str = "";
        for ($i = 0; $i<count($sch); $i++)
        {
            $section = $phpWord->addSection();
            self::printVertex($sch[$i], $str, 1);
            $editedHTML = OfflineReference::XHTMLTagsFix($str);
            PhpWord\Shared\Html::addHtml($section,$editedHTML, false, false);
            $str = "";
        }
        self::startDownload($phpWord, $filename);
    }
    public static function downloadOne($filename,$article_id)
    {
        $phpWord = new PhpWord\PhpWord();
        PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord->addParagraphStyle('Heading2', array('alignment' => 'center'));
        $section = $phpWord->addSection();
        $article = new RSArticle($article_id,RSArticleModes::ID);
        $str = "<span style='font-size: 16px; font-weight: bold;'>".$article->getCaption() . "</span><br/>\r\n";
        $str .= $article->getContent() . "<br/>\r\n";
        $article->getCaption();
        $editedHTML = OfflineReference::XHTMLTagsFix($str);
        PhpWord\Shared\Html::addHtml($section,$editedHTML, false, false);
        self::startDownload($phpWord, $filename);
    }
    public static function downloadBranch($filename,$article_id)
    {
        $phpWord = new PhpWord\PhpWord();
        PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord->addParagraphStyle('Heading2', array('alignment' => 'center'));
        $article = new RSArticle($article_id,RSArticleModes::ID);
        $rsi = new ReferenceSchemeItem($article,[],null);
        $sch = [$rsi];
        $rsi->FirstLevel = $sch;
        $sch = ReferenceSystem::GetReferenceScheme($sch);
        $str = "";
        for ($i = 0; $i<count($sch); $i++)
        {
            $section = $phpWord->addSection();
            self::printVertex($sch[$i], $str, 1);
            $editedHTML = OfflineReference::XHTMLTagsFix($str);
            PhpWord\Shared\Html::addHtml($section,$editedHTML, false, false);
            $str = "";
        }
        self::startDownload($phpWord, $filename);
    }

    /**
     * @param PhpWord\PhpWord $phpWord
     * @param $filename
     * @throws PhpWord\Exception\Exception
     */
    private static function startDownload($phpWord, $filename)
    {
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document" );// you should look for the real header that you need if it's not Word 2007!!!
        header( 'Content-Disposition: attachment; filename='.$filename );

        //$h2d_file_uri = tempnam( "", "htd" );
        $objWriter = PhpWord\IOFactory::createWriter( $phpWord, "Word2007" );
        $objWriter->save( "php://output" );
    }
    private static function printVertex($Vertex, &$str, $depth)
    {
        $str .= "<span style='font-size: 16px; font-weight: bold;'>".$Vertex->Article->getCaption() . "</span><br/>\r\n";
        $str .= $Vertex->Article->getContent() . "<br/>\r\n";
        for($i = 0; $i<count($Vertex->Children); $i++)
        {
            self::printVertex($Vertex->Children[$i], $str, $depth+1);
        }
    }
}