<?php

namespace DPolac\OnlyOnce\Tests;

use DPolac\OnlyOnce\OnlyOnceExtension;

class IntegrationTest extends \Twig_Test_IntegrationTestCase
{

    public function getExtensions()
    {
        return array(
            new OnlyOnceExtension(),
        );
    }

    public function getFixturesDir()
    {
        return dirname(__FILE__).'/Fixtures/';
    }
}
