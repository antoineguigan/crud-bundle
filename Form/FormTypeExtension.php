<?php
namespace Qimnet\CRUDBundle\Form;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
/*
 *  This file is part of Qimnet CRUD Bundle
 *  (c) Antoine Guigan <aguigan@qimnet.com>
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 */

/**
 * Description of FormTypeExtension
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class FormTypeExtension extends AbstractTypeExtension
{

    public function getExtendedType()
    {
        return 'form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'filter_options'=>array()
        ));
    }
}
