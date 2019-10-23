<?php

use ReferenceSystem\Modules\OfflineReference;

require_once __DIR__.'/vendor/autoload.php';

if(isset($_GET['mode']))
{
    switch ($_GET['mode'])
    {
        case 'all':
            OfflineReference::downloadAll("full_help.docx");
            break;
        case 'this':
            if (isset($_GET['article_id']))
                OfflineReference::downloadOne("article_".$_GET['article_id']."_help.docx",$_GET['article_id']);
            break;
        case 'branch':
            if (isset($_GET['article_id']))
                OfflineReference::downloadBranch("part_of_help.docx",$_GET['article_id']);
            break;
    }
}
