<?php

declare(strict_types=1);

namespace ProductOnlineWithoutStock\Hook;

use ProductOnlineWithoutStock\ProductOnlineWithoutStock;
use ProductOnlineWithoutStock\Service\ProductListService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Tools\URL;

class BackHook extends BaseHook
{
    public function __construct(
        private readonly ProductListService $productListService,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'module.configuration' => [
                ['type' => 'back', 'method' => 'onModuleConfiguration'],
            ],
            'main.top-menu-catalog' => [
                ['type' => 'back', 'method' => 'onMainTopMenuCatalog'],
            ],
        ];
    }

    public function onModuleConfiguration(HookRenderEvent $event): void
    {
        $request = $this->getRequest();

        $list = $this->productListService->getPage(
            (int) ($request?->query->get('page', 1) ?? 1),
            (string) ($request?->query->get('product_order', 'id') ?? 'id'),
            $this->getSession()->getAdminEditionLang()->getLocale(),
        );

        $event->add($this->render('productonlinewithoutstock/module-configuration.html.twig', $list));
    }

    public function onMainTopMenuCatalog(HookRenderBlockEvent $event): void
    {
        $event->add([
            'id' => 'productonlinewithoutstock-menu',
            'url' => URL::getInstance()->absoluteUrl('/admin/module/ProductOnlineWithoutStock'),
            'title' => $this->trans('Product Online Without Stock', [], ProductOnlineWithoutStock::DOMAIN_NAME),
        ]);
    }
}
