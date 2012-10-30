<?php

namespace AdminModule;



final class AuthorPresenter extends BasePresenter
{
    
    private $author;
    
    
    public function actionEdit($id)
    {
	if (empty($id))
	    throw new \Nette\Application\BadRequestException("Author id cannot be set.");
	
	$this->author = $this->db->table("author")->where("author_id", $id)->fetch();
	if (!$this->author)
 	    throw new \Nette\Application\BadRequestException("Author with id #$id not found.");
	
	$this["authorForm"]->setDefaults($this->author);
	
	$this->setView("default");
    }
    
    
    public function renderDefault()
    {
	$this->template->authors = $this->db->table("author");
    }
    
    
    protected function createComponentAuthorForm($name)
    {
	$form = $this->context->createForm();
	
	$form->addText("name", "Jméno:")->setRequired("Je nutné zadat jméno.");
	
	$form->addSubmit("save", "Uložit");
	
	$form->onSuccess[] = callback($this, "processAuthorForm");
	$form->onValidate[] = callback($this, "validateAuthorForm");
	return $form;
    }
    
    public function validateAuthorForm(\Nette\Forms\Form $form)
    {
 	$feed = $this->db->table("author")->where("author_id <> ?", $this->author->author_id)->where("name", $form["name"]->value)->fetch();
	if ($feed) $form->addError("Tento autor již byl dříve přidán.", self::FM_ERROR);	
    }    
    
    public function processAuthorForm(\Nette\Forms\Form $form)
    {
	$update = array(
	    "name" => $form["name"]->value,
	);
	
	if (isset($this->author))
	    $this->author->update($update);
	else    
	    $this->db->table("author")->insert($update);
	
	$this->flashMessage("Autor byl uložen.", self::FM_SUCCESS);
	$this->redirect("default");
    }
    
    
    public function handleDelete($id)
    {
	$this->author = $this->db->table("author")->where("author_id", $id)->fetch();
	if (!$this->author)
 	    throw new \Nette\Application\BadRequestException("Autor with id #$id not found.");
	
	$this->author->delete();
    }
    
    
}