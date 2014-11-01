<?php

$I = new FunctionalTester($scenario);
$I->am('a Larabook user');
$I->wantTo('delete existing conversation');

$user = $I->signIn();
$otheruser = $I->sendMessage('AnotherUser', 'other random message');
$otheruser = $I->sendMessage('other', 'some random message');

$I->click('Messages');

//TODO::test is not working properly, but function is right
$I->click('Delete conversation');

$I->seeCurrentUrlEquals('/inbox');
$I->dontSee('another random message');
$I->see('some random message');
