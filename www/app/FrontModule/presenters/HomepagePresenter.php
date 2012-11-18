<?php

namespace FrontModule;

use Nette\Application\BadRequestException;
use Nette\Database\SqlLiteral;
use Nette\DateTime;
use Nette\Utils\Strings;
use Nette\Application\UI\Form;


/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{

    const LIMIT = 20;
    
    
    public function actionDefault($category_id, $author_id, $seo)
    {
		$query = $this->db->table("article")->where("state", 1)->order("added DESC");

		if (!is_null($category_id)) {
			$query->where("article_category:category.category_id", $category_id);
			$this->template->category = $this->db->table("category")->where("category_id", $category_id)->fetch();
		}

		if (!is_null($author_id)) {
			$query->where("article.author_id", $author_id);
			$this->template->author = $author = $this->db->table("author")->where("author_id", $author_id)->fetch();
			if (\Nette\Utils\Strings::webalize($author->name) != $seo) $this->redirect("Homepage:default", array("author_id" => $author_id, "seo" => \Nette\Utils\Strings::webalize($author->name)));
		}

		if (!empty($this->langs)) {
			$query->where("article.language_id", $this->langs);
		}

		$paginator = $this["paginator"]->getPaginator();
		$paginator->setItemCount($query->count("*"));
		$paginator->setItemsPerPage(self::LIMIT);

		$this->template->articles = $query->limit($paginator->itemsPerPage, $paginator->offset);
    }

	/**
	 * @return Form
	 */
	protected function createComponentFilterForm()
	{
		$form = new Form();

		$langContainer = $form->addContainer("langs");
		$langs = $this->db->table("language")
						->select("language.language_id, COUNT(article:article_id) AS articles")
						->where("article:state", 1)
						->group("article:language_id");
		foreach ($langs as $lang) {
			$langContainer->addCheckbox($lang->language_id)
				->setDefaultValue(in_array($lang->language_id, $this->langs))
				->getLabelPrototype()->dataCount = $lang->articles;
		}

	    $form->addSubmit("send", "Filtrovat");
		$form->onSuccess[] = $this->filterFormFormSubmitted;

		return $form;
	}



	/**
	 * @param Form $form
	 */
	public function filterFormFormSubmitted(Form $form)
	{
		$langs = array();
		foreach ($form["langs"]->values as $lang => $state)
			if ($state) $langs[] = $lang;
		$this->redirect("this", array("langs" => $langs));
	}


	/***/
	public function actionAdd()
	{

	}

	/**
	 * @return Form
	 */
	protected function createComponentArticleForm()
	{
		$form = $this->context->createForm();

		$form->addText("author", "Autor:", null, 100)
			->setRequired("Je nutné zadat autora článku.");

		$items = $this->db->table("language")->fetchPairs("language_id", "name");
		$form->addSelect("language_id", "Jazyk:", $items)
			->setRequired("Je nutné zadat jazyk článku.");

		$form->addText("url", "URL:", null, 255)
			->addRule(Form::URL, "URL adresa není validní.")
			->setRequired("Je nutné zadat URL adresu detailu článku.")
			->setDefaultValue("http://www.");

		$form->addText("title", "Titulek:", null, 120)
			->setRequired("Je nutné zadat titulek článku");

		$form->addTextarea("perex", "Perex:")
			->setRequired("Je nutné zadat perex článku.")
			->addRule(Form::MAX_LENGTH, "Maximální délka perexu je %d.", 300);

	    $form->addSubmit("send", "Přidat článek");
		$form->onSuccess[] = $this->articleFormFormSubmitted;
		$form->onValidate[] = $this->articleFormFormValidate;
		return $form;
	}

	/***/
	public function articleFormFormValidate(Form $form)
	{
		$article = $this->db->table("article")->where("url", $form["url"]->value)->fetch();
		if ($article) {
			switch ($article->state) {
				case 0:
					$form->addError("Článek již byl přidán, pouze čeká na schválení.");
					break;
				case 1:
					$form->addError("Článek již v databázi existuje - viz: " . $this->link("//Detail:", array("article_id" => $article->article_id, "seo" => Strings::webalize($article->title))));
					break;
				case 2:
					$form->addError("Článek již v databázi existuje, avšak byl zakázán.");
					break;
			}
		}
	}


	/**
	 * @param Form $form
	 */
	public function articleFormFormSubmitted(Form $form)
	{
		$values = $form->getValues();

		$this->db->beginTransaction();

		$author = $this->db->table("author")->where("name", $values->author)->fetch();
		if (!$author)
			$author = $this->db->table("author")->insert(array("name" => $values->author));

		$this->db->table("article")->insert(array(
			"author_id" => $author->author_id,
			"language_id" => $values->language_id,
			"url" => $values->url,
			"title" => $values->title,
			"perex" => $values->perex,
			"added" => new SqlLiteral("NOW()")
		));

		$this->db->commit();
		$this->flashMessage("Článek byl přidán do databáze, po jeho schválení bude zveřejněn.");
		$this->redirect("this");
	}





}
