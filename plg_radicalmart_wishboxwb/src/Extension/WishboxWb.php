<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace Joomla\Plugin\RadicalMart\WishboxWb\Extension;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Event\Application\BeforeCompileHeadEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Toolbar\Button\CustomButton;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Adds RadicalMart-to-Wildberries stock synchronization actions.
 *
 * @since 1.0.0
 *
 * @noinspection PhpUnused
 */
final class WishboxWb extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var bool
	 *
	 * @since 1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Return the Joomla events handled by this plugin.
	 *
	 * @return array<string, string> Event handler map.
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onBeforeCompileHead' => 'onBeforeCompileHead',
			'onBeforeRender'      => 'onBeforeRender',
		];
	}

	/**
	 * Enable toolbar assets before Joomla compiles the document head.
	 *
	 * @param BeforeCompileHeadEvent $event Joomla before-compile-head event.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function onBeforeCompileHead(BeforeCompileHeadEvent $event): void
	{
		$app = $event->getApplication();

		if (!$app instanceof AdministratorApplication || !$this->isProductsView($app))
		{
			return;
		}

		/** @var HtmlDocument $document */
		$document = $app->getDocument();
		$document->getWebAssetManager()
			->useScript('core')
			->useScript('webcomponent.toolbar-button')
			->useScript('plg_radicalmart_wishboxwb.script');
	}

	/**
	 * Add Wildberries stock actions to the RadicalMart products toolbar.
	 *
	 * @param   Event  $event  Joomla before-render event.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function onBeforeRender(Event $event): void
	{
		$app = $this->getApplication();

		if (!$app instanceof AdministratorApplication || !$this->isProductsView($app))
		{
			return;
		}

		/** @var HtmlDocument $document */
		$document = $app->getDocument();

		/** @var Toolbar $toolbar */
		$toolbar = $document->getToolbar();

		$container = Factory::getContainer();
		$toolbarFactory = $container->get(ToolbarFactoryInterface::class);

		$this->appendToolbarButton(
			$toolbar,
			$toolbarFactory,
			'wishboxradicalmartwb-get-ids',
			'products.getids',
			'PLG_RADICALMART_WISHBOXWB_TOOLBAR_GET_IDS'
		);
		$this->appendToolbarButton(
			$toolbar,
			$toolbarFactory,
			'wishboxradicalmartwb-update-stocks',
			'products.updatestocks',
			'PLG_RADICALMART_WISHBOXWB_TOOLBAR_UPDATE_STOCKS',
			true,
			true
		);
		$this->appendToolbarButton(
			$toolbar,
			$toolbarFactory,
			'wishboxradicalmartwb-update-all-stocks',
			'products.updateallstocks',
			'PLG_RADICALMART_WISHBOXWB_TOOLBAR_UPDATE_ALL_STOCKS'
		);
	}

	/**
	 * Append a synchronization action to the RadicalMart toolbar.
	 *
	 * @param   Toolbar                  $toolbar          RadicalMart toolbar instance.
	 * @param   ToolbarFactoryInterface  $toolbarFactory   Joomla toolbar factory.
	 * @param   string                   $name             Unique action name.
	 * @param   string                   $task             Component controller task.
	 * @param   string                   $title            Button language key.
	 * @param   bool                     $listSelection    Whether selection is required.
	 * @param   bool                     $copySelection    Whether selected IDs are submitted.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private function appendToolbarButton(
		Toolbar                 $toolbar,
		ToolbarFactoryInterface $toolbarFactory,
		string                  $name,
		string                  $task,
		string                  $title,
		bool                    $listSelection = false,
		bool                    $copySelection = false
	): void
	{
		$buttonHtml = LayoutHelper::render(
			'plugins.radicalmart.wishboxwb.toolbar.action',
			[
				'buttonId'      => 'toolbar-' . $name,
				'formId'        => $name . '-form',
				'task'          => $task,
				'title'         => $title,
				'component'     => 'com_wishboxradicalmartwb',
				'redirectUrl'   => 'index.php?option=com_radicalmart&view=products',
				'listSelection' => $listSelection,
				'copySelection' => $copySelection,
			]
		);

		/** @var CustomButton $button */
		$button = $toolbarFactory->createButton($toolbar, 'Custom')
			->name($name)
			->text('')
			->task('');

		$button->html($buttonHtml);
		$toolbar->appendButton($button);
	}

	/**
	 * Check whether the current request targets the authorized RadicalMart products view.
	 *
	 * @param   AdministratorApplication  $app  Joomla administrator application.
	 *
	 * @return bool True for the supported products view.
	 *
	 * @since 1.0.0
	 */
	private function isProductsView(AdministratorApplication $app): bool
	{
		$identity = $app->getIdentity();

		return $app->isClient('administrator')
			&& $identity->authorise('core.manage', 'com_wishboxradicalmartwb')
			&& $app->getInput()->getCmd('option', '') === 'com_radicalmart'
			&& $app->getInput()->getCmd('view', '') === 'products';
	}
}
