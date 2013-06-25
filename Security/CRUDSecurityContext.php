<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Qimnet\CRUDBundle\Security;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface as SymfonySecurityContextInterface;
use Qimnet\TableBundle\Table\Action;
use Qimnet\TableBundle\Security\SecurityContextInterface;

class CRUDSecurityContext implements SecurityContextInterface
{
    protected $security;
    public function __construct(SymfonySecurityContextInterface $security, array $options=array())
    {
        $resolver = new OptionsResolver;
        $this->security = $security;
        $this->setDefaultOptions($resolver, $options);
        $this->options = $resolver->resolve($options);
    }
    protected function setDefaultOptions(OptionsResolverInterface $resolver, $options)
    {
        $resolver->setDefaults(array(
            'credentials'=>array(
                Action::CREATE => 'IS_AUTHENTICATED_ANONYMOUSLY',
                Action::DELETE => 'IS_AUTHENTICATED_ANONYMOUSLY',
                Action::SHOW => 'IS_AUTHENTICATED_ANONYMOUSLY',
                Action::UPDATE => 'IS_AUTHENTICATED_ANONYMOUSLY',
                Action::INDEX => 'IS_AUTHENTICATED_ANONYMOUSLY'
            )
        ));
    }

    public function isActionAllowed($action, $object = null, $objectVars = null)
    {
        if (isset($this->options['credentials'][$action])) {
            return $this->security->isGranted($this->options['credentials'][$action], $object);
        } else {
            return false;
        }
    }
}
