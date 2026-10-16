<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartWbLibrary\Service\Provider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use RuntimeException;
use WishboxRadicalMartLibrary\Service\Product\ProductService;
use WishboxRadicalMartLibrary\Service\Provider\ProductServiceProvider;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartProductIdSyncService;
use WishboxWbLibrary\Api\WildberriesApiClient;
use WishboxWbLibrary\Service\Provider\WildberriesApiClientProvider;
use WishboxWbLibrary\Service\WildberriesProductService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the Wildberries-to-RadicalMart product identifier synchronization service.
 *
 * @since 1.0.0
 */
final class WildberriesToRadicalMartProductIdSyncServiceProvider implements ServiceProviderInterface
{
	/**
	 * Register the configured product identifier synchronization service.
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

		if (!$container->has(WildberriesToRadicalMartProductIdSyncService::class))
		{
			$container->share(
				WildberriesToRadicalMartProductIdSyncService::class,
				static fn (Container $container): WildberriesToRadicalMartProductIdSyncService =>
					new WildberriesToRadicalMartProductIdSyncService(
						$container->get(WildberriesProductService::class),
						$container->get(ProductService::class)
					)
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
			|| !class_exists(WildberriesProductService::class))
		{
			throw new RuntimeException(
				'The Wishbox Wildberries product service must be installed before enabling this integration.'
			);
		}

		if (!$container->has(WildberriesApiClient::class))
		{
			$container->registerServiceProvider(new WildberriesApiClientProvider);
		}

		if (!$container->has(WildberriesProductService::class))
		{
			$container->share(
				WildberriesProductService::class,
				static fn (Container $container): WildberriesProductService => new WildberriesProductService(
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
				'The Wishbox RadicalMart library must be installed before enabling this integration.'
			);
		}

		if (!$container->has(ProductService::class))
		{
			$container->registerServiceProvider(new ProductServiceProvider);
		}
	}
}
