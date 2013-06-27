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


Configuration
=============

Main configuration
------------------


Table types
-----------


Filter types
------------