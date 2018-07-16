<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use frontend\models\Microcicle;
use frontend\models\Training;
use frontend\models\ExerciseManager;
use frontend\models\MicrocicleTrainingManager;
use frontend\models\CompletedMicrocicles;

class MicrocicleManager extends Model
{
    const MICROCICLE_MANIPULATION = 'MICROCICLE_MANIPULATION';

    public $microcicleId;
    public $planId;
    public $sessionId;

    public function rules()
    {
        return [
            [['planId', 'sessionId'], 'required', 'on' => self::MICROCICLE_MANIPULATION]
        ];
    }
    /** Метод удаляет микроцикл(вообще полностью - используеться администрацыией и при поном удалении тренировочного плана) */
    public function deleteMicrocicle($microcicleId)
    {
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $microcicle = Microcicle::findOne($microcicleId);
            if(empty($microcicle)) return false;

            $trainings = Training::getTrainingByMicrocicleId($microcicle->id);
            foreach($trainings as $training) {
                $trainingExercises = $training->trainingExercises;
                $exerciseManager = new ExerciseManager();
                foreach($trainingExercises as $exercise) {
                    /** Удаляем тренировочное упражнение (заменить на удаление раскладок) */
                    $exerciseManager->deleteTrainingExercise($exercise->id);
                }
                /** Удаляем тренировку */
                $training->delete();
            }
            /** Удаляем микроцикл */
            $microcicle->delete();
            $transaction->commit();
            return true;
        } catch(\Throwable $error) {
            // Залогинить ошибку
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** Метод удаляет микроцикл из тренировочного плана */
    public function deleteMicrocicleFromTrainingPlan($microcicleId)
    {
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
//            $exerciseManager = new ExerciseManager();
            $trainingManager = new MicrocicleTrainingManager();
            $microcicle = Microcicle::find()->where(['id' => $microcicleId])->with('trainings')->one();
            if (empty($microcicle)) {
                return;
            }
            /** Удаляем тренировки */
            foreach ($microcicle->trainings as $training) {
                $result = $trainingManager->deleteTrainingFromTrainingPlan($training->id);
                if (!$result) { throw new \Exception("Training $training->name not deleted"); }
            }

            $microcicle->delete();
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

    /** Создает микроцыкл + тренировки и раскладки к нему */
    /** it's internally method and it doesn't need to wrap code inside transaction */
    public function createMicrocicle($macrocicleId, $name, $previousId, $trainings)
    {
        $resultingData = [];

        $microcicle = new Microcicle();
        $microcicle->macrocicle_id = $macrocicleId;
        $microcicle->name = $name;
        $microcicle->save();
        /** Обрабатываем тренировочные данные (модель TrainingModel)*/
        $createdTrainings = []; /** хранилище данных созданых тренировок */
        $createdExercises = []; /** хранилище данных созданых упражнений */
        if(!empty($trainings)) {
            $exerciseManager = new ExerciseManager();
            $trainingManager = new MicrocicleTrainingManager();
            foreach ($trainings as $train) {
                $trainingId = $trainingManager->createTraining($microcicle->id, $train['name']);
                /** Создаем упражнения для тренировки (модель TrainingExerciseModel) */
                if (!empty($train['exercises']) && !empty($trainingId)) {
                    foreach ($train['exercises'] as $exercise) {
                        $createdExercises[] = $exerciseManager->createTrainingExercise(
                            $microcicle->id,
                            $trainingId,
                            $exercise['id'],
                            $exercise['uniqueId'],
                            $exercise['plans']
                        );
                    }
                }
                $createdTrainings[] = [
                    'previousTrainingId' => $train['id'],
                    'trainingId' => $trainingId,
                    'createdExercises' => $createdExercises
                ];
            }
        }

        $resultingData = [
            'previousMicrocicleId' => $previousId,
            'microcicleId' => $microcicle->id,
            'createdTrainings' => $createdTrainings
        ];

        return $resultingData;
    }

    /** Метод обновляет микроцикл */
    /** It is internal method, and inside this method don't need to use transaction */
    public function updateMicrocicle($microcicleId, $trainingData)
    {
        if(empty($trainingData)) { return false; }
        $resultingData = [];

        $trainingManager = new MicrocicleTrainingManager();
        $exerciseManager = new ExerciseManager();
        $updatedTrainings = [];
        $createdTrainings = [];
        $createdExercises = [];
        foreach ($trainingData as $data) {
            $training = Training::findOne($data['id']); /* this object may be provided to updateTraining method */
            /** Если такой тренировки нету(была добавлена новая) */
            if (empty($training)) {
                $trainingId = $trainingManager->createTraining($microcicleId, $data['name']);
                if(!empty($data['exercises']) && !empty($trainingId)){
                    foreach($data['exercises'] as $exercise) {
                        $createdExercises[] = $exerciseManager->createTrainingExercise(
                            $microcicleId,
                            $trainingId,
                            $exercise['id'],
                            $exercise['uniqueId'],
                            $exercise['plans']
                        );
                    }
                }

                $createdTrainings[] = [
                    'trainingId' => $trainingId,
                    'previousTrainingId' => $data['id'],
                    'createdExercises' => $createdExercises
                ];

            } else {
                /** Если такая тренировка уже есть */
                $updatedTrainings[] = $trainingManager->updateTraining(
                    $microcicleId,
                    $data['id'],
                    $data['exercises']
                );
            }
        }

        $resultingData = [
            'microcicleId' => $microcicleId,
            'updatedTrainings' => $updatedTrainings,
            'createdTrainings' => $createdTrainings
        ];

        return $resultingData;
    }

    /** Метод возвращает завершенные микроциклы */
    public function getCompletedMicrocicles($userId, $planId = null, $sessionId = null)
    {
        $planId = empty($planId) ? $this->planId : $planId;
        $sessionId = empty($sessionId) ? $this->sessionId : $sessionId;
        try {
            $completedMicrocicles = CompletedMicrocicles::find()->where([
                'macrocicle_id' => $planId,
                'user_id' => $userId,
                'session_id' => $sessionId
            ])->all();
            if (empty($completedMicrocicles)) {
                return false;
            }
            $data = [];
            foreach($completedMicrocicles as $microcicle) {
                $data[] = [
                    'id' => $microcicle->microcicle_id
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

    /** Метод отмечает микроцыкл как завершенный */
    public function markMicrocicleAsComplete($userId, $microcicleId)
    {
        try {
            $newCompletedMicrocicle = new CompletedMicrocicles();
            $newCompletedMicrocicle->user_id = $userId;
            $newCompletedMicrocicle->microcicle_id = $microcicleId;
            $newCompletedMicrocicle->macrocicle_id = $this->planId;
            $newCompletedMicrocicle->session_id = $this->sessionId;
            $newCompletedMicrocicle->save();
            return true;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
}
