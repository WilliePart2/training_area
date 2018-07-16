<?php

namespace frontend\models;

use yii\base\Model;
use frontend\models\Macrocicle;
use frontend\models\MacrocicleRating;
use frontend\models\MacrocicleComments;
use frontend\models\ExerciseManager;
use frontend\models\MicrocicleManager;
use frontend\models\BaseTrainingPlan;
use frontend\models\MacrocicleUsers;
use frontend\models\CompletedMicrocicles;
use frontend\models\MacrocicleActions;
use frontend\behaviors\LogActionBehavior;
use frontend\events\MacrocicleEvent;

class TrainingPlanManager extends Model
{
    /* scenarios */
    const CREATE_MACROCICLE = 'CREATE_MACROCICLE';
    const DELETE_MACROCICLE = 'DELETE_MACROCICLE';
    const SAVE_MACROCICLE_EDITION = 'SAVE_MACROCICLE_EDITION';
    const MACROCICLE_MANIPULATION = 'MACROCICLE_MANIPULATION';

    /* actions */
    const CREATE_TRAINING_PLAN = MacrocicleActions::CREATE_TRAINING_PLAN;
    const ALTER_TRAINING_PLAN = MacrocicleActions::ALTER_TRAINING_PLAN;
    const SUBSCRIBE_TO_TRAINING_PLAN = MacrocicleActions::SUBSCRIBE_TO_TRAINING_PLAN;

    /* const which describe state */
    const TRAINING_PLAN_COMPLETED = 2;
    const TRAINING_PLAN_ACTIVE = 1;

    public $name;
    public $readme;
    public $visible;
    public $category;
    public $id;

    public function rules()
    {
        return [
            ['name', 'required', 'on' => self::CREATE_MACROCICLE],
            [['readme', 'visible', 'category'], 'safe', 'on' => self::CREATE_MACROCICLE],
            ['id', 'required', 'on' => self::DELETE_MACROCICLE],
            [
                'id',
                'exist',
                'targetClass' => Macrocicle::className(),
                'targetAttribute'=> 'id',
                'on' => self::DELETE_MACROCICLE
            ],
            ['id', 'required', 'on' => self::SAVE_MACROCICLE_EDITION],
            [
                'id',
                'exist',
                'targetClass' => Macrocicle::className(),
                'targetAttribute' => 'id',
                'on' => self::SAVE_MACROCICLE_EDITION
            ],
            ['id', 'required', 'on' => self::MACROCICLE_MANIPULATION]
        ];
    }

    public function behaviors()
    {
        return [
            LogActionBehavior::className()
        ];
    }

    /** Методы для получения листинга тренировочных планов */
    public function getListingOwnTrainingPlans($mentorId, $offset, $limit)
    {
        $macrocicles = Macrocicle::findAllMacrocicleByMentorId($mentorId, $offset, $limit);
        $data = [];
        foreach($macrocicles as $macrocicle) {
            $data[] = [
                'id' => $macrocicle->id,
                'name' => $macrocicle->name,
                'mentor_id' => $macrocicle->mentor_id,
                'category' => $macrocicle->category,
                'rating' => MacrocicleRating::getAverageRatingTrainingPlan($macrocicle->id)
            ];
        }
        return $data;
    }
    public function getCountOwnTrainingPlans($mentorId)
    {
        return Macrocicle::getCount($mentorId);
    }

    /** Метод для создания тренировочного плана */
    public function createTrainingPlan($mentorId, $trainingData)
    {
        $exerciseManager = new ExerciseManager();

        try {
            $transaction = \Yii::$app->getDb()->beginTransaction();
            /** Создание макроцикла */
            $macrocicle = new Macrocicle();
            $macrocicle->name = $this->name;
            $macrocicle->readme = isset($this->readme) && !empty($this->readme) ? $this->readme : '';
            $macrocicle->visible = isset($this->visible) && !empty($this->visible) ? $this->visible : 1;
            $macrocicle->category = isset($this->category) && !empty($this->category) ? $this->category : 0;
            $macrocicle->mentor_id = $mentorId;
            $macrocicle->date = gmdate('Y-m-d H:i:s'); /** it's new string */
            $macrocicle->save(); // После сохранения в объекте ActiveRecord получаем сохраненные данные

            /** Создаем шаблон для тренировочного плана */
            $result = $exerciseManager->createLayoutForTrainingPlan($macrocicle->id, $trainingData);

            if(!$result) {
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            $this->trigger(self::CREATE_TRAINING_PLAN, new MacrocicleEvent($mentorId, $macrocicle->id));
            return $macrocicle->id;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** Метод для удаления тренировочного плана */
    public function deleteTrainingPlan()
    {
        try {
            $transaction = \Yii::$app->getDb()->beginTransaction();

            $macrocicle = Macrocicle::findOne($this->id);
            $microcileManager = new MicrocicleManager();
            /** Удаляем шаблоны макроцикла */
            $layoutExercises = $macrocicle->trainingExercises;
            if (!empty($layoutExercises)) {
                foreach ($layoutExercises as $l_exercise) {
                    $l_exercise->delete();
                }
            }
            /** Удалить микроциклы связаные с макроциклом + тренировочные планы и раскладки */
            $microcicles = Microcicle::findMicrocicleByMacrocicleId($macrocicle->id);
            if (!empty($microcicles)) {
                foreach ($microcicles as $microcicle) {
                    $microcileManager->deleteMicrocicle($microcicle->id);
                }
            }

            /** Удалить сам макроцикл */
            $macrocicle->delete();
            $transaction->commit();
            return true;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** Метод девалидирует тренировочный план */
    public function invalidateTrainingPlan()
    {
        try {
            $macrocicle = Macrocicle::findOne($this->id);
            $macrocicle->valid = 0;
            $macrocicle->save();
            return true;
        } catch (\Throwable $error) {
            // Залогинить ошибку
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** Метод выдает полную информацию о тренировочном плане */
    public function getTrainingPlanInfo($macrocicleId)
    {
        try {
            $transaction = \Yii::$app->getDb()->beginTransaction();
            $macrocicle = Macrocicle::findOne($macrocicleId);
            if(empty($macrocicle)) return false;

            $microcicles = Microcicle::findMicrocicleByMacrocicleId($macrocicleId);

            $data = [];
            if(!empty($microcicles)) {
                /** Обрабатываем микроциклы */
                foreach ($microcicles as $microcicle) {
                    /** Обрабатываем тренировки */
                    $trainings = $microcicle->trainings;
                    $trainingData = [];
                    $handledRelation = [];
                    foreach ($trainings as $training) {
                        /** Обрабатываем тренировочные упражнения */
                        $relationToExercises = $training->relationToTrainingExercises;
                        $exercises = $training->trainingExercises;
                        $exercisesData = [];
                        foreach ($relationToExercises as $relation) {
                            if (in_array($relation->unique_id . '_' . $training->id, $handledRelation)) {
                                continue;
                            }

                            /** Обрабатываем тренировочные планы */
                            $plans = $relation->relatedPlans;
                            $planData = [];
                            foreach ($plans as $plan) {
                                $planData[] = [
                                    'id' => $plan->id,
                                    'exerciseId' => $plan->exersise_unique_id,
                                    'weight' => $plan->weight,
                                    'repeats' => $plan->repeats,
                                    'repeatSection' => $plan->repeat_section
                                ];
                            }

                            $exercisesData[] = [
                                'id' => $relation->trainingExercise->id,
                                'uniqueId' => $relation->unique_id,
                                'exerciseId' => $relation->trainingExercise->exersise_id,
                                'exerciseName' => $relation->trainingExercise->exercise->name,
                                'oneRepeatMaximum' => $relation->trainingExercise->one_repeat_maximum,
                                'trainingId' => $training->id,
                                'plans' => $planData
                            ];

                            $handledRelation[] = $relation->unique_id . '_' . $training->id;
                        }
                        $trainingData[] = [
                            'id' => $training->id,
                            'microcicleId' => $training->microcicle_id,
                            'name' => $training->name,
                            'exercises' => $exercisesData
                        ];
                    }
                    $data[] = [
                        'microcicleId' => $microcicle->id,
                        'microcicleName' => $microcicle->name,
                        'trainingData' => $trainingData
                    ];
                }
            }

            $transaction->commit();
            return [
                'id' => $macrocicle->id,
                'mentorId' => $macrocicle->mentor_id,
                'name' => $macrocicle->name,
                'category' => $macrocicle->category,
                'readme' => $macrocicle->readme,
                'visible' => $macrocicle->visible,
                'data' => $data
            ];
        } catch(\Throwable $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /**
     * Метод сохраняет редакцию тренировочного плана
     * (Получаемые данные соответствуют модели микроцикла)
     */
    public function saveTrainingPlanEdition($microcicles, $deletingData)
    {
        try {
            $transaction = \Yii::$app->db->beginTransaction();
            $resultingData = [];

            $microcicleManager = new MicrocicleManager();
            if(!empty($microcicles)) {
                $createdMicrocicles = [];
                $updatedMicrocicles = [];
                /** Обрабатываеться ситуация обновления и создания микроцикла */
                foreach ($microcicles as $data) {
                    $microcicle = Microcicle::findOne($data['microcicleId']);
                    if (empty($microcicle)) {
                        $createdMicrocicles[] = $microcicleManager->createMicrocicle(
                            $this->id,
                            $data['microcicleName'],
                            $data['microcicleId'],
                            $data['trainingData']
                        );
                    } else {
                        $updatedMicrocicles[] = $microcicleManager->updateMicrocicle(
                            $data['microcicleId'],
                            $data['trainingData']
                        );
                    }
                }
                $resultingData = [
                    'createdMicrocicles' => $createdMicrocicles,
                    'updatedMicrocicles' => $updatedMicrocicles
                ];
            }
            /** Обрабатываеться ситуация удаления микроцыкла(передаються в отдельном свойстве) */
            if (!empty($deletingData)) {
                /** Удаляем микроциклы */
                if(!empty($deletingData['microcicles'])){
                    foreach($deletingData['microcicles'] as $microcicleData){
                        $microcicleManager->deleteMicrocicleFromTrainingPlan($microcicleData['microcicleId']);
                    }
                }
                if(!empty($deletingData['trainings']) || !empty($deletingData['exercises']) || !empty($deletingData['plans'])) {
                    $trainingManager = new MicrocicleTrainingManager();
                    /** Удаляем тренировки */
                    if(!empty($deletingData['trainings'])) {
                        foreach ($deletingData['trainings'] as $trainingData) {
                            $trainingManager->deleteTrainingFromTrainingPlan($trainingData['id']);
                        }
                    }
                    /** Удаляем тренировочные упражнения */
                    if (!empty($deletingData['exercises'])) {
                        $exerciseManager = new ExerciseManager();
                        foreach($deletingData['exercises'] as $exercise){
                            $exerciseManager->deleteExerciseFromTraining($exercise['uniqueId']);
                        }
                    }
                    /** Удаляем тренировочные планы */
                    if (!empty($deletingData['plans'])) {
                        foreach($deletingData['plans'] as $planData){
                            try {
                                BaseTrainingPlan::findOne($planData['id'])->delete();
                            } catch (\Throwable $error) {
                                // Залогинить ошибку
                                throw $error;
                            }
                        }
                    }
                }
            }

            $transaction->commit();
            $this->trigger(self::ALTER_TRAINING_PLAN, new MacrocicleEvent(\Yii::$app->user->identity->getId(), $this->id));
            return $resultingData;
        } catch (\Throwable $error) {
//            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
        // Получаем микроцыклы которые изменились

        // В микроцыклах находяться тренировки которые изменились

        // Обновляем тренировочные данные
    }

    /**
     * Метод возвращает полную информациб о текущем/завершенном тренировоном плане
    */
    public function getFullDataAboutTrainingPlan($userId, $planId = null, $sessionId = null)
    {
        try {
            $microcicleManager = new MicrocicleManager();
            $trainingManager = new MicrocicleTrainingManager();
            if (empty($planId)) {
                $ids = $this->getCurrentTrainingPlan($userId);
            } else {
                $ids = [
                    'planId' => $planId,
                    'sessionId' => $sessionId
                ];
            }
            if (empty($ids)) {
                return false;
            }
            $completedMicrocicles = $microcicleManager->getCompletedMicrocicles(
                $userId,
                $ids['planId'],
                $ids['sessionId']
            );
            $completedTrainings = $trainingManager->getCompletedTrainings(
                $userId,
                $ids['planId'],
                $ids['sessionId']
            );
            $completedExercisePlans = $this->getCompletedExercisePlans(
                $userId,
                $ids['sessionId'],
                $ids['planId']
            );
            return [
                'id' => $ids['planId'],
//                'relationId' => $ids['id'],
                'sessionId' => $ids['sessionId'],
                'completedMicrocicles' => $completedMicrocicles,
                'completedTrainings' => $completedTrainings,
                'completedExercisePlans' => $completedExercisePlans
            ];
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /**
     * Метод возвращает текущий тренировочный план
     * Текущий тренировочный план может быть только один
    */
    public function getCurrentTrainingPlan($userId)
    {
        try {
            /** Проверяем есть ли у пользователя текущий план */
            $hasCurrentPlan = MacrocicleUsers::find()->where([
                'state' => self::TRAINING_PLAN_ACTIVE,
                'users_id' => $userId
            ])->one();
            if (!empty($hasCurrentPlan)) {
                return [
                    'planId' => $hasCurrentPlan->macrocicle_id,
                    'id' => $hasCurrentPlan->id,
                    'sessionId' => $hasCurrentPlan->session_id
                ];
            }
            return false;
        } catch (\Throwable $error) {
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }

    }

    /**
     * Метод назначает тренировочный план как текущий
    */
    public function setTrainingPlanAsCurrent($userId)
    {
        try {
            $transaction = \Yii::$app->getDb()->beginTransaction();

            /** Проверяем есть ли текущий тренировочный план */
            $currentTrainingPlan = $this->getCurrentTrainingPlan($userId);
            if (!empty($currentTrainingPlan)) {
                $result = $this->setTrainingPlanAsCompleted(
                    $userId,
                    $currentTrainingPlan['sessionId'],
                    $currentTrainingPlan['planId']
                );
                if (!$result) {
                    throw new \Exception("Current training plan doesn't set as completed");
                }
            }

            /** Устанавливаем указаный тренировочный план текущим */
            $newCurrentTrainingPlan = new MacrocicleUsers();
            $newCurrentTrainingPlan->users_id = $userId;
            $newCurrentTrainingPlan->macrocicle_id = $this->id;
            $newCurrentTrainingPlan->state = self::TRAINING_PLAN_ACTIVE;
            $newCurrentTrainingPlan->date = gmdate('Y-m-d H:i:s');
            $newCurrentTrainingPlan->session_id = implode('_', [
                \Yii::$app->getSecurity()->generateRandomString(),
                microtime(true)
            ]);
            $newCurrentTrainingPlan->save();

            $transaction->commit();
            $this->trigger(self::SUBSCRIBE_TO_TRAINING_PLAN, new MacrocicleEvent($userId, $this->id));
            return true;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /**
     * Метод возвращает пройденые тренировочные планы пользователей
    */
    public function getCompletedTrainingPlans($userId)
    {
        try {
            $completedPlans = MacrocicleUsers::find()->where([
                'users_id' => $userId,
                'state' => self::TRAINING_PLAN_COMPLETED
            ])->with('macrocicle')->all();
            $data = [];
            foreach ($completedPlans as $plan) {
                $data[] = [
                    'id' => $plan->macrocicle->id,
                    'relationId' => $plan->id,
                    'sessionId' => $plan->session_id,
                    'name' => $plan->macrocicle->name,
                    'category' => $plan->macrocicle->category,
                    'dateComplete' => $plan->date_complete
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

    /**
     * Метод отмечает тренировочный план как пройденый
     * it's internal method and it's doesn't need to wrap inside transaction
     */
    public function setTrainingPlanAsCompleted($userId, $sessionId, $planId = null)
    {
        $planId = empty($planId) ? $this->id : $planId;
        try {
            $trainingPlan = MacrocicleUsers::find()->where([
                'users_id' => $userId,
                'macrocicle_id' => $planId,
                'session_id' => $sessionId
            ])->all();
            if (empty($trainingPlan)) {
                throw new \Exception('Training plan not found');
            }

            foreach($trainingPlan as $tPlan) {
                if (intval($tPlan->users_id) !== $userId) {
                    throw new \Exception("Training plan doesn't belong to this user");
                }

                $tPlan->state = self::TRAINING_PLAN_COMPLETED;
                $tPlan->save();
            }

            return true;
        } catch (\Throwable $error) {
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }
    /**
     * Метод возвращает раскладки выполненые пользователем для тренировочного плана
    */
    public function getCompletedExercisePlans($userId, $sessionId, $planId = null)
    {
        $planId = empty($planId) ? $this->id : $planId;
//        $trainingManager = new MicrocicleTrainingManager();
        try {
            $microcicles = Microcicle::find()->where([
                'macrocicle_id' => $planId
            ])->with('trainings')->all();
            if (empty($microcicles)) {
                return false;
            }
            $data = [];
            foreach ($microcicles as $microcicle) {
                $trainings = $microcicle->getTrainings()->with('relationToTrainingExercises')->all();
                if (empty($trainings)) {
                    continue;
                }

                $trainingData = [];
                foreach ($trainings as $training) {
                    /** К раскладкам обращаемся через уникальный id тренировочного упражнения */
                    $relation = $training->getRelationToTrainingExercises()->with('relatedPlans')->all();
                    if (empty($relation)) { continue; }

                    $plansForExercise = [];
                    foreach ($relation as $rel) {
                        if (empty($rel)) { continue; }

                        $relPlans = $rel->getRelatedPlans()->with('userTrainingPlans')->all();
                        if (empty($relPlans)) { continue; }

                        foreach ($relPlans as $plan) {
                            $userPlans = $plan->userTrainingPlans;
                            if (empty($userPlans)) { continue; }
                            /** Данные генерируються из раскладок выполненых пользователем */
                            foreach ($userPlans as $uPlan) {
                                if (empty($uPlan) || $uPlan->user_id !== $userId || $uPlan->session_id !== $sessionId) {
                                    continue;
                                }
                                /** 
                                 * Данные соответствуют модели: IPlanForPerformMutableModel
                                 */
                                $data[] = [
                                    'id' => $uPlan->id,
                                    'parentPlanId' => $uPlan->base_training_plan_id,
                                    'planWeight' => $plan->weight,
                                    'planRepeats' => $plan->repeats,
                                    'planRepeatSection' => $plan->repeat_section,
                                    'doneWeight' => $uPlan->weight,
                                    'doneRepeats' => $uPlan->repeats,
                                    'doneRepeatSection' => $uPlan->repeat_section,
                                    'isComplete' => null, /** вычислиться на фронтэнде */
                                    'isFullDone' => null, /** вычислиться на фронтэнде */
                                    'order' => $uPlan->order
                                ];
                            }
                        }
                    }
                    // $trainingData[] = [
                    //     'id' => $training->id,
                    //     'plans' => $plansForExercise
                    // ];
                }
                // $data[] = [
                //     'microcicle' => [
                //         'id' => $microcicle->id,
                //         'training' => $trainingData
                //     ],
                    /** Устранить метод по возможности */
//                    'completedTrainings' => $trainingManager->getCompletedTrainings($userId, $planId, $sessionId)
                // ];
            }
            return $data;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** метод возвращает результаты поиска тренировочного плана */
    public function performSearchTrainingPlan($name = null, $category = null, $type, $offset, $limit)
    {
        if ($name === null && $category === null) { return false; }

        $cacheParams = \Yii::$app->params['searchingTrainingPlansPParams'];
        $cacheKey = $cacheParams['cache_key_'] . $name ? $name : $category;
        $data = \Yii::$app->cache->get($cacheKey);

        if ($data) {
            if (isset($data['offset']) && (int)$data['offset'] < (int)$offset) {
                $mainData = $data['mainData'];
                $totalCount = $data['totalCount'];
                $data = $data['mainData'];
            } else {
                $data = null;
            }
        }

        if (!$data) {
            $query = Macrocicle::find()
                ->joinWith('rating');
            if ($name !== null) {
                if ($type === 'planName') {
                    $query->andWhere(['like', 'name', trim($name)]);
                } else {
                    $selectMentor = (new \yii\db\Query())->select('id')
                        ->from('users')
                        ->where('username=:name')
                        ->addParams([':name' => $name]);
                    $query->andWhere(['in', 'mentor_id', $selectMentor]);
                }
            }
            if ($category !== null) {
                $query->andWhere(['category' => $category]);
            }
            $clone = clone $query;
            $mainData = $query->offset($offset)->limit($cacheParams['limit_fetching_rows'])->all();
            $totalCount = $clone->count();
        }

        $parts = array_chunk($mainData, $limit);
        $resultingMainData = array_shift($parts);
        if (count($parts) > 0) {
            \Yii::$app->cache->set($cacheKey, [
                'mainData' => array_reduce($parts, function($store, $item) {
                    array_merge($store, $item);
                    return $store;
                }, []),
                'totalCount' => $totalCount,
                'offset' => $offset
            ], $cacheParams['cacheDuration']);
        }

        $modResult = null;
        if ($resultingMainData) {
            $modResult = array_map(function (\yii\db\ActiveRecord $item) {
                $_item = $item->attributes;
                $_item['rating'] = isset($item->rating) && $item->rating ? array_reduce($item->rating, function ($summ, \yii\db\ActiveRecord $item) {
                        $summ += intval($item->rating);
                        return $summ;
                    }, 0) / count($item->rating) : 0;
                return $_item;
            }, $resultingMainData);
        }
        return [
            'mainData' => $modResult,
            'totalCount' => $totalCount
        ];
    }
    /** метод добаляет рейтинг тренировочному плану */
    public function addRatingForTrainingPlan($userId, $ratingValue)
    {
        $macrocicleId = $this->id;
        $dao = \Yii::$app->getDb();
        $transaction = $dao->beginTransaction();
        try {
            $finalQuery = $dao->createCommand("CALL setRating ($userId, $macrocicleId, $ratingValue);")->execute();
            $transaction->commit();
            if ($finalQuery != false) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
    /** метод возвращает рейтинг тренировочного плана */
    public function getTrainingPlanRating($trainingPlanId)
    {
        return MacrocicleRating::find()->where('macrocicle_id=:id', [':id' => $trainingPlanId])
            ->average('[[rating]]');
    }
}