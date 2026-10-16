<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartWbLibrary\Service;

use WishboxRadicalMartLibrary\Service\Product\ProductService;
use WishboxRadicalMartWbLibrary\Dto\ProductIdSyncResult;
use WishboxWbLibrary\Service\WildberriesProductService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Orchestrates Wildberries-to-RadicalMart product identifier synchronization.
 *
 * @since        1.0.0
 *
 * @noinspection PhpUnused
 */
final readonly class WildberriesToRadicalMartProductIdSyncService
{
	private const string WB_PRODUCT_ID_FIELD = 'wishbox_wb_id';

	/**
	 * Initialize the product identifier synchronization service.
	 *
	 * @param   WildberriesProductService  $wildberriesProductService  Wildberries product identifier source.
	 * @param   ProductService             $radicalMartProductService  RadicalMart product destination.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		private WildberriesProductService $wildberriesProductService,
		private ProductService            $radicalMartProductService
	)
	{
	}

	/**
	 * Store Wildberries chrtIds for RadicalMart products matched by code.
	 *
	 * @return ProductIdSyncResult Synchronization result.
	 *
	 * @since 1.0.0
	 */
	public function updateProductIds(): ProductIdSyncResult
	{
		$wildberriesProductIds = $this->wildberriesProductService->getProductIds();
		$syncResult            = $this->radicalMartProductService->setExternalProductIdsByCode(
			self::WB_PRODUCT_ID_FIELD,
			$wildberriesProductIds
		);

		return new ProductIdSyncResult(...$syncResult);
	}
}
