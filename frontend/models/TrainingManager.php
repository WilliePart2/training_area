<?php

namespace frontend\models;

use common\models\User;
use yii\base\Model;
use frontend\models\Macrocicle;
use frontend\models\MacrocicleTrainingExersise;
use frontend\models\TrainingExersise;
use frontend\models\Microcicle;
use frontend\models\TrainingExersiseTraining;
use frontend\models\Training;
use frontend\models\BaseTrainingPlan;
use frontend\models\UsersMentors;
use frontend\models\Users;
use frontend\models\MacrocicleRating;
use frontend\models\MacrocicleComments;

class TrainingManager extends Model
{
    const MACROCICLE_ADD = 'MACROCICLE_ADD';

    public $readme;
    public $name;

    Public function rules()
    {
        return [
            ['name', 'required', 'on' => self::MACROCICLE_ADD, 'message' => $this->_errorMessage('Поле должно быть заполнено')],
            ['readme', 'default', 'value' => 'Описание отсутствует']
        ];
    }

    /**
     * Блок создания данных
    */
    public function createMacrocicle()
    {
        $mentorId = \Yii::$app->mentor->getId();
        $db = \Yii::$app->getDb();
        $transaction = $db->beginTransaction();
        $counter = Macrocicle::find()->select('counter')->where(['mentor_id' => $mentorId])->max('counter');
        try {
            $macrocicle = new Macrocicle;
            $macrocicle->mentor_id = $mentorId;
            $macrocicle->name = $this->name;
            $macrocicle->readme = $this->readme;
            $macrocicle->counter = !empty($counter) ? intval($counter) + 1 : 1 ;
            $macrocicle->save();

            $transaction->commit();
        } catch (\Throwable $err) {
            $transaction->rollBack();
            throw $err;
        }
    }
    /** Методы для создания микроцыкла и его наполнения */
    /***/
    public function saveMicrocicle($dataFromFrontend)
    {
        $data = $dataFromFrontend;
        $db = \Yii::$app->db;
        // Создаем транзакцию
        $transaction = $db->beginTransaction();
        try{
            // Создаем микроцикл
            $insertedOrder = $this->createMicrocicle(
                $data['microcicleName'],
                $data['macrocicleId'],
                $data['microcicleDuration'],
                $data['dateBegin']
            );
            $microcicleId = Microcicle::getLastInsert($data['macrocicleId'], $insertedOrder)->id;
            /** Создаем тренировку в микроцыкле + это метод запускает создание раскладок и их привязку */
            $this->createTrainingInMicrocicle($data['trainingData'], $microcicleId);

            $transaction->commit();
        } catch (\Throwable $error) {
            $transaction->rollBack();
            throw $error;
        }


        // Создаем тренировки и привязываем их к макроциклу через промежуточную таблицу
    }
    /** Метод создает микроцикл */
    public function createMicrocicle($name, $macrocicleId, $duration, $dateBegin = null)
    {
        $exists = Microcicle::checkMicrocicle($macrocicleId);
        if($exists) $order = Microcicle::getMaxOrder($macrocicleId) + 1;
        else $order = 1;

        $microcicle = new Microcicle();
        $microcicle->name = $name;
        $microcicle->macrocicle_id = $macrocicleId;
        $microcicle->duration = $duration;
        if(!empty($dateBegin)){
            $microcicle->date_begin = $dateBegin;
            $microcicle->date_end = \date('Y/m/d',strtotime($dateBegin) + ($duration * 60 * 60 * 24));
        }
        $microcicle->order = $order;
        $microcicle->save();
        return $order;
    }
    /** Метод создает тренировку в микроцикле */
    public function createTrainingInMicrocicle($trainingData, $microcicleId)
    {
        /** Создаем тренировку */

        $uniqueTrainings = []; /** получаем список уникальных тренировок */
        foreach($trainingData as $training) {
            if(in_array($training['trainingName'], $uniqueTrainings)){
                $uniqueTrainings[] = $training['trainingName'];
            }
        }
        /** Перебираем список тренировок и создаем для них тренировочные упражнения и раскладки */
        $createdTrainings = [];
//        foreach($uniqueTrainings as $trainingName){
            foreach($trainingData as $training) {
                /** Получаем идентификатор тренировочного упражнения */
                $parts = explode('_', $training['exerciseId']);
                $trainingExerciseId = $parts[1];


                if (!isset($createdTrainings[$training['trainingName']])) {
                    /** Создаем тренировку если тренировки с таким именем еще нету */
                    $exists = Training::checkTraining($microcicleId);
                    if($exists) $order = Training::getMaxOrder($microcicleId) + 1;
                    else $order = 1;

                    $newTraining = new Training();
                    $newTraining->name = $training['trainingName'];
                    $newTraining->microcicle_id = $microcicleId;
                    $newTraining->order = $order;
                    $newTraining->save();
                    /** Добавляет им тренировки в сисок созданых тренировок */
                    $createdTrainings[$training['trainingName']] = $training['trainingName'];

                    $id = Training::getTrainingByName($training['trainingName'], $microcicleId)->id;

                    $this->bindTrainingExerciseWithTraining($trainingExerciseId, $id);
                } else {
                    /** Если тренировка с таким именем уже есть тогда создаем для нее раскладки */
                    $id = Training::getTrainingByName($training['trainingName'], $microcicleId)->id;
                    $this->bindTrainingExerciseWithTraining($trainingExerciseId, $id);
                }


                /** Перечисляем все раскладки в каждом тренировочном упражнении */
                foreach($training['trainingPlans'] as $plan) {
                    if(!isset($plan['id']) || empty($plan['id'])) continue;


                    $this->createTrainingPlan($trainingExerciseId, $plan['weight'], $plan['repeat'], $plan['repeatSections'], $microcicleId, $id);
                }
            }

//        }

        // Привязать тренировку к макроциклу
    }
    /** Метод привязывает тренировочное упражнение к тренировке */
    public function bindTrainingExerciseWithTraining($trainingExerciseId, $trainingId)
    {
        $junctionTable = new TrainingExersiseTraining();
        $junctionTable->training_exersise_id = $trainingExerciseId;
        $junctionTable->training_id = $trainingId;
        $junctionTable->save();
    }
    /** Метод создает тренировочную раскладку */
    public function createTrainingPlan($traingExerciseId, $weight, $repeat, $repeatSections, $microcicleId, $trainingId)
    {
        $exists = BaseTrainingPlan::checkBasePlan($traingExerciseId, $microcicleId);
        if($exists) $order = intval(BaseTrainingPlan::getMaxOrder($traingExerciseId, $microcicleId)) + 1;
        else $order = 1;

        $basePlan = new BaseTrainingPlan();
        $basePlan->training_exersise_id = $traingExerciseId;
        $basePlan->weight = $weight;
        $basePlan->repeats = $repeat;
        $basePlan->repeat_section = $repeatSections;
        $basePlan->order = $order;
        $basePlan->microcicle_id = $microcicleId;
        $basePlan->training_id = $trainingId;
        $basePlan->save();
    }
    public function addExercisesInMacrocicleLayout($exercises, $macrocicleId)
    {
        if(!is_array($exercises) || empty($macrocicleId)) throw new \Exception('Unsupported arguments');

        $dataForLayout = [];

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach($exercises as $item){
                \Yii::$app->db->createCommand()->insert('training_exersise', [
                'exersise_id' => $item['exerciseId'], // Идентификатор упражнения
                'macrocicle_id' => $macrocicleId,
                'one_repeat_maximum' => $item['onePM']
                ])->execute();

                $dataForLayout[] = [
                    intval(TrainingExersise::getLastInsertedExercise($macrocicleId)),
                    $macrocicleId
                ];
            }

            \Yii::$app->db->createCommand()->batchInsert(
                'macrocicle_training_exersise',
                ['training_exersise_id', 'macrocicle_id'],
                $dataForLayout
            )->execute();
            $transaction->commit();

            return true;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            throw $error;
        }
    }

    /**
     * Блок получения данных
    */
    /**
     * Метод возвращает список тренировочных планов(макроциклов) по идентификатору ментора
    */
    public function getListingTrainingPlans($mentorId, $offset, $limit)
    {
//        else $offset = $offset * $limit - $limit; - По идее это не нужно.
        $macrocicles =  Macrocicle::findAllMacrocicleByMentorId($mentorId, $limit, $offset);
        $data = [];
        foreach($macrocicles as $macrocicle){
            $data[] = [
                'id' => $macrocicle->id,
                'name' => $macrocicle->name,
                'mentor_id' => $macrocicle->mentor_id,
                'category' => $macrocicle->category
            ];
        }
    }
    /**
     * Метод возвращает количество тенировочных планов зарегистрированых ментором
    */
    public function getCountTrainingPlans($mentorId)
    {
        return Macrocicle::getCount($mentorId);
    }
    /**
     * Метод возвращает последний тренировочный план добавленый ментором
    */
    public function getCurrentTrainingPlan($mentorId)
    {
        return Macrocicle::getLatestInsert($mentorId);
    }
    /**
     * Метод возвращает тренировочный план(макроцикл) по его идентификатору
    */
    public function getTrainingPlanById($planId)
    {
        return Macrocicle::findOneMacrocicleById($planId);
    }
    /** Метод получает получает список микроциклов которые принадлежат определенному тренировочному плану */
    public function getListingMicrociclesByTrainingPlanId($planId, $offset = 0, $limit = 100)
    {
        return Microcicle::find()->where(['macrocicle_id' => $planId, 'valid' => 1])->offset($offset)->limit($limit)->all();
    }
    /** Получает идентификатор макроцикла по идентификатору микроцикла */
    public function getPlanIdByMicrocicleId($microcicleId)
    {
        return Microcicle::find()->select('macrocicle_id')->where(['id' => $microcicleId])->scalar();
    }
    /** Получает микроцикл по идентификатору */
    public function getMicrocicleById($microcicleId)
    {
        return Microcicle::find()->where(['id' => $microcicleId])->one();
    }
    /**
     * Метод находи данные по тренировочным упражнениям и возвращает их в виде пригодном для фронтэнда
     * @microcicleId - идентификатор микроцикла
    */
    public function getMicrocicleDataForFrontend($microcicleId)
    {
        $microcicle = Microcicle::findMicrocicleById($microcicleId); // Пчему то по этому идентификатору не находит упражнений
        $exerciseCounter = 1;
        $planCounter = 1;
        $result = [];

        /** Перебираем тренировочные упражнения у полученого микроцикла */
        foreach($microcicle->trainings as $training){
            /** Перебираем тренировочные упражнения связаные с тренировкой -> получаем weight, repeat, repeatSections + формируем id */
            foreach($training->trainingExercises as $trainingExercise){

                /** Обходим связаные раскладки */
                $trainingPlans = [];
                foreach($trainingExercise->plans as $plan){
                    if($plan->microcicle_id !== $microcicle->id || $plan->training_id !== $training->id) continue;

                    $trainingPlans[] = [
                        'id' => ($planCounter++) . '_' . $plan->id . '_' . rand() . time(),
                        'weight' => $plan->weight,
                        'repeat' => $plan->repeats,
                        'repeatSections' => $plan->repeat_section,
                        'exercisePM' => $trainingExercise->one_repeat_maximum
                    ];
                }

                $exerciseParams = [
                    'trainingName' => $training->name,
                    'exerciseId' => ($exerciseCounter++) . '_' . $trainingExercise->id . '_' . rand() . '_' . time(),
                    'exerciseName' => $trainingExercise->exercise->name,
                    'exercisePM' => $trainingExercise->one_repeat_maximum,
                    'trainingPlans' => $trainingPlans
                ];

                $result[] = $exerciseParams;
            }
        }
        return $result;
    }
    /**
     * Получить базовый шаблон упражнений
    */
    public function getBaseLayout($planId)
    {

    }
    /**
     * Методы получения подопечных ментора
    */
    /** Метод вовращает даные о подопечных ментора */
    public function getPadawans($mentorId, $offset, $limit)
    {
        $activeUser = UsersMentors::findActiveUsers($mentorId, $offset, $limit);
        $data = [];
        foreach($activeUser as $user) {
            $data[] = [
                'id' => $user->users->id,
                'username' => $user->users->username,
                'trainings' => count($user->users->macrocicles)
            ];
        }
        return $data;
    }
    /** Метод возвращает количество подопеных ментора */
    public function getCountPadawans($mentorId)
    {
        return UsersMentors::getCountActiveUsers($mentorId);
    }

    /**
     * Метод для получения все пользователей
    */
    public function getAllUsers($offset, $limit)
    {
        $users = Users::findUsersList($offset, $limit);
        $data = [];
        foreach($users as $user) {
            $data[] = [
                'username' => $user->username,
                'type' => $user->type,
                'trainings' => count($user->macrocicles)
            ];
        }
        return $data;
    }
    /** Метод возвращает количество всех пользователей */
    public function getCountUsers()
    {
        return Users::getCountUsers();
    }

    /**
     * Метод возвращающий все связи пользователей ожидающих одобрения\отклонения(переместить!)
    */
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
                    'username' => $item->users->username,
                    'type' => $item->users->type,
                    'trainings' => count($item->users->macrocicles)
                ];
                $fromUserIdHandled[] = $item->users->id;
            }

            if(intval($item->initialithator) === 2){
                if(in_array($item->users->id, $fromMentorIdHandled)) continue;
                $fromUserToMentor[] = [
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
    /**  */

    /** Метод возвращает запросы от пользователей к менторам */
    public function getRequestsToMentor($mentorId, $offset, $limit)
    {
        $requests = UsersMentors::findAllRequestToRelationByMentor($mentorId, $offset, $limit);
        $data = [];
        $hasHandledRequests = [];
        foreach($requests as $request) {
            if(in_array($request->users_id, $hasHandledRequests) || $request->initialithator !== 2) continue;
            $data[] = $this->_prepareUserData($request->users);
            $hasHandledRequests[] = $request->users_id;
        }
        return $data = [
            'userData' => $data
        ];
    }

    /** Метод возвращает запросы от менторов к пользователям */
    public function getRequestFromMentor($mentorID, $offset, $limit)
    {
        $requests = UsersMentors::findAllRequestToRelationByMentor($mentorID, $offset, $limit);
        $data = [];
        $hasHandledRequest = [];
        foreach($requests as $request) {
            if(in_array($request->users_id, $hasHandledRequest) || $request->initialithator !== 1) continue;
            $data[] = $this->_prepareUserData($request->users);
            $hasHandledRequest[] = $request->users_id;
        }
        return $data = [
            'userData' => $data
        ];
    }

    /**
     * Метод возвращает тренировочный план который назначил ментор пользователю
    */
    public function trainingPlanFromMentor($mentorId)
    {
        return [];
        // Пока что оставлю пустым. Перед этим нужно реализовать сами тренировочные планы
    }

    /**
     * Метод для получение информации по конкретному пользователю
     */
    public function getDataAboutUser()
    {

    }

    /**
     * Блок редактирования данных
    */
    public function alterTrainingPlan()
    {

    }
    /**
     * Метод делает микроцикл невалидным, то есть удаляет микроцикл из видимости менторов и пользователей
    */
    public function invalidateMicrocicle($microcicleId)
    {
        $currentMicrocicle = Microcicle::findMicrocicleById($microcicleId);
        $currentMicrocicle->valid = 0;
        $currentMicrocicle->save();
    }

    /**
     * Блок вспомогательных функций
    */
    public function _errorMessage($message)
    {
        return "<div class='ui fitted hidden divider'></div><div class='ui error message'>$message</div>";
    }
    private function _prepareUserData($userObj)
    {
        return [
            'id' => $userObj->id,
            'username' => $userObj->username,
            'type' => $userObj->type,
            'avatar' => $userObj->getAvatar(),
            'mentor' => (function($mentor){
                return $mentor ? $this->_prepareUserData($mentor) : null;
            })($userObj->mentor),
            'rating' => Users::computeUserRating($userObj),
            'trainings' => [
                'completedPlans' => (function($user) {
                    if (!$user->userMacrocicles) {
                        $completedMacrociles = array_filter($user->userMacrocicles, function (\frontend\models\MacrocicleUsers $item) {
                            return $item->state === MacrocicleUsers::COMPLETED_MACROCICLE ? true : false;
                        });
                        if ($completedMacrociles) {
                            return array_map(function (\frontend\models\MacrocicleUsers $item) {
                                return Macrocicle::getMacrocicleInfo($item->macrocicle);
                            }, $completedMacrociles);
                        }
                    }
                    return [];
                })($userObj),
                'currentPlan' => (function($user) {
                    if ($user->userMacrocicles) {
                        $completedPlan = array_filter($user->userMacrocicles, function(\frontend\models\MacrocicleUsers $item) {
                            $item->state === MacrocicleUsers::COMPLETED_MACROCICLE ? true: false;
                        });
                        if ($completedPlan) {
                            return Macrocicle::getMacrocicleInfo($completedPlan->microcicle);
                        }
                    }
                    return null;
                })($userObj),
                'ownPlans' => (function($user) {
                    if ($user->trainingPlans) {
                        $ownPlans = array_filter($user->trainingPlans, function(\frontend\models\Macrocicle $macrocicle) use ($user) {
                            return $macrocicle->mentor_id === $user->id ? true : false;
                        });
                        if ($ownPlans) {
                            return array_map(function(\frontend\models\Macrocicle $macrocicle) {
                                return Macrocicle::getMacrocicleInfo($macrocicle);
                            }, $ownPlans);
                        }
                    }
                    return [];
                })($userObj)
            ],
            'countPadawans' => $userObj->padawans ? count($userObj->padawans) : 0
        ];
    }
}