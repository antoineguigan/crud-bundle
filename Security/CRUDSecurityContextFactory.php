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
use Symfony\Component\Security\Core\SecurityContextInterface;

class CRUDSecurityContextFactory implements CRUDSecurityContextFactoryInterface
{
    protected $security;
    protected $defaultClass;

    public function __construct(SecurityContextInterface $security, $defaultClass)
    {
        $this->security = $security;
        $this->defaultClass = $defaultClass;
    }
    public function create(array $options=array(), $class="")
    {
        $class = $class?:$this->defaultClass;

        return new $class($this->security, $options);
    }
}
