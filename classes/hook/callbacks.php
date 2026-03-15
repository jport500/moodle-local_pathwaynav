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
 * Hook callback for injecting activity navigation.
 *
 * @package   local_pathwaynav
 * @copyright 2025 Your Company
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pathwaynav\hook;

use core\hook\output\before_standard_top_of_body_html_generation;

/**
 * Hook callbacks for local_pathwaynav.
 *
 * @package   local_pathwaynav
 * @copyright 2025 Your Company
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class callbacks {

    /**
     * Inject activity navigation on activity pages within Pathway courses.
     *
     * Checks the configured navigation style and renders either the
     * progress bar, the slide-out drawer, or nothing.
     *
     * @param before_standard_top_of_body_html_generation $hook The hook instance.
     */
    public static function inject_navigation(before_standard_top_of_body_html_generation $hook): void {
        global $PAGE, $COURSE;

        // Check which navigation style is configured.
        $navstyle = get_config('local_pathwaynav', 'navstyle');
        if (empty($navstyle) || $navstyle === 'disabled') {
            return;
        }

        // Quick bail-out: only act on module pages within a real course.
        if (!isset($COURSE->id) || $COURSE->id <= 1) {
            return;
        }

        // Only inject on activity/module pages.
        $pagetype = $PAGE->pagetype ?? '';
        if (strpos($pagetype, 'mod-') !== 0) {
            return;
        }

        // Check if the course uses the Pathway format.
        $format = course_get_format($COURSE);
        if ($format->get_format() !== 'pathway') {
            return;
        }

        // Render the appropriate navigation style.
        try {
            $renderer = $PAGE->get_renderer('local_pathwaynav');

            if ($navstyle === 'bar') {
                $widget = new \local_pathwaynav\output\progress_bar($COURSE, $format);
                $html = $renderer->render($widget);
                $PAGE->requires->js_call_amd('local_pathwaynav/progress_bar', 'init');
            } else if ($navstyle === 'drawer') {
                $widget = new \local_pathwaynav\output\activity_sidebar($COURSE, $format);
                $html = $renderer->render($widget);
                $PAGE->requires->js_call_amd('local_pathwaynav/activity_sidebar', 'init');
            } else {
                return;
            }

            $hook->add_html($html);
        } catch (\Exception $e) {
            debugging('local_pathwaynav: Failed to render navigation: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
