<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartWbLibrary\Service;

use Joomla\Registry\Registry;
use Wishbox\Dto\OrderImport\OrderImportResult;
use WishboxRadicalMartLibrary\Service\Order\OrderImportService;
use WishboxWbLibrary\Service\WildberriesOrderService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Orchestrates Wildberries-to-RadicalMart order imports.
 *
 * @since 1.0.0
 */
final readonly class WildberriesToRadicalMartOrderImportService
{
	/**
	 * Initialize the order import service.
	 *
	 * @param   WildberriesOrderService  $wildberriesOrderService  Wildberries order source.
	 * @param   OrderImportService        $orderImportService       RadicalMart order destination.
	 * @param   Registry                  $params                   Integration parameters.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private WildberriesOrderService $wildberriesOrderService,
		private OrderImportService       $orderImportService,
		private Registry                 $params
	)
	{
	}

	/**
	 * Import new Wildberries FBS orders into RadicalMart.
	 *
	 * @param   int  $limit  Maximum Wildberries orders to import.
	 *
	 * @return OrderImportResult Import result.
	 *
	 * @since 1.0.0
	 */
	public function importNewOrders(int $limit = 100): OrderImportResult
	{
		$orders = $this->wildberriesOrderService->getNormalizedNewOrders($limit);

		return $this->orderImportService->importOrders(
			$orders,
			[
				'source'   => 'wishboxradicalmartwb',
				'user_id'  => (int) $this->params->get('radicalmart_order_user_id', 0),
				'currency' => (string) $this->params->get('order_currency', 'RUB'),
			]
		);
	}
}
