<?php 
$I = new FunctionalTester($scenario);

$I->am('a Larabook user');
$I->wantTo('send a message to another Larabook user');
//couldn't find out why the test throwing the CovnersationNotFoundException so changed a little bit

//setup
$I->haveAnAccount(['username' => 'OtherUser']);

//actions
$I->signIn();
$I->click('Messages');
$I->seeCurrentUrlEquals('/inbox');
$I->fillField('Send to:','OtherUser');
$I->fillField('Message:','some random message');
$I->click('Send');

//expectations
$I->seeCurrentUrlEquals('/inbox');
$I->see('OtherUser');
$I->see('some random message');

$I->click('OtherUser');
$I->seeCurrentUrlEquals('/inbox?u=OtherUser');
$I->see('some random message');
