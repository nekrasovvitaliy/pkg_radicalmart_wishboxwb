<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace Joomla\Component\WishboxRadicalMartWb\Administrator\View\Dashboard;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Renders the RadicalMart Wildberries dashboard.
 *
 * @since 1.0.0
 */
final class HtmlView extends BaseHtmlView
{
	/**
	 * Whether the current user can edit component options.
	 *
	 * @var bool
	 *
	 * @since 1.0.0
	 */
	public bool $canConfigure = false;

	/**
	 * Display the dashboard.
	 *
	 * @param   string|null  $tpl  Template name.
	 *
	 * @return void
	 *
	 * @throws Exception When the application cannot be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function display($tpl = null): void
	{
		$app = Factory::getApplication();
		$identity = $app->getIdentity();

		$this->canConfigure = $identity->authorise('core.admin', 'com_wishboxradicalmartwb')
			|| $identity->authorise('core.options', 'com_wishboxradicalmartwb');

		ToolbarHelper::title(Text::_('COM_WISHBOXRADICALMARTWB_DASHBOARD_TITLE'), 'refresh');

		if ($this->canConfigure)
		{
			ToolbarHelper::preferences('com_wishboxradicalmartwb');
		}

		parent::display($tpl);
	}
}
