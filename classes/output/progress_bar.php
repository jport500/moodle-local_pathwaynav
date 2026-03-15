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
 * Progress bar renderable for local_pathwaynav.
 *
 * Builds the data for the horizontal course progress/navigation bar
 * that appears on activity pages within Pathway-formatted courses.
 *
 * @package   local_pathwaynav
 * @copyright 2025 Your Company
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pathwaynav\output;

use renderable;
use templatable;
use renderer_base;
use stdClass;
use completion_info;
use core_courseformat\base as course_format;

/**
 * Progress bar output class.
 *
 * @package   local_pathwaynav
 * @copyright 2025 Your Company
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class progress_bar implements renderable, templatable {

    /** @var stdClass The course object. */
    protected stdClass $course;

    /** @var course_format The course format instance. */
    protected course_format $format;

    /**
     * Constructor.
     *
     * @param stdClass $course The course record.
     * @param course_format $format The course format instance.
     */
    public function __construct(stdClass $course, course_format $format) {
        $this->course = $course;
        $this->format = $format;
    }

    /**
     * Export data for the mustache template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass Template data.
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $PAGE;

        $course = $this->course;
        $format = $this->format;
        $modinfo = get_fast_modinfo($course);
        $completioninfo = new completion_info($course);
        $sections = $modinfo->get_section_info_all();

        $formatoptions = $format->get_format_options();
        $showsection0 = !empty($formatoptions['pathwayshowsection0']);

        // Determine which section the current activity belongs to.
        $currentsectionnum = $this->get_current_section_num($modinfo);

        // Build section step data.
        $steps = [];
        $overalltotal = 0;
        $overallcomplete = 0;

        foreach ($sections as $section) {
            if (!$section->uservisible) {
                continue;
            }
            if ($section->section == 0 && !$showsection0) {
                continue;
            }

            [$sectioncomplete, $sectiontotal] = $this->calculate_section_completion(
                $modinfo, $completioninfo, $section
            );
            $overalltotal += $sectiontotal;
            $overallcomplete += $sectioncomplete;

            $iscomplete = ($sectiontotal > 0 && $sectioncomplete === $sectiontotal);
            $isinprogress = ($sectioncomplete > 0 && !$iscomplete);
            $iscurrent = ($currentsectionnum == $section->section);

            $sectionurl = $format->get_view_url($section);

            // Determine the status class for the step.
            $status = 'notstarted';
            if ($iscomplete) {
                $status = 'complete';
            } else if ($isinprogress) {
                $status = 'inprogress';
            } else if ($iscurrent) {
                $status = 'current';
            }

            $steps[] = [
                'num' => $section->section,
                'name' => $format->get_section_name($section),
                'shortname' => $this->get_short_name($format->get_section_name($section)),
                'url' => $sectionurl ? $sectionurl->out(false) : '#',
                'iscurrent' => $iscurrent,
                'iscomplete' => $iscomplete,
                'isinprogress' => $isinprogress,
                'status' => $status,
                'issection0' => ($section->section == 0),
                'completedcount' => $sectioncomplete,
                'totalcount' => $sectiontotal,
                'hastrackeditems' => ($sectiontotal > 0),
            ];
        }

        $overallpct = ($overalltotal > 0) ? round(($overallcomplete / $overalltotal) * 100) : 0;

        // Build back-to-section link.
        $backtosectionurl = null;
        $backtosectionname = null;
        if ($currentsectionnum !== null && isset($sections[$currentsectionnum])) {
            $url = $format->get_view_url($sections[$currentsectionnum]);
            if ($url) {
                $backtosectionurl = $url->out(false);
                $backtosectionname = $format->get_section_name($sections[$currentsectionnum]);
            }
        }

        $data = new stdClass();
        $data->coursename = format_string($course->fullname);
        $data->steps = $steps;
        $data->overallpct = $overallpct;
        $data->completionenabled = $completioninfo->is_enabled();
        $data->hasbacklink = !empty($backtosectionurl);
        $data->backtosectionurl = $backtosectionurl;
        $data->backtosectionname = $backtosectionname;

        return $data;
    }

    /**
     * Get a shortened version of the section name for the compact bar.
     *
     * @param string $name The full section name.
     * @return string Shortened name (max 20 chars).
     */
    protected function get_short_name(string $name): string {
        if (\core_text::strlen($name) > 20) {
            return \core_text::substr($name, 0, 18) . '...';
        }
        return $name;
    }

    /**
     * Determine which section the current activity belongs to.
     *
     * @param \course_modinfo $modinfo The course modinfo.
     * @return int|null The section number, or null if not determinable.
     */
    protected function get_current_section_num(\course_modinfo $modinfo): ?int {
        global $PAGE;

        try {
            $cm = $PAGE->cm;
            if ($cm) {
                return (int)$cm->sectionnum;
            }
        } catch (\Exception $e) {
            // CM not available on this page.
        }

        return null;
    }

    /**
     * Calculate section completion stats.
     *
     * @param \course_modinfo $modinfo Fast modinfo.
     * @param completion_info $completioninfo Completion info.
     * @param \section_info $section The section.
     * @return array [completed_count, total_count]
     */
    protected function calculate_section_completion(
        \course_modinfo $modinfo,
        completion_info $completioninfo,
        \section_info $section
    ): array {
        $total = 0;
        $complete = 0;

        if (!$completioninfo->is_enabled() || empty($modinfo->sections[$section->section])) {
            return [$complete, $total];
        }

        foreach ($modinfo->sections[$section->section] as $cmid) {
            $cm = $modinfo->cms[$cmid];
            if (!$cm->uservisible) {
                continue;
            }
            if ($completioninfo->is_enabled($cm) == COMPLETION_TRACKING_NONE) {
                continue;
            }

            $total++;
            $data = $completioninfo->get_data($cm);
            if ($data->completionstate == COMPLETION_COMPLETE
                    || $data->completionstate == COMPLETION_COMPLETE_PASS) {
                $complete++;
            }
        }

        return [$complete, $total];
    }
}
