<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace Joomla\Component\WishboxRadicalMartWb\Administrator\Extension;

use Joomla\CMS\Extension\MVCComponent;
use WishboxRadicalMartWbLibrary\Service\RadicalMartToWildberriesStockSyncServiceAwareInterface;
use WishboxRadicalMartWbLibrary\Service\RadicalMartToWildberriesStockSyncServiceAwareTrait;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartProductIdSyncServiceAwareInterface;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartProductIdSyncServiceAwareTrait;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Wishbox RadicalMart Wildberries component.
 *
 * @since 1.0.0
 */
final class WishboxRadicalMartWbComponent extends MVCComponent implements
	RadicalMartToWildberriesStockSyncServiceAwareInterface,
	WildberriesToRadicalMartProductIdSyncServiceAwareInterface
{
	use RadicalMartToWildberriesStockSyncServiceAwareTrait;
	use WildberriesToRadicalMartProductIdSyncServiceAwareTrait;
}
