<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

namespace Joomla\Component\WishboxRadicalMartWb\Administrator\Controller;

use Exception;
use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\WishboxRadicalMartWb\Administrator\Extension\WishboxRadicalMartWbComponent;
use WishboxRadicalMartWbLibrary\Dto\ProductIdSyncResult;
use WishboxRadicalMartWbLibrary\Dto\StockSyncResult;
use WishboxRadicalMartWbLibrary\Service\RadicalMartToWildberriesStockSyncService;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartProductIdSyncService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Handles Wildberries stock synchronization requests from the RadicalMart toolbar.
 *
 * @since        1.0.0
 *
 * @noinspection PhpUnused
 */
final class ProductsController extends BaseController
{
	private const string DEFAULT_REDIRECT_URL = 'index.php?option=com_radicalmart&view=products';

	/**
	 * Handle the Wildberries product identifier synchronization action.
	 *
	 * @return void
	 *
	 * @throws Exception When synchronization cannot be started.
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function getIds(): void
	{
		$this->validateRequest();

		$productIdSyncService = $this->getProductIdSyncService();

		$result = $productIdSyncService->updateProductIds();

		$this->enqueueResult('COM_WISHBOXRADICALMARTWB_MESSAGE_IDS_UPDATED', $result);
		$this->redirectToProducts();
	}

	/**
	 * Publish stocks for selected RadicalMart products to Wildberries.
	 *
	 * @return void
	 *
	 * @throws Exception When synchronization cannot be started.
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function updateStocks(): void
	{
		$this->validateRequest();

		$productIds = array_map('intval', (array) $this->input->post->get('cid', [], 'array'))
				|> (fn($x) => array_filter($x, static fn(int $productId): bool => $productId > 0))
				|> array_unique(...)
				|> array_values(...);

		if ($productIds === [])
		{
			$this->app->enqueueMessage(
				Text::_('COM_WISHBOXRADICALMARTWB_MESSAGE_SELECT_PRODUCTS'),
				'warning'
			);
			$this->redirectToProducts();

			return;
		}

		$result = $this->getStockSyncService()
			->updateStocks($productIds);

		$this->enqueueResult('COM_WISHBOXRADICALMARTWB_MESSAGE_STOCKS_UPDATED', $result);
		$this->redirectToProducts();
	}

	/**
	 * Publish stocks for all linked RadicalMart products to Wildberries.
	 *
	 * @return void
	 *
	 * @throws Exception When synchronization cannot be started.
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function updateAllStocks(): void
	{
		$this->validateRequest();

		$result = $this->getStockSyncService()
			->updateStocks();

		$this->enqueueResult('COM_WISHBOXRADICALMARTWB_MESSAGE_STOCKS_UPDATED', $result);
		$this->redirectToProducts();
	}

	/**
	 * Get the stock synchronization service from the component.
	 *
	 * @return RadicalMartToWildberriesStockSyncService Stock synchronization service.
	 *
	 * @throws Exception When the component cannot be booted.
	 *
	 * @since 1.0.0
	 */
	private function getStockSyncService(): RadicalMartToWildberriesStockSyncService
	{
		/** @var WishboxRadicalMartWbComponent $wishboxRadicalMartWbComponent */
		$wishboxRadicalMartWbComponent = $this->app->bootComponent('com_wishboxradicalmartwb');

		return $wishboxRadicalMartWbComponent->getRadicalMartToWildberriesStockSyncService();
	}

	/**
	 * Get the product identifier synchronization service from the component.
	 *
	 * @return WildberriesToRadicalMartProductIdSyncService Product identifier synchronization service.
	 *
	 * @throws Exception When the component cannot be booted.
	 *
	 * @since 1.0.0
	 */
	private function getProductIdSyncService(): WildberriesToRadicalMartProductIdSyncService
	{
		/** @var WishboxRadicalMartWbComponent $wishboxRadicalMartWbComponent */
		$wishboxRadicalMartWbComponent = $this->app->bootComponent('com_wishboxradicalmartwb');

		return $wishboxRadicalMartWbComponent->getWildberriesToRadicalMartProductIdSyncService();
	}

	/**
	 * Add synchronization results to the Joomla message queue.
	 *
	 * @param   string                               $languageKey  Result language key.
	 * @param   StockSyncResult|ProductIdSyncResult  $result       Synchronization result.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private function enqueueResult(
		string                              $languageKey,
		StockSyncResult|ProductIdSyncResult $result
	): void
	{
		$this->app->enqueueMessage(
			Text::sprintf(
				$languageKey,
				$result->updated,
				$result->skipped,
				$result->failed
			),
			$result->failed > 0 ? 'warning' : 'success'
		);

		foreach ($result->errors as $message)
		{
			$this->app->enqueueMessage($message, 'error');
		}
	}

	/**
	 * Validate the request token and component permission.
	 *
	 * @return void
	 *
	 * @throws Exception When the request is not authorized.
	 *
	 * @since 1.0.0
	 */
	private function validateRequest(): void
	{
		$this->checkToken();

		$identity = $this->app->getIdentity();

		if (!$identity->authorise('core.manage', 'com_wishboxradicalmartwb'))
		{
			throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}

	/**
	 * Redirect to the RadicalMart products list.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private function redirectToProducts(): void
	{
		$redirectUrl = $this->input->post->getString('redirect_url', self::DEFAULT_REDIRECT_URL);

		if ($redirectUrl === '' || !Uri::isInternal($redirectUrl))
		{
			$redirectUrl = self::DEFAULT_REDIRECT_URL;
		}

		$this->setRedirect(Route::_($redirectUrl, false));
	}
}
