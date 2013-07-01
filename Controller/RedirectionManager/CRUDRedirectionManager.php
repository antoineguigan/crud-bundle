<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Controller\RedirectionManager;

use Qimnet\CRUDBundle\HTTP\CRUDRequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Qimnet\CRUDBundle\Configuration\CRUDAction;

/**
 * A basic CRUDRedirectionManagerInterface implementation
 */
class CRUDRedirectionManager  implements CRUDRedirectionManagerInterface
{
    /**
     * @var CRUDRequestInterface
     */
    protected $CRUDRequest;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    /**
     * Sets the current CRUD Request
     *
     * @param CRUDRequestInterface $CRUDRequest
     */
    public function setCRUDRequest(CRUDRequestInterface $CRUDRequest=null)
    {
        $this->CRUDRequest = $CRUDRequest;
    }

    /**
     * @inheritdoc
     */
    public function getCreateResponse($entity)
    {
        $this->addFlash($this->translator->trans('CREATE_FLASH', array('entity'=>$entity), 'crud'));

        return $this->getIndexRedirectResponse();
    }

    /**
     * @inheritdoc
     */
    public function getDeleteResponse($entity)
    {
        $this->addFlash($this->translator->trans('DELETE_FLASH', array('entity'=>$entity), 'crud'));

        return $this->getIndexRedirectResponse();
    }

    /**
     * @inheritdoc
     */
    public function getDeletesResponse($error = '')
    {
        $this->addFlash($this->translator->trans($error?:'DELETES_FLASH',array(), 'crud'));

        return $this->getIndexRedirectResponse();
    }

    /**
     * @inheritdoc
     */
    public function getFilterResponse()
    {
        return $this->getIndexRedirectResponse();
    }

    /**
     * @inheritdoc
     */
    public function getUpdateResponse($entity)
    {
        $this->addFlash($this->translator->trans('UPDATE_FLASH', array('entity'=>$entity), 'crud'));

        return $this->getIndexRedirectResponse();
    }

    private function getIndexRedirectResponse()
    {
        return new RedirectResponse($this->CRUDRequest->getConfiguration()->getPathGenerator()->generate(CRUDAction::INDEX));
    }

    private function addFlash($text)
    {
        $this->CRUDRequest->getRequest()->getSession()->getFlashBag()->add('notice', $text);
    }
}
