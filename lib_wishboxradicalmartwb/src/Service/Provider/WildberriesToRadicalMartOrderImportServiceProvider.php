<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace WishboxRadicalMartWbLibrary\Service\Provider;

use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;
use RuntimeException;
use WishboxRadicalMartLibrary\Service\Order\OrderImportService;
use WishboxRadicalMartLibrary\Service\Provider\OrderImportServiceProvider;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartOrderImportService;
use WishboxWbLibrary\Api\WildberriesApiClient;
use WishboxWbLibrary\Service\Provider\WildberriesApiClientProvider;
use WishboxWbLibrary\Service\WildberriesOrderService;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Registers the Wildberries-to-RadicalMart order import services.
 *
 * @since 1.0.0
 */
final class WildberriesToRadicalMartOrderImportServiceProvider implements ServiceProviderInterface
{
	/**
	 * Register the configured order import services.
	 *
	 * @param   Container  $container  Joomla dependency injection container.
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

		$plugin = (array) PluginHelper::getPlugin('task', 'wishboxradicalmartwb');
		$params = new Registry($plugin['params'] ?? '');
		if (!$container->has(WildberriesToRadicalMartOrderImportService::class))
		{
			$container->share(
				WildberriesToRadicalMartOrderImportService::class,
				static fn (Container $container): WildberriesToRadicalMartOrderImportService =>
					new WildberriesToRadicalMartOrderImportService(
						$container->get(WildberriesOrderService::class),
						$container->get(OrderImportService::class),
						$params
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
			|| !class_exists(WildberriesOrderService::class))
		{
			throw new RuntimeException(
				'The Wishbox Wildberries library must be installed before enabling this plugin.'
			);
		}

		if (!$container->has(WildberriesApiClient::class))
		{
			$container->registerServiceProvider(new WildberriesApiClientProvider);
		}

		if (!$container->has(WildberriesOrderService::class))
		{
			$container->share(
				WildberriesOrderService::class,
				static fn (Container $container): WildberriesOrderService => new WildberriesOrderService(
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
		if (!class_exists(OrderImportServiceProvider::class))
		{
			throw new RuntimeException(
				'The Wishbox RadicalMart library must be installed before enabling this plugin.'
			);
		}

		if (!$container->has(OrderImportService::class))
		{
			$container->registerServiceProvider(new OrderImportServiceProvider);
		}
	}
}
