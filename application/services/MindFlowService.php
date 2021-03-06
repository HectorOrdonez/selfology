<?php
/**
 * Project: The Mindcraft Project
 * User: Hector Ordonez
 * Description:
 * Service that manages MindFlow logic:
 *  BrainStorm step, which allows new Ideas and their edition and deletion.
 *  Select step, as above plus selecting and un-selecting which ideas proceed with the flow.
 *  Prioritize step, which allows User to set Ideas importance and urgency.
 *  ApplyTime step, which enables Missions to have specified date and time frame and to convert them into Routines,
 *      with their related Date and Time frames, plus frequencies.
 *  PerForm step, to be implemented.
 *
 * Date: 14/01/14 18:30
 */

namespace application\services;

use application\engine\Service;
use application\models\Action;
use application\models\Idea;

use application\models\Mission;
use application\models\Routine;
use engine\drivers\Exception;

class MindFlowService extends Service
{
    /**
     * Service constructor of MindFlow logic.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ideas request. Returns a list of ideas with different conditionals and different output fields, depending on the
     * purpose of the request (step).
     *
     * @param int $userId User id.
     * @param string $step Stage for which the list is for.
     * @return array List of ideas.
     * @throws Exception If requested step is unexpected.
     */
    public function getIdeas($userId, $step)
    {
        // Initializing parameters
        $response = array(); // Array that will be returned.

        switch ($step) {
            case 'brainStorm':
                $missionConditions = array('user_id = ?', $userId);
                $routineConditions = array('user_id = ? AND (selected = ? OR last_update is NULL)', $userId, Idea::SELECTED_TRUE);
                $fields = array('id', 'title', 'date_creation');

                // Getting missions
                $response['missions'] = $this->getMissions($fields, $missionConditions);

                // Getting routines
                $response['routines'] = $this->getRoutines($fields, $routineConditions);
                break;
            case 'select':
                $missionConditions = array('user_id = ?', $userId);
                $routineConditions = array('user_id = ? AND (selected = ? OR last_update is NULL)', $userId, Idea::SELECTED_TRUE);
                $fields = array('id', 'title', 'date_creation', 'selected');

                // Getting missions
                $response['missions'] = $this->getMissions($fields, $missionConditions);

                // Getting routines
                $response['routines'] = $this->getRoutines($fields, $routineConditions);
                break;
            case 'prioritize':
                $conditions = array('selected = ? AND user_id = ?', Idea::SELECTED_TRUE, $userId);
                $fields = array('id', 'title', 'date_creation', 'important', 'urgent');

                // Getting missions
                $response['missions'] = $this->getMissions($fields, $conditions);

                // Getting routines
                $response['routines'] = $this->getRoutines($fields, $conditions);
                break;
            case 'applyTime':
                $missionConditions = array('selected = ? AND user_id = ?', Idea::SELECTED_TRUE, $userId);
                $routineConditions = array('user_id = ?', $userId);
                $missionFields = array('id', 'title', 'date_todo', 'time_from', 'time_till');
                $routineFields = array('id', 'title', 'selected', 'frequency_days', 'frequency_weeks', 'date_start', 'date_finish', 'time_from', 'time_till');

                // Getting missions
                $response['missions'] = $this->getMissions($missionFields, $missionConditions);

                // Getting routines
                $response['routines'] = $this->getRoutines($routineFields, $routineConditions);
                break;
            default:
                throw new Exception('Unexpected step ' . $step . ' ');
        }

        return $response;
    }

    /**
     * Given passed conditions, requests the missions for this user.
     * Returns them in an array with passed fields.
     *
     * @param $fields
     * @param $conditions
     * @param $order
     * @return array
     */
    private function getMissions($fields, $conditions, $order = 'id')
    {
        $response = array();

        /**
         * @var Mission[] $rawMissionsArray
         */
        $rawMissionsArray = Mission::all(array(
                'joins' => array('idea'),
                'conditions' => $conditions,
                'order' => $order)
        );

        foreach ($rawMissionsArray as $mission) {
            $response[] = $mission->toArray($fields);
        }
        return $response;
    }

    /**
     * Given passed conditions, requests the routines for this user.
     * Returns them in an array with passed fields.
     *
     * @param $fields
     * @param $conditions
     * @param $order
     * @return array
     */
    private function getRoutines($fields, $conditions, $order = 'id')
    {
        $response = array();

        /**
         * @var Routine[] $rawRoutinesArray
         */
        $rawRoutinesArray = Routine::all(array(
                'joins' => array('idea'),
                'conditions' => $conditions,
                'order' => $order)
        );

        foreach ($rawRoutinesArray as $routine) {
            $response[] = $routine->toArray($fields);
        }
        return $response;
    }

    /**
     * Creates an idea related to given user.
     *
     * @param int $userId User id.
     * @param string $title
     * @return array
     */
    public function newIdea($userId, $title)
    {
        /**
         * @var Idea $idea
         */
        $idea = Idea::create(array(
            'user_id' => $userId,
            'title' => $title,
            'date_creation' => date('Y-m-d')
        ));
        Mission::create(array('idea_id' => $idea->id));

        return $idea->toArray(array('id', 'title', 'date_creation', 'selected'));
    }

    /**
     * Edit Idea
     *
     * @param int $userId User id.
     * @param int $ideaId Idea id.
     * @param string $newTitle
     * @throws Exception If idea to be edited is not found or not owned by user.
     */
    public function editIdea($userId, $ideaId, $newTitle)
    {
        /**
         * @var Idea $idea
         */
        $idea = Idea::find_by_id($ideaId);

        if (is_null($idea) OR $userId != $idea->user_id) {
            throw new Exception('The idea you are trying to modify does not exist or it is not yours.');
        }

        $idea->title = $newTitle;
        $idea->save();
    }

    /**
     * Delete idea
     *
     * @param int $userId User id.
     * @param int $ideaId Idea id.
     * @throws Exception If idea to be deleted is not found or not owned by user.
     */
    public function deleteIdea($userId, $ideaId)
    {
        /**
         * @var Idea $idea
         */
        $idea = Idea::find_by_id($ideaId);

        if (is_null($idea) OR $userId != $idea->user_id) {
            throw new Exception('The idea you are trying to delete does not exist or it is not yours.');
        }

        $idea->delete();
    }

    /**
     * Set idea selection state
     *
     * @param int $userId User id.
     * @param int $ideaId Idea id.
     * @param string $selectionState True or false.
     * @throws Exception If idea being changed is not found or not owned by user.
     */
    public function setIdeaSelectionState($userId, $ideaId, $selectionState)
    {
        // Parsing boolean to num
        $selectedValue = ('true' === $selectionState) ? Idea::SELECTED_TRUE : Idea::SELECTED_FALSE;

        /**
         * @var Idea $idea
         */
        $idea = Idea::find_by_id($ideaId);

        if (is_null($idea) or $idea->user_id != $userId) {
            throw new Exception('The idea you are trying to select or un-select does not exist or it is not yours.');
        }

        $idea->selected = $selectedValue;
        $idea->save();
    }

    /**
     * Set idea important state
     *
     * @param int $userId User id.
     * @param int $ideaId Idea id.
     * @param string $importantState True or false.
     * @throws Exception If idea being changed is not found or not owned by user.
     */
    public function setIdeaImportantState($userId, $ideaId, $importantState)
    {
        // Parsing boolean to num
        $importantValue = ('true' === $importantState) ? Idea::IMPORTANT_TRUE : Idea::IMPORTANT_FALSE;

        /**
         * @var Idea $idea
         */
        $idea = Idea::find_by_id($ideaId);

        if (is_null($idea) or $idea->user_id != $userId) {
            throw new Exception('The idea you are trying to set to important or not important does not exist or it is not yours.');
        }

        $idea->important = $importantValue;
        $idea->save();
    }

    /**
     * Set idea urgent state
     *
     * @param int $userId User id.
     * @param int $ideaId Idea id.
     * @param string $urgentState True or false.
     * @throws Exception If idea being changed is not found or not owned by user.
     */
    public function setIdeaUrgentState($userId, $ideaId, $urgentState)
    {
        // Parsing boolean to num
        $urgentValue = ('true' === $urgentState) ? Idea::URGENT_TRUE : Idea::URGENT_FALSE;

        /**
         * @var Idea $idea
         */
        $idea = Idea::find_by_id($ideaId);

        if (is_null($idea) or $idea->user_id != $userId) {
            throw new Exception('The idea you are trying to set to urgent or not urgent does not exist or it is not yours.');
        }

        $idea->urgent = $urgentValue;
        $idea->save();
    }

    /**
     * Sets an idea as a Mission, updating it with given dateTodo, timeFrom and timeTill.
     * Note: these three parameters can be null..
     *
     * @param int $userId User id.
     * @param int $ideaId Idea id.
     * @param null|\DateTime $dateTodo Date to start the routine.
     * @param string $timeFrom Time from
     * @param string $timeTill Time till
     * @throws Exception If Mission being set is not found or not owned by user.
     */
    public function setMissionDateTime($userId, $ideaId, $dateTodo, $timeFrom, $timeTill)
    {
        /**
         * @var Idea $idea
         */
        $idea = Idea::find_by_id($ideaId);

        if (is_null($idea) or $idea->user_id != $userId) {
            throw new Exception('The idea which Date and/or Time you are trying to set does not exist or it is not yours.');
        }

        // Making sure the idea is a mission
        $mission = $idea->convertIdeaToMission();

        // Resetting the time related properties
        $mission->date_todo = $dateTodo;
        $mission->time_from = $timeFrom;
        $mission->time_till = $timeTill;
        $mission->save();
    }

    /**
     * Sets an idea as a Routine, updating it with given dateStart, dateFinish, timeFrom and timeTill.
     * Note: dateStart, dateFinish, timeFrom and timeTill can be null.
     *
     * @param int $userId User id
     * @param int $ideaId Idea id
     * @param string $inputRoutineFrequencyDays Days of the week to apply this routine
     * @param string $inputRoutineFrequencyWeeks Every how many weeks this routine is to be applied
     * @param null|\DateTime $dateStart Date to start the routine
     * @param null|\DateTime $dateFinish Date to finish the routine
     * @param string $timeFrom Time from
     * @param string $timeTill Time till
     * @throws Exception If Routine being set is not found or not owned by user.
     */
    public function setRoutineDateTime($userId, $ideaId, $inputRoutineFrequencyDays, $inputRoutineFrequencyWeeks, $dateStart, $dateFinish, $timeFrom, $timeTill)
    {
        /**
         * @var Idea $idea
         */
        $idea = Idea::find_by_id($ideaId);

        if (is_null($idea) or $idea->user_id != $userId) {
            throw new Exception('The idea which Date and/or Time you are trying to set does not exist or it is not yours.');
        }

        // Making sure the idea is a routine
        $routine = $idea->convertIdeaToRoutine();
        
        // Resetting the time related properties
        $routine->frequency_days = $inputRoutineFrequencyDays;
        $routine->frequency_weeks = $inputRoutineFrequencyWeeks;
        $routine->date_start = $dateStart;
        $routine->date_finish = $dateFinish;
        $routine->time_from = $timeFrom;
        $routine->time_till = $timeTill;
        
        // Resetting actions as they have to be regenerated.
        $routine->resetActions();
        
        // Un-setting the last_update, in order to force the routine to generate actions again. 
        $routine->last_update = null;
        $routine->save();
    }

    /**
     * Builds a list of actions to display.
     * 
     * The filter is as follows:
     * 1 - Gets the actions of this user.
     * 2 - Gets the actions with completed time frame and date_todo between yesterday and tomorrow.     * 
     * 3 - Gets the actions with uncompleted time frame and
     *  3.1 - date_todo is either not defined or today.
     *  3.2 - action is not done or it is done today.
     * 
     * @param $userId
     * @return array
     */
    public function getActions($userId)
    {
        $this->generateActions($userId);

        $response = array();

        /**
         * @var Action[] $rawActionsArray
         */
        $rawActionsArray = Action::find('all', array(
                'conditions' => array(
                    'user_id = ? AND (
                        (
                            time_from IS NOT NULL AND
                            time_till IS NOT NULL AND
                            date_todo BETWEEN DATE(NOW() - INTERVAL 1 DAY) AND DATE(NOW() + INTERVAL 1 DAY)
                        ) OR ( 
                            time_from IS NULL AND 
                            time_till IS NULL AND
                            (date_todo IS NULL or date_todo = DATE(NOW())) AND
                            (date_done IS NULL or date_done = DATE(NOW()))
                        )
                    )',
                    $userId
                ),
                'order' => 'time_from')
        );

        foreach ($rawActionsArray as $action) {
            $response[] = $action->toArray();
        }

        return $response;
    }

    private function generateActions($userId)
    {
        // Missions to Actions
        /**
         * @var Mission[] $missions
         */
        $missions = Mission::all(array(
            'joins' => array('idea'),
            'conditions' => array(
                'selected = ? AND user_id = ?', Idea::SELECTED_TRUE, $userId
            )));

        foreach ($missions as $mission) {
            $mission->turnIntoAction();
        }

        // Generating routine Actions
        /**
         * @var Routine[] $routines
         */
        $routines = Routine::all(array(
                'joins' => array('idea'),
                'conditions' => array(
                    'selected = ? AND user_id = ?', Idea::SELECTED_TRUE, $userId
                )
            )
        );

        foreach ($routines as $routine) {
            if ($routine->isGenerationNeeded())
            {
                $routine->generateActions();
            }
            $routine->idea->setSelect(Idea::SELECTED_FALSE);
        }
    }

    /**
     * Toggles the Action date_done from date to empty or from empty to current date.
     *
     * @param int $userId User id
     * @param int $actionId Action id
     * @return array Action id with the new date_done, which can be a date or empty string-
     * @throws Exception If Action being toggled is not found or not owned by user.
     */
    public function toggleActionDoneState($userId, $actionId)
    {
        /**
         * @var Action $action
         */
        $action = Action::find_by_pk($actionId);

        if (is_null($action) or $action->user_id != $userId) {
            throw new Exception('The action trying to do or undo does not exist or does not belong to you.');
        }

        if ($action->date_done == null) {
            $action->date_done = date('Y-m-d');

        } else {
            $action->date_done = null;
        }

        $action->save();
        return $action->toArray(array('id', 'date_done'));
    }

    public function toggleShowRoutines($userId, $to)
    {
        $setToSelect = ($to == 'show') ? Idea::SELECTED_TRUE : Idea::SELECTED_FALSE;

        /**
         * @var Routine[] $routinesList
         */
        $routinesList = Routine::all(array(
            'joins' => array('idea'),
            'conditions' => array(
                'selected != ? AND user_id = ?', $setToSelect, $userId
            )
        ));

        foreach ($routinesList as $routine) {
            $routine->idea->selected = $setToSelect;
            $routine->idea->save();
        }
    }

    /**
     * Delete action
     *
     * @param int $userId User id.
     * @param int $actionId Action id.
     * @throws Exception If action to be deleted is not found or not owned by user.
     */
    public function deleteAction($userId, $actionId)
    {
        /**
         * @var Action $action
         */
        $action = Action::find_by_id($actionId);

        if (is_null($action) OR $userId != $action->user_id) {
            throw new Exception('The action you are trying to delete does not exist or it is not yours.');
        }

        $action->delete();
    }
}