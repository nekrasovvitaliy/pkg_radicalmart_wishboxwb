<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;

defined('_JEXEC') or die;

/**
 * Register the plugin installer script.
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
	 * @since        1.0.0
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
				 * Initialize the plugin installer.
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
					private readonly DatabaseInterface        $db,
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
				 * @since        1.0.0
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
				 * @since        1.0.0
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
				 * @since        1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function uninstall(InstallerAdapter $adapter): bool
				{
					return true;
				}

				/**
				 * Validate Joomla and PHP compatibility before installation or update.
				 *
				 * @param   string            $type     Installer operation type.
				 * @param   InstallerAdapter  $adapter  Active installer adapter.
				 *
				 * @return bool True when the environment is compatible.
				 *
				 * @since        1.0.0
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
								'PLG_RADICALMART_WISHBOXWB_ERROR_COMPATIBLE_JOOMLA',
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
								'PLG_RADICALMART_WISHBOXWB_ERROR_COMPATIBLE_PHP',
								self::MINIMUM_PHP
							),
							'error'
						);

						return false;
					}

					return true;
				}

				/**
				 * Complete the installer lifecycle without downloading third-party code.
				 *
				 * @param   string            $type     Installer operation type.
				 * @param   InstallerAdapter  $adapter  Active installer adapter.
				 *
				 * @return bool True when post-install handling succeeds.
				 *
				 * @since        1.0.0
				 *
				 * @noinspection PhpUnused
				 */
				public function postflight(string $type, InstallerAdapter $adapter): bool
				{
					$installer = $adapter->getParent();

					if ($type !== 'uninstall')
					{
						// Parse layouts
						$this->parseLayouts($installer->getManifest()->layouts, $installer);
					}
					else
					{
						// Remove layouts
						$this->removeLayouts($installer->getManifest()->layouts);
					}

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

				/**
				 * Method to parse through a layouts' element of the installation manifest and take appropriate action.
				 *
				 * @param   SimpleXMLElement|null  $element    The XML node to process.
				 * @param   Installer|null         $installer  Installer calling object.
				 *
				 * @return  boolean  True on success.
				 *
				 * @since  1.0.0
				 */
				public function parseLayouts(?SimpleXMLElement $element = null, ?Installer $installer = null): bool
				{
					if (!$element || !count($element->children()))
					{
						return false;
					}

					// Get destination
					$folder      = ((string) $element->attributes()->destination) ? '/' . $element->attributes()->destination : null;
					$destination = Path::clean(JPATH_ROOT . '/layouts' . $folder);

					// Get source
					$folder = (string) $element->attributes()->folder;
					$source = ($folder && file_exists($installer->getPath('source') . '/' . $folder))
						? $installer->getPath('source') . '/' . $folder : $installer->getPath('source');

					// Prepare files
					$copyFiles = [];

					foreach ($element->children() as $file)
					{
						$path['src']  = Path::clean($source . '/' . $file);
						$path['dest'] = Path::clean($destination . '/' . $file);

						// Is this path a file or folder?
						$path['type'] = $file->getName() === 'folder' ? 'folder' : 'file';

						if (basename($path['dest']) !== $path['dest'])
						{
							$newdir = dirname($path['dest']);

							if (!Folder::create($newdir))
							{
								Log::add(
									Text::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newdir),
									Log::WARNING,
									'jerror'
								);

								return false;
							}
						}

						$copyFiles[] = $path;
					}

					return $installer->copyFiles($copyFiles, true);
				}

				/**
				 * Method to parse through a layout's element of the installation manifest and remove the files that were installed.
				 *
				 * @param   SimpleXMLElement|null  $element  The XML node to process.
				 *
				 * @return  boolean  True on success.
				 *
				 * @since 1.0.0
				 */
				protected function removeLayouts(?SimpleXMLElement $element = null): bool
				{
					if (!$element || !count($element->children()))
					{
						return false;
					}

					// Get the array of file nodes to process
					$files = $element->children();

					// Get source
					$folder = ((string) $element->attributes()->destination) ? '/' . $element->attributes()->destination : null;
					$source = Path::clean(JPATH_ROOT . '/layouts' . $folder);

					// Process each file in the $files array (children of $tagName).
					foreach ($files as $file)
					{
						$path = Path::clean($source . '/' . $file);

						// Actually delete the files/folders
						if (is_dir($path))
						{
							$val = Folder::delete($path);
						}
						else
						{
							$val = File::delete($path);
						}

						if ($val === false)
						{
							Log::add('Failed to delete ' . $path, Log::WARNING, 'jerror');

							return false;
						}
					}

					if (!empty($folder))
					{
						Folder::delete($source);
					}

					return true;
				}
			}
		);
	}
};
