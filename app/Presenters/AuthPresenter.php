<?php

declare(strict_types=1);

namespace App\Presenters;


use Nette\Application\UI\Form;
use Nette;

class AuthPresenter extends Nette\Application\UI\Presenter
{
    private $database;
    private $passwords;

    public function __construct(Nette\Database\Explorer $database, Nette\Security\Passwords $passwords)
    {
        $this->database = $database;
        $this->passwords = $passwords;
    }

    protected function createComponentRegistrationForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Username:')->setRequired('Please enter your username.');
        $form->addText('email', 'Email:')->setRequired('Please enter your password.');
        $form->addPassword('password', 'Password:')->setRequired();
        $form->addSubmit('send', 'Register');
        $form->onSuccess[] = [$this, 'registerSucceeded'];
        return $form;
    }

    public function registerSucceeded(Form $form, $data): void
    {
        $this->database->query('INSERT INTO users ?', [
            'username' => $data->username,
            'email' => $data->email,
            'password' => $this->passwords->hash($data->password),
            'role' => 'user'
        ]);

//        $this->database->table('users')->insert([
//            'username' => $data->username,
//            'email' => $data->email,
//            'password' => $this->passwords->hash($data->password),
//            'role' => 'user'
//        ]);

        $this->flashMessage('You have successfully registered.');
        $this->redirect('Homepage:');
    }

    protected function createComponentLoginForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Username:')->setRequired('Please enter your username.');
        $form->addPassword('password', 'Password:')->setRequired('Please enter your password.');
        $form->addSubmit('send', 'Log In');
        $form->onSuccess[] = [$this, 'loginSucceeded'];
        return $form;
    }

    public function loginSucceeded(Form $form, $data): void
    {
        try {
            $this->getUser()->login($data->username, $data->password);
            $this->redirect('Homepage:');

        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Bad username or password.');
        }
    }

    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('Successfully signed out.');
        $this->redirect('Homepage:');
    }
}