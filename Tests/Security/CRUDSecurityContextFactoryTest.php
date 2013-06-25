<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Tests\Security;

use Qimnet\CRUDBundle\Security\CRUDSecurityContextFactory;

class CRUDSecurityContextFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $security;
    protected $factory;
    protected $options = array('credentials'=>array('action1'=>'credential1'));

    public function setUp()
    {
        $this->security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->factory = new CRUDSecurityContextFactory($this->security, 'crud_security_context_default_test_class');
    }
    public function setMockSecurityContextClass($class)
    {
        $this->getMockForAbstractClass('Qimnet\CRUDBundle\Security\CRUDSecurityContext', array(
                $this->security
            ), $class);
    }
    public function testCreate()
    {
        $this->setMockSecurityContextClass('crud_security_context_test_class');
        $this->assertInstanceOf('crud_security_context_test_class', $this->factory->create($this->options, 'crud_security_context_test_class'));
    }
    public function testCreateWithDefaultClass()
    {
        $this->setMockSecurityContextClass('crud_security_context_default_test_class');
        $this->assertInstanceOf('crud_security_context_default_test_class', $this->factory->create($this->options));
    }
}
