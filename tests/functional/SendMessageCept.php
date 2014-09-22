<?php 
$I = new FunctionalTester($scenario);

$I->am('a Larabook user');
$I->wantTo('send a message to another Larabook user');

//setup
$I->haveAnAccount(['username' => 'OtherUser']);

//actions
$I->signIn();
$I->click('Messages');
$I->seeCurrentUrlEquals('/inbox');
$I->fillField('send to','OtherUser');
$I->fillField('Message:','some random message');
$I->click('Send');

//expectations
$I->seeCurrentUrlEquals('/inbox');
$I->see('OtherUser');
$I->click('OtherUser');
$I->see('some random message');