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
 * Defines injection of the Wildberries-to-RadicalMart order import service.
 *
 * @since 1.0.0
 */
interface WildberriesToRadicalMartOrderImportServiceAwareInterface
{
	/**
	 * Set the Wildberries-to-RadicalMart order import service.
	 *
	 * @param   WildberriesToRadicalMartOrderImportService  $service  Order import service.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setWildberriesToRadicalMartOrderImportService(
		WildberriesToRadicalMartOrderImportService $service
	): void;
}
