<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartWbLibrary\Service;

use UnexpectedValueException;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Provides RadicalMart to Wildberries stock synchronization service injection.
 *
 * @since 1.0.0
 */
trait RadicalMartToWildberriesStockSyncServiceAwareTrait
{
	private ?RadicalMartToWildberriesStockSyncService $radicalMartToWildberriesStockSyncService = null;

	/**
	 * Get the configured RadicalMart to Wildberries stock synchronization service.
	 *
	 * @return RadicalMartToWildberriesStockSyncService Stock synchronization service.
	 *
	 * @throws UnexpectedValueException When the service has not been injected.
	 *
	 * @since 1.0.0
	 */
	public function getRadicalMartToWildberriesStockSyncService(): RadicalMartToWildberriesStockSyncService
	{
		if ($this->radicalMartToWildberriesStockSyncService instanceof RadicalMartToWildberriesStockSyncService)
		{
			return $this->radicalMartToWildberriesStockSyncService;
		}

		throw new UnexpectedValueException(
			'RadicalMartToWildberriesStockSyncService not set in ' . static::class . '.'
		);
	}

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
	): void
	{
		$this->radicalMartToWildberriesStockSyncService = $radicalMartToWildberriesStockSyncService;
	}
}
