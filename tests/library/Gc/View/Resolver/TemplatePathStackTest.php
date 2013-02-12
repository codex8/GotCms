<?php
/**
 * This source file is part of GotCms.
 *
 * GotCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GotCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with GotCms. If not, see <http://www.gnu.org/licenses/lgpl-3.0.html>.
 *
 * PHP Version >=5.3
 *
 * @category Gc_Tests
 * @package  Library
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link     http://www.got-cms.com
 */

namespace Gc\View\Resolver;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-10-17 at 20:40:07.
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 * @group Gc
 * @category Gc_Tests
 * @package  Library
 */
class TemplatePathStackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplatePathStack
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new TemplatePathStack;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Gc\View\Resolver\TemplatePathStack::resolve
     */
    public function testNormalResolve()
    {
        $this->_object->addPath(__DIR__ . '/_templates');

        $markup = $this->_object->resolve('one.phtml');
        $this->assertEquals(__DIR__ . '/_templates/one.phtml', $markup);
    }

    /**
     * @covers Gc\View\Resolver\TemplatePathStack::resolve
     */
    public function testWithoutPaths()
    {
        $markup = $this->_object->resolve('one.phtml');
        $this->assertFalse($markup);
    }

    /**
     * @covers Gc\View\Resolver\TemplatePathStack::resolve
     */
    public function testResolveWithoutDefaultSuffix()
    {
        $this->_object->setDefaultSuffix('.bar');
        $this->_object->addPath(__DIR__ . '/_templates');

        $markup = $this->_object->resolve('two.phtml');
        $this->assertEquals(__DIR__ . '/_templates/two.phtml.bar', $markup);
    }

    /**
     * @covers Gc\View\Resolver\TemplatePathStack::resolve
     */
    public function testResolveWithLfiProtection()
    {
        $this->_object->setLfiProtection(TRUE)
            ->addPath(__DIR__ . '/_templates');

        $this->setExpectedException('Zend\View\Exception\ExceptionInterface');
        $this->_object->resolve('../one.phtml');
    }

    /**
     * @covers Gc\View\Resolver\TemplatePathStack::resolve
     */
    public function testResolveWithStream()
    {
        $existed = in_array('zend.view', stream_get_wrappers());
        if($existed)
        {
            stream_wrapper_unregister('zend.view');
        }

        stream_wrapper_register('zend.view', '\Gc\View\Stream');
        $this->_object->setUseStreamWrapper(TRUE);
        $markup = $this->_object->resolve('foo.bar');
        $this->assertEquals('zend.view://foo.bar', $markup);
    }

    /**
     * @covers Gc\View\Resolver\TemplatePathStack::resolve
     */
    public function testResolveWithStreamAndNoStreamWrapperActive()
    {
        $markup = $this->_object->resolve('foo.bar');
        $this->assertFalse($markup);

    }

    /**
     * @covers Gc\View\Resolver\TemplatePathStack::resolve
     */
    public function testResolveWithPharProtocol()
    {
        $path  = 'phar://' . __DIR__
            . DIRECTORY_SEPARATOR . '_templates'
            . DIRECTORY_SEPARATOR . 'view.phar'
            . DIRECTORY_SEPARATOR . 'start'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'views';
        $this->_object->addPath($path);
        $markup = $this->_object->resolve('foo' . DIRECTORY_SEPARATOR . 'hello.phtml');
        $this->assertEquals($path . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'hello.phtml', $markup);
    }

    /**
     * @covers Gc\View\Resolver\TemplatePathStack::resolve
     */
    public function testResolveWithFakePharProtocol()
    {
        $path  = 'phar://' . __DIR__
            . DIRECTORY_SEPARATOR . '_templates'
            . DIRECTORY_SEPARATOR . 'fake-view.phar';
        $this->_object->addPath($path);
        $markup = $this->_object->resolve('hello.phtml');
        $this->assertFalse($markup);
    }
}
