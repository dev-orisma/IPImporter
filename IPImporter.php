<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\IPImporter;
use Piwik\Menu\MenuMain;
use Piwik\Menu\MenuTop;

/**
 */
class IPImporter extends \Piwik\Plugin
{
    /**
     * @see Piwik\Plugin::getListHooksRegistered
     */
    public function getListHooksRegistered()
    {
        return array(
            'Menu.Reporting.addItems' => 'addIPImporterMenuItems',
        );
    }

    function addIPImporterMenuItems()
    {
        MenuMain::getInstance()->add('IPImporter', '', array('module' => 'IPImporter'), true, 30);
    }
}
