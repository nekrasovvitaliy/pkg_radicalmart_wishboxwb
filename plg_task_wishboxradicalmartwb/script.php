<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

defined('_JEXEC') or die;

/**
 * Registers the task plugin installer script.
 *
 * @since 1.0.0
 */
return new class implements ServiceProviderInterface {
	/**
	 * Register the installer script service.
	 *
	 * @param   Container  $container  Joomla dependency container.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function register(Container $container): void
	{
		$container->set(
			InstallerScriptInterface::class,
			new class(
				$container->get(AdministratorApplication::class),
				$container->get(DatabaseInterface::class)
			) implements InstallerScriptInterface {
				private const string MINIMUM_JOOMLA = '6.1.4';

				private const string MINIMUM_PHP = '8.5';

				/**
				 * Initialize the task plugin installer.
				 *
				 * @param   AdministratorApplication  $app  Joomla administrator application.
				 * @param   DatabaseInterface         $db   Joomla database connection.
				 *
				 * @return void
				 *
				 * @since 1.0.0
				 */
				public function __construct(
					private readonly AdministratorApplication $app,
					private readonly DatabaseInterface $db
				)
				{
				}

				/**
				 * Enable the plugin after installation.
				 *
				 * @param   InstallerAdapter  $adapter  Active installer adapter.
				 *
				 * @return bool True when installation handling succeeds.
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function install(InstallerAdapter $adapter): bool
				{
					$this->enablePlugin($adapter);

					return true;
				}

				/**
				 * Handle a plugin update.
				 *
				 * @param   InstallerAdapter  $adapter  Active installer adapter.
				 *
				 * @return bool True when update handling succeeds.
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function update(InstallerAdapter $adapter): bool
				{
					return true;
				}

				/**
				 * Handle plugin removal.
				 *
				 * @param   InstallerAdapter  $adapter  Active installer adapter.
				 *
				 * @return bool True when uninstall handling succeeds.
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function uninstall(InstallerAdapter $adapter): bool
				{
					return true;
				}

				/**
				 * Validate Joomla and PHP compatibility.
				 *
				 * @param   string            $type     Installer operation type.
				 * @param   InstallerAdapter  $adapter  Active installer adapter.
				 *
				 * @return bool True when the environment is compatible.
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function preflight(string $type, InstallerAdapter $adapter): bool
				{
					if ($type === 'uninstall')
					{
						return true;
					}

					$version = new Version;

					if (!$version->isCompatible(self::MINIMUM_JOOMLA))
					{
						$this->app->enqueueMessage(
							Text::sprintf(
								'PLG_TASK_WISHBOXRADICALMARTWB_ERROR_COMPATIBLE_JOOMLA',
								self::MINIMUM_JOOMLA
							),
							'error'
						);

						return false;
					}

					if (version_compare(PHP_VERSION, self::MINIMUM_PHP, '<'))
					{
						$this->app->enqueueMessage(
							Text::sprintf(
								'PLG_TASK_WISHBOXRADICALMARTWB_ERROR_COMPATIBLE_PHP',
								self::MINIMUM_PHP
							),
							'error'
						);

						return false;
					}

					return true;
				}

				/**
				 * Complete an installer operation.
				 *
				 * @param   string            $type     Installer operation type.
				 * @param   InstallerAdapter  $adapter  Active installer adapter.
				 *
				 * @return bool True when post-install handling succeeds.
				 *
				 * @since 1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function postflight(string $type, InstallerAdapter $adapter): bool
				{
					return true;
				}

				/**
				 * Enable the installed plugin record.
				 *
				 * @param   InstallerAdapter  $adapter  Active installer adapter.
				 *
				 * @return void
				 *
				 * @since 1.0.0
				 */
				private function enablePlugin(InstallerAdapter $adapter): void
				{
					$parent = $adapter->getParent();
					$plugin = (object) [
						'type'    => 'plugin',
						'element' => $adapter->getElement(),
						'folder'  => (string) $parent->manifest->attributes()['group'],
						'enabled' => 1,
					];

					$this->db->updateObject('#__extensions', $plugin, ['type', 'element', 'folder']);
				}
			}
		);
	}
};
