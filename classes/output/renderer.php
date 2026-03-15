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
 * Renderer for the local_pathwaynav plugin.
 *
 * @package   local_pathwaynav
 * @copyright 2025 Your Company
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pathwaynav\output;

use plugin_renderer_base;

/**
 * Renderer class for local_pathwaynav.
 *
 * @package   local_pathwaynav
 * @copyright 2025 Your Company
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Render the progress bar.
     *
     * @param progress_bar $progressbar The progress bar renderable.
     * @return string HTML output.
     */
    protected function render_progress_bar(progress_bar $progressbar): string {
        $data = $progressbar->export_for_template($this);
        return $this->render_from_template('local_pathwaynav/progress_bar', $data);
    }

    /**
     * Render the activity sidebar drawer.
     *
     * @param activity_sidebar $sidebar The sidebar renderable.
     * @return string HTML output.
     */
    protected function render_activity_sidebar(activity_sidebar $sidebar): string {
        $data = $sidebar->export_for_template($this);
        return $this->render_from_template('local_pathwaynav/activity_sidebar', $data);
    }
}
