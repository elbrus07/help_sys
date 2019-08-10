<?php


namespace ReferenceSystem\Modules;


class ReferenceSchemeItem
{
    public $Article;
    public $Children;
    public $FirstLevel;
    public $Path;

    /**
     * ReferenceSchemeItem конструктор.
     *
     * @param RSArticle $article
     * @param ReferenceSchemeItem[] $children
     * @param ReferenceSchemeItem[] $firstLevel
     * @param string $path
     */
    public function __construct($article, $children, $firstLevel, $path)
    {
        $this->Article = $article;
        $this->Children = $children;
        $this->FirstLevel = $firstLevel;
        $this->Path = $path;
    }
}