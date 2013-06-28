<?php

/*
 *  This file is part of Qimnet CRUD Bundle
 *  (c) Antoine Guigan <aguigan@qimnet.com>
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 */

namespace Qimnet\CRUDBundle\Tests\Controller\RedirectionManager;
use Qimnet\CRUDBundle\Controller\RedirectionManager\CRUDRedirectionManager;
use Qimnet\CRUDBundle\Configuration\CRUDAction;

/**
 * Description of CRUDRedirectionManagerTest
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class CRUDRedirectionManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $CRUDRequest;
    protected $translator;
    protected $manager;
    protected $request;
    protected $session;
    protected $flashBag;
    protected $configuration;
    protected $pathGenerator;

    protected function setUp()
    {
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->CRUDRequest = $this->getMock('Qimnet\CRUDBundle\HTTP\CRUDRequestInterface');
        $this->manager = new CRUDRedirectionManager($this->translator);
        $this->manager->setCRUDRequest($this->CRUDRequest);
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                ->disableOriginalConstructor()
                ->getMock();
        $this->CRUDRequest
                ->expects($this->any())
                ->method('getRequest')
                ->will($this->returnValue($this->request));
        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
                ->disableOriginalConstructor()
                ->getMock();
        $this->request
                ->expects($this->any())
                ->method('getSession')
                ->will($this->returnValue($this->session));
        $this->flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $this->session
                ->expects($this->any())
                ->method('getFlashBag')
                ->will($this->returnValue($this->flashBag));
        $this->configuration = $this->getMock('Qimnet\CRUDBundle\Configuration\CRUDConfigurationInterface');
        $this->CRUDRequest
                ->expects($this->any())
                ->method('getConfiguration')
                ->will($this->returnValue($this->configuration));
        $this->pathGenerator = $this->getMock('Qimnet\CRUDBundle\Routing\CRUDPathGeneratorInterface');
        $this->configuration
                ->expects($this->any())
                ->method('getPathGenerator')
                ->will($this->returnValue($this->pathGenerator));
        $this->pathGenerator
                ->expects($this->once())
                ->method('generate')
                ->with($this->equalTo(CRUDAction::INDEX))
                ->will($this->returnValue('url'));

    }
    public function assertFlashAdded()
    {
        $this->flashBag
                ->expects($this->once())
                ->method('add')
                ->with($this->equalTo('notice'));
    }
    public function assertIndexRedirect($response)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals('url', $response->getTargetUrl());
    }

    public function testCreateResponse()
    {
        $this->assertFlashAdded();
        $this->assertIndexRedirect($this->manager->getCreateResponse(new \stdClass));
    }

    public function testDeleteResponse()
    {
        $this->assertFlashAdded();
        $this->assertIndexRedirect($this->manager->getDeleteResponse(new \stdClass));
    }

    public function testDeletesResponse()
    {
        $this->assertFlashAdded();
        $this->assertIndexRedirect($this->manager->getDeletesResponse(new \stdClass));
    }

    public function testFilterResponse()
    {
        $this->assertIndexRedirect($this->manager->getFilterResponse(new \stdClass));
    }

    public function testUpdateResponse()
    {
        $this->assertFlashAdded();
        $this->assertIndexRedirect($this->manager->getUpdateResponse(new \stdClass));
    }
}
