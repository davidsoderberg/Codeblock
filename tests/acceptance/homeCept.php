<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage('/');
$I->see('codeblock');