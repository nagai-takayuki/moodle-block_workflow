<?php

/**
 * Script to add/update an existing task
 *
 * @package    block
 * @subpackage workflow
 * @copyright  2011 Lancaster University Network Services Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/edittask_form.php');
require_once($CFG->libdir . '/adminlib.php');

$taskid = optional_param('id', 0, PARAM_INT);
$task = new block_workflow_todo();

// This is an admin page
admin_externalpage_setup('blocksettingworkflow');

// Require login
require_login();

// Require the workflow:editdefinitions capability
require_capability('block/workflow:editdefinitions', get_context_instance(CONTEXT_SYSTEM));
if ($taskid) {
    // An existing task was specified
    $task->load_by_id($taskid);
    $returnurl  = new moodle_url('/blocks/workflow/editstep.php', array('stepid' => $task->stepid));
    $PAGE->set_title(get_string('edittask', 'block_workflow', $task->task));
    $PAGE->set_url('/blocks/workflow/edittask.php', array('taskid' => $taskid));
    $data = (object) $task;
}
else {
    // Creating a new task. We require the stepid
    $stepid         = required_param('stepid', PARAM_INT);
    $step           = new block_workflow_step($stepid);
    $returnurl      = new moodle_url('/blocks/workflow/editstep.php', array('stepid' => $step->id));
    $PAGE->set_title(get_string('createtask', 'block_workflow', $step->name));
    $PAGE->set_url('/blocks/workflow/edittask.php');
    $data = new stdClass();
    $data->stepid   = $step->id;
}

// Create/edit task form
$editform = new task_edit();

$editform->set_data($data);

if ($editform->is_cancelled()) {
    redirect($returnurl);
}
else if ($data = $editform->get_data()) {
    $formdata = new stdClass();
    $formdata->id   = $data->id;
    $formdata->task = $data->task;

    if ($taskid) {
        $task->update_todo($formdata);
    }
    else {
        $formdata->stepid = $data->stepid;
        $task->create_todo($formdata);
    }

    redirect($returnurl);
}

// Display the page
echo $OUTPUT->header();
$editform->display();
echo $OUTPUT->footer();