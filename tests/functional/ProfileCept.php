<?php
$I = new FunctionalTester($scenario);
$I->am('a Larabook memeber');
$I->wantTo('View my profile');

$I->signIn();
$I->postAStatus('My new status');

$I->click('Your Profile');
$I->seeCurrentUrlEquals('/@FooBar');

$I->see('My new status');
