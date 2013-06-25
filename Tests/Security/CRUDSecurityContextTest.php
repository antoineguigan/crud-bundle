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
use Qimnet\CRUDBundle\Security\CRUDSecurityContext;

class CRUDSecurityContextTest extends \PHPUnit_Framework_TestCase
{
    public function testIsActionAllowed()
    {
        $security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $CRUDsecurity = new CRUDSecurityContext($security, array(
            'credentials'=>array(
                'action1'=>'credential1',
                'action2'=>'credential2'
            )
        ));
        $security
                ->expects($this->once())
                ->method('isGranted')
                ->with($this->equalTo('credential2'), 'entity')
                ->will($this->returnValue('success'));

        $this->assertEquals('success', $CRUDsecurity->isActionAllowed('action2', 'entity', 'object_vars'));
        $this->assertEquals(false, $CRUDsecurity->isActionAllowed('action3', 'entity', 'object_vars'));
    }
}
