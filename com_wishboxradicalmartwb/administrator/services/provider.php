<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\WishboxRadicalMartWb\Administrator\Extension\WishboxRadicalMartWbComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use WishboxRadicalMartWbLibrary\Service\Provider\RadicalMartToWildberriesStockSyncServiceProvider;
use WishboxRadicalMartWbLibrary\Service\Provider\WildberriesToRadicalMartProductIdSyncServiceProvider;
use WishboxRadicalMartWbLibrary\Service\RadicalMartToWildberriesStockSyncService;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartProductIdSyncService;

defined('_JEXEC') or die;

/**
 * Registers the Wishbox RadicalMart Wildberries component services.
 *
 * @since 1.0.0
 */
return new class implements ServiceProviderInterface {
	/**
	 * Register the component in the Joomla dependency injection container.
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
			|| !class_exists(WildberriesToRadicalMartProductIdSyncServiceProvider::class))
		{
			throw new RuntimeException(
				'The Wishbox RadicalMart Wildberries library must be installed before using this component.'
			);
		}

		if (!ComponentHelper::isEnabled('com_wishboxwb'))
		{
			throw new RuntimeException(
				'The Wishbox Wildberries component must be installed before using this component.'
			);
		}

		$container->registerServiceProvider(new RadicalMartToWildberriesStockSyncServiceProvider());
		$container->registerServiceProvider(new WildberriesToRadicalMartProductIdSyncServiceProvider());

		$container->registerServiceProvider(
			new MVCFactory('\\Joomla\\Component\\WishboxRadicalMartWb')
		);
		$container->registerServiceProvider(
			new ComponentDispatcherFactory('\\Joomla\\Component\\WishboxRadicalMartWb')
		);

		$container->set(
			ComponentInterface::class,
			static function (Container $container): ComponentInterface {
				$wishboxRadicalMartWbComponent = new WishboxRadicalMartWbComponent(
					$container->get(ComponentDispatcherFactoryInterface::class)
				);
				$wishboxRadicalMartWbComponent->setMVCFactory(
					$container->get(MVCFactoryInterface::class)
				);
				$wishboxRadicalMartWbComponent->setRadicalMartToWildberriesStockSyncService(
					$container->get(RadicalMartToWildberriesStockSyncService::class)
				);
				$wishboxRadicalMartWbComponent->setWildberriesToRadicalMartProductIdSyncService(
					$container->get(WildberriesToRadicalMartProductIdSyncService::class)
				);

				return $wishboxRadicalMartWbComponent;
			}
		);
	}
};
