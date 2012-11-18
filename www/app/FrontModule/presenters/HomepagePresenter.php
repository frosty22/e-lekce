<?php

namespace FrontModule;
use Nette\Application\BadRequestException;
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




}
