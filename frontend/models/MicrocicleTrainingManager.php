<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use frontend\models\Training;
use frontend\models\TrainingExersiseTraining;
use frontend\models\ExerciseManager;
use frontend\models\CompletedTrainings;
use frontend\models\UserTrainingPlan;

class MicrocicleTrainingManager extends Model
{
    const TRAINING_MANIPULATION = 'TRAINING_MANIPULATION';

    public $id;
    public $trainingId;

    public function rules()
    {
        return [
            [['id', 'trainingId'], 'required', 'on' => self::TRAINING_MANIPULATION]
        ];
    }
    /**
     * Метод создает тренировку
     * it's inner method and it does't need to wrap code inside try/catch
     */
    public function createTraining($microcicleId, $name)
    {
//        try {
            $training = new Training();
            $training->microcicle_id = $microcicleId;
            $training->name = $name;
            $training->save();
            return  $training->id;
//        } catch (\Throwable $error) {
//            // Залогинить ошибку
//            if (YII_ENV_DEV) {
//                throw $error;
//            }
//            return false;
//        }
    }

    /**
     * Метод удаляет тренировку
     * it's internal method and it's doesn't need to wrap inside try\catch
     */
    public function deleteTrainingFromTrainingPlan($trainingId)
    {
//        $transaction = \Yii::$app->getDb()->beginTransaction();
//        try {
            $exerciseManager = new ExerciseManager();
            $training = Training::find()->where(['id' => $trainingId])->with('relationToTrainingExercises')->one();
            if (empty($training)) {
                throw new \Exception('Training not found');
            }
            /* получаем раскладки по uniqueId */
            $relations = $training->getRelationToTrainingExercises()->with('relatedPlans')->all();
            if(!empty($relations)) {
                /** Обрабатываем удаление тренировочных упражнений */
                foreach ($relations as $relation) {
                    /** Удаляем тренировочные планы */
                    $relatedPlans = $relation->getRelatedPlans()->with('userTrainingPlans')->all();
                    if (!empty($relatedPlans)) {
                        foreach ($relation->relatedPlans as $plan) {
                            foreach ($plan->userTrainingPlans as $userPlan) {
                                $userPlan->delete();
                            }
                            $plan->delete();
                        }
                    }

                    $relation->delete();
                }
            }

            $training->delete();
//            $transaction->commit();
            return true;
//        } catch (\Throwable $error) {
//            $transaction->rollBack();
//            if (YII_ENV_DEV) {
//                throw $error;
//            }
//            return false;
//        }
    }

    /**
     * Метод обновляет тренировку
     * @exercises - следуют модели TrainingExerciseModel
    */
    public function updateTraining($microcicleId, $trainingId, $exercises)
    {
        $resultingData = [];
        $createdExercise = [];
        $updatedExercise = [];
//        $transaction = \Yii::$app->getDb()->beginTransaction();
//        Yii::$app->db->pdo->exec('START TRANSACTION READ WRITE');
//        try {
            if (empty($exercises)) {
                return false;
            }

            $exerciseManager = new ExerciseManager();
            foreach ($exercises as $exercise) {
                $exerciseBindingRecord = TrainingExersiseTraining::find()->where([
                    'training_exersise_id' => $exercise['id'],
                    'unique_id' => $exercise['uniqueId']
                ])->one();
                /** Обрабатываем ситуацию когда такого упражнения нету */
                if (empty($exerciseBindingRecord)) {
                    $createdExercise[] = $exerciseManager->createTrainingExercise(
                        $microcicleId, 
                        $trainingId, 
                        $exercise['id'], 
                        $exercise['uniqueId'],
                        $exercise['plans']
                    );
                } else {
                    /** Обрабатываем ситуацию когда упражнение уже есть */
                    $updatedExercise[] = $exerciseManager->updateTrainingExercise(
                        $microcicleId, 
                        $trainingId, 
                        $exercise['id'], 
                        $exerciseBindingRecord->unique_id, 
                        $exercise['plans']
                    );
                }
            }

            $resultingData = [
                'trainingId' => $trainingId,
                'createdExercises' => $createdExercise,
                'updatedExercises' => $updatedExercise,
            ];

//            $transaction->commit();
//            Yii::$app->db->pdo->exec('COMMIT');
            return $resultingData;
//        } catch (\Throwable $error) {
////            $transaction->rollBack();
//            Yii::$app->db->pdo->exec('ROLLBACK');
//            if (YII_ENV_DEV) {
//                throw $error;
//            }
//            return false;
//        }
    }

    /** Метод отмечает тренировку как пройденую */
    public function setTrainingAsComplete($userId, $plans, $sessionId)
    {
        $transaction = \Yii::$app->getDb()->beginTransaction();
        $resultData = [];
        try {
            $completeTraining = CompletedTrainings::find()->where([
                'macrocicle_id' => $this->id,
                'training_id' => $this->trainingId,
                'user_id' => $userId,
                'session_id' => $sessionId
            ])->one();
            
            if (empty($completeTraining)) {
                /** Вносим запис в таблицу завершенных тренировок */
                $completedTrainings = new CompletedTrainings();
                $completedTrainings->macrocicle_id = $this->id;
                $completedTrainings->training_id = $this->trainingId;
                $completedTrainings->user_id = $userId;
                $completedTrainings->session_id = $sessionId;
                $completedTrainings->save();
            };

            /** Создаем пользовательскую раскладку */
            if (!empty($plans['newItems'])) {
                /** Создаем новые раскладки */
                foreach ($plans['newItems'] as $newPlanData) {
                    $creatingResult = $this->createCompletedPlan($newPlanData, $userId, $sessionId);
                    $resultData[] = $creatingResult;
                    if (empty($creatingResult)) {
                        throw new \Exception('Failed when tries to create completed plan');
                    }
                }
            }

            /** Пока что не отправлять данные под ключем modifiedItems */
            if (!empty($plans['modifiedItems'])) {
                /** Модифицируем существующую */
                foreach ($plans['modifiedItems'] as $modItemData) {
                    $modifiedResult = $this->modifyCompletedPlan($modItemData);
                    if (!$modifiedResult) {
                        throw new \Exception('Failed when tries to update completed plans');
                    }
                }
            }

            $transaction->commit();
            return $resultData;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
    /** Метод создает пользовательскую раскладку */
    public function createCompletedPlan($planData, $userId, $sessionId)
    {
        try {
            $newCompletedPlan = new UserTrainingPlan();
            $newCompletedPlan->weight = $planData['doneWeight'];
            $newCompletedPlan->repeats = $planData['doneRepeats'];
            $newCompletedPlan->repeat_section = $planData['doneRepeatSection'];
            $newCompletedPlan->order = $planData['order'];
            $newCompletedPlan->base_training_plan_id = $planData['parentPlanId'];
            $newCompletedPlan->user_id = $userId;
            $newCompletedPlan->session_id = $sessionId;
            $newCompletedPlan->save();
            return [
                'previousPerformPlanId' => $planData['id'],
                'newPerformPlanId' => $newCompletedPlan->id,
                'parentPlanId' => $planData['parentPlanId']
            ];
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** Метод модифицырует пользовательскую раскладку */
    public function modifyCompletedPlan($planData)
    {
        try {
            $oldCompletedPlan = UserTrainingPlan::findOne($planData['id']);
            if (empty($oldCompletedPlan)) {
                throw new \Exception('Specified user training plan not found');
            }
            if (intval($oldCompletedPlan->repeats) !== intval($planData['doneRepeats']) ||
                intval($oldCompletedPlan->repeat_section) !== intval($planData['doneRepeatSection']) ||
                intval($oldCompletedPlan->weight) !== intval($planData['doneWeight'])) {
                $oldCompletedPlan->weight = $planData['doneWeight'];
                $oldCompletedPlan->repeats = $planData['doneRepeats'];
                $oldCompletedPlan->repeat_section = $planData['doneRepeatSection'];
                $oldCompletedPlan->save();
            }
            return true;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** Метод возвращает завершенные тренировки */
    public function getCompletedTrainings($userId, $planId, $sessionId)
    {
        try {
            $completedTrainings = CompletedTrainings::find()->where([
                'user_id' => $userId,
                'macrocicle_id' => $planId,
                'session_id' => $sessionId
            ])->all();
            if (empty($completedTrainings)) {
                return false;
            }
            $data = [];
            foreach($completedTrainings as $training) {
                $data[] = [
                    'id' => $training->training_id
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
}
