<?php
/*
 * Загрузчик оффлайн версии справки
 */

namespace ReferenceSystem\Modules;

require_once __DIR__.'/../vendor/autoload.php';




class OfflineReference
{
    /**
     * Загрузка всей справки
     * @param string $filename имя загружаемого файла
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public static function downloadAll(string $filename) : void
    {
        $html2doc = new HTML2DOCGenerator();
        $sch = ReferenceSystem::GetReferenceScheme();
        $html = "";
        for ($i = 0; $i<count($sch); $i++)
        {
            $html2doc->addSection();
            self::DFSReferenceContentParse($sch[$i], $html);
            $html2doc->addHTML($html);
            $html = "";
        }
        $html2doc->startDownload($filename);
    }

    /**
     * Загрузка отдельной статьи
     * @param string $filename имя загружаемого файла
     * @param int $article_id id статьи
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public static function downloadOne(string $filename,int $article_id) : void
    {
        $html2doc = new HTML2DOCGenerator();
        $html2doc->addSection();
        $article = new RSArticle($article_id,RSArticleModes::ID);
        $html = "<h1>".$article->getCaption()."</h1>" . "<br/>\r\n";
        $html .= $article->getContent() . "<br/>\r\n";
        $html2doc->addHTML($html);
        $html2doc->startDownload($filename);
    }

    /**
     * Загрузка ветки справки (элемент + дети + дети детей + ...)
     * @param string $filename имя загружаемого файла
     * @param int $article_id id статьи
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public static function downloadBranch(string $filename, int $article_id) : void
    {
        $html2doc = new HTML2DOCGenerator();
        $article = new RSArticle($article_id,RSArticleModes::ID);
        $rsi = new ReferenceSchemeItem($article,[],null);
        $sch = [$rsi];
        $rsi->FirstLevel = $sch;
        $sch = ReferenceSystem::GetReferenceScheme($sch);
        $html = "";
        for ($i = 0; $i<count($sch); $i++)
        {
            $html2doc->addSection();
            self::DFSReferenceContentParse($sch[$i], $html);
            $html2doc->addHTML($html);
            $html = "";
        }
        $html2doc->startDownload($filename);
    }

    /**
     * Обход в глубину элементов справки
     * @param ReferenceSchemeItem $Vertex Элемент справки
     * @param string $str Переменная для вывода контента
     * @param int $depth Глубина
     */
    private static function DFSReferenceContentParse(ReferenceSchemeItem $Vertex, string &$str, int $depth = 1) : void
    {
        $str .= "<h$depth>".$Vertex->Article->getCaption() . "</h$depth>\r\n";
        $str .= $Vertex->Article->getContent() . "<br/>\r\n";
        for($i = 0; $i<count($Vertex->Children); $i++)
        {
            self::DFSReferenceContentParse($Vertex->Children[$i], $str, $depth+1);
        }
    }
}