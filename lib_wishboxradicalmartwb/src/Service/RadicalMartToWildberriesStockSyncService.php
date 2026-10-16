<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartWbLibrary\Service;

use Exception;
use WishboxRadicalMartLibrary\Service\Product\ProductService;
use WishboxRadicalMartWbLibrary\Dto\StockSyncResult;
use WishboxWbLibrary\Service\WildberriesStockService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Orchestrates RadicalMart-to-Wildberries stock synchronization.
 *
 * @since 1.0.0
 */
final readonly class RadicalMartToWildberriesStockSyncService
{
	private const string WB_PRODUCT_ID_FIELD = 'wishbox_wb_id';

	/**
	 * Initialize the stock synchronization service.
	 *
	 * @param   ProductService            $radicalMartProductService  RadicalMart product source.
	 * @param   WildberriesStockService  $wildberriesStockService   Wildberries stock destination.
	 * @param   int                       $warehouseId                Wildberries seller warehouse ID.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private ProductService           $radicalMartProductService,
		private WildberriesStockService $wildberriesStockService,
		private int                      $warehouseId
	)
	{
	}

	/**
	 * Publish RadicalMart stock quantities for products linked to Wildberries.
	 *
	 * @param   list<int>  $productIds  RadicalMart product identifiers, or an empty list for all linked products.
	 *
	 * @return StockSyncResult Synchronization result.
	 *
	 * @throws Exception When RadicalMart products cannot be loaded.
	 *
	 * @since 1.0.0
	 */
	public function updateStocks(array $productIds = []): StockSyncResult
	{
		$products = $this->radicalMartProductService->getProducts(
			self::WB_PRODUCT_ID_FIELD,
			'RUB',
			1.0,
			$productIds
		);
		$result = $this->wildberriesStockService->updateProductStocks(
			$products,
			$this->warehouseId
		);

		return new StockSyncResult(...$result);
	}
}
