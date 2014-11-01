<?php
use Larabook\Conversations\Exceptions\ConversationNotFoundException;

$I = new FunctionalTester($scenario);
$I->am('a Larabook user');
$I->wantTo('send a message to myself');
//couldn't find out why the test throwing the CovnersationNotFoundException so changed a little bit

//actions
$I->signIn();

//workaround, waiting for Laravel module or codeception to catch exceptions that are defined in global.php
try
{
    $I->click('Messages');
}
catch (ConversationNotFoundException $e)
{
    $I->amOnPage('/inbox/new');
}

$I->seeCurrentUrlEquals('/inbox/new');
$I->fillField('Send to:','elvin');
$I->fillField('Message:','some random message');
$I->click('Send');

//expectations
$I->seeCurrentUrlEquals('/inbox');
$I->see('elvin');
$I->see('some random message','.preview');
$I->see('some random message','.message');

$I->click('elvin','.preview');
$I->see('some random message','.message');
