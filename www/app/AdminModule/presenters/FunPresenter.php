<?php

namespace AdminModule;
use Nette\Application\UI\Form;
use Nette\Database\SqlLiteral;

/**
 * Created by JetBrains PhpStorm.
 * User: frosty22
 * Date: 28.11.12
 * Time: 22:35
 */
class FunPresenter extends BasePresenter
{
	const PER_PAGE = 50;

	/***/
	public function renderDefault()
	{
		$paginator = $this["paginator"]->getPaginator();
		$paginator->itemsPerPage = self::PER_PAGE;
		$paginator->itemCount = $this->db->table("joke")->count("*");

		$this->template->jokes = $this->db->table("joke")->order("added DESC")
			->limit($paginator->itemsPerPage, $paginator->offset);
	}

	/**
	 * @return Form
	 */
	protected function createComponentAddJoke()
	{
		$form = $this->context->createForm();

		$form->addTextarea("text", "Vtip")->setRequired("Je nutné zadat vtip.");
	
	    $form->addSubmit("send", "Přidat");
		$form->onSuccess[] = $this->addJokeFormSubmitted;
	
		return $form;
	}

	/***/
	public function handleDelete($id)
	{
		$this->db->table("joke")->where("joke_id", $id)->delete();
		$this->flashMessage("Vtip byl odstraněn.");
		$this->redirect("this");
	}
	
	/**
	 * @param Form $form
	 */
	public function addJokeFormSubmitted(Form $form)
	{
		$values = $form->getValues();
		$this->db->table("joke")->insert(array(
			"text" => $values->text,
			"added" => new SqlLiteral("NOW()")
		));
		$this->flashMessage("Vtip byl úspěšně přidán do databáze.");
		$this->redirect("this");
	}




}
