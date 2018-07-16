<?php
namespace frontend\models;

use yii\base\Model;
use frontend\models\UsersMentors;
use frontend\models\MacrocicleUsers;

class PadawanManager extends Model
{
    public function getOwnPadawans($mentorId, $offset, $limit)
    {
        try {
            /**
             * getting current user for filtering
             */
            $userRequests = UsersMentors::find()
                ->where([
                    'in',
                    'status',
                    [
                        UsersMentors::PENDING_REQUEST_TO_USER
                    ]
                ])
                ->with('users')
                ->all();

            if ($userRequests) {
                $filteredUsersIds = array_map(function($item) {
                    return $item->users->id;
                }, $userRequests);
            }
            /**
             * getting users
             */
            $query = UsersMentors::find()->where(['mentors_id' => $mentorId, 'status' => 1])
                ->with([
                    'users' => function(\yii\db\ActiveQuery $query) {
                        return $query->with([
                            'rating',
                            'trainingPlans'
                        ]);
                    },
                    'usersMacrocicles' => function (\yii\db\ActiveQuery $query) {
                        $query->andWhere(['in', 'state', [
                            MacrocicleUsers::CURRENT_MACROCICLE,
                            MacrocicleUsers::COMPLETED_MACROCICLE
                        ]])->with('macrocicle');
                    }
                ])
                ->offset($offset);
            if($limit) {
                $query->limit($limit);
            }
            $activeUser = $query->all();
            $data = [];
            foreach ($activeUser as $user) {
//                $userTrainingPlans = MacrocicleUsers::find()
//                    ->where(['users_id' => $user->users_id])
//                    ->andWhere(['in', 'state', [1, 2]])
//                    ->with('macrocicle')
//                    ->all();
                $userTrainingPlans = $user->usersMacrocicles;
                $completedPlans = [];
                $currentPlan = null;
                foreach ($userTrainingPlans as $trainingPlan) {
                    if (intval($trainingPlan->state) === /* 1 */ MacrocicleUsers::CURRENT_MACROCICLE) {
                        $currentPlan = [
                            'id' => $trainingPlan->macrocicle->id,
                            'sessionId' => $trainingPlan->session_id,
                            'name' => $trainingPlan->macrocicle->name,
                            'readme' => $trainingPlan->macrocicle->readme,
                            'category' => $trainingPlan->macrocicle->category,
                            'rating' => Macrocicle::computateRating($trainingPlan->macrocicle)
                        ];
                        /** текущий план может быть только один */
                    }
                    if (intval($trainingPlan->state) === /* 2 */ MacrocicleUsers::COMPLETED_MACROCICLE) {
                        $completePlans[] = [
                            'id' => $trainingPlan->macrocicle->id,
                            'sessionId' => $trainingPlan->session_id,
                            'name' => $trainingPlan->macrocicle->name,
                            'readme' => $trainingPlan->macrocicle->readme,
                            'category' => $trainingPlan->macrocicle->category,
                            'rating' => Macrocicle::computateRating($trainingPlan->macrocicle)
                        ];
                    }
                }

                if (isset($filteredUsersIds) && in_array($user->users->id, $filteredUsersIds)) {
                    continue;
                }

                $data[] = [
                    'id' => $user->users->id,
                    'avatar' => $user->users->getAvatar(),
                    'username' => $user->users->username,
                    'type' => $user->users->type,
                    'rating' => (function() use ($user) {
                        $countRecords = 0;
                        $totalRating = array_reduce($user->users->rating, function($store, $item) use (&$countRecords) {
                            $countRecords += 1;
                            $store += $item->value;
                            return $store;
                        }, 0);
                        return $countRecords > 0 ? $totalRating / $countRecords : 0;
                    })(),
//                    'completedTrainingPlans' => $completedPlans,
//                    'currentTrainingPlan' => $currentPlan
                    'trainings' => [
                        'completedPlans' => $completedPlans,
                        'currentPlan' => $currentPlan,
                        'ownPlans' => array_map(function ($_macrocicle) {
                            return Macrocicle::getMacrocicleInfo($_macrocicle);
                        }, $user->users->trainingPlans)
                    ]
                ];
            }
            return $data;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function getCountOwnPadawans($mentorId)
    {
        return UsersMentors::getCountActiveUsers($mentorId);
    }

    public function getRequestsToRelation($mentorId, $offset, $limit)
    {
        $relations = UsersMentors::findAllRequestToRelationByMentor($mentorId, $offset, $limit);
        $data = [];
        $fromMentorToUser = [];
        $fromUserToMentor = [];
        $fromMentorIdHandled = [];
        $fromUserIdHandled = [];
        foreach($relations as $item){

            if(intval($item->initialithator) === 1){
                if(in_array($item->users->id, $fromUserIdHandled)) continue;
                $fromMentorToUser[] = [
                    'id' => $item->users->id,
                    'username' => $item->users->username,
                    'type' => $item->users->type,
                    'trainings' => count($item->users->macrocicles)
                ];
                $fromUserIdHandled[] = $item->users->id;
            }

            if(intval($item->initialithator) === 2){
                if(in_array($item->users->id, $fromMentorIdHandled)) continue;
                $fromUserToMentor[] = [
                    'id' => $item->users->id,
                    'username' => $item->users->username,
                    'type' => $item->users->type,
                    'trainings' => count($item->users->macrocicles)
                ];
                $fromMentorIdHandled[] = $item->users->id;
            }
        }

        return $data[] = [
            'fromMentor' => $fromMentorToUser,
            'toMentor' => $fromUserToMentor
        ];
    }
}