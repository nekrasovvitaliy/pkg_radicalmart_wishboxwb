<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartWbLibrary\Service\Provider;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use RuntimeException;
use WishboxRadicalMartLibrary\Service\Product\ProductService;
use WishboxRadicalMartLibrary\Service\Provider\ProductServiceProvider;
use WishboxRadicalMartWbLibrary\Service\RadicalMartToWildberriesStockSyncService;
use WishboxWbLibrary\Api\WildberriesApiClient;
use WishboxWbLibrary\Service\Provider\WildberriesApiClientProvider;
use WishboxWbLibrary\Service\WildberriesStockService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the RadicalMart-to-Wildberries stock synchronization service.
 *
 * @since 1.0.0
 */
final class RadicalMartToWildberriesStockSyncServiceProvider implements ServiceProviderInterface
{
	/**
	 * Register the configured stock synchronization service.
	 *
	 * @param   Container  $container  Dependency injection container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function register(Container $container): void
	{
		$this->registerWildberriesServices($container);
		$this->registerRadicalMartServices($container);

		if (!$container->has(RadicalMartToWildberriesStockSyncService::class))
		{
			$container->share(
				RadicalMartToWildberriesStockSyncService::class,
				static function (Container $container): RadicalMartToWildberriesStockSyncService {
					$wishboxWbComponentParams = ComponentHelper::getParams('com_wishboxwb');
					$wishboxRadicalMartWbComponentParams = ComponentHelper::getParams(
						'com_wishboxradicalmartwb'
					);

					return new RadicalMartToWildberriesStockSyncService(
						$container->get(ProductService::class),
						$container->get(WildberriesStockService::class),
						(int) $wishboxWbComponentParams->get('wildberries_default_warehouse_id', 0),
						(string) $wishboxRadicalMartWbComponentParams->get(
							'wildberries_chrt_id_field',
							'wishbox_wb_chrt_id'
						)
					);
				}
			);
		}
	}

	/**
	 * Register services supplied by the Wildberries library.
	 *
	 * @param   Container  $container  Dependency injection container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private function registerWildberriesServices(Container $container): void
	{
		if (!class_exists(WildberriesApiClientProvider::class)
			|| !class_exists(WildberriesStockService::class))
		{
			throw new RuntimeException(
				'The Wishbox Wildberries library must be installed before enabling this plugin.'
			);
		}

		if (!$container->has(WildberriesApiClient::class))
		{
			$container->registerServiceProvider(new WildberriesApiClientProvider);
		}

		if (!$container->has(WildberriesStockService::class))
		{
			$container->share(
				WildberriesStockService::class,
				static fn (Container $container): WildberriesStockService => new WildberriesStockService(
					$container->get(WildberriesApiClient::class)
				)
			);
		}
	}

	/**
	 * Register services supplied by the RadicalMart library.
	 *
	 * @param   Container  $container  Dependency injection container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private function registerRadicalMartServices(Container $container): void
	{
		if (!class_exists(ProductServiceProvider::class))
		{
			throw new RuntimeException(
				'The Wishbox RadicalMart library must be installed before enabling this plugin.'
			);
		}

		if (!$container->has(ProductService::class))
		{
			$container->registerServiceProvider(new ProductServiceProvider);
		}
	}
}
