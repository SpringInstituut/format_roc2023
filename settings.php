<?php
/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     format_roc2023
 * @copyright   2025 Peter Meint Heida <info@heidaservices.nl>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_heading(
        'format_roc2023/color_font',
        get_string('settings', 'format_roc2023'), null
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'format_roc2023/fontcolor',
        get_string('fontcolor', 'format_roc2023'),
        get_string('fontcolor_desc', 'format_roc2023'),
        '#FFFFFF' // Default value.
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'format_roc2023/bgcolor',
        get_string('bgcolor', 'format_roc2023'),
        get_string('bgcolor_desc', 'format_roc2023'),
        '#4d2433' // Default value.
    ));

    $options = array(
        'number' => get_string('option1', 'format_roc2023'),
        'leter_lowercase' => get_string('option2', 'format_roc2023'),
        'leter_uppercase' => get_string('option3', 'format_roc2023'),
        'roman_numbers' => get_string('option4', 'format_roc2023')
    );

    $settings->add(new admin_setting_configselect(
        'format_roc2023/selectoption',
        get_string('numeretion', 'format_roc2023'),
        get_string('numeretion_desc', 'format_roc2023'),
        'option1', // Default value.
        $options
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'format_roc2023/fontcolor_selected',
        get_string('fontcolor_selected', 'format_roc2023'),
        get_string('fontcolor_selected_desc', 'format_roc2023'),
        '#e7e7e7' // Default value.
    ));

    $settings->add(new admin_setting_configcolourpicker(
        'format_roc2023/bgcolor_selected',
        get_string('bgcolor_selected', 'format_roc2023'),
        get_string('bgcolor_selected_desc', 'format_roc2023'),
        '#959494' // Default value.
    ));

    $settings->add(new admin_setting_configstoredfile(
        'format_roc2023/image_sections',
        get_string('selectd_file', 'format_roc2023'),
        get_string('selectd_file_desc', 'format_roc2023'),
        'format_roc2023_file',
        itemid: 0,
        options: array('accepted_types' => '.png', 'maxfiles' => 1)
    ));

    $strings = array(
        'zero', 'one', 'two', 'three', 'four', 'five', 'six',
        'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve',
        'thirteen', 'fourteen', 'fifteen', 'sixteen'
    );
    $options = [];
    $counter = 0;
    foreach ($strings as $str) {
        $options[$counter] = get_string($str, 'format_roc2023');
        $counter++;
    }
    $settings->add(new admin_setting_configselect(
        'format_roc2023/max_groups',
        get_string('groups_course', 'format_roc2023'),
        get_string('groups_course_desc', 'format_roc2023'),
        4,
        $options
    ));
}
