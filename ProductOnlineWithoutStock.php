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

namespace ProductOnlineWithoutStock;

use Thelia\Module\BaseModule;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;

/**
 * Class ProductOnlineWithoutStock
 * @package ProductOnlineWithoutStock
 * @author Julien Citerne <jciterne@openstudio.fr>
 * @author Gilles Bourgeat <gbourgeat@openstudio.fr>
 */
class ProductOnlineWithoutStock extends BaseModule
{
    /** @var string */
    const MODULE_DOMAIN = 'productonlinewithoutstock';

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR . ucfirst(self::getModuleCode()). "/I18n/*"])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
