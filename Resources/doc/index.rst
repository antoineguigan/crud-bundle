qimnet/crud-bundle
==================

This bundle provides tools for quickly creating simple CRUD interfaces.


Installation
============

Add qimnet/table-bundle to composer.json. Depending on your
``minimum_stability`` setting, you might need to also add its dependencies.


.. code-block:: javascript

    "require": {

        "qimnet/table-bundle": "~1.0@dev",
        "qimnet/paginator-bundle": "~1.0@dev",
        "qimnet/crud-bundle": "~1.0@dev"
    }


Add QimnetTableBundle, QimnetPaginatorBundle and QimnetCRUDBundle to your
application kernel.

.. code-block:: php

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Qimnet\TableBundle\QimnetTableBundle(),
            new Qimnet\TableBundle\QimnetPaginatorBundle(),
            new Qimnet\TableBundle\QimnetCRUDBundle(),
            // ...
        );
    }

Finally, add the CRUD controller to your routing

.. code-block:: yaml

    qimnet_crud:
        resource: "@QimnetCRUDBundle/Resources/config/routing.yml"


Configuration
=============

Configuration example
---------------------

The bundle automatically creates configuration services for all configured
object classes. A configuration example could be the following :

.. code-block:: yaml

    qimnet_crud:
        defaults:
            options:
                security_context_options:
                    credentials:
                        index: ROLE_ADMIN
                        create: ROLE_ADMIN
                        update: ROLE_ADMIN
                        delete: ROLE_ADMIN

        services:
            administrator:
                options:
                    object_class: 'ACME\WebsiteBundle\Entity\Administrator'
                    form_type:  'administrator'
                    table_type: 'ACME\BackendBundle\Table\AdministratorType'

            configuration:
                options:
                    object_class: 'ACME\WebsiteBundle\Entity\Configuration'
                    form_type:  'ACME\BackendBundle\Form\ConfigurationType'
                    id_column: locale
                    security_context_options:
                        credentials:
                            index: ROLE_ADMIN
                            update: ROLE_ADMIN

This configuration would create two CRUD configuration services.

* a full CRUD interface accessible for the Administrator entity,
  at http://localhost/backend/administrator

* a CRUD interface limited to listing and editing for the Configuration entity
  at http://localhost/backend/configuration


Configuration options
---------------------

The following options are available with the default CRUDConfigurationInterface
implementation:

object_class:
  The class of the managed objects. This option is **required**.

base_template
  The twig template from which all CRUD templates inherit.

edit_template
  The twig template for the edit action.

new_template
  The twig template for the create action.

form_template
  The form template included in the edit and create action templates.

index_template
  The twig template used for the index action.

show_template
  The twig template for the show action. If left blank, the show action will
  be deactivated.

edit_title
  The title of the edit page

new_title
  The title of the create page

index_title
  The title of the index page

limit_per_page
  The maximum number of objects displayed on the index page

route_prefix
  The prefix for crud route names

csrf_intention
  The CSRF intention used to generate tokens for all forms and actions

id_column
  The name of the id column for the object

object_creation_parameters
  Contains an array of request arguments names for the create action. If
  the corresponding request arguments exist, they will be set as properties
  in the created object.

object_manager_class
  The class for the object manager instance

object_manager_options
  See the `Object manager options`_.


Security context options
------------------------

Object manager options
----------------------

Table types
-----------


Filter types
------------