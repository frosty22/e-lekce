<?php

namespace FrontModule;
use Nette\Application\BadRequestException;

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
	
	$paginator = $this["paginator"]->getPaginator();
	$paginator->setItemCount($query->count("*"));
	$paginator->setItemsPerPage(self::LIMIT);
	
	$this->template->articles = $query->limit($paginator->itemsPerPage, $paginator->offset);
    }






}
