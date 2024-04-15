<?php

namespace src\utils;

require_once 'vendor/autoload.php';

use src\utils\DB;
use src\class\Comment;

class CommentManager
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

	public function listComments()
	{
		$rows = $this->database->select('SELECT * FROM `comment`');

		$comments = [];
		foreach($rows as $row) {
			$n = new Comment();
			$comments[] = $n->setId($row['id'])
			  ->setBody($row['body'])
			  ->setCreatedAt($row['created_at'])
			  ->setNewsId($row['news_id']);
		}

		return $comments;
	}

	public function addCommentForNews($body, $newsId)
	{
		// Prepare the SQL statement with placeholders to prevent SQL injection
        $sql = "INSERT INTO `comment` (`body`, `created_at`, `news_id`) VALUES(:body, :created_at, :news_id)";

        // Prepare the statement
        $stmt = $this->database->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':news_id', $newsId);
        $stmt->bindParam(':created_at', date('Y-m-d'));

        // Execute the statement
        $stmt->execute();

        // Return the ID of the newly inserted comment item
        return $this->database->lastInsertId();
	}

	public function deleteComment($id)
	{
		// Delete the comment item
        $sql = "DELETE FROM `comment` WHERE `id`=:id";
        $stmt = $this->database->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Return the number of affected rows
        return $stmt->rowCount();
	}
}