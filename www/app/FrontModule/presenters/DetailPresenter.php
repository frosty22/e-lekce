<?php

namespace FrontModule;
use Nette\Utils\Strings;
use Nette\Http\Response;


/**
 * Created by JetBrains PhpStorm.
 * User: frosty22
 * Date: 11.11.12
 * Time: 17:28
 */
class DetailPresenter extends BasePresenter
{

	public function actionDefault($article_id, $seo)
	{
		$article = $this->db->table("article")->where("article_id", $article_id)->fetch();
		if (!$article)
			throw new BadRequestException("Article with id $article_id not found.");

		if ($seo != Strings::webalize($article->title)) {
			$this->redirect(Response::S301_MOVED_PERMANENTLY, "this", array("seo" => Strings::webalize($article->title)));
		}

		$this->template->article = $article;
	}

}
