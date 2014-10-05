<?php
namespace Codeception\Module;

use Laracasts\TestDummy\Factory as TestDummy;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class IntegrationHelper extends \Codeception\Module
{
    public $user;

    public function signIn()
    {
        $email = 'elvin@mail.ru';
        $username = 'FooBar';
        $password = '123';

        $this->haveAnAccount(compact('username', 'email', 'password'));
        $I = $this->getModule('Laravel4');

        $I->amOnPage('/login');
        $I->fillField('email', $email);
        $I->fillField('password', $password);
        $I->click('Sign In');

        return $this->user;
    }

    public function postAStatus($body)
    {
        $I = $this->getModule('Laravel4');
        $I->fillField('body', $body);
        $I->click('Post Status');
    }

    public function have($model, $overrides)
    {
        return TestDummy::create($model, $overrides);
    }

    public function haveAnAccount($overrides = [])
    {
        $this->user = $this->have('Larabook\Users\User', $overrides);

        return $this->user;
    }
}