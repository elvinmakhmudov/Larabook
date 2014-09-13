<?php
$I = new FunctionalTester($scenario);
$I->am('a Larabook member');
$I->wantTo('log out of my Larabook account');

$I->signIn();

$I->seeInCurrentUrl('/statuses');
$I->see('Welcome back!');
$I->assertTrue(Auth::check());

$I->click('Log out');
$I->seeCurrentUrlEquals('');
$I->see('Register');
$I->see('Log in');