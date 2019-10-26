<?php
namespace ReferenceSystem\Modules;
use \mysqli;
use ReferenceSystem\Modules\Database\Settings as DBSettings;

include_once(__DIR__ . '/../vendor/autoload.php');


class ReferenceSystem
{
    public static $HierarchyItems = ['Chapter','Section','Subsection'];
    /**
     * Массив статей, содержащих вхождение строки в статью/заголовок
     *
     * @param string $searchString строка для поиска
     * @return RSArticle[]|string
     */
    public static function SearchReference(string $searchString)
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $searchString = $mysqli->real_escape_string($searchString);
        $sql = "SELECT id FROM ref_system_data WHERE content LIKE '%$searchString%' OR caption LIKE '%$searchString%'";
        if (!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        $arr = array();
        if($result->num_rows === 0)
            return $arr;
        for($i = 0; $obj = $result->fetch_assoc(); $i++)
            $arr[$i] = new RSArticle($obj['id'], RSArticleModes::ID);
        return $arr;
    }

    /**
     * Получение схемы оглавления. По факту практически обход в глубину
     *
     * @param ReferenceSchemeItem[] $scheme
     * @return ReferenceSchemeItem[]|string
     */
    public static function GetReferenceScheme($scheme = array())
    {
        if(count($scheme) === 0)
        {
            $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
            if($mysqli->connect_errno)
                return "Ошибка: " . $mysqli->error . "\n";
            $type = self::$HierarchyItems[0];
            $sql = "SELECT id,type,caption FROM ref_system_data WHERE type = '$type'";
            if(!$result = $mysqli->query($sql))
                return "Ошибка: " . $mysqli->error . "\n";
            if($result->num_rows === 0)
                return $scheme;
            for ($i = 0; $arr = $result->fetch_assoc(); $i++) {
                $scheme[$i] = new ReferenceSchemeItem(new RSArticle($arr['id'], RSArticleModes::ID),
                    array(), null);
            }
            for($i=0; $i<count($scheme); $i++)
                $scheme[$i]->FirstLevel = $scheme;
            $mysqli->close();
            return self::GetReferenceScheme($scheme);
        }
        else
        {
            for($i=0; $i<count($scheme); $i++)
            {
                $children = $scheme[$i]->Article->getChildren();
                for ($j = 0; $j<count($children); $j++)
                {
                    $scheme[$i]->Children[$j] = new ReferenceSchemeItem($children[$j],array(),$scheme[$i]->FirstLevel);
                    if(count($children)>0)
                        self::GetReferenceScheme($scheme[$i]->Children);
                }

            }
            return $scheme[0]->FirstLevel;
        }
    }

    /**
     * Конвертирование пути в id статьи в БД
     *
     * @param string $path путь к статье в формате "Chapter/Section/Subsection/Subsubsection/.../(n*Sub)section"
     * @return int|string
     */
    public static function PathToId(string $path)
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $path = $mysqli->real_escape_string($path);
        $path = explode('/',$path);
        $parent_id = -1;
        for($i = 0; $i<count($path); $i++)
        {
            $type = self::$HierarchyItems[$i];
            $sql = ($i > 0) ?
                "SELECT id FROM ref_system_data WHERE caption = '$path[$i]' AND type = '$type'  AND parent_id=$parent_id"
                :
                "SELECT id FROM ref_system_data WHERE caption = '$path[$i]' AND type = '$type'";
            if (!$result = $mysqli->query($sql))
                return "Ошибка: " . $mysqli->error . "\n";
            $parent_id = $result->fetch_assoc()['id'];
        }
        $mysqli->close();
        return $parent_id;
    }

    /**
     * Конвертирование id статьи в бд в путь к ней
     *
     * @param int $id
     * @return string
     */
    public static function IdToPath(int $id)
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $id = $mysqli->real_escape_string($id);
        $path = '';
        for ($i=0;$id != null;$i++)
        {
            $sql = "SELECT caption,parent_id FROM ref_system_data WHERE id = $id";
            if (!$result = $mysqli->query($sql))
                return "Ошибка: " . $mysqli->error . "\n";
            $result = $result->fetch_assoc();
            $path = ($i == 0) ? $result['caption'] : $result['caption'] . "/$path";
            $id = $result['parent_id'];
        }
        $mysqli->close();
        return $path;
    }
}