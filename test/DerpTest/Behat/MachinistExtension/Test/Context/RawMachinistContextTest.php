<?php
/**
 * Copyright (c) 2013 Adam L. Englander
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace DerpTest\Behat\MachinistExtension\Test\Context;

use Phake;
use DerpTest\Behat\MachinistExtension\Context\RawMachinistContext;

class RawMachinistContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RawMachinistContext
     */
    private $context;

    /**
     * @var \DerpTest\Machinist\Machinist
     */
    private $machinist;

    protected function setUp()
    {
        $this->machinist = Phake::mock('\DerpTest\Machinist\Machinist');
        $this->context = new RawMachinistContext();
        $this->context->setMachinist($this->machinist);
    }

    protected function tearDown()
    {
        $this->machinist = null;
        $this->context = null;
    }

    public function testGetSubcontextReturnsNull()
    {
        $actual = $this->context->getSubcontext('anything');
        $this->assertNull($actual);
    }

    public function testGetSubcontextsReturnsEmptyArray()
    {
        $actual = $this->context->getSubcontexts();
        $this->assertInternalType('array', $actual);
        $this->assertEmpty($actual);
    }

    public function testGetSubcontextByClassNameReturnsNull()
    {
        $actual = $this->context->getSubcontextByClassName('anything');
        $this->assertNull($actual);
    }
}
