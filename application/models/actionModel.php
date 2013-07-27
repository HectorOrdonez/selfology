<?php
/**
 * Project: Selfology
 * User: Hector Ordonez
 * Description:
 * Model that manages the interactions to the table user.
 * Date: 26/07/13 21:00
 */

namespace application\models;

use application\engine\Model;
use engine\Encrypter;
use engine\Exception;

class ActionModel extends Model
{
    /**
     * Fields of the Table Action
     * @var array
     */
    protected $actionFields = array(
        'id',
        'user_id',
        'title',
        'date_creation'
    );

    /**
     * Action Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Special method of selection for grids. Requires an array of parameters with the followings:
     * user_id - User from which we want to extract the actions.
     * sidx - Field to index the results.
     * sord - Sorting direction.
     * start - Starting page.
     * rows - Number of max rows of the list.
     *
     * @param array $parameters
     * @return array of actions of the user.
     */
    public function getUserActionsList($parameters)
    {
        $sql = 'SELECT ' . implode(',', $this->actionFields) . ' FROM action';
        $sql .= ' WHERE `user_id` = :user_id';
        $sql .= ' AND (`date_creation` is NULL OR `date_creation` <= :today)';
        $sql .= ' ORDER BY :sidx :sord';
        $sql .= ' LIMIT :start, :rows';

        $parameters['today'] = date('Y-m-d');

        $result = $this->db->complexQuery($sql, $parameters);

        return $result;
    }

    /**
     * Selects all actions from User.
     *
     * @param int $userId User Id
     * @return array of actions of the user.
     */
    public function getAllUserActions($userId)
    {
        $sql = 'SELECT ' . implode(',', $this->actionFields) . ' FROM action';
        $sql .= ' WHERE `user_id` = :user_id';
        $sql .= ' AND (`date_creation` is NULL OR `date_creation` <= :today)';

        $parameters = array();
        $parameters['user_id'] = $userId;
        $parameters['today'] = date('Y-m-d');

        $result = $this->db->complexQuery($sql, $parameters);

        return $result;
    }

    /**
     * Collects the data from a specific action.
     *
     * @param int $actionId Action Id.
     * @return array Action data
     */
    public function selectById($actionId)
    {
        $fields = $this->actionFields;

        $conditions = array(
            'id' => $actionId
        );

        $result = $this->db->select('action', $fields, $conditions);

        if (count($result) > 0) {
            return $result[0];
        } else {
            return FALSE;
        }
    }

    /**
     * Selects all Action.
     *
     * @return array List of Actions.
     */
    public function selectAll()
    {
        $result = $this->db->select('action');

        return $result;
    }

    /**
     * Creates an action with the specified parameters for given user.
     *
     * @param int $userId owner of the action
     * @param string $title
     * @param string $date_creation
     */
    public function insert($userId, $title, $date_creation)
    {
        $valuesArray = array(
            'user_id' => $userId,
            'title' => $title,
            'date_creation' => $date_creation
        );

        $this->db->insert('action', $valuesArray);
    }

    /**
     * Updates action with the data sent in the array newData.
     * The newData contains the fields and values to update. Notice that:
     * 1 - Column Id cannot be modified.
     *
     * @param $actionId int - Id of the Action to update.
     * @param $userId int - Id of the User who should own the action.
     * @param $newData array - Fields to update and their new values.
     * @throws Exception
     */
    public function update($actionId, $userId, $newData)
    {
        $setArray = array();

        foreach ($newData as $setField => $setValue) {
            if (!in_array($setField, $this->actionFields)) {
                throw new Exception('Error in the update of the table action. The field ' . $setField . ' does not belong to this model.');
            }

            if ($setField == 'id') {
                throw new Exception('Action table column id cannot be modified.');
            } else {
                $setArray[$setField] = $setValue;
            }
        }

        $conditionsArray = array(
            'id' => $actionId,
            'user_id' => $userId
        );

        $this->db->update('action', $setArray, $conditionsArray);
    }

    /**
     * Deletes action.
     *
     * @param $actionId int - Id of the action to delete.
     * @param $userId int - Id of the user who should own the action.
     */
    public function delete($actionId, $userId)
    {
        $conditionsArray = array(
            'id' => $actionId,
            'user_id' => $userId
        );

        $this->db->delete('action', $conditionsArray);
    }
}