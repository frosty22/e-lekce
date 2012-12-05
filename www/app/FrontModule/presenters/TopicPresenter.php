<?php

namespace FrontModule;
use Nette\Application\BadRequestException;
use Nette\Utils\Strings;

/**
 * Created by JetBrains PhpStorm.
 * User: frosty22
 * Date: 02.12.12
 * Time: 2:12
 */
class TopicPresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->topics = $this->db->table("topic")
			->select("topic.topic_id, topic.title, topic.perex, COUNT(topic_article:article_id) AS articles")
			->group("topic_article:topic_id")
			->order("topic.title");
	}

	/***/
	public function actionDetail($topic_id, $seo)
	{
		$topic = $this->db->table("topic")->where("topic_id", $topic_id)->fetch();
		if (!$topic)
			throw new BadRequestException("Topic $topic_id not found.");

		if ($seo != Strings::webalize($topic->title))
			$this->redirect(\Nette\Http\IResponse::S301_MOVED_PERMANENTLY, "this", array("seo" => Strings::webalize($topic->title)));

		$this->template->topic = $topic;
	}


}
