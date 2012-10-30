<?php

namespace AdminModule;



final class CategoryPresenter extends BasePresenter
{
    
    private $category;
    
    
    public function  actionEdit($id)
    {
	if (empty($id))
	    throw new \Nette\Application\BadRequestException("Category id cannot be set.");
	
	$this->category = $this->db->table("category")->where("category_id", $id)->fetch();
	if (!$this->category)
 	    throw new \Nette\Application\BadRequestException("Category with id #$id not found.");
	
	$this["categoryForm"]->setDefaults($this->category);
	
	$this->setView("default");
    }
    
    
    public function renderDefault()
    {
	$this->template->categories = $this->db->table("category");
    }
    
    
    protected function createComponentCategoryForm($name)
    {
	$form = $this->context->createForm();
	
	$form->addText("name", "Kategorie:")->setRequired("Je nutné zadat kategorii.");
	
	$form->addText("weight", "Váha")
		->addRule(\Nette\Forms\Form::INTEGER, "Váha musí být celé číslo.")
		->addRule(\Nette\Forms\Form::FILLED, "Je nutné zadat váhu.")
		->setDefaultValue(0);
	
	$form->addSubmit("save", "Uložit");
	
	$form->onSuccess[] = callback($this, "processCategoryForm");
	
	return $form;
    }
    
    
    public function processCategoryForm(\Nette\Forms\Form $form)
    {
	$update = array(
	    "name" => $form["name"]->value,
	    "weight" => $form["weight"]->value
	);
	
	if (isset($this->category))
	    $this->category->update($update);
	else    
	    $this->db->table("category")->insert($update);
	
	$this->flashMessage("Kategorie byla uložena.", self::FM_SUCCESS);
	$this->redirect("default");
    }
    
    
    public function handleDelete($id)
    {
	$this->category = $this->db->table("category")->where("category_id", $id)->fetch();
	if (!$this->category)
 	    throw new \Nette\Application\BadRequestException("Category with id #$id not found.");
	
	$this->category->delete();
    }
    
    
}