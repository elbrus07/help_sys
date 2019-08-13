<?php


namespace ReferenceSystem\Modules;


class ReferenceSchemeItem
{
    public $Article;
    public $Children;
    public $FirstLevel;

    /**
     * ReferenceSchemeItem конструктор.
     *
     * @param RSArticle $article
     * @param ReferenceSchemeItem[] $children
     * @param ReferenceSchemeItem[] $firstLevel
     */
    public function __construct($article, $children, $firstLevel)
    {
        $this->Article = $article;
        $this->Children = $children;
        $this->FirstLevel = $firstLevel;
    }
}