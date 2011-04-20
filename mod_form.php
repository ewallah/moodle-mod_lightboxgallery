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
 * The main newmodule configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package   mod_ligboxgallery
 * @copyright 2011 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_lightboxgallery_mod_form extends moodleform_mod {

    function definition() {

        global $CFG;

        $mform =& $this->_form;

        // General options

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size' => '48', 'maxlength' => '255'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('htmleditor', 'description', get_string('description'), array('size' => '64'));
        $mform->setType('content', PARAM_RAW);

        // Advanced options

        $mform->addElement('header', 'galleryoptions', get_string('advanced'));

        $mform->addElement('select', 'perpage', get_string('imagesperpage', 'lightboxgallery'), $this->get_perpage_options());
        $mform->setType('perpage', PARAM_INTEGER);
        $mform->setAdvanced('perpage');

        $mform->addElement('select', 'perrow', get_string('imagesperrow', 'lightboxgallery'), $this->get_perrow_options());
        $mform->setType('perrow', PARAM_INTEGER);
        $mform->setAdvanced('perrow');

        $autoresizegroup = array();
        $autoresizegroup[] = &$mform->createElement('select', 'autoresize', get_string('autoresize', 'lightboxgallery'), $this->get_autoresize_options());
        $autoresizegroup[] = &$mform->createElement('checkbox', 'autoresizedisabled', null, get_string('disable'));
        $mform->addGroup($autoresizegroup, 'autoresizegroup', get_string('autoresize', 'lightboxgallery'), ' ', false);
        $mform->setType('autoresize', PARAM_INTEGER);
        $mform->disabledIf('autoresizegroup', 'autoresizedisabled', 'checked');
        $mform->setAdvanced('autoresizegroup');
        $mform->addHelpButton('autoresizegroup', 'autoresize', 'lightboxgallery');

        $mform->addElement('select', 'resize', sprintf('%s (%s)', get_string('edit_resize', 'lightboxgallery'), strtolower(get_string('upload'))), lightboxgallery_resize_options());
        $mform->setType('resize', PARAM_INTEGER);
        $mform->setAdvanced('resize');
        $mform->disabledIf('resize', 'autoresize', 'eq', 1);
        $mform->disabledIf('resize', 'autoresizedisabled', 'checked');

        $yesno = array(0 => get_string('no'), 1 => get_string('yes'));

        $mform->addElement('select', 'comments', get_string('allowcomments', 'lightboxgallery'), $yesno);
        $mform->setType('comments', PARAM_INTEGER);
        $mform->setAdvanced('comments');

        $mform->addElement('select', 'ispublic', get_string('makepublic', 'lightboxgallery'), $yesno);
        $mform->setType('ispublic', PARAM_INTEGER);
        $mform->setAdvanced('ispublic');

        if (lightboxgallery_rss_enabled()) {
            $mform->addElement('select', 'rss', get_string('allowrss', 'lightboxgallery'), $yesno);
            $mform->setType('rss', PARAM_INTEGER);
            $mform->setAdvanced('rss');
        } else {
            $mform->addElement('static', 'rssdisabled', get_string('allowrss', 'lightboxgallery'), get_string('rssglobaldisabled', 'admin'));
            $mform->setAdvanced('rssdisabled');
        }

        $mform->addElement('select', 'extinfo', get_string('extendedinfo', 'lightboxgallery'), $yesno);
        $mform->setType('extinfo', PARAM_INTEGER);
        $mform->setAdvanced('extinfo');

        // Module options

        $features = array('groups' => false, 'groupings' => false, 'groupmembersonly' => false,
                          'outcomes' => false, 'gradecat' => false, 'idnumber' => false);

        $this->standard_coursemodule_elements($features);

        $this->add_action_buttons();

    }

    function data_preprocessing(&$defaults){
        $defaults['autoresizedisabled'] = (isset($defaults['autoresize']) && $defaults['autoresize'] ? 0 : 1);
    }

    // Custom functions

    function get_course_directories() {
        global $CFG, $COURSE;
        $dirs = get_directory_list($CFG->dataroot . '/' . $COURSE->id, array($CFG->moddata, 'backupdata', '_thumb'), true, true, false);
        $result = array('' => get_string('maindirectory', 'resource'));
        foreach ($dirs as $dir) {
            $result[$dir] = $dir;
        }
        return $result;
    }

    function get_perpage_options() {
        $perpages = array(10, 25, 50, 100, 200);
        $result = array(0 => get_string('showall', 'lightboxgallery'));
        foreach ($perpages as $perpage) {
            $result[$perpage] = $perpage;
        }
        return $result;
    }

    function get_perrow_options() {
        $perpages = array(2, 3, 4, 5, 6);
        foreach ($perpages as $perpage) {
            $result[$perpage] = $perpage;
        }
        return $result;
    }

    function get_autoresize_options() {
        $screen = get_string('screen', 'lightboxgallery');
        $upload = get_string('upload');
        return array(AUTO_RESIZE_SCREEN => $screen,
                     AUTO_RESIZE_UPLOAD => $upload,
                     AUTO_RESIZE_BOTH   => $screen . ' &amp; ' . $upload);
    }
}

