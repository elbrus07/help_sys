<?php


namespace ReferenceSystem;


class RSArticle
{
    public $Id;
    public $Caption;
    public $Content;
    public $Type;

    /**
     * RSArticle конструткор.
     *
     * @param int $id
     * @param string $caption
     * @param string $content
     * @param string $type
     */
    public function __construct($id, $caption, $content, $type)
    {
        $this->Id = $id;
        $this->Caption = $caption;
        $this->Content = $content;
        $this->Type = $type;
    }

    public function toJSON()
    {
        return json_encode(['Id' => $this->Id,
                    'Caption' => $this->Caption,
                    'Content' => $this->Content,
                    'Type' => $this->Type
            ]);
    }
}