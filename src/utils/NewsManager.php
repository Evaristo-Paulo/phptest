<?php

namespace src\utils;

require_once 'vendor/autoload.php';

use src\utils\DB;
use src\utils\CommentManager;
use src\class\News;

class NewsManager
{
    private static $instance = null;
    private  $database;

    public function __construct()
    {
        $this->database = DB::getInstance();
    }

    public static function getInstance()
    {
        if (null === self::$instance) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }

    /**
     * list all news
     */
    public function listNews()
    {
        $rows = $this->database->select('SELECT * FROM `news`');

        $news = [];
        foreach ($rows as $row) {
            $n = new News();
            $news[] = $n->setId($row['id'])
                ->setTitle($row['title'])
                ->setBody($row['body'])
                ->setCreatedAt($row['created_at']);
        }

        return $news;
    }

    /**
     * add a record in news table
     */
    public function addNews($title, $body)
    {
        // Prepare the SQL statement with placeholders to prevent SQL injection
        $sql = "INSERT INTO `news` (`title`, `body`, `created_at`) VALUES(:title, :body, :created_at)";

        // Prepare the statement
        $stmt = $this->database->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':created_at', date('Y-m-d'));

        // Execute the statement
        $stmt->execute();

        // Return the ID of the newly inserted news item
        return $this->database->lastInsertId();
    }

    /**
     * deletes a news, and also linked comments
     */
    public function deleteNews($id)
    {
        $comments = CommentManager::getInstance()->listComments();
        $idsToDelete = [];

        foreach ($comments as $comment) {
            if ($comment->getNewsId() == $id) {
                $idsToDelete[] = $comment->getId();
            }
        }

        foreach ($idsToDelete as $id) {
            CommentManager::getInstance()->deleteComment($id);
        }

        // Delete the news item
        $sql = "DELETE FROM `news` WHERE `id`=:id";
        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Return the number of affected rows
        return $stmt->rowCount(); 
    }
}
