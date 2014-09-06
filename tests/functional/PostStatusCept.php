<?php 
$I = new FunctionalTester($scenario);
$I->am('a Larabook memeber');
$I->wantTo('Post Statuses to my profile');

$I->signIn();
$I->amOnPage('/statuses');
$I->postAStatus('My first post!');

$I->seeCurrentUrlEquals('/statuses');
$I->see('My first post!');
