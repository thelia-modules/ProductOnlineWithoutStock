<?php
/*************************************************************************************/
/*      This file is part of the module ProductOnlineWithoutStock                    */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace ProductOnlineWithoutStock\Loop;

use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Loop\Product;
use Thelia\Model\Map\ProductSaleElementsTableMap;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\ProductQuery;

/**
 * Class ProductOnlineWithoutStockLoop
 * @package ProductOnlineWithoutStock\Loop
 * @author Julien Citerne <jciterne@openstudio.fr>
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class ProductOnlineWithoutStockLoop extends Product
{
    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        /** @var ProductQuery $search */
        $search = parent::buildModelCriteria();

        $criteria = new Criteria;

        $query = sprintf(
            "%s NOT IN (SELECT %s FROM %s WHERE %s = %s AND %s > 0)",
            ProductTableMap::ID,
            ProductSaleElementsTableMap::PRODUCT_ID,
            ProductSaleElementsTableMap::TABLE_NAME,
            ProductSaleElementsTableMap::PRODUCT_ID,
            ProductTableMap::ID,
            ProductSaleElementsTableMap::QUANTITY
        );

        $criteria->add(ProductTableMap::ID, $query, Criteria::CUSTOM);

        $search->where($criteria);

        return $search;
    }
}
