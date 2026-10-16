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
 * Provides access to the Wildberries-to-RadicalMart product identifier synchronization service.
 *
 * @since 1.0.0
 */
trait WildberriesToRadicalMartProductIdSyncServiceAwareTrait
{
	private ?WildberriesToRadicalMartProductIdSyncService $wildberriesToRadicalMartProductIdSyncService = null;

	/**
	 * Get the product identifier synchronization service.
	 *
	 * @return WildberriesToRadicalMartProductIdSyncService Product identifier synchronization service.
	 *
	 * @throws UnexpectedValueException When the service has not been injected.
	 *
	 * @since 1.0.0
	 */
	public function getWildberriesToRadicalMartProductIdSyncService(): WildberriesToRadicalMartProductIdSyncService
	{
		if (!$this->wildberriesToRadicalMartProductIdSyncService instanceof WildberriesToRadicalMartProductIdSyncService)
		{
			throw new UnexpectedValueException(
				'WildberriesToRadicalMartProductIdSyncService has not been set.'
			);
		}

		return $this->wildberriesToRadicalMartProductIdSyncService;
	}

	/**
	 * Set the product identifier synchronization service.
	 *
	 * @param   WildberriesToRadicalMartProductIdSyncService  $service  Product identifier synchronization service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setWildberriesToRadicalMartProductIdSyncService(
		WildberriesToRadicalMartProductIdSyncService $service
	): void
	{
		$this->wildberriesToRadicalMartProductIdSyncService = $service;
	}
}
