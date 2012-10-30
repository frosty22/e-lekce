<?php

namespace AdminModule;



/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{


	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = $this->context->createForm();
		
		$form->addText('username', 'Login:')
			->setRequired('Prosím zadejte login.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Prosím zadejte heslo.');

		$form->addCheckbox('remember', 'Zapamatovat si přihlášení');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = callback($this, "processSignInForm");
		
		return $form;
	}



	public function processSignInForm(\Nette\Forms\Form $form)
	{
		$values = $form->getValues();

		if ($values->remember) {
			$this->getUser()->setExpiration('+ 14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('+ 20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
		} catch (\Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
			return;
		}

		$this->redirect('Homepage:');
	}



	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('Byly jste úspěšně přihlášeni.');
		$this->redirect('in');
	}

}
