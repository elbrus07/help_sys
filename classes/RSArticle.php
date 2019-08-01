<?php


namespace ReferenceSystem;


class RSArticle
{
    public $Id;
    public $Caption;
    public $Content;
    public $Type;

    public function __construct($id,$caption,$content,$type)
    {
        $this->Id = $id;
        $this->Caption = $caption;
        $this->Content = $content;
        $this->Type = $type;
    }
}