<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings for the local_pathwaynav plugin.
 *
 * @package   local_pathwaynav
 * @copyright 2025 Your Company
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_pathwaynav', new lang_string('pluginname', 'local_pathwaynav'));

    $settings->add(new admin_setting_configselect(
        'local_pathwaynav/navstyle',
        new lang_string('navstyle', 'local_pathwaynav'),
        new lang_string('navstyle_desc', 'local_pathwaynav'),
        'drawer',
        [
            'disabled' => new lang_string('navstyle_disabled', 'local_pathwaynav'),
            'bar' => new lang_string('navstyle_bar', 'local_pathwaynav'),
            'drawer' => new lang_string('navstyle_drawer', 'local_pathwaynav'),
        ]
    ));

    $ADMIN->add('localplugins', $settings);
}
