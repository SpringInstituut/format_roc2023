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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     format_roc2023
 * @category    upgrade
 * @copyright   2025 Peter Meint Heida <info@heidaservices.nl>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_roc2023\output\courseformat\content;

use core_courseformat\base as course_format;
use core_courseformat\output\local\content\section as section_base;
use renderer_base;
use section_info;
use stdClass;

global $CFG;
require_once($CFG->dirroot.'/local/spring_core/springlib.php');
require_once($CFG->dirroot . '/course/format/roc2023/lib.php');

class section extends section_base
{

    public function __construct(course_format $format, section_info $section)
    {
        parent::__construct($format, $section);
    }

    /**
     * Returns the output class template path.
     *
     * This method redirects the default template when the course section is rendered.
     */
    public function get_template_name(\renderer_base $renderer): string
    {

        return 'format_roc2023/local/content/section';
    }

    public function export_for_template(renderer_base $output): stdClass
    {
        global $PAGE, $CFG, $OUTPUT;

        $format = $this->format;
        $course = $format->get_course();
        $section = $this->section;

        $summary = new $this->summaryclass($format, $section);
        $section_numer = $section->section ?? '0';
        $title_section_view = $course->title_section_view;

        if (method_exists($format, 'get_sectionnum')) {
            // For Moodle 4.4 and above.
            $sectionreturnid = $format->get_sectionnum() ?? 0;
        } else {
            // Backward compatibility for Moodle 4.3 and below.
            $sectionreturnid = $format->get_section_number();
        }

        $data = (object)[
            'num' => $section_numer,
            'id' => $section->id,
            'sectionreturnid' => $sectionreturnid,
            'insertafter' => false,
            'summary' => $summary->export_for_template($output),
            'highlightedlabel' => $format->get_section_highlighted_name(),
            'sitehome' => $course->id == SITEID,
            'editing' => $PAGE->user_is_editing(),
            //propios
            'title_section' => $title_section_view,
            'title' => get_section_name($course, $section)
        ];

        $haspartials = [];
        $haspartials['availability'] = $this->add_availability_data($data, $output);
        $haspartials['visibility'] = $this->add_visibility_data($data, $output);
        $haspartials['editor'] = $this->add_editor_data($data, $output);
        $haspartials['header'] = $this->add_header_data($data, $output);
        $haspartials['cm'] = $this->add_cm_data($data, $output);


        // Het Spring Instituut CODE
        foreach($data as $key => &$dataitem) {
            if($key == 'cmlist' ) {

                foreach ($dataitem->cms as $cm_index => &$cmitem) {
                    $tags = get_tags('course_modules', $cmitem->cmitem->id);
                    $files = scandir($CFG->dirroot.'/course/format/roc2023/pix');
                    $spring_icon_exists = FALSE;
                    foreach ($tags as $tag) {
                        if($spring_icon_exists==FALSE) {
                            foreach ($files as $filename) {
                                if (strpos($filename, $tag->name) !== false) {
                                    $spring_icon_exists = $tag->name;
                                    break;
                                }
                            }
                        } else {
                            break;
                        }
                    }
                    if ($spring_icon_exists) {
                        $url = $OUTPUT->image_url($spring_icon_exists, 'format_roc2023');
                        $cmitem->cmitem->cmformat->cmname['activityicon']['purpose'] = 'other';
                        $cmitem->cmitem->cmformat->cmname['activityicon']['icon'] = $url;
                        $cmitem->cmitem->cmformat->cmname['activityicon']['iconclass'] = 'spring-course-icon';
                    }
                }
            }
        }

        $this->add_format_data($data, $haspartials, $output);

        return $data;
    }

    /**
     * @return int
     * Número de la sección
     */
    public function get_section_number()
    {
        return $this->section->section;
    }

}