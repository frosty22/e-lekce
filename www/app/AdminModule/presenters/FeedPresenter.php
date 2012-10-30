<?php

namespace AdminModule;

/**
 * Description of FeedPresenter
 *
 * @author frosty
 */
class FeedPresenter extends BasePresenter {

    
     private $feed;
    
    
    public function  actionEdit($id)
    {
	if (empty($id))
	    throw new \Nette\Application\BadRequestException("Feed id cannot be set.");
	
	$this->feed = $this->db->table("feed")->where("feed_id", $id)->fetch();
	if (!$this->feed)
 	    throw new \Nette\Application\BadRequestException("Feed with id #$id not found.");
	
	$this["feedForm"]->setDefaults($this->feed);
	
	$this->setView("default");
    }
    
    
    public function renderDefault()
    {
	$this->template->feeds = $this->db->table("feed");
    }
    
    
    protected function createComponentFeedForm($name)
    {
	$form = $this->context->createForm();

	
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
	
	$items = $this->db->table("author")->fetchPairs("author_id", "name");
	$form->addSelect("author_id", "Výchozí autor:", $items)
		->setPrompt("vybrat");
	
	$form->addCheckbox("enable", "Povoleno");
	
	$form->addSubmit("save", "Uložit");
	
	$form->onSuccess[] = callback($this, "processFeedForm");
	$form->onValidate[] = callback($this, "validateFeedForm");
	return $form;
    }
    
    public function validateFeedForm(\Nette\Forms\Form $form)
    {
 	$feed = $this->db->table("feed")->where("feed_id <> ?", $this->feed->feed_id)->where("feed", $form["feed"]->value)->fetch();
	if ($feed) $form->addError("Tento feed již byl dříve přidán.", self::FM_ERROR);	
    }    
    
    public function processFeedForm(\Nette\Forms\Form $form)
    {
	$update = array(
	    "name" => $form["name"]->value,
	    "url" => $form["url"]->value,
	    "feed" => $form["feed"]->value,
	    "author_id" => $form["author_id"]->value,
	    "enable" => $form["enable"]->value ? 1 : 0,
	    "language_id" => $form["language_id"]->value
	);
	
	if (isset($this->feed))
	    $this->feed->update($update);
	else    
	    $this->db->table("feed")->insert($update);
	
	$this->flashMessage("Feed byl uložen.", self::FM_SUCCESS);
	$this->redirect("default");
    }
    
    
    public function handleDelete($id)
    {
	$this->feed = $this->db->table("feed")->where("feed_id", $id)->fetch();
	if (!$this->feed)
 	    throw new \Nette\Application\BadRequestException("Feed with id #$id not found.");
	
	$this->feed->delete();
    }   
    
    
}



