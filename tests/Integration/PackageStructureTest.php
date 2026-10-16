<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

/**
 * Verifies the installable package structure and migration cleanup.
 *
 * @since 1.0.0
 */
final class PackageStructureTest extends TestCase
{
	private string $projectRoot;

	/**
	 * Resolve the package project root.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	protected function setUp(): void
	{
		$this->projectRoot = dirname(__DIR__, 2);
	}

	/**
	 * Ensure all required extensions are included in the package manifest.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function testPackageContainsAllExtensions(): void
	{
		$manifest = $this->read('pkg_radicalmart_wishboxwb.xml');

		self::assertStringContainsString('lib_wishboxradicalmartwb.zip', $manifest);
		self::assertStringContainsString('com_wishboxradicalmartwb.zip', $manifest);
		self::assertStringContainsString('plg_radicalmart_wishboxwb.zip', $manifest);
		self::assertStringContainsString('plg_task_wishboxradicalmartwb.zip', $manifest);
	}

	/**
	 * Ensure the RadicalMart component contains only its Wildberries mapping setting.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function testComponentContainsOnlyChrtIdFieldSetting(): void
	{
		$config = simplexml_load_string(
			$this->read('com_wishboxradicalmartwb/administrator/config.xml')
		);

		self::assertNotFalse($config);

		$fieldNames = [];

		foreach ($config->xpath('//field') ?: [] as $field)
		{
			if ((string) $field['type'] !== 'rules')
			{
				$fieldNames[] = (string) $field['name'];
			}
		}

		self::assertSame(['wildberries_chrt_id_field'], $fieldNames);
	}

	/**
	 * Ensure the component exposes only its dashboard view.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function testComponentContainsOnlyDashboardView(): void
	{
		$viewDirectories = glob(
			$this->projectRoot . '/com_wishboxradicalmartwb/administrator/src/View/*',
			GLOB_ONLYDIR
		);

		self::assertIsArray($viewDirectories);
		self::assertSame(['Dashboard'], array_map('basename', $viewDirectories));
	}

	/**
	 * Ensure all required RadicalMart toolbar actions are registered.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function testToolbarContainsTwoWildberriesStockActions(): void
	{
		$plugin = $this->read('plg_radicalmart_wishboxwb/src/Extension/WishboxWb.php');
		$provider = $this->read('plg_radicalmart_wishboxwb/services/provider.php');

		self::assertStringContainsString("'products.updatestocks'", $plugin);
		self::assertStringContainsString("'products.updateallstocks'", $plugin);
		self::assertStringContainsString("'onBeforeCompileHead' => 'onBeforeCompileHead'", $plugin);
		self::assertStringContainsString("->useScript('plg_radicalmart_wishboxwb.script')", $plugin);
		self::assertStringContainsString('instanceof AdministratorApplication', $plugin);
		self::assertStringContainsString(
			"->addExtensionRegistryFile('plg_radicalmart_wishboxwb')",
			$provider
		);
	}

	/**
	 * Ensure all RadicalMart Wildberries scheduler tasks are registered.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function testTaskPluginContainsTwoSchedulerTasks(): void
	{
		$plugin = $this->read(
			'plg_task_wishboxradicalmartwb/src/Extension/WishboxRadicalMartWb.php'
		);
		$manifest = $this->read(
			'plg_task_wishboxradicalmartwb/wishboxradicalmartwb.xml'
		);

		self::assertStringContainsString(
			'plg_task_wishboxradicalmartwb_update_stocks',
			$plugin
		);
		self::assertStringContainsString(
			'plg_task_wishboxradicalmartwb_import_new_orders',
			$plugin
		);
		self::assertStringContainsString('<folder>forms</folder>', $manifest);
		self::assertFileExists(
			$this->projectRoot . '/plg_task_wishboxradicalmartwb/forms/import_new_orders.xml'
		);
	}

	/**
	 * Ensure all installer scripts require Joomla 6.1.4.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function testInstallersRequireJoomla614(): void
	{
		self::assertStringContainsString("MINIMUM_JOOMLA = '6.1.4'", $this->read('script.php'));
		self::assertStringContainsString(
			"MINIMUM_JOOMLA = '6.1.4'",
			$this->read('plg_radicalmart_wishboxwb/script.php')
		);
		self::assertStringContainsString(
			"MINIMUM_JOOMLA = '6.1.4'",
			$this->read('plg_task_wishboxradicalmartwb/script.php')
		);
	}

	/**
	 * Read a project file.
	 *
	 * @param   string  $relativePath  Project-relative file path.
	 *
	 * @return string File contents.
	 *
	 * @since 1.0.0
	 */
	private function read(string $relativePath): string
	{
		$contents = file_get_contents($this->projectRoot . '/' . $relativePath);

		self::assertNotFalse($contents);

		return $contents;
	}
}
