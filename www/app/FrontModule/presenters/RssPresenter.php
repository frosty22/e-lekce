<?php

namespace FrontModule;



class RssPresenter extends BasePresenter
{
    
    
    public function actionDefault()
    {
		$this->template->feeds = $this->db->table("feed")->order("feed_id ASC");
    }
    
    
    protected function createComponentFeedForm($name)
    {
	$form = $this->context->createForm();
	
	$form->addGroup("Přidat RSS kanál k odběru");
	
	$form->addText("name", "Název webu:")
		->addRule(\Nette\Forms\Form::MAX_LENGTH, "Maximální délka názvu je %s.", 50)
		->setRequired("Je nutné zadat název webu.");
	
	$form->addText("url", "URL webu:")
		->addRule(\Nette\Forms\Form::MAX_LENGTH, "Maximální délka URL webu je %s.", 50)
		->addRule(\Nette\Forms\Form::URL, "Neplatná URL adresa webu.")
		->setRequired("Je nutné zadat název webu.");
	
	$form->addText("feed", "URL feedu:")
		->addCondition(\Nette\Forms\Form::FILLED)
		    ->addRule(\Nette\Forms\Form::MAX_LENGTH, "Maximální délka URL feedu je %s.", 255)
		    ->addRule(\Nette\Forms\Form::URL, "Neplatná URL adresa feedu.");	
	
	$items = $this->db->table("language")->fetchPairs("language_id", "name");
	$form->addSelect("language_id", "Jazyk:", $items)
		->setRequired("Je nutné vybrat jazyk.");	
	
	$form->addSubmit("save", "Přidat feed");
	$form->onSuccess[] = callback($this, "processFeedForm");
	$form->onValidate[] = callback($this, "validateFeedForm");
	return $form;
    }
    
    public function validateFeedForm(\Nette\Forms\Form $form)
    {
 	$feed = $this->db->table("feed")->where("feed", $form["feed"]->value)->fetch();
	if ($feed) $form->addError("Tento feed již byl dříve přidán.", self::FM_ERROR);	
    }	
    
    public function processFeedForm(\Nette\Forms\Form $form)
    {
	$update = array(
	    "name" => $form["name"]->value,
	    "url" => $form["url"]->value,
	    "feed" => $form["feed"]->value,
	    "language_id" => $form["language_id"]->value,
	    "enable" => 0,
	);
	
	$this->db->table("feed")->insert($update);
	
	$this->flashMessage("Feed byl uložen, děkujeme.", self::FM_SUCCESS);
	$this->redirect("this");	
    }

	/***/
	public function actionExport()
    {
		$query = $this->db->table("article")->where("state", 1)->order("added DESC")->limit(50);

		if (!empty($this->langs)) $query->where("language_id", $this->langs);

		$this->template->articles = $query;
	}

}
