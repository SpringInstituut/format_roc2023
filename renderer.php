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
 * format_roc2023_renderer
 *
 * @package    format_roc2023
 * @author     Peter Meint Heida
 * @copyright  2024 Peter Meint Heida
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/spring_core/springlib.php');
require_once($CFG->dirroot.'/course/format/topics/renderer.php');

/**
 * Format_roc2023_renderer
 *
 * @package    format_roc2023
 * @author     Peter Meint Heida
 * @copyright  2024 Peter Meint Heida
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_roc2023_renderer extends format_topics_renderer
{

    /**
     * Get_button_section
     *
     * @param stdclass $course
     * @param string $name
     * @return string
     */
    protected function get_color_config($course, $name) {
        $return = false;
        if (isset($course->{$name})) {
            $color = str_replace('#', '', $course->{$name});
            $color = substr($color, 0, 6);
            if (preg_match('/^#?[a-f0-9]{6}$/i', $color)) {
                $return = '#'.$color;
            }
        }
        return $return;
    }

    /**
     * Number_to_roman
     *
     * @param integer $number
     * @return string
     */
    protected function number_to_roman($number) {
        $number = intval($number);
        $return = '';
        $romanarray = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];
        foreach ($romanarray as $roman => $value) {
            $matches = intval($number / $value);
            $return .= str_repeat($roman, $matches);
            $number = $number % $value;
        }
        return $return;
    }

    /**
     * Number_to_alphabet
     *
     * @param integer $number
     * @return string
     */
    protected function number_to_alphabet($number) {
        $number = $number - 1;
        $alphabet = range("A", "Z");
        if ($number <= 25) {
            return $alphabet[$number];
        } else if ($number > 25) {
            $dividend = ($number + 1);
            $alpha = '';
            while ($dividend > 0) {
                $modulo = ($dividend - 1) % 26;
                $alpha = $alphabet[$modulo] . $alpha;
                $dividend = floor((($dividend - $modulo) / 26));
            }
            return $alpha;
        }
    }

    /**
     * Get_button_section
     *
     * @param stdclass $course
     * @param string $sectionvisible
     * @return string
     */
    protected function get_button_section($course, $sectionvisible) {
        global $PAGE;
        $html = '';
        $css = '';
        if ($colorcurrent = $this->get_color_config($course, 'colorcurrent')) {
            $css .=
            '#buttonsection_roc2023container .buttonsection_roc2023.current {
                background: ' . $colorcurrent . ';
            }
            ';
        }
        if ($colorvisible = $this->get_color_config($course, 'colorvisible')) {
            $css .=
            '#buttonsection_roc2023container .buttonsection_roc2023.sectionvisible {
                background: ' . $colorvisible . ';
            }
            ';
        }
        if ($css) {
            $html .= html_writer::tag('style', $css);
        }
        $withoutdivisor = true;
        for ($k = 1; $k <= 12; $k++) {
            if ($course->{'divisor' . $k}) {
                $withoutdivisor = false;
            }
        }
        if ($withoutdivisor) {
            $course->divisor1 = 999;
        }
        $divisorshow = false;
        $count = 1;
        $currentdivisor = 1;
        $modinfo = get_fast_modinfo($course);
        $inline = '';
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                continue;
            }
            if ($section > $course->numsections) {
                continue;
            }
            if ($course->hiddensections && !(int)$thissection->visible) {
                continue;
            }
            if (isset($course->{'divisor' . $currentdivisor}) &&
                $count > $course->{'divisor' . $currentdivisor}) {
                $currentdivisor++;
                $count = 1;
            }
            if (isset($course->{'divisor' . $currentdivisor}) &&
                $course->{'divisor' . $currentdivisor} != 0 &&
                !isset($divisorshow[$currentdivisor])) {
                $currentdivisorhtml = format_string($course->{'divisortext' . $currentdivisor});
                $currentdivisorhtml = str_replace('[br]', '<br>', $currentdivisorhtml);
                $currentdivisorhtml = html_writer::tag('div', $currentdivisorhtml, ['class' => 'divisortext']);
                if ($course->inlinesections) {
                    $inline = 'inlinebuttonsections_roc2023';
                }
                $html .= html_writer::tag('div', $currentdivisorhtml, ['class' => "divisorsection $inline"]);
                $divisorshow[$currentdivisor] = true;
            }
            $id = 'buttonsection_roc2023-' . $section;
            if ($course->sequential) {
                $name = $section;
            } else {
                if (isset($course->{'divisor' . $currentdivisor}) &&
                $course->{'divisor' . $currentdivisor} == 1) {
                    $name = '&bull;&bull;&bull;';
                } else {
                    $name = $count;
                }
            }
            if ($course->sectiontype == 'alphabet' && is_numeric($name)) {
                $name = $this->number_to_alphabet($name);
            }
            if ($course->sectiontype == 'roman' && is_numeric($name)) {
                $name = $this->number_to_roman($name);
            }
            $class = 'buttonsection_roc2023';
            $onclick = 'M.format_roc2023.show(' . $section . ',' . $course->id . ')';
            if (!$thissection->available &&
                !empty($thissection->availableinfo)) {
                $class .= ' sectionhidden';
            } else if (!$thissection->uservisible || !$thissection->visible) {
                $class .= ' sectionhidden';
                $onclick = false;
            }
            if ($course->marker == $section) {
                $class .= ' current';
            }
            if ($sectionvisible == $section) {
                $class .= ' sectionvisible';
            }
            if ($PAGE->user_is_editing()) {
                $onclick = false;
            }
            $html .= html_writer::tag('div', $name, ['id' => $id, 'class' => $class, 'onclick' => $onclick]);
            $count++;
        }
        $html = html_writer::tag('div', $html, ['id' => 'buttonsection_roc2023container', 'class' => $course->buttonstyle]);
        if ($PAGE->user_is_editing()) {
            $html .= html_writer::tag('div', get_string('editing', 'format_roc2023'), ['class' => 'alert alert-warning alert-block fade in']);
        }
        return $html;
    }

    /**
     * Start_section_list
     *
     * @return string
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', ['class' => 'roc2023']);
    }

    /**
     * Section_header
     *
     * @param stdclass $section
     * @param stdclass $course
     * @param bool $onsectionpage
     * @param int $sectionreturn
     * @return string
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null) {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
             'class' => 'section main clearfix'.$sectionstyle, 'role'=>'region',
             'aria-label'=> get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o.= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }
        $sectionname = html_writer::tag('span', $this->section_title($section, $course));

        // Button format - ini
        if ($course->showdefaultsectionname) {
            $o.= $this->output->heading($sectionname, 3, 'sectionname' . $classes);
        }
        // Button format - end

        $o .= $this->section_availability($section);

        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown in collapsed form".
            $o .= $this->format_summary_text($section);
        }
        $o .= html_writer::end_tag('div');

        return $o;
    }

    /**
     * Print_multiple_section_page
     *
     * @param stdclass $course
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
        global $PAGE;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        $context = context_course::instance($course->id);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);

        // Roc2023 format - ini
        if (isset($_COOKIE['sectionvisible_' . $course->id])) {
            $sectionvisible = $_COOKIE['sectionvisible_' . $course->id];
        } else if ($course->marker > 0) {
            $sectionvisible = $course->marker;
        } else {
            $sectionvisible = 1;
        }
        $htmlsection = false;
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            $htmlsection[$section] = '';
            if ($section == 0) {
                $section0 = $thissection;
                continue;
            }
            if ($section > $course->numsections) {
                continue;
            }
            /* If is not editing verify the rules to display the sections */
            if (!$PAGE->user_is_editing()) {
                if ($course->hiddensections && !(int)$thissection->visible) {
                    continue;
                }
                if (!$thissection->available && !empty($thissection->availableinfo)) {
                    $htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
                    continue;
                }
                if (!$thissection->uservisible || !$thissection->visible) {
                    $htmlsection[$section] .= $this->section_hidden($section, $course->id);
                    continue;
                }
            }
            $htmlsection[$section] .= $this->section_header($thissection, $course, false, 0);
            if ($thissection->uservisible) {
                $htmlsection[$section] .= $this->course_section_cm_list_roc2023($course, $thissection, 0);
///*PMH*/         $htmlsection[$section] .= $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                $htmlsection[$section] .= $this->courserenderer->course_section_add_cm_control($course, $section, 0);
            }
            $htmlsection[$section] .= $this->section_footer();
        }
        if ($section0->summary || !empty($modinfo->sections[0]) || $PAGE->user_is_editing()) {
            $htmlsection0 = $this->section_header($section0, $course, false, 0);
            $htmlsection0 .= $this->courserenderer->course_section_cm_list($course, $section0, 0);
            $htmlsection0 .= $this->courserenderer->course_section_add_cm_control($course, 0, 0);
            $htmlsection0 .= $this->section_footer();
        }
        echo $completioninfo->display_help_icon();
        echo $this->output->heading($this->page_title(), 2, 'accesshide');
        echo $this->course_activity_clipboard($course, 0);
        echo $this->start_section_list();
        if ($course->sectionposition == 0 and isset($htmlsection0)) {
            echo html_writer::tag('span', $htmlsection0, ['class' => 'above']);
        }
        echo $this->get_button_section($course, $sectionvisible);
        foreach ($htmlsection as $current) {
            echo $current;
        }
        if ($course->sectionposition == 1 and isset($htmlsection0)) {
            echo html_writer::tag('span', $htmlsection0, ['class' => 'below']);
        }
        if ($PAGE->user_is_editing() and has_capability('moodle/course:update', $context)) {
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $course->numsections or empty($modinfo->sections[$section])) {
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }
            echo $this->end_section_list();
            echo html_writer::start_tag('div', ['id' => 'changenumsections', 'class' => 'mdl-right']);
            $straddsection = get_string('increasesections', 'moodle');
            $url = new moodle_url('/course/changenumsections.php', ['courseid' => $course->id,
                'increase' => true, 'sesskey' => sesskey()]);
            $icon = $this->output->pix_icon('t/switch_plus', $straddsection);
            echo html_writer::link($url, $icon.get_accesshide($straddsection), ['class' => 'increase-sections']);
            if ($course->numsections > 0) {
                $strremovesection = get_string('reducesections', 'moodle');
                $url = new moodle_url('/course/changenumsections.php', ['courseid' => $course->id,
                    'increase' => false, 'sesskey' => sesskey()]);
                $icon = $this->output->pix_icon('t/switch_minus', $strremovesection);
                echo html_writer::link(
                    $url,
                    $icon.get_accesshide($strremovesection),
                    ['class' => 'reduce-sections']
                );
            }
            echo html_writer::end_tag('div');
        } else {
            echo $this->end_section_list();
        }
        if (!$PAGE->user_is_editing()) {
            $PAGE->requires->js_init_call('M.format_roc2023.init', [$course->numsections, $sectionvisible, $course->id]);
        }
        // Button format - end
    }

    /**
     * Renders HTML to display a list of course modules in a course section
     * Also displays "move here" controls in Javascript-disabled mode.
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This function calls {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param int $displayoptions
     * @return void
     */
    public function course_section_cm_list_roc2023($course, $section, $sectionreturn = null, $displayoptions = []) {
        global $USER;

        $output = '';

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();

        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new completion_info($course);

        // check if we are currently in the process of moving a module with JavaScript disabled
        $ismoving = $format->show_editor() && ismoving($course->id);

        if ($ismoving) {
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one)
        $moduleshtml = [];
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving and $mod->id == $USER->activitycopy) {
                    // do not display moving mod
                    continue;
                }

                if ($modulehtml = $this->course_section_cm_list_item_roc2023($course,
                    $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new moodle_url('/course/mod.php', array('moveto' => $modnumber, 'sesskey' => sesskey()));
                    $sectionoutput .= html_writer::tag('li',
                        html_writer::link($movingurl, '', array('title' => $strmovefull, 'class' => 'movehere')),
                        array('class' => 'movehere'));
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new moodle_url('/course/mod.php', array('movetosection' => $section->id, 'sesskey' => sesskey()));
                $sectionoutput .= html_writer::tag('li',
                    html_writer::link($movingurl, '', array('title' => $strmovefull, 'class' => 'movehere')),
                    array('class' => 'movehere'));
            }
        }

        // Always output the section module list.
        $output .= html_writer::tag('ul', $sectionoutput, array('class' => 'section img-text'));

        return $output;
    }

    /**
     * Renders HTML to display one course module for display within a section.
     *
     * @deprecated since 4.0 - use core_course output components or course_format::course_section_updated_cm_item instead.
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return String
     */
    public function course_section_cm_list_item_roc2023($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = []) {

        debugging(
            'course_section_cm_list_item is deprecated. Use renderer course_section_updated_cm_item instead',
            DEBUG_DEVELOPER
        );

        $output = '';
        if ($modulehtml = $this->course_section_cm_roc2023($course, $completioninfo, $mod, $sectionreturn, $displayoptions)) {
            $modclasses = 'activity ' . $mod->modname . ' modtype_' . $mod->modname . ' ' . $mod->extraclasses;
            $output .= html_writer::tag('li', $modulehtml, array('class' => $modclasses, 'id' => 'module-' . $mod->id));
        }
        return $output;
    }

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_roc2023($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = []) {
        global $CFG, $USER;

        $spring_icon_exists = false;
        $iconname = '';

        if (!$mod->is_visible_on_course_page()) {
            return '';
        }

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();
        // Output renderers works only with real section_info objects.
        if ($sectionreturn) {
            $format->set_section_number($sectionreturn);
        }
        $section = $modinfo->get_section_info($format->get_section_number());

        $cmclass = $format->get_output_classname('content\\cm');
        $cm = new $cmclass($format, $section, $mod, $displayoptions);
        // The course outputs works with format renderers, not with course renderers.
        $renderer = $format->get_renderer($this->page);
        $data = $cm->export_for_template($renderer);

        if ($data->activityinfo->completiondetails[0]->statuscompletefail == 1) {
            switch(strtolower($data->modname)) {
                case 'praktijkscan':
                    if($this->check_praktijkscan_waitingforgrade($mod->instance, $USER->id) == 'waitingforgrade') {
                        $data->spring_completion = 1;
                        $data->activityinfo->completiondetails[0]->statuscompletefail = null;
                        $data->activityinfo->completiondetails[0]->statussubmitted = true;
                        $data->activityinfo->completiondetails[0]->description = 'Wacht op de beoordeling';
                    }
                    break;
                case 'quiz':
                    if($this->check_quiz_waitingforgrade($mod->instance, $USER->id) == 'waitingforgrade') {
                        $data->spring_completion = 1;
                        $data->activityinfo->completiondetails[0]->statuscompletefail = null;
                        $data->activityinfo->completiondetails[0]->statussubmitted = true;
                        $data->activityinfo->completiondetails[0]->description = 'Wacht op de beoordeling';
                    }
                    break;
                case 'lesson':
                    if($this->check_lesson_waitingforgrade($mod->instance, $USER->id) == 'waitingforgrade') {
                        $data->spring_completion = 1;
                        $data->activityinfo->completiondetails[0]->statuscompletefail = null;
                        $data->activityinfo->completiondetails[0]->statussubmitted = true;
                        $data->activityinfo->completiondetails[0]->description = 'Wacht op de beoordeling';
                    }
                    break;
            }
        }

        $tags = get_tags('course_modules', $mod->id);
        $files = scandir($CFG->dirroot.'/course/format/roc2023/pix');
        foreach ($tags as $tag) {
            if($spring_icon_exists==FALSE) {
                foreach ($files as $filename) {
                    if(strpos($filename, $tag->name) !== false) {
                        $spring_icon_exists = $tag->name;
                        break;
                    }
                }
            } else {
                break;
            }
        }
        if ($spring_icon_exists) {
            $url = $this->output->image_url($spring_icon_exists, 'format_roc2023');
            $data->cmname['purpose'] = 'other';
            $data->cmname['icon'] = $url;
        }

        return $this->output->render_from_template('format_roc2023/cm', $data);
    }

    function check_quiz_waitingforgrade ($quizid, $userid) {
        global $DB;
        $sql = "SELECT qs.id,
                   q.qtype
              FROM {quiz_slots} qs
              JOIN {question} q
                ON qs.questionid = q.id
             WHERE qs.quizid = ?
               AND q.qtype = 'essay'";

        if ($DB->record_exists_sql($sql, array($quizid))) {
            $sql = "SELECT qa.id,
                       qa.sumgrades
                  FROM {quiz_attempts} qa
                 WHERE qa.quiz = ?
                   AND qa.userid = ?
                   AND qa.state = 'finished'
              ORDER BY qa.attempt DESC";
            if ($attempts = $DB->get_records_sql($sql, array($quizid, $userid))) {
                $attempt = reset($attempts);
                if (is_null($attempt->sumgrades)) {
                    return 'waitingforgrade';
                } else {
                    return 'incomplete';
                }
            }
        } else {
            $sql = "SELECT qa.id, q.name, qa.sumgrades
                FROM `mdl_quiz_attempts` AS qa, `mdl_quiz` AS q
                WHERE qa.quiz = ? 
                AND qa.userid =?
                AND qa.quiz = q.id
                AND qa.sumgrades < q.grade
                AND qa.state = 'finished'
                ORDER BY qa.attempt DESC";
            if($attempts = $DB->get_records_sql($sql, array($quizid, $userid))) {
                $attempt = reset($attempts);
                if (is_null($attempt->sumgrades)) {
                    return 'waitingforgrade';
                } else {
                    return 'incomplete';
                }
            }
        }
        return 'notattempted';
    }

    function check_lesson_waitingforgrade ($lessonid, $userid) {
        // PMH: Check the state of the lesson!!!
        global $DB;
        $sql = "SELECT *
              FROM {lesson_grades} lg
              WHERE lg.userid = ?
              AND  lg.lessonid = ?
              ORDER BY completed DESC";

        if ($DB->record_exists_sql($sql, array($userid, $lessonid))) {
            if ($attempts = $DB->get_records_sql($sql, array($userid, $lessonid))) {
                $attempt = reset($attempts);
                switch ($attempt->grade) {
                    case 0:     return 'waitingforgrade';
                        break;
                    case 100:   return 'complete';
                        break;
                    default:    return 'incomplete';
                }
            }
        }
        return 'notattempted';
    }

    function check_praktijkscan_waitingforgrade ($praktijkscanid, $userid) {
        // PMH: Check the state of the praktijkscan!!!
        global $DB;
        $sql = "SELECT *
            FROM {praktijkscan_grades} psg
            WHERE psg.userid = ?
            AND  psg.praktijkscanid = ?
            ORDER BY psg.attempt DESC";
//PMH
        if ($DB->record_exists_sql($sql, array($userid, $praktijkscanid))) {
            if ($attempts = $DB->get_records_sql($sql, array($userid, $praktijkscanid))) {
                $attempt = reset($attempts);
                switch ($attempt->grade) {
                    case 0:     return 'waitingforgrade';
                        break;
                    case 100:   return 'complete';
                        break;
                    default:    return 'incomplete';
                }
            }
        }
        return 'notattempted';
    }

}
