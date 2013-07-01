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
 * Adds the 'filter_options' option to all field types.
 *
 * The filter_options option are used by the entity manager to filter the
 * results of the index page
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class FormTypeExtension extends AbstractTypeExtension
{

    /**
     * @inheritdoc
     */
    public function getExtendedType()
    {
        return 'form';
    }

    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'filter_options'=>array()
        ));
    }
}
