<?php
namespace Qimnet\CRUDBundle\HTTP;
use Qimnet\CRUDBundle\Configuration\CRUDConfigurationRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/*
 *  This file is part of QIMNET CRUD Bundle
 *  (c) Antoine Guigan <aguigan@qimnet.com>
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 */

/**
 * Description of CRUDRequest
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class CRUDRequest implements CRUDRequestInterface
{
    protected $request;
    protected $configurationRepository;
    private $configuration;
    public function __construct(CRUDConfigurationRepositoryInterface $configurationRepository, Request $request)
    {
        $this->request = $request;
        $this->configurationRepository = $configurationRepository;
        $this->configuration = null;
    }

    public function getConfiguration()
    {
        if (!$this->configuration) {
            $this->configuration = $this->configurationRepository->get($this->request->get('configName'));
        }

        return $this->configuration;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getRedirectionManager()
    {
        if ($this->getConfiguration()) {
            return $this->configurationRepository->getRedirectionManager($this->getConfiguration()->getName());
        }
    }

    public function getWorker()
    {
        if ($this->getConfiguration()) {
            return $this->configurationRepository->getWorker($this->getConfiguration()->getName());
        }

    }
}
