<?php

namespace ConsoleModule;

/**
 * Description of BasePresenter
 *
 * @author frosty
 */
class FeedPresenter extends \ConsoleModule\BasePresenter {

    
    public function actionImport()
    {
	$feed = $this->db->table("feed")->where("feed IS NOT NULL AND enable = 1")->order("imported ASC")->fetch();
	$feed->update(array("imported" => new \Nette\Database\SqlLiteral("NOW()")));
	
	echo "Fetch feed " . $feed->feed . PHP_EOL;
	$xml = new \SimpleXMLElement($feed->feed, null, TRUE);

	$author = "";
	$blogUrl = (string)$xml->channel->link;
	$blogName = (string)$xml->channel->title;

	$dc = $xml->channel->children('http://purl.org/dc/elements/1.1/');
	if ($dc && !in_array($dc->creator, $restrictAuthorNames)) $author = $dc->creator;

	foreach ($xml->channel->item as $article) {
		$link = (string)$article->link;
	    
		// Check existence of article
		$articleRow = $this->db->table("article")->where("url", $link)->fetch();
		if ($articleRow) {
		    echo "Found exist article #" . $articleRow["article_id"] . PHP_EOL;
		    continue;
		}
		
		$title = (string)$article->title;	
		$date = new \DateTime((string)$article->pubDate);		

		$perex = (string)$article->description;
		$perex = preg_replace('~<a.*?>.*?</a>$~i', '\1', $perex);
		$perex = str_replace('[…]', '', $perex);
		$perex = strip_tags($perex);

		$dc = $article->children('http://purl.org/dc/elements/1.1/');
		if ($dc->creator && !in_array($dc->creator, $restrictAuthorNames)) $author = (string)$dc->creator;
		
		// Fetch author or create it
		if (!empty($author)) {
		    $autorRow = $this->db->table("author")->where("name", $author)->fetch();
		    if (!$autorRow) $autorRow = $this->db->table("author")->insert(array("name" => $author)); 
		} else {
		    $autorRow = $feed;		
		}
		
		$articleRow = $this->db->table("article")->insert(array(
		    "feed_id" => $feed["feed_id"],
		    "author_id" => $autorRow["author_id"],
		    "language_id" => $feed["language_id"],
		    "added" => $date,
		    "title" => $title,
		    "perex" => $perex,
		    "url" => $link,
		));
		
		echo "Inserted new article #" . $articleRow["article_id"] . PHP_EOL;

	}	
    }
    
    
}

