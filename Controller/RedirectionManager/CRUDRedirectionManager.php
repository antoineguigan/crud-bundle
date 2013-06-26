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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CRUDRedirectionManager  implements CRUDRedirectionManagerInterface
{
    /**
     * @var CRUDRequestInterface
     */
    protected $CRUDRequest;
    protected $translator;


    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    public function setCRUDRequest(CRUDRequestInterface $CRUDRequest=null)
    {
        $this->CRUDRequest = $CRUDRequest;
    }
    public function getCreateResponse($entity)
    {
        $this->addFlash($this->translator->trans('CREATE_FLASH', array('entity'=>$entity), 'crud'));
        return $this->getIndexRedirectResponse();
    }

    public function getDeleteResponse($entity)
    {
        $this->addFlash($this->translator->trans('DELETE_FLASH', array('entity'=>$entity), 'crud'));
        return $this->getIndexRedirectResponse();
    }

    public function getDeletesResponse($error = '')
    {
        $this->addFlash($this->translator->trans($error?:'DELETES_FLASH',array(), 'crud'));
        return $this->getIndexRedirectResponse();
    }

    public function getFilterResponse()
    {
        return $this->getIndexRedirectResponse();
    }

    public function getUpdateResponse($entity)
    {
        $this->addFlash($this->translator->trans('UPDATE_FLASH', array('entity'=>$entity), 'crud'));
        return $this->getIndexRedirectResponse();
    }
    protected function getIndexRedirectResponse()
    {
        return new RedirectResponse($this->CRUDRequest->getConfiguration()->getPathGenerator()->generate(CRUDAction::INDEX));
    }
    protected function addFlash($text)
    {
        $this->CRUDRequest->getRequest()->getSession()->getFlashBag()->add('notice', $text);
    }
}
