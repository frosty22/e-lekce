<?php

namespace AdminModule;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    
    const FM_SUCCESS = "success";
    const FM_ERROR = "error";

    /**
     * @var \Nette\Database\Connection
     */
    protected $db;
    
    
    public function startup()
    {
	parent::startup();
	$this->db = $this->context->nette->database->default;
	
	if (!$this->user->isLoggedIn() && $this->name !== "Admin:Sign")
	    $this->redirect("Sign:in");
    }
    
    
}
