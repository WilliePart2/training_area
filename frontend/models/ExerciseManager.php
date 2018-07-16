<?php
/** Модель для управления тренировочными упражнениями */
namespace frontend\models;

use Yii;
use yii\base\Model;
use frontend\models\TrainingExersise;
use frontend\models\MacrocicleTrainingExersise;
use frontend\models\TrainingExersiseTraining;
use frontend\models\Exersises;
use frontend\models\Macrocicle;
use frontend\models\BaseTrainingPlan;

class ExerciseManager extends Model
{
    const MANAGE_LAYOUT = 'MANAGE_LAYOUT';

    public $id;
    public $data;

    public function rules()
    {
        return [
            [['id', 'data'], 'required', 'on' => self::MANAGE_LAYOUT],
            [
                'id',
                'exist',
                'targetAttribute' => 'id',
                'targetClass' => Macrocicle::className(),
                'on' => self::MANAGE_LAYOUT
            ],
        ];
    }
    /** Метод определяет какую операцию нужно производить для манипуляции тренировочный планом */
    public function manageLayoutForTrainingPlan()
    {
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $resultData = [];
            foreach ($this->data as $data) {
                $item = TrainingExersise::find()->where([
                    'macrocicle_id' => $this->id,
                    'exersise_id' => $data['id']
                ])->exists();
                if (empty($item)) {
                    $result = $this->createLayoutForTrainingPlan($this->id, [$data]);
                    if(empty($result)){
                        throw new \Exception('Unsuccess adding exercise');
                    }
                } else {
                    $result = $this->updateTrainingExerciseInLayout($this->id, $data);
                    if(empty($result)){
                        throw new \Exception('Unsuccess update exercise');
                    }
                }
                $resultData[] = $result;
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
    /**
     * Метод создает тренировочный шаблон для тренировочного плана(макроцикла)
     * it's inner method and it's doesn't need to wrap inside transaction
     */
    public function createLayoutForTrainingPlan($macrocicleId, array $trainingExerciseData)
    {
        $resultData = [];
        foreach ($trainingExerciseData as $data) {
            $trainingExercise = new TrainingExersise();
            $trainingExercise->exersise_id = $data['id'];
            $trainingExercise->macrocicle_id = $macrocicleId;
            $trainingExercise->one_repeat_maximum = $data['oneRepeatMaximum'];
            $trainingExercise->save();

            $relatedExercise = $trainingExercise->exercise;
            $exerciseGroup = $relatedExercise->groups;
            $resultData = [
                'id' => $trainingExercise->id,
                'exercise' => [
                    'id' => $relatedExercise->id,
                    'name' => $relatedExercise->name,
                    'group' => $exerciseGroup->muskul_group,
                    'groupId' => $exerciseGroup->id
                ],
                'oneRepeatMaximum' => $data['oneRepeatMaximum']
            ];

            $binding = $this->bindExerciseForLayout($macrocicleId, $trainingExercise->id);
            if (!$binding) {
                throw new \Exception('Unsuccess binding to layout');
            }
        }
        return $resultData;
    }

    /**
     * Метод свзывает тренировочное упражнение с тренировочным планом
    */
    public function bindExerciseForLayout($macrocicleId, $trainingExerciseId)
    {
        try {
            $relation = new MacrocicleTrainingExersise();
            $relation->macrocicle_id = $macrocicleId;
            $relation->training_exersise_id = $trainingExerciseId;
            $relation->save();
            return true;
        } catch (\Throwable $error) {
            return false;
        }
    }

    /**
     * Метод удаляет тренировочное упражнение
     * Поступающие данные соответствуют модели 'LayoutExerciseModel'
    */
    public function deleteExerciseFromLayout($macrocicleId, $deletingExerciseData)
    {
        if(empty($deletingExerciseData)){
            return;
        }
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            foreach ($deletingExerciseData as $data) {
                $layoutItem = MacrocicleTrainingExersise::find()->where([
                    'macrocicle_id' => $macrocicleId,
                    'training_exersise_id' => $data['id']
                ])->one();
                if (empty($layoutItem)) continue;
                /** Удаляем записи в таблице training_exercise */
                $trainingExerciseItem = TrainingExersise::find()->where([
                    'macrocicle_id' => $macrocicleId,
                    'exersise_id' => $data['id']
                ])->with('plans')->all();
                if (!empty($trainingExerciseItem)) {
                    foreach ($trainingExerciseItem as $trainingItem) {
                        /** Удаляем связаные раскладки */
                        $plans = $trainingItem->getPlans()->with('userTrainingPlans')->all();
                        foreach ($plans as $plan) {
                            foreach ($plan->userTrainingPlans as $userPlan) {
                                $userPlan->delete();
                            }
                            $plan->delete();
                        }

                        /** Удаляем данные из таблицы training_exercise_training */
                        $trainingRelations = TrainingExersiseTraining::find()->where([
                            'training_exersise_id' => $trainingItem->id
                        ])->all();
                        foreach($trainingRelations as $trainingRelation){
                            $trainingRelation->delete();
                        }

                        /** Удаляем данные в таблице */
                        $trainingItem->delete();
                    }
                }

                /** Удаляем упражнение из тренировочного плана */
                $layoutItem->delete();
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }

    /**
     * Метод обновляет тренировочное упражнение входящее в шаблон
     * it's inner method and it doesn't need tp wrap inside transaction
    */
    public function updateTrainingExerciseInLayout($macrocicleId, $trainingData)
    {
        try {
            $trainingExerciseItem = TrainingExersise::find()->where([
                'macrocicle_id' => $macrocicleId,
                'exersise_id' => $trainingData['id']
            ])->one();
            if(empty($trainingExerciseItem)){
                return false;
            }
            $trainingExerciseItem->one_repeat_maximum = $trainingData['oneRepeatMaximum'];
            $trainingExerciseItem->save();

            $relatedExercise = $trainingExerciseItem->exercise;
            $exerciseGroup = $relatedExercise->groups;
            return [
                'id' => $trainingExerciseItem->id,
                'exercise' => [
                    'id' => $relatedExercise->id,
                    'name' => $relatedExercise->name,
                    'group' => $exerciseGroup->muskul_group,
                    'groupId' => $exerciseGroup->id
                ],
                'oneRepeatMaximum' => $trainingExerciseItem->one_repeat_maximum
            ];
        } catch (\Throwable $error) {
            if(YII_ENV_DEV){
                throw $error;
            }
            return false;
        }
    }

    /** Метод создает упражнение для тренировки + раскладки */
    public function createTrainingExercise(
        $microcicleId, 
        $trainingId,
        $trainingExerciseId,
        $previousUniqueId,
        $plans
    )
    {
//        $transaction = \Yii::$app->db->beginTransaction();
//        \Yii::$app->db->pdo->exec('START TRANSACTION');
//        try {
            $resultingData = [];

            /** Привязываем тренировочное упражнение к тренировке */
            $uniqueId = \Yii::$app->getSecurity()->generateRandomString();
            $trainingBinding = new TrainingExersiseTraining();
            $trainingBinding->training_exersise_id = $trainingExerciseId;
            $trainingBinding->training_id = $trainingId;
            $trainingBinding->unique_id = $uniqueId;
            $trainingBinding->save();

            /** Создаем тренировочные планы */
            $createdPlans = [];
            if (!empty($plans)) {
                foreach ($plans as $plan) {
                    $p = new BaseTrainingPlan();
                    $p->training_exersise_id = $trainingExerciseId;
                    $p->training_id = $trainingId;
                    $p->microcicle_id = $microcicleId;
                    $p->weight = $plan['weight'];
                    $p->repeats = $plan['repeats'];
                    $p->repeat_section = $plan['repeatSection'];
                    $p->exersise_unique_id = $uniqueId;
                    $p->save();
                    $createdPlans[] = [
                        'previousPlanId' => $plan['id'],
                        'planId' => $p->id
                    ];
                }
            }

            $resultingData = [
                'previousUniqueId' => $previousUniqueId,
                'exerciseUniqueId' => $uniqueId,
                'createdPlans' => $createdPlans
            ];

//            \Yii::$app->db->pdo->exec('COMMIT');
//            $transaction->commit();
            return $resultingData;
//        } catch (\Throwable $error) {
//            $transaction->rollBack();
//            \Yii::$app->db->pdo->exec('ROLLBACK');
//            if (YII_ENV_DEV) {
//                throw $error;
//            }
//            return false;
//        }
    }
    /** Метод обновляет упражнение в тренировке */
    public function updateTrainingExercise(
        $microcicleId, 
        $trainingId, 
        $trainingExerciseId, 
        $exerciseUniqueId, 
        $plans
    )
    {
        $resultingData = [];
        $createdPlans = [];

        if (empty($plans)) {
            return false;
        }

       foreach($plans as $plan) {
            $p = BaseTrainingPlan::findOne($plan['id']);
            /** Обрабатываем ситуацию когда тренировочный план не существует */
            if(empty($p)){
                $newPlan = new BaseTrainingPlan();
                $newPlan->training_exersise_id = $trainingExerciseId;
                $newPlan->microcicle_id = $microcicleId;
                $newPlan->training_id = $trainingId;
                $newPlan->weight = $plan['weight'];
                $newPlan->repeats = $plan['repeats'];
                $newPlan->repeat_section = $plan['repeatSection'];
                $newPlan->exersise_unique_id = $exerciseUniqueId;
                $newPlan->save();
                $createdPlans[] = [
                    'previousPlanId' => $plan['id'],
                    'planId' => $newPlan->id
                ];
                continue;
            }
            /** Обрабатываем ситуацию когда такой тренировочный план уже есть */
            $p->weight = $plan['weight'];
            $p->repeats = $plan['repeats'];
            $p->repeat_section = $plan['repeatSection'];
            $p->save();
       }

       $resultingData = [
           'exerciseUniqueId' => $exerciseUniqueId,
           'createdPlans' => $createdPlans
       ];

        return $resultingData;
    }

    /**
     * Метод возвращает листинг упражнений в системе
     * Соотвествует модели 'Exercise'.
     */
    public function getExerciseList()
    {
        try {
            $exercises = Exersises::getAllExercises();
            $data = [];
            foreach ($exercises as $exercise) {
                $data[] = [
                    'id' => $exercise->id,
                    'name' => $exercise->name,
                    'group' => $exercise->groups->muskul_group,
                    'groupId' => $exercise->groups->id
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

    /** Метод удаляет тренировочное упражнение(удаляет упражнение из шаблона) */
    public function deleteTrainingExercise($trainingExerciseId)
    {
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $exercise = TrainingExersise::findOne($trainingExerciseId);
            if (empty($exercise)) {
                return false;
            }

            $trainingPlans = $exercise->plans;
            if (empty($trainingPlans)) return;

            foreach ($trainingPlans as $plan) {
                $usersPlans = $plan->userTrainingPlans;
                if (!empty($usersPlans)) {
                    /** Удаляем связаные пользовательские раскладки */
                    foreach ($usersPlans as $userPlan) {
                        $userPlan->delete();
                    }
                }
                /** Удаляем основную раскладку */
                $plan->delete();
            }

            $exercise->delete();
            $transaction->commit();
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /**
     * Метод удаляет упражнение из тренировки
     * it's internal method and it doesn't need to wrap code inside try/catch
     */
    public function deleteExerciseFromTraining($uniqueId)
    {
//        $transaction = \Yii::$app->getDb()->beginTransaction();
//        try {
            $exercise = TrainingExersiseTraining::find()->where(['unique_id' => trim($uniqueId)])->with('relatedPlans')->one();
            if (empty($exercise)) {
                throw new \Exception('Relation from training to training exercise not found');
            }
            /** Удаляем связаные планы */
            foreach($exercise->relatedPlans as $plan){
                /** Удаляються пользовательские планы */
                $userPlans = $plan->userTrainingPlans;
                if(!empty($userPlans)){
                    foreach($userPlans as $uPlan){
                        $uPlan->delete();
                    }
                }
                $plan->delete();
            }

            $exercise->delete();
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

    /** Метод возвращает шаблон для тренировочного плана */
    public function getTrainingPlanLayout($id)
    {
        try {
            $macrocicle = Macrocicle::findOne($id);
            $exercises = $macrocicle->trainingExercises;
            $data = [];
            if (!empty($exercises)) {
                foreach ($exercises as $exercise) {
                    $data[] = [
                        'id' => $exercise->id, /** Идентификатор тренировочного упражнения */
//                        'uniqueId' => $exercise->unique_id, /** Уникальный идентификатор связи тренировки и тренировочного упражнения */
                        'exercise' => [
                            'id' => $exercise->exercise->id, /** Идентификатор обычного упражнения */
                            'name' => $exercise->exercise->name,
                            'group' => $exercise->exercise->groups->muskul_group,
                            'groupId' => $exercise->exercise->groups->id
                        ],
                        'oneRepeatMaximum' => $exercise->one_repeat_maximum
                    ];
                }
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
