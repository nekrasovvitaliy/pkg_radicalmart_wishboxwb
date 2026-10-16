<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartWbLibrary\Service;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Defines the contract for classes that depend on RadicalMart to Wildberries stock synchronization.
 *
 * @since 1.0.0
 */
interface RadicalMartToWildberriesStockSyncServiceAwareInterface
{
	/**
	 * Get the configured RadicalMart to Wildberries stock synchronization service.
	 *
	 * @return RadicalMartToWildberriesStockSyncService Stock synchronization service.
	 *
	 * @since 1.0.0
	 */
	public function getRadicalMartToWildberriesStockSyncService(): RadicalMartToWildberriesStockSyncService;

	/**
	 * Set the RadicalMart to Wildberries stock synchronization service.
	 *
	 * @param   RadicalMartToWildberriesStockSyncService  $radicalMartToWildberriesStockSyncService  Stock synchronization service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setRadicalMartToWildberriesStockSyncService(
		RadicalMartToWildberriesStockSyncService $radicalMartToWildberriesStockSyncService
	): void;
}
