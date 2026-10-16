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
 * Provides access to the Wildberries-to-RadicalMart order import service.
 *
 * @since 1.0.0
 */
trait WildberriesToRadicalMartOrderImportServiceAwareTrait
{
	private ?WildberriesToRadicalMartOrderImportService $wildberriesToRadicalMartOrderImportService = null;

	/**
	 * Get the Wildberries-to-RadicalMart order import service.
	 *
	 * @return WildberriesToRadicalMartOrderImportService Order import service.
	 *
	 * @since 1.0.0
	 */
	protected function getWildberriesToRadicalMartOrderImportService(): WildberriesToRadicalMartOrderImportService
	{
		if (!$this->wildberriesToRadicalMartOrderImportService instanceof WildberriesToRadicalMartOrderImportService)
		{
			throw new UnexpectedValueException('WildberriesToRadicalMartOrderImportService has not been set.');
		}

		return $this->wildberriesToRadicalMartOrderImportService;
	}

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
	): void
	{
		$this->wildberriesToRadicalMartOrderImportService = $service;
	}
}
