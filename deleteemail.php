<?php

/**
 * Script for deleting email templates
 *
 * @package    block
 * @subpackage workflow
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once($CFG->libdir . '/adminlib.php');

// Get the submitted paramaters
$emailid = required_param('emailid', PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

// This is an admin page
admin_externalpage_setup('blocksettingworkflow');

// Require login
require_login();

// Require the workflow:editdefinitions capability
require_capability('block/workflow:editdefinitions', get_context_instance(CONTEXT_SYSTEM));

// Load the email template for later and check that we are allowed to delete it
$email   = new block_workflow_email($emailid);
$email->require_deletable();

// The confirmation strings
$confirmstr = get_string('deleteemailcheck', 'block_workflow', $email->shortname);
$confirmurl = new moodle_url('/blocks/workflow/deleteemail.php', array('emailid' => $emailid, 'confirm' => 1));
$returnurl  = new moodle_url('/blocks/workflow/manage.php');

// Set the page and return urls
$PAGE->set_url('/blocks/workflow/deleteemail.php', array('emaild' => $emailid));

// Set the heading and page title
$title = get_string('confirmemaildeletetitle', 'block_workflow', $email->shortname);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add(get_string('deletetemplate', 'block_workflow'));

if ($confirm) {
    // Confirm the session key to stop CSRF
    require_sesskey();

    // Delete the email
    $email->delete();

    // Redirect
    redirect($returnurl);
}
echo $OUTPUT->header();
echo $OUTPUT->confirm($confirmstr, $confirmurl, $returnurl);
echo $OUTPUT->footer();