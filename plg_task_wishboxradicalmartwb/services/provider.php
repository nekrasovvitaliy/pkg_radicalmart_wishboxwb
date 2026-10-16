<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\Task\WishboxRadicalMartWb\Extension\WishboxRadicalMartWb;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartOrderImportService;
use WishboxRadicalMartWbLibrary\Service\Provider\WildberriesToRadicalMartOrderImportServiceProvider;
use WishboxRadicalMartWbLibrary\Service\Provider\RadicalMartToWildberriesStockSyncServiceProvider;
use WishboxRadicalMartWbLibrary\Service\RadicalMartToWildberriesStockSyncService;

defined('_JEXEC') or die;

/**
 * Registers scheduled RadicalMart and Wildberries synchronization tasks.
 *
 * @since 1.0.0
 */
return new class implements ServiceProviderInterface {
	/**
	 * Register the task plugin and its services.
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
		if (!class_exists(RadicalMartToWildberriesStockSyncServiceProvider::class)
			|| !class_exists(WildberriesToRadicalMartOrderImportServiceProvider::class))
		{
			throw new RuntimeException(
				'The Wishbox RadicalMart Wildberries library must be installed before enabling this plugin.'
			);
		}

		$container->registerServiceProvider(new RadicalMartToWildberriesStockSyncServiceProvider);
		$container->registerServiceProvider(new WildberriesToRadicalMartOrderImportServiceProvider);

		$container->set(
			PluginInterface::class,
			static function (Container $container): PluginInterface {
				$plugin = new WishboxRadicalMartWb(
					$container->get(DispatcherInterface::class),
					(array) PluginHelper::getPlugin('task', 'wishboxradicalmartwb')
				);

				$plugin->setApplication(Factory::getApplication());
				$plugin->setRadicalMartToWildberriesStockSyncService(
					$container->get(RadicalMartToWildberriesStockSyncService::class)
				);
				$plugin->setWildberriesToRadicalMartOrderImportService(
					$container->get(WildberriesToRadicalMartOrderImportService::class)
				);

				return $plugin;
			}
		);
	}
};
