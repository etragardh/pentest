<?php

class WPFirstCest
{

    private $id;

    public function _before(AcceptanceTester $I)
    {

        $this->id = $I->havePostInDatabase(
            [
                'post_title' => 'SUP IM A POST',
                'post_content' => 'yo yo',
                'post_name' => 'imcoolyo',
                'post_status' => 'publish',
            ]
        );

    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {

        $I->seePostInDatabase(['post_name' => 'imcoolyo']);
        $I->amOnPage('/?p=' . $this->id);
        $I->see('yo yo');
    }
}
