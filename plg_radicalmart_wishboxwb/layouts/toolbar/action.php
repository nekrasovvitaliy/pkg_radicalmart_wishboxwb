<?php
/**
 * @copyright   (c) 2013-2026 Nekrasov Vitaliy <nekrasov_vitaliy@list.ru>
 * @license         GNU General Public License version 2 or later;
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * @var array{
 *     buttonId: string,
 *     formId: string,
 *     task: string,
 *     title: string,
 *     component: string,
 *     redirectUrl: string,
 *     listSelection?: bool,
 *     copySelection?: bool
 * } $displayData
 */
$buttonId      = htmlspecialchars($displayData['buttonId'], ENT_QUOTES, 'UTF-8');
$formId        = htmlspecialchars($displayData['formId'], ENT_QUOTES, 'UTF-8');
$task          = htmlspecialchars($displayData['task'], ENT_QUOTES, 'UTF-8');
$component     = htmlspecialchars($displayData['component'], ENT_QUOTES, 'UTF-8');
$redirectUrl   = htmlspecialchars($displayData['redirectUrl'], ENT_QUOTES, 'UTF-8');
$listSelection = (bool) ($displayData['listSelection'] ?? false);
$copySelection = (bool) ($displayData['copySelection'] ?? false);
$title         = Text::_($displayData['title']);

?>
<joomla-toolbar-button<?php echo $listSelection ? ' list-selection' : ''; ?> id="<?php echo $buttonId; ?>">
    <button
            type="button"
            class="btn btn-primary"
            data-radicalmart-wishboxwb-form="<?php echo $formId; ?>"
            <?php echo $copySelection ? 'data-radicalmart-wishboxwb-copy-selection' : ''; ?>
    >
        <span class="icon-refresh" aria-hidden="true"></span>
        <?php echo $title; ?>
    </button>
</joomla-toolbar-button>
<form
        action="<?php echo Route::_('index.php?option=' . $component); ?>"
        method="post"
        id="<?php echo $formId; ?>"
>
    <input type="hidden" name="task" value="<?php echo $task; ?>">
    <input type="hidden" name="redirect_url" value="<?php echo $redirectUrl; ?>">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
