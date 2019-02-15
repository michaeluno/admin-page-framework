<?php

/**
 * @group   factory
 * @group   utility
 * @group   core
 */
class AdminPageFramework_Utility_URL_Test extends \APF_UnitTestCase {

    /**
     * Sores the utility object.
     */
    public $oUtil;

    public function setUp() {
        parent::setUp();

        $this->oUtil = new AdminPageFramework_WPUtility;
    }

    public function tearDown() {
        parent::tearDown();
    }


    public function test_getQueryValueInURLByKey() {

        $this->assertEquals(
            'bbb',
            $this->oUtil->getQueryValueInURLByKey(
                'http://localhost/?a=aaa&b=bbb&c=ccc',
                'b'
            )
        );
        $this->assertEquals(
            '123',
            $this->oUtil->getQueryValueInURLByKey(
                'http://localhost/?a=aaa&b=bbb&c=ccc&n=123',
                'n'
            )
        );

    }

}
