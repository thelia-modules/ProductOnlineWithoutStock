<?php

declare(strict_types=1);

namespace ProductOnlineWithoutStock\Service;

use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\ConfigQuery;
use Thelia\Model\Map\ProductSaleElementsTableMap;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\ProductQuery;

/**
 * Builds the paginated list of visible products whose sale elements all have a quantity <= 0.
 */
class ProductListService
{
    public const ORDERS = [
        'id' => [ProductTableMap::COL_ID, Criteria::ASC],
        'id_reverse' => [ProductTableMap::COL_ID, Criteria::DESC],
        'ref' => [ProductTableMap::COL_REF, Criteria::ASC],
        'ref_reverse' => [ProductTableMap::COL_REF, Criteria::DESC],
    ];

    /**
     * @return array{items: list<array{id: int, ref: string, title: string, visible: bool}>, order: string, current_page: int, total_pages: int, total_count: int}
     */
    public function getPage(int $page, string $order, string $locale): array
    {
        $order = \array_key_exists($order, self::ORDERS) ? $order : 'id';
        [$column, $direction] = self::ORDERS[$order];

        $query = ProductQuery::create()
            ->filterByVisible(1)
            ->addAnd(ProductTableMap::COL_ID, \sprintf(
                '%s NOT IN (SELECT %s FROM %s WHERE %s > 0)',
                ProductTableMap::COL_ID,
                ProductSaleElementsTableMap::COL_PRODUCT_ID,
                ProductSaleElementsTableMap::TABLE_NAME,
                ProductSaleElementsTableMap::COL_QUANTITY,
            ), Criteria::CUSTOM)
            ->orderBy($column, $direction);

        $perPage = max(1, (int) ConfigQuery::read('number_default_results_per_page.category_list', 20));
        $page = max(1, $page);
        $pager = $query->paginate($page, $perPage);

        $lastPage = max(1, $pager->getLastPage());
        if ($page > $lastPage) {
            $pager = $query->paginate($lastPage, $perPage);
        }

        $items = [];
        foreach ($pager->getResults() as $product) {
            $product->setLocale($locale);
            $items[] = [
                'id' => $product->getId(),
                'ref' => (string) $product->getRef(),
                'title' => (string) $product->getTitle(),
                'visible' => (bool) $product->getVisible(),
            ];
        }

        return [
            'items' => $items,
            'order' => $order,
            'current_page' => $pager->getPage(),
            'total_pages' => $lastPage,
            'total_count' => $pager->getNbResults(),
        ];
    }
}
