<?php

declare(strict_types=1);

namespace ProductOnlineWithoutStock\Controller;

use ProductOnlineWithoutStock\Service\ProductListService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\Product\ProductToggleVisibilityEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\ProductQuery;
use Thelia\Tools\URL;

class ProductVisibilityController extends BaseAdminController
{
    #[Route('/admin/module/ProductOnlineWithoutStock/toggle-visibility', name: 'productonlinewithoutstock.toggle-visibility', methods: ['POST'])]
    public function toggleVisibilityAction(Request $request, EventDispatcherInterface $dispatcher): Response
    {
        if (null !== $response = $this->checkAuth(AdminResources::PRODUCT, [], AccessManager::UPDATE)) {
            return $response;
        }

        $this->getTokenProvider()->checkToken((string) $request->request->get('_token', ''));

        if (null !== $product = ProductQuery::create()->findPk((int) $request->request->get('product_id', 0))) {
            $dispatcher->dispatch(new ProductToggleVisibilityEvent($product), TheliaEvents::PRODUCT_TOGGLE_VISIBILITY);
        }

        $order = (string) $request->request->get('product_order', 'id');

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/ProductOnlineWithoutStock', [
            'page' => max(1, (int) $request->request->get('page', 1)),
            'product_order' => \array_key_exists($order, ProductListService::ORDERS) ? $order : 'id',
        ]));
    }
}
