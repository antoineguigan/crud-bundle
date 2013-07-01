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
 * Base implementation for the qimnet.crud.request service
 *
 * The qimnet.crud.request service is a synchronized service giving access to
 * the current CRUD configuration.
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class CRUDRequest implements CRUDRequestInterface
{
    private $request;
    private $configurationRepository;
    private $configuration;

    /**
     * Constructor
     *
     * @param CRUDConfigurationRepositoryInterface $configurationRepository
     * @param Request                              $request
     */
    public function __construct(CRUDConfigurationRepositoryInterface $configurationRepository, Request $request)
    {
        $this->request = $request;
        $this->configurationRepository = $configurationRepository;
        $this->configuration = null;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration()
    {
        if (!$this->configuration) {
            $this->configuration = $this->configurationRepository->get($this->request->get('configName'));
        }

        return $this->configuration;
    }

    /**
     * @inheritdoc
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    public function getRedirectionManager()
    {
        if ($this->getConfiguration()) {
            return $this->configurationRepository->getRedirectionManager($this->getConfiguration()->getName());
        }
    }

    /**
     * @inheritdoc
     */
    public function getWorker()
    {
        if ($this->getConfiguration()) {
            return $this->configurationRepository->getWorker($this->getConfiguration()->getName());
        }

    }
}
