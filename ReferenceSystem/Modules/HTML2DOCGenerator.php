<?php
/*
 * Конвертор html->docx, использующий PhpOffice\PhpWord
 */

namespace ReferenceSystem\Modules;
require_once __DIR__.'/../vendor/autoload.php';

use Sunra\PhpSimple\HtmlDomParser;
use PhpOffice\PhpWord;

class HTML2DOCGenerator
{
    /** @var PhpWord\PhpWord  */
    private $phpWord;
    /** @var PhpWord\Element\Section */
    private $section;

    public function __construct()
    {
        $this->phpWord = new PhpWord\PhpWord();
        PhpWord\Settings::setOutputEscapingEnabled(true);
        //Задаем шрифт и размер текста
        $this->phpWord->setDefaultFontName("Calibri");
        $this->phpWord->setDefaultFontSize(12);
        //Стили заголовков
        $this->phpWord->addTitleStyle(1, ['size'=>16, 'color'=>'000000', 'bold' => true], [
            'align' => 'center' ]);
        $this->phpWord->addTitleStyle(2, ['size'=>15, 'color'=>'000000', 'bold' => true]);
        $this->phpWord->addTitleStyle(3, ['size'=>14, 'color'=>'000000', 'bold' => true]);
    }

    public function addSection() : void
    {
        $this->section = $this->phpWord->addSection();
    }

    /**
     * Добавление фрагмента html-кода в последнюю или заданную секцию
     * @param string $html html код
     * @param PhpWord\Element\Section|null $section
     */
    public function addHTML(string $html, PhpWord\Element\Section $section = null) : void
    {
        $fixed_html = self::XHTMLTagsFix($html);
        if($section === null)
            PhpWord\Shared\Html::addHtml($this->section,$fixed_html, false, false);
        else
            PhpWord\Shared\Html::addHtml($section,$fixed_html, false, false);
    }

    /**
     * Инициализация процесса загрузки итогового файла
     * @param string $filename имя файла
     * @throws PhpWord\Exception\Exception
     */
    public function startDownload(string $filename) : void
    {
        header( "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document" );
        // you should look for the real header that you need if it's not Word 2007!!!
        header( 'Content-Disposition: attachment; filename='.$filename );

        //$h2d_file_uri = tempnam( "", "htd" );
        $objWriter = PhpWord\IOFactory::createWriter( $this->phpWord, "Word2007" );
        $objWriter->save( "php://output" );
    }

    /**
     * Перевод необходимых одиночных xhtml тегов в html
     * @param string $html html код
     * @return string пофикшенный html
     */
    private static function XHTMLTagsFix(string $html) : string
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

    /**
     * @return PhpWord\PhpWord
     */
    public function getPhpWord(): PhpWord\PhpWord
    {
        return $this->phpWord;
    }

    /**
     * @param PhpWord\PhpWord $phpWord
     */
    public function setPhpWord(PhpWord\PhpWord $phpWord): void
    {
        $this->phpWord = $phpWord;
    }
}