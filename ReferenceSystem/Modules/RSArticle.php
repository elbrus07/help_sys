<?php


namespace ReferenceSystem\Modules;

use mysqli;
use ReferenceSystem\Modules\Database\Settings as DBSettings;

class RSArticle
{
    private $id;
    private $path;

    /**
     * RSArticle конструткор
     * @param string|int $identifier id статьи || путь к статье || id HTML элемента
     * @param int $mode RSArticleModes::
     * @param string $uniqueClass
     * @param string $pathname
     */
    public function __construct($identifier, $mode, $uniqueClass = '',$pathname = '')
    {
        $this->id = -1;
        $this->path = '';
        switch ($mode)
        {
            case RSArticleModes::ID:
                $this->id = intval($identifier);
                break;
            case RSArticleModes::PATH:
                $this->id = ReferenceSystem::PathToId($identifier);
                $this->path = $identifier;
                break;
            case RSArticleModes::HTML_CHILD_DATA:
                $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
                if($mysqli->connect_errno)
                    die("Ошибка: " . $mysqli->error . "\n");
                $itemId = $mysqli->real_escape_string($identifier);
                $sql = "SELECT data_id FROM ref_system_html_owners WHERE element_id = '$itemId' AND uniqueClass='$uniqueClass'" .
                    " AND pathname = '$pathname'";
                if(!$result = $mysqli->query($sql))
                    die("Ошибка: " . $mysqli->error . "\n");

                if($result->num_rows !== 0)
                    $this->id = $result->fetch_assoc()['data_id'];
                break;
        }
    }

    /**
     * @return int|string
     */
    public function getPath()
    {
        if($this->path == '')
            $this->path = ReferenceSystem::IdToPath($this->id);
        return $this->path;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Добавить статью в БД
     *
     * @param string $path путь в формате "Chapter/Section/Subsection/Subsubsection/.../(n*Sub)section"
     *              причем (n*Sub)section будет заголовком будущей статьи
     * @param string $content содержимаое статьи (скажем, html код). Идею с содержимым мб надо доработать
     * @return int|string
     */
    public static function create($path, $content)
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if ($mysqli->connect_errno)
            return false;
        $path = $mysqli->real_escape_string($path);
        $content = $mysqli->real_escape_string($content);

        //Разбиваем путь и получаем необходимые данные
        $explodedPath = explode('/',$path);

        //Определяем иерархический тип добавляемого элемента
        $type = ReferenceSystem::$HierarchyItems[count($explodedPath)-1];

        //Определяем его имя
        $caption = $explodedPath[count($explodedPath)-1];

        //Получаем id родителя добавляемого элемента
        if(count($explodedPath) > 1)
        {
            //Отгрызваем последний элемент пути, ибо он не нужен
            $path = mb_substr($path,0,mb_strripos($path,'/'));
            $parent_id = ReferenceSystem::PathToId($path);
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
        $last_id = $mysqli->insert_id;
        $mysqli->close();
        return $last_id;
    }

    /**
     * Редактирование справки
     *
     * @param string $newCaption
     * @param string $newContent
     * @return bool|string
     */
    public function edit($newCaption, $newContent)
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        $id = $this->id;
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $sql = "UPDATE ref_system_data SET caption = '$newCaption', content='$newContent' WHERE id=$id";
        if (!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        $mysqli->close();
        return true;
    }

    /**
     * Удалить статью справки
     *
     * @return bool|string
     */
    public function remove()
    {
        $children = $this->getChildren();
        for ($i = 0; $i < count($children); $i++)
            $children[$i]->remove();

        $id = $this->id;
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $sql = "DELETE FROM ref_system_data WHERE id=$id";
        if (!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        $mysqli->close();
        return true;
    }

    /**
     * Получить содержимое статьи и ее заголовок
     *
     * @return string[]|string
     */
    public function getArticle()
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $id = $mysqli->real_escape_string($this->id);
        $sql = "SELECT caption, content FROM ref_system_data WHERE id = $id";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";

        if($result->num_rows === 0)
            return ['Id' => $this->id,'Caption' => '', 'Content' => '', 'Path' => ''];
        $arr = $result->fetch_assoc();
        $mysqli->close();
        return ['Id' => $this->id,'Caption' => $arr['caption'], 'Content' => $arr['content'], 'Path' => $this->getPath()];
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $id = $mysqli->real_escape_string($this->id);
        $sql = "SELECT caption FROM ref_system_data WHERE id = $id";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";

        if($result->num_rows === 0)
            return "Ошибка: Данного элемента не существует \r\n";
        $arr = $result->fetch_assoc();
        $mysqli->close();
        return $arr['caption'];
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $id = $mysqli->real_escape_string($this->id);
        $sql = "SELECT content FROM ref_system_data WHERE id = $id";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";

        if($result->num_rows === 0)
            return "Ошибка: Данного элемента не существует \r\n";
        $arr = $result->fetch_assoc();
        $mysqli->close();
        return $arr['content'];
    }

    /**
     * Получить массив дочерних статей
     *
     * @return RSArticle[]|string
     */
    public function getChildren()
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $id = $this->id;
        $sql = "SELECT id,type,caption FROM ref_system_data WHERE parent_id = $id";
        if(!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        $children = array();
        for ($i = 0; !($result->num_rows === 0) and $arr = $result->fetch_assoc(); $i++)
        {
            $children[$i] = new RSArticle($arr['id'], RSArticleModes::ID);
        }
        $mysqli->close();
        return $children;
    }

    /**
     * Связать id HTML элемента со статьей
     *
     * @param string $itemId id html элемента, к которому надо добавить статью
     * @param string $uniqueClass уникальный класс элемента (если более одного элемента с одинаковыми id на странице)
     * @param string $pathname путь к файлу, в котором вызывается справка ($(location).attr('pathname'))
     * @return bool|string
     */
    public function addHTMLChild($itemId, $uniqueClass, $pathname)
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $itemId = $mysqli->real_escape_string($itemId);
        $uniqueClass = $mysqli->real_escape_string($uniqueClass);
        $pathname = $mysqli->real_escape_string($pathname);

        $parentId = $this->id;
        $sql = "INSERT INTO ref_system_html_owners(element_id, uniqueClass, pathname, data_id) " .
            "VALUES('$itemId', '$uniqueClass', '$pathname', $parentId)";
        if (!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        $mysqli->close();
        return true;
    }

    /**
     * Удалить связь (id HTML элменента <-> статья)
     *
     * @param string $itemId
     * @param string $uniqueClass
     * @param string $pathname
     * @return bool|string
     */
    public function removeHTMLChild($itemId, $uniqueClass, $pathname)
    {
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $itemId = $mysqli->real_escape_string($itemId);
        $parentId = $this->id;
        $sql = "DELETE FROM ref_system_html_owners WHERE element_id = '$itemId' AND uniqueClass = '$uniqueClass' " .
            "AND pathname = '$pathname' AND data_id= '$parentId'";
        if (!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        $mysqli->close();
        return true;
    }

    /**
     * Получить id HTML элементов, связанных со статьей
     *
     * @return array|string
     */
    public function getHTMLChildren()
    {
        $id = $this->id;
        $mysqli = new mysqli(DBSettings::DB_HOST, DBSettings::DB_LOGIN, DBSettings::DB_PASSWORD, DBSettings::DB_DATABASE);
        if($mysqli->connect_errno)
            return "Ошибка: " . $mysqli->error . "\n";
        $sql = "SELECT element_id, uniqueClass, pathname FROM ref_system_html_owners WHERE data_id=$id";
        if (!$result = $mysqli->query($sql))
            return "Ошибка: " . $mysqli->error . "\n";
        $items = array();
        for ($i = 0; $item = $result->fetch_assoc(); $i++)
        {
            $items[$i]['element_id'] = $item['element_id'];
            $items[$i]['uniqueClass'] = $item['uniqueClass'];
            $items[$i]['pathname'] = $item['pathname'];
        }
        $mysqli->close();
        return $items;
    }


    public function toJSON()
    {
        return json_encode($this->getArticle());
    }

}