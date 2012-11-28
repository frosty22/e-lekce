<?php

namespace FrontModule;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    
    const FM_SUCCESS = "success";
    const FM_ERROR = "error";

	/**
	 * @persistent
	 */
	public $langs = array();

    /**
     * @var \Nette\Database\Connection
     */
    protected $db;
    
    
    public function startup()
    {
	parent::startup();
	$this->db = $this->context->nette->database->default;
    }
    
    public function beforeRender() {
		parent::beforeRender();
	
		$query = $this->db->table("article")
					->select("article_category:category.category_id, article_category:category.name, COUNT(article.article_id) AS articles")
					->where("article.state", 1)
					->group("article_category:category.category_id")
					->order("article_category:category.weight");
		if (!empty($this->langs)) $query->where("article.language_id", $this->langs);

		$this->template->categories = $query;
		$this->template->jokeCount = $this->db->table("joke")->count("*");
    }
    
    protected function createComponentPaginator($name)
    {
	return new \VisualPaginator($this, $name);
    }
    
    
    
}
