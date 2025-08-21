<?php
/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     format_roc2023
 * @copyright   2025 Peter Meint Heida <info@heidaservices.nl>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_roc2023\privacy;

use core_privacy\local\metadata\null_provider;

class provider implements null_provider
{

    /**
     * @inheritDoc
     */
    public static function get_reason(): string
    {
        return 'privacy:metadata';
    }
}