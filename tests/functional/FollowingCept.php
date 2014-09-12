<?php 
$I = new FunctionalTester($scenario);

$I->am('a Larabook user');
$I->wantTo('follow other Larabook users');

//setup
$I->haveAnAccount(['username' => 'OtherUser']);
$I->signIn();

//actions
$I->click('Browse All');
$I->click('OtherUser');
$I->seeCurrentUrlEquals('/@OtherUser');

//Follow a user
$I->click('Follow OtherUser');
$I->seeCurrentUrlEquals('/@OtherUser');
$I->see('Unfollow OtherUser');

//Unfollow a user
$I->click('Unfollow OtherUser');
$I->seeCurrentUrlEquals('/@OtherUser');
$I->see('Follow OtherUser');

