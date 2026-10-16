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
 * Defines injection of the Wildberries-to-RadicalMart product identifier synchronization service.
 *
 * @since 1.0.0
 */
interface WildberriesToRadicalMartProductIdSyncServiceAwareInterface
{
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
	): void;
}
