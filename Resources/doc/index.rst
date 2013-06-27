qimnet/table-bundle
===================

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


Usage
=====

Defining a table type
---------------------

To define a new table types, implement the ``Qimnet\Table\TableType`` interface.
Qimnet tables work quite similarily to Symfony forms :

.. code-block:: php

    <?php

    namespace ACME\WebsiteBundle\Table;

    use Qimnet\TableBundle\Table\TableTypeInterface;
    use Qimnet\TableBundle\Table\TableBuilderInterface;

    class AdministratorType implements TableTypeInterface
    {
        public function buildTable(TableBuilderInterface $builder)
        {
            $builder
                    ->add('id','link', array('link'='/some-url')
                    ->add('username','crud_link')
                    ->add('date');
        }
    }

The following column rendering strategies are included in the bundle :

* **text**
* **boolean**
* **date**
* **link** (for this strategy, the ``link`` option **MUST** be provided)
* **translated**

If no strategy is provided, the best strategy is chosen depending on the type
of the data.

If your table type uses external services, or if you want to refer to it by
alias, you have to declare it as a service :

.. code-block:: xml

    <service id="acme.table.my_table" class="%acme.table.my_table.class%">
        <tag name="qimnet.table.type"/>
    </service>


Rendering data with a table type
--------------------------------

Use the ``qimnet.table.builder.factory`` service to create a table in your controller

.. code-block:: php

    <?php
    namespace ACME\WebsiteBundle\Controller;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;

    class TableController extends Controller {
        public function tableAction() {
            $data = array(
                array('key1'=>'value1', 'key2'=>'value2'),
                array('key2'=>'value3', 'key2'=>'value4'),
            );
            $tableBuilder = $this->container->get('qimnet.table.builder.factory')
                    ->createFromType('acme.table.my_table');
            $table = $tableBuilder->getTable();
            return $this->render(
                'ACMEWebsiteBundle:Table:table.html.twig',
                array(
                    'table'=>$table->createView()
                )
            );
        }
    }

The table can then be rendered in the template:

.. code-block:: twig

    {# ACMEWebsiteBundle:Table:table.html.twig #}
    {# ... #}
    <table>
        <thead>
            <tr>
                {% for column in table.columnNames %}
                    <th>{{table.renderHeader(column)|raw}}</th>
                {% endfor %}
            <tr>
        </thead>
            {% for object in table %}
                <tr>
                    {% for column in table.columnNames %}
                        <td>{{{table.render(object, {}, column)|raw}}</td>
                    {% endfor %}
                </tr>
            {% endfor %}
        </tbody>
    </table>


Configuration
=============

Main configuration
------------------


Table types
-----------


Filter types
------------