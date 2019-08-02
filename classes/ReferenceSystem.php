<?php
namespace ReferenceSystem;

include_once ($_SERVER['DOCUMENT_ROOT'].'/settings/dblogin.php');
include_once 'RSArticle.php';
include_once 'ReferenceSchemeItem.php';


class ReferenceSystem
{
    public static $HierarchyItems = ['Chapter','Section','Subsection'];

    /**
     * Получить статью, относящуюся к HTML элементу с заданным id
     *
     * @param string $itemId id html элемента, документацию которого требуется получить
     * @return array|bool|string
     */
    public static function GetItemReference($itemId)
    {
        $mysqli = new \mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $itemId = $mysqli->real_escape_string($itemId);
        $sql = "SELECT data_id FROM ref_system_html_owners WHERE element_id = '$itemId'";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";

        if($result->num_rows === 0)
            return false;

        $ids = array();
        $i = 0;
        while ($id = $result->fetch_assoc()['data_id'])
        {
            if(is_numeric($id))
            {
                $ids[$i] = $id;
                $i++;
            }
        }
        $ids = implode(', ',$ids);

        $sql = "SELECT * FROM ref_system_data WHERE id IN ($ids)";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";

        if($result->num_rows === 0)
            return false;
        $arr = array();
        for($i = 0; $obj = $result->fetch_assoc(); $i++)
            $arr[$i] = new RSArticle($obj['id'],$obj['caption'],$obj['content'],$obj['type']);
        return $arr;
    }

    /**
     * Получить статью по id
     * @param int $id id в БД
     * @return RSArticle|string
     */
    public static function GetArticleByDBId($id)
    {
        $mysqli = new \mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $id = $mysqli->real_escape_string($id);
        $sql = "SELECT * FROM ref_system_data WHERE id = $id";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";

        if($result->num_rows === 0)
            return "Ошибка: Данного элемента не существует \r\n";
        $arr = $result->fetch_assoc();
        return new RSArticle($arr['id'],$arr['caption'], $arr['content'], $arr['type']);
    }

    /**
     * Добавить статью в БД
     *
     * @param string $path путь в формате "Chapter/Section/Subsection/Subsubsection/.../(n*Sub)section"
     *              причем (n*Sub)section будет заголовком будущей статьи
     * @param string $content содержимаое статьи (скажем, html код). Идею с содержимым мб надо доработать
     * @return bool|string
     */
    public static function AddReference($path, $content)
    {
        $mysqli = new \mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
        if ($mysqli->connect_errno)
            return false;
        $path = $mysqli->real_escape_string($path);
        $content = $mysqli->real_escape_string($content);

        //Разбиваем путь и получаем необходимые данные
        $explodedPath = explode('/',$path);

        //Определяем иерархический тип добавляемого элемента
        $type = self::$HierarchyItems[count($explodedPath)-1];

        //Определяем его имя
        $caption = $explodedPath[count($explodedPath)-1];

        //Получаем id родителя добавляемого элемента
        if(count($explodedPath) > 1)
        {
            //Отгрызваем последний элемент пути, ибо он не нужен
            $path = mb_substr($path,0,mb_strripos($path,'/'));
            $parent_id = self::PathToId($path);
            $sql = "INSERT INTO ref_system_data(type, parent_id, caption, content) VALUES('$type', $parent_id, '$caption', '$content')";
            if (!$result = $mysqli->query($sql))
                return "Ошибка: " . $mysqli->error . "\n";
        }
        else
        {
            //Если это глава, то никаких дейсвтвий не требуется
            $sql = "INSERT INTO ref_system_data(type, caption, content) VALUES('$type', '$caption', '$content')";
            if (!$result = $mysqli->query($sql))
                return "Ошибка: " . $mysqli->error . "\n";
        }
        return true;
    }

    /**
     * Связать id HTML элемента со статьей
     *
     * @param int $itemId id html элемента, к которому надо добавить статью
     * @param string $path путь к статье в формате "Chapter/Section/Subsection/Subsubsection/.../(n*Sub)section"
     * @return bool|string
     */
    public static function AddReferenceToItem($itemId, $path)
    {
        $mysqli = new \mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $itemId = $mysqli->real_escape_string($itemId);
        $parentId = self::PathToId($mysqli->real_escape_string($path));
        $sql = "INSERT INTO ref_system_html_owners(element_id, data_id) VALUES('$itemId',$parentId)";
        if (!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        return true;
    }

    /**
     * Проверка на вхождение строки в статью/заголовок
     *
     * @param $searchString string строка для поиска
     * @return array|string
     */
    public static function SearchReference($searchString)
    {
        $mysqli = new \mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $searchString = $mysqli->real_escape_string($searchString);
        $sql = "SELECT * FROM ref_system_data WHERE content LIKE '%$searchString%' OR caption LIKE '%$searchString%'";
        if (!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        if($result->num_rows === 0)
            return "Не найдено элементов, удовлетворяющих запросу";
        $arr = array();
        for($i = 0; $obj = $result->fetch_assoc(); $i++)
            $arr[$i] = new RSArticle($obj['id'],$obj['caption'],$obj['content'],$obj['type']);
        return $arr;
    }

    /**
     * Получить массив детей элемента
     *
     * @param string $path
     * @return array|string
     */
    public static function GetItemChildren($path)
    {
        $mysqli = new \mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $id = self::PathToId($path);
        $sql = "SELECT id,type,caption FROM ref_system_data WHERE parent_id = $id";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        $children = array();
        for ($i = 0; !($result->num_rows === 0) and $arr = $result->fetch_assoc(); $i++)
        {
            $children[$i] = new RSArticle($arr['id'],$arr['caption'], null, $arr['type']);
        }
        $mysqli->close();
        return $children;
    }

    /**
     * Получение схемы оглавления. По факту практически обход в глубину
     *
     * @param array() $scheme
     * @return ReferenceSchemeItem[]
     */
    public static function GetReferenceScheme($scheme)
    {
        if(count($scheme) === 0)
        {
            $mysqli = new \mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
            if($mysqli->connect_errno)
                return "Ошибка: " . $mysqli->error . "\n";
            $type = self::$HierarchyItems[0];
            $sql = "SELECT id,type,caption FROM ref_system_data WHERE type = '$type'";
            if(!$result = $mysqli->query($sql))
                return "Ошибка: " . $mysqli->error . "\n";
            for ($i = 0; !($result->num_rows === 0) and $arr = $result->fetch_assoc(); $i++) {
                $scheme[$i] = new ReferenceSchemeItem(new RSArticle($arr['id'], $arr['caption'], null, $arr['type']),
                    array(), null, $arr['caption']);
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
                $path = $scheme[$i]->Path;
                $children = self::GetItemChildren($path);
                for ($j = 0; $j<count($children); $j++)
                {
                    $scheme[$i]->Children[$j] = new ReferenceSchemeItem($children[$j],array(),$scheme[$i]->FirstLevel,
                                                                        $path . '/' . $children[$j]->Caption);
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
    private static function PathToId($path)
    {
        $mysqli = new \mysqli(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_DATABASE);
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
}