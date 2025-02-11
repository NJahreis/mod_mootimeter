<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     mod_mootimeter
 * @category    upgrade
 * @copyright   2023, ISB Bayern
 * @author      Peter Mayer <peter.mayer@isb.bayern.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/upgradelib.php');

/**
 * Execute mod_mootimeter upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_mootimeter_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at {@link https://docs.moodle.org/dev/XMLDB_editor}.

    if ($oldversion < 2023020905) {

        mootimetertool_wordcloud_create_tables();

        // Mootimeter savepoint reached.
        upgrade_mod_savepoint(true, 2023020905, 'mootimeter');
    }

    if ($oldversion < 2023020908) {

        mod_mootimeter_create_mootimeter_tool_settings_table();

        // Mootimeter savepoint reached.
        upgrade_mod_savepoint(true, 2023020908, 'mootimeter');
    }

    if ($oldversion < 2023020912) {

        // Define field question to be dropped from mootimeter_pages.
        $table = new xmldb_table('mootimeter_pages');
        $field = new xmldb_field('description');

        // Conditionally launch drop field question.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Create new field question
        $field = new xmldb_field('question', XMLDB_TYPE_TEXT, null, null, null, null, null, 'title');

        // Conditionally launch add field question.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mootimeter savepoint reached.
        upgrade_mod_savepoint(true, 2023020912, 'mootimeter');
    }


    return true;
}
