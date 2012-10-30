<?php

namespace ConsoleModule;

/**
 * Description of BasePresenter
 *
 * @author frosty
 */
class BasePresenter extends \Nette\Application\UI\Presenter {

    /**
     * @var \Nette\Database\Connection
     */
    protected $db;
    
    
    public function startup()
    {
	parent::startup();
	$this->db = $this->context->nette->database->default;
    }    
    
    protected function beforeRender() {
	parent::beforeRender();
	$this->terminate();
    }
    
    
    
}

