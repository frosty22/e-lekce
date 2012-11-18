<?php

namespace AdminModule;

/**
 * Description of ArticlePresenter
 *
 * @author frosty
 */
class ArticlePresenter extends BasePresenter {

    
     private $article;
    
    
    public function actionEdit($id)
    {
	if (empty($id))
	    throw new \Nette\Application\BadRequestException("Article id cannot be set.");
	
	$this->article = $this->db->table("article")->where("article_id", $id)->fetch();
	if (!$this->article)
 	    throw new \Nette\Application\BadRequestException("Article with id #$id not found.");
	
	$this["articleForm"]->setDefaults($this->article);
	
	foreach ($this->article->related("article_category")->select("category_id") as $row) {
	    $this["articleForm"]->setDefaults(array("category" => array($row["category_id"] => true)));
	}
	
	$this->setView("add");
    }
    
    
    public function renderDefault($state = 0)
    {
	$query = $this->db->table("article")->order("state ASC, added DESC");
	
	if (!is_null($state)) $query->where("state", $state);
	
	$this->template->articles = $query;
    }
    
    
    protected function createComponentArticleForm($name)
    {
	$form = $this->context->createForm();

	$form->addGroup("Informace o článku");
	
	$items = $this->db->table("feed")->fetchPairs("feed_id", "name");
	$form->addSelect("feed_id", "Feed:", $items)
		->setPrompt("vybrat");
	
	$images = array();
	foreach (\Nette\Utils\Finder::findFiles("*.png")->in(WWW_DIR . "/upload") as $file) {
	    $images[$file->getFilename()] = $file->getFilename();
	}
	$form->addSelect("image", "Obrázek:", $images)->setPrompt("Vybrat");
	
	$form->addText("title", "Titulek:")
		->addRule(\Nette\Forms\Form::MAX_LENGTH, "Maximální délka titulku je %s.", 255)
		->setRequired("Je nutné zadat titulek článku.");
	
	$form->addTextarea("perex", "Perex:")
		->addRule(\Nette\Forms\Form::MAX_LENGTH, "Maximální délka perexu je %s.", 1000)
		->setRequired("Je nutné zadat perex.");
	
	$form->addText("url", "URL:")
		->addRule(\Nette\Forms\Form::MAX_LENGTH, "Maximální délka URL je %s.", 255)
		->addRule(\Nette\Forms\Form::URL, "Neplatná URL adresa.")
		->setRequired("Je nutné zadat URL webu.");	
	
	$items = $this->db->table("author")->fetchPairs("author_id", "name");
	$form->addSelect("author_id", "Autor:", $items)
		->setPrompt("vybrat");
	
	$items = $this->db->table("language")->fetchPairs("language_id", "name");
	$form->addSelect("language_id", "Jazyk:", $items)
		->setRequired("Je nutné vybrat jazyk.");
	
	$form->addCheckbox("state", "Schválit článek");
	
	$form->addGroup("Kategorie článku");
	
	$items = $this->db->table("category")->order("name");
	$categoryCont = $form->addContainer("category");
	foreach ($items as $item) 
	    $categoryCont->addCheckbox($item["category_id"], $item["name"]);
	
	$form->addSubmit("save", "Uložit");
	
	$form->onSuccess[] = callback($this, "processArticleForm");
	
	return $form;
    }
    
    
    public function processArticleForm(\Nette\Forms\Form $form)
    {
	$update = array(
	    "feed_id" => $form["feed_id"]->value,
	    "author_id" => $form["author_id"]->value,
	    "language_id" => $form["language_id"]->value,
	    "image" => $form["image"]->value,
	    "title" => $form["title"]->value,
	    "perex" => $form["perex"]->value,
	    "url" => $form["url"]->value,	    
	    "state" => $form["state"]->value ? 1 : 2,
	);
	
	if (isset($this->article)) {
	    $this->db->table("article_category")->where("article_id", $this->article["article_id"])->delete();
	    $this->article->update($update);
	} else {   
	    $update["added"] = new \Nette\Database\SqlLiteral("NOW()");
	    $this->article = $this->db->table("article")->insert($update);
	}
	
	foreach ($form["category"]->getControls() as $control) {
	    if ($control->value) {
		$this->db->table("article_category")->insert(
			array("article_id" => $this->article["article_id"],
			      "category_id" => $control->name)
		);
	    }
	}
	
	$this->flashMessage("Článek byl uložen.", self::FM_SUCCESS);
	$this->redirect("default");
    }
    
    
    public function handleDelete($id)
    {
	$this->article = $this->db->table("article")->where("article_id", $id)->fetch();
	if (!$this->article)
 	    throw new \Nette\Application\BadRequestException("Article with id #$id not found.");
	
	$this->article->update(array("state" => 2));
	$this->redirect("default");
    }   
     
    
    public function handleAllow($id)
    {
	$this->article = $this->db->table("article")->where("article_id", $id)->fetch();
	if (!$this->article)
 	    throw new \Nette\Application\BadRequestException("Article with id #$id not found.");
	
	$this->article->update(array("state" => 1));
	$this->redirect("default");
    }      
    
}



