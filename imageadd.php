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
 * A page for uploading new images
 *
 * @package   mod_lightworkgallery
 * @copyright 2011 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/imageadd_form.php');

$id = required_param('id', PARAM_INT);

$cm         = get_coursemodule_from_id('lightboxgallery', $id, 0, false, MUST_EXIST);
$course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$gallery    = $DB->get_record('lightboxgallery', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course->id);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/lightboxgallery:addimage', $context);

$PAGE->set_url('/mod/lightboxgallery/view.php', array('id' => $cm->id));
$PAGE->set_title($gallery->name);
$PAGE->set_heading($course->shortname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'lightboxgallery')));

$mform = new mod_lightboxgallery_imageadd_form(null, array('id' => $cm->id));

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/mod/lightboxgallery/view.php?id='.$cm->id);

} else if (($formdata = $mform->get_data()) && confirm_sesskey()) {
    require_once($CFG->dirroot . '/lib/uploadlib.php');

    $filename = $mform->get_new_filename('image');

    $fileinfo = array(
        'contextid'     => $cm->id,
        'component'     => 'mod_lightboxgallery',
        'filearea'      => 'gallery_images',
        'itemid'        => 0,
        'filepath'      => '/',
        'filename'      => $filename);

    $fs = get_file_storage();
    $stored_file = $fs->create_file_from_string($fileinfo, $mform->get_file_content('image'));

    $image = new lightboxgallery_image($stored_file, $gallery, $cm);
    $image->set_caption($filename);

    redirect($CFG->wwwroot.'/mod/lightboxgallery/view.php?id='.$cm->id);

}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();

