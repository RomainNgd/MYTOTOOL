<?php

namespace App\Tests;

use Monolog\Test\TestCase;

class AppTest extends TestCase
{
    /***
     * See if Test is ok
     * @return void
     */
    public function testTestAreWorking(){
        $this->assertEquals(2, 1+1);
    }
}