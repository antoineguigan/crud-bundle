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
use Qimnet\TableBundle\Table\Action;
use Qimnet\CRUDBundle\Routing\CRUDPathGeneratorFactoryInterface;
use Qimnet\CRUDBundle\Security\CRUDSecurityContextFactoryInterface;
use Qimnet\CRUDBundle\Persistence\ObjectManagerFactoryInterface;

class CRUDConfiguration implements CRUDConfigurationInterface
{
    protected $options;
    protected $pathGeneratorFactory;
    protected $objectManagerFactory;
    protected $securityContextFactory;

    private $pathGenerator;
    private $objectManager;
    private $securityContext;

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
            'path_generator_class'=>'',
        ));
    }

    public function getBaseTemplate()
    {
        return $this->options['base_template'];
    }

    public function getCSRFIntention()
    {
        return $this->options['csrf_intention'];
    }

    public function getFormTemplate()
    {
        return $this->options['form_template'];
    }

    public function getEditTemplate()
    {
        return $this->options['edit_template'];
    }

    public function getEditTitle()
    {
        return $this->options['edit_title'];
    }

    public function getIndexTemplate()
    {
        return $this->options['index_template'];
    }

    public function getIndexTitle()
    {
        return $this->options['index_title'];
    }

    public function getLimitPerPage()
    {
        return $this->options['limit_per_page'];
    }

    public function getNewTemplate()
    {
        return $this->options['new_template'];
    }

    public function getNewTitle()
    {
        return $this->options['new_title'];
    }

    public function getName()
    {
        return $this->options['name'];
    }

    public function getTableType()
    {
        return $this->options['table_type'];
    }

    public function getDefaultViewVars(Request $request)
    {
        return array(
            'type_name' => $this->getName(),
            'base_template' => $this->options['base_template'],
            'index_allowed' => $this->getSecurityContext()->isActionAllowed(Action::INDEX),
            'route_prefix' => $this->options['route_prefix'],
            'route_parameters' => $this->getRouteParameters(),
            'index_url'=>  $this->getPathGenerator()->generate(Action::INDEX),
            'new_url'=>  $this->getPathGenerator()->generate(Action::CREATE),
            'form_template' => $this->options['form_template'],
        );
    }

    protected function getRouteParameters()
    {
        return array(
            'configName' => $this->getName()
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
    protected function createPathGenerator()
    {
        return $this->pathGeneratorFactory->create(
                $this->options['route_prefix'],
                $this->options['name'],
                $this->options['id_column'],
                $this->options['path_generator_class']);
    }

    public function getShowTemplate()
    {
        return $this->options['show_template'];
    }

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
