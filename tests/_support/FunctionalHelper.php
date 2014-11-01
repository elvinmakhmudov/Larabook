<?php
namespace Codeception\Module;

use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Laracasts\TestDummy\Factory as TestDummy;
// here you can define custom actions
// all public methods declared in helper class will be available in $I

class FunctionalHelper extends \Codeception\Module
{
    public function signIn($email = 'elvin@mail.ru', $username = 'elvin', $password = '123')
    {
        $user = $this->haveAnAccount(compact('username', 'email', 'password'));
        $I = $this->getModule('Laravel4');

        $I->amOnPage('/login');
        $I->fillField('email', $email);
        $I->fillField('password', $password);
        $I->click('Sign In');
        $I->amLoggedAs($user);

        return $user;
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
        return $this->have('Larabook\Users\User', $overrides);
    }

    public function sendMessage($username, $message)
    {
        $I = $this->getModule('Laravel4');

        //setup
        $otheruser = $this->haveAnAccount(['username' => $username]);

        //actions

        //workaround, waiting for Laravel module or codeception to catch exceptions that are defined in global.php
        try
        {
            $I->click('Messages');
            $I->click('New Message');
        }
        catch (ConversationNotFoundException $e)
        {
            $I->amOnPage('/inbox/new');
        }
        $I->seeCurrentUrlEquals('/inbox/new');
        $I->fillField('Send to:', $username);
        $I->fillField('Message:', $message);
        $I->click('Send');

        return $otheruser;
    }

    public function signOut()
    {
        $I = $this->getModule('Laravel4');

        $I->click('Log out');
    }
}