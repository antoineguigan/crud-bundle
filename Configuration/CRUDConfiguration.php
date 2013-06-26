<?php
/*
 * This file is part of the Qimnet CRUD Bundle.
 *
 * (c) Antoine Guigan <aguigan@qimnet.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Qimnet\CRUDBundle\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Qimnet\CRUDBundle\Configuration\CRUDAction;
use Qimnet\CRUDBundle\Routing\CRUDPathGeneratorFactoryInterface;
use Qimnet\CRUDBundle\Security\CRUDSecurityContextFactoryInterface;
use Qimnet\CRUDBundle\Persistence\ObjectManagerFactoryInterface;
use Qimnet\CRUDBundle\Routing\CRUDPathGeneratorInterface;

/**
 * Contains the configuration for a CRUD instance
 *
 * Contains the configuration needed for the CRUDController. To see the default
 * configuration options, please see
 * https://github.com/qimnet/crud-bundle/blob/master/Resources/doc/index.rst
 *
 */
class CRUDConfiguration implements CRUDConfigurationInterface
{
    /**
     * Contains the options of the instance
     *
     * @var array
     */
    protected $options;
    /**
     * @var CRUDPathGeneratorFactoryInterface
     */
    protected $pathGeneratorFactory;
    /**
     * @var ObjectManagerFactoryInterface
     */
    protected $objectManagerFactory;
    /**
     * @var CRUDSecurityContextFactoryInterface
     */
    protected $securityContextFactory;

    private $pathGenerator;
    private $objectManager;
    private $securityContext;

    /**
     * Constructor
     *
     * @param ObjectManagerFactoryInterface $objectManagerFactory
     * @param CRUDSecurityContextFactoryInterface $securityContextFactory
     * @param CRUDPathGeneratorFactoryInterface $pathGeneratorFactory
     * @param array $options
     */
    public function __construct(
            ObjectManagerFactoryInterface $objectManagerFactory,
            CRUDSecurityContextFactoryInterface $securityContextFactory,
            CRUDPathGeneratorFactoryInterface $pathGeneratorFactory,
            array $options = array())
    {
        $resolver = new OptionsResolver;
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
        $this->pathGeneratorFactory = $pathGeneratorFactory;
        $this->securityContextFactory = $securityContextFactory;
        $this->objectManagerFactory = $objectManagerFactory;
    }

    /**
     * Sets the default options for the instance.
     * 
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'name',
            'object_class',
        ));
        $resolver->setDefaults(array(
            'edit_template' => 'QimnetCRUDBundle:CRUD:edit.html.twig',
            'new_template' => 'QimnetCRUDBundle:CRUD:new.html.twig',
            'base_template' => 'QimnetCRUDBundle::layout.html.twig',
            'form_template' => 'QimnetCRUDBundle:CRUD:form.html.twig',
            'show_template' => false,
            'index_template' => 'QimnetCRUDBundle:CRUD:index.html.twig',
            'route_prefix' => 'qimnet_crud',
            'query_alias' => 't',
            'csrf_intention' => 'qimnet_crud',
            'edit_title' => 'Modifying %typeName% %entity%',
            'new_title' => 'New %typeName%',
            'index_title' => '%typeName% list',
            'id_column' => 'id',
            'limit_per_page' => 10,
            'new_route_parameter_names'=>array(),
            'object_manager_class'=>'Qimnet\CRUDBundle\Persistence\DoctrineEntityManager',
            'object_manager_options'=>array(),
            'paginator_type'=>'doctrine',
            'paginator_options'=>array(),
            'security_context_class'=>'',
            'security_context_options'=>array(),
            'form_type'=>false,
            'table_type'=>false,
            'filter_type'=>false,
            'filter_defaults'=>array(),
            'sort_link_renderer_options'=>array('type'=>'sort_link'),
            'path_generator_class'=>'',
        ));
    }

    /**
     * @inheritdoc
     */
    public function getBaseTemplate()
    {
        return $this->options['base_template'];
    }

    /**
     * @inheritdoc
     */
    public function getSortLinkRendererOptions()
    {
        return $this->options['sort_link_renderer_options'];
    }

    /**
     * @inheritdoc
     */
    public function getCSRFIntention()
    {
        return $this->options['csrf_intention'];
    }

    /**
     * @inheritdoc
     */
    public function getFormTemplate()
    {
        return $this->options['form_template'];
    }

    /**
     * @inheritdoc
     */
    public function getEditTemplate()
    {
        return $this->options['edit_template'];
    }

    /**
     * @inheritdoc
     */
    public function getEditTitle()
    {
        return $this->options['edit_title'];
    }

    /**
     * @inheritdoc
     */
    public function getIndexTemplate()
    {
        return $this->options['index_template'];
    }

    /**
     * @inheritdoc
     */
    public function getIndexTitle()
    {
        return $this->options['index_title'];
    }

    /**
     * @inheritdoc
     */
    public function getLimitPerPage()
    {
        return $this->options['limit_per_page'];
    }

    /**
     * @inheritdoc
     */
    public function getNewTemplate()
    {
        return $this->options['new_template'];
    }

    /**
     * @inheritdoc
     */
    public function getNewTitle()
    {
        return $this->options['new_title'];
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->options['name'];
    }

    /**
     * @inheritdoc
     */
    public function getTableType()
    {
        return $this->options['table_type'];
    }

    /**
     * @inheritdoc
     */
    public function getDefaultViewVars(Request $request)
    {
        return array(
            'type_name' => $this->getName(),
            'base_template' => $this->options['base_template'],
            'form_template' => $this->options['form_template'],
        );
    }


    /**
     * @inheritdoc
     */
    final public function getPathGenerator()
    {
        if (!isset($this->pathGenerator)) {
            $this->pathGenerator = $this->createPathGenerator();
        }

        return $this->pathGenerator;
    }
    /**
     * Creates the path generator instance
     * 
     * @return CRUDPathGeneratorInterface
     */
    protected function createPathGenerator()
    {
        return $this->pathGeneratorFactory->create(
                $this->options['route_prefix'],
                $this->options['name'],
                $this->options['id_column'],
                $this->options['path_generator_class']);
    }

    /**
     * @inheritdoc
     */
    public function getShowTemplate()
    {
        return $this->options['show_template'];
    }

    /**
     * @inheritdoc
     */
    final public function getObjectManager()
    {
        if (!isset($this->objectManager)) {
            $this->objectManager = $this->createObjectManager();
        }

        return $this->objectManager;
    }
    protected function createObjectManager()
    {
        return $this->objectManagerFactory
                ->create($this->getObjectManagerOptions(),
                         $this->options['object_manager_class']);
    }
    protected function getObjectManagerOptions()
    {
        return $this->options['object_manager_options'] + array(
            'class' =>  $this->getObjectClass(),
            'id_column' =>  $this->options['id_column']
        );
    }
    final public function getSecurityContext()
    {
        if (!isset($this->securityContext)) {
            $this->securityContext = $this->createSecurityContext();
        }

        return $this->securityContext;
    }
    protected function createSecurityContext()
    {
        return $this->securityContextFactory->create(
                $this->options['security_context_options'],
                $this->options['security_context_class']);
    }

    public function getObjectClass()
    {
        return $this->options['object_class'];
    }

    public function getFormType($entity)
    {
        return $this->options['form_type'];
    }

    public function getQueryAlias()
    {
        return $this->options['query_alias'];
    }

    public function getFilterType()
    {
        return $this->options['filter_type'];
    }

    public function getPaginatorOptions()
    {
        return $this->options['paginator_options'];
    }

    public function getPaginatorType()
    {
        return $this->options['paginator_type'];
    }

    public function getNewRouteParameterNames()
    {
        return $this->options['new_route_parameter_names'];
    }

}
