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
 * Observer for partial course cache rebuild.
 *
 * @package   core
 * @copyright Copyright (c) 2016 Mathieu Viossat
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\observer;

defined('MOODLE_INTERNAL') || die();

class partial_course_cache_rebuild
{
    /**
     * Triggered when a course section is updated or deleted.
     *
     * @param \core\event\base $event
     */
    public static function invalidate_section_cache(\core\event\base $event)
    {
        global $CFG;

        if (isset($CFG->partial_course_cache_rebuild) && $CFG->partial_course_cache_rebuild) {
            $cachecoursemodinfo = \cache::make('core', 'coursemodinfo');
            $cachecoursemodinfo->acquire_lock($event->courseid);
            $coursemodinfo = $cachecoursemodinfo->get($event->courseid);
            if ($coursemodinfo !== false) {
                unset($coursemodinfo->sectioncache[$event->other['sectionnum']]);
                $cachecoursemodinfo->set($event->courseid, $coursemodinfo);
            }
            $cachecoursemodinfo->release_lock($event->courseid);
        }
    }

    /**
     * Triggered when a course module is updated or deleted.
     *
     * @param \core\event\base $event
     */
    public static function invalidate_module_cache(\core\event\base $event)
    {
        global $CFG;

        if (isset($CFG->partial_course_cache_rebuild) && $CFG->partial_course_cache_rebuild) {
            $cachecoursemodinfo = \cache::make('core', 'coursemodinfo');
            $cachecoursemodinfo->acquire_lock($event->courseid);
            $coursemodinfo = $cachecoursemodinfo->get($event->courseid);
            if ($coursemodinfo !== false) {
                unset($coursemodinfo->modinfo[$event->objectid]);
                $cachecoursemodinfo->set($event->courseid, $coursemodinfo);
            }
            $cachecoursemodinfo->release_lock($event->courseid);
        }
    }
}
