<?php

// using composer autoload feature in order to load classes as they are needed automatically, eliminating the need for manual inclusion of files.
require_once 'vendor/autoload.php';

// Using namespace in order to help organize code and prevent naming conflicts. 
// They also make it easier to understand the relationship between classes.
use src\utils\NewsManager;
use src\utils\CommentManager;

/**
* Functions allow you to break down your code into smaller, manageable units. 
* Each function can perform a specific task or operation, making it easier to understand and maintain the codebase.
* 
*/

function displayNewsWithComments()
{
	// Instantiate news and comment managers
	$newsManager = NewsManager::getInstance();
	$allNews = $newsManager->listNews();

	// Get all comments
	$commentManager = CommentManager::getInstance();
	$allComments = $commentManager->listComments();

	// Display news and comments
	foreach ($allNews as $news) {
		echo ("############ NEWS " . $news->getTitle() . " ############\n");
		echo ($news->getBody() . "\n");

		// Filter relevant comments for this news
		$relevantComments = array_filter($allComments, function ($comment) use ($news) {
			return $comment->getNewsId() == $news->getId();
		});

		// Display relevant comments
		foreach ($relevantComments as $comment) {
			echo ("Comment " . $comment->getId() . " : " . $comment->getBody() . "\n");
		}
	}
}

// Exibir as notícias com comentários
displayNewsWithComments();
