<?php
namespace Qimnet\CRUDBundle\Table;

use Qimnet\CRUDBundle\HTTP\CRUDRequestInterface;
use Qimnet\CRUDBundle\Configuration\CRUDAction;
use Qimnet\TableBundle\Templating\AbstractTableRendererStrategyDecorator;
/*
 *  This file is part of Qimnet CRUD Bundle
 *  (c) Antoine Guigan <aguigan@qimnet.com>
 *  This source file is subject to the MIT license that is bundled
 *  with this source code in the file LICENSE.
 */

/**
 * Renders sort links in the header of CRUD index tables
 *
 * @author Antoine Guigan <aguigan@qimnet.com>
 */
class SortLinkRendererStrategy extends AbstractTableRendererStrategyDecorator
{
    /**
     * @var CRUDRequestInterface
     */
    protected $CRUDRequest;
    public function setCRUDRequest(CRUDRequestInterface $CRUDRequest=null)
    {
        $this->CRUDRequest = $CRUDRequest;
    }

    public function canRender($value, array $options = array())
    {
       return true;
    }

    public function getName()
    {
        return 'sort_link';
    }

    public function getPriority()
    {
        return false;
    }

    public function render($value, array $options = array())
    {
        $request = $this->CRUDRequest->getRequest();
        $sortDirection = $request->get('sortDirection','asc');
        $classes = '';
        if (($request->get('sortField')==$options['column_name'])) {
            if (($sortDirection=='asc')) {
                $sortDirection = 'desc';
            }
            $classes = "sorted $sortDirection";
        }
        $link = $this->CRUDRequest->getConfiguration()
                ->getPathGenerator()
                ->generate(CRUDAction::INDEX,array(
                    'sortField'=>$options['column_name'],
                    'sortDirection'=>$sortDirection));
        return sprintf('<a class="%s" href="%s">%s</a>', $classes, htmlspecialchars($link), $this->renderParent($value, $options));
    }
}
