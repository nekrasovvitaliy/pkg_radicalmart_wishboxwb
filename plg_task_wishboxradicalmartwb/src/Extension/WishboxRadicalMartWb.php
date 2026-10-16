<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license     GNU General Public License version 2 or later;
 */

namespace Joomla\Plugin\Task\WishboxRadicalMartWb\Extension;

use Exception;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Event\SubscriberInterface;
use Throwable;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartOrderImportServiceAwareInterface;
use WishboxRadicalMartWbLibrary\Service\WildberriesToRadicalMartOrderImportServiceAwareTrait;
use WishboxRadicalMartWbLibrary\Service\RadicalMartToWildberriesStockSyncServiceAwareInterface;
use WishboxRadicalMartWbLibrary\Service\RadicalMartToWildberriesStockSyncServiceAwareTrait;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Provides scheduled synchronization between RadicalMart and Wildberries.
 *
 * @since 1.0.0
 */
final class WishboxRadicalMartWb extends CMSPlugin implements
	SubscriberInterface,
	RadicalMartToWildberriesStockSyncServiceAwareInterface,
	WildberriesToRadicalMartOrderImportServiceAwareInterface
{
	use TaskPluginTrait;
	use RadicalMartToWildberriesStockSyncServiceAwareTrait;
	use WildberriesToRadicalMartOrderImportServiceAwareTrait;

	protected const array TASKS_MAP = [
		'plg_task_wishboxradicalmartwb_update_stocks' => [
			'langConstPrefix' => 'PLG_TASK_WISHBOXRADICALMARTWB_UPDATE_STOCKS',
			'method'          => 'updateStocks',
		],
		'plg_task_wishboxradicalmartwb_import_new_orders' => [
			'langConstPrefix' => 'PLG_TASK_WISHBOXRADICALMARTWB_IMPORT_NEW_ORDERS',
			'form'            => 'import_new_orders',
			'method'          => 'importNewOrders',
		],
	];

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
			'onTaskOptionsList'    => 'advertiseRoutines',
			'onExecuteTask'        => 'standardRoutineHandler',
			'onContentPrepareForm' => 'enhanceTaskItemForm',
		];
	}

	/**
	 * Publish all linked RadicalMart product stocks to Wildberries.
	 *
	 * @param   ExecuteTaskEvent  $event  Scheduler execution event.
	 *
	 * @return int Scheduler status code.
	 *
	 * @throws Exception When task logging fails.
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private function updateStocks(ExecuteTaskEvent $event): int
	{
		try
		{
			$result = $this->getRadicalMartToWildberriesStockSyncService()
				->updateStocks();

			$this->logTask(
				Text::sprintf(
					'PLG_TASK_WISHBOXRADICALMARTWB_UPDATE_STOCKS_RESULT',
					$result->updated,
					$result->skipped,
					$result->failed
				),
				$result->failed > 0 ? 'warning' : 'info'
			);

			foreach ($result->errors as $message)
			{
				$this->logTask($message, 'error');
			}

			return $result->failed > 0 ? Status::KNOCKOUT : Status::OK;
		}
		catch (Throwable $throwable)
		{
			$this->logTask((string) $throwable, 'error');

			return Status::KNOCKOUT;
		}
	}

	/**
	 * Import new Wildberries FBS orders into RadicalMart.
	 *
	 * @param   ExecuteTaskEvent  $event  Scheduler execution event.
	 *
	 * @return int Scheduler status code.
	 *
	 * @throws Exception When task logging fails.
	 *
	 * @since 1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	private function importNewOrders(ExecuteTaskEvent $event): int
	{
		try
		{
			$params = $event->getArgument('params');
			$limit = (int) ($params->limit ?? $this->params->get('order_import_limit', 100));
			$result = $this->getWildberriesToRadicalMartOrderImportService()
				->importNewOrders($limit);

			$this->logTask(
				Text::sprintf(
					'PLG_TASK_WISHBOXRADICALMARTWB_IMPORT_NEW_ORDERS_RESULT',
					$result->created,
					$result->skipped,
					$result->failed
				),
				$result->failed > 0 ? 'warning' : 'info'
			);

			foreach ($result->errors as $orderId => $message)
			{
				$this->logTask('Wildberries order ' . $orderId . ': ' . $message, 'error');
			}

			return $result->failed > 0 ? Status::KNOCKOUT : Status::OK;
		}
		catch (Throwable $throwable)
		{
			$this->logTask((string) $throwable, 'error');

			return Status::KNOCKOUT;
		}
	}
}
