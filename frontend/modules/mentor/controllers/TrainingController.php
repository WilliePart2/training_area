<?php

namespace frontend\modules\mentor\controllers;

use frontend\modules\common_actions\AddModifyTextToComment;
use frontend\modules\common_actions\GetAvailableAvatars;
use yii\rest\Controller;
use yii\web\Response;
use frontend\modules\mentor\filters\HttpBearerAuthMod;
use frontend\models\TrainingManager;
use frontend\models\TrainingPlanManager;
use frontend\models\Exersises;
use frontend\models\GroupsExersises;

use frontend\modules\common_actions\GetListingOwnTrainingPlans;
use frontend\modules\common_actions\GetListingExercises;
use frontend\modules\common_actions\CreateMacrocicle;
use frontend\modules\common_actions\DeleteTrainingPlan;
use frontend\modules\common_actions\InvalidateTrainingPlan;
use frontend\modules\common_actions\LoadTrainingPlanData;
use frontend\modules\common_actions\LoadBaseLayoutForTrainingPlan;
use frontend\modules\common_actions\UpdateTrainingPlan;
use frontend\modules\common_actions\AddExerciseToLayout;
use frontend\modules\common_actions\DeleteExerciseFromLayout;
use frontend\modules\common_actions\GetCommentsForTrainingPlan;
use frontend\modules\common_actions\AddCommentForTrainingPlan;
use frontend\modules\common_actions\DeleteCommentFromTrainingPlan;
use frontend\modules\common_actions\AddLikeToComment;
use frontend\modules\common_actions\RemoveLikeFromComment;
use frontend\modules\common_actions\AddDislikeToComment;
use frontend\modules\common_actions\RemoveDislikeFromComment;
use frontend\modules\common_actions\LoadCurrentTrainingPlan;
use frontend\modules\common_actions\SetTrainingPlanAsCurrent;
use frontend\modules\common_actions\SetTrainingPlanAsComplete;
use frontend\modules\common_actions\GetCompletedTrainingPlans;
use frontend\modules\common_actions\GetCompletedPlan;
use frontend\modules\common_actions\SetTrainingAsComplete;
use frontend\modules\common_actions\SetMicrocicleAsComplete;
use frontend\modules\common_actions\DeleteMicrocicleFromTrainingPlan;
use frontend\modules\common_actions\GetSearchResult;
use frontend\modules\common_actions\GetTrainingPlanRating;
use frontend\modules\common_actions\SetRatingForTrainingPlan;

class TrainingController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['class'] = HttpBearerAuthMod::className();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_HTML;
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }
    const PAGE_SIZE = 10;

    public function actions()
    {
        return [
            'index' => GetListingOwnTrainingPlans::className(),
            'training-plan-create' => CreateMacrocicle::className(),
            'get-exercise-list' => GetListingExercises::className(),
            'delete-training-plan' => DeleteTrainingPlan::className(),
            'invalidate-training-plan' => InvalidateTrainingPlan::className(),
            'load-training-plan-data' => LoadTrainingPlanData::className(),
            'load-base-layout-for-training-plan' => LoadBaseLayoutForTrainingPlan::className(),
            'update-training-plan' => UpdateTrainingPlan::className(),
            'add-exercise-to-layout' => AddExerciseToLayout::className(),
            'delete-exercise-from-layout' => DeleteExerciseFromLayout::className(),
            'get-comment-list' => GetCommentsForTrainingPlan::className(),
            'add-comment' => AddCommentForTrainingPlan::className(),
            'modify-comment' => AddModifyTextToComment::className(),
            'delete-comment' => DeleteCommentFromTrainingPlan::className(),
            'add-like' => AddLikeToComment::className(),
            'remove-like' => RemoveLikeFromComment::className(),
            'add-dislike' => AddDislikeToComment::className(),
            'remove-dislike' => RemoveDislikeFromComment::className(),
            'load-current-training-plan' => LoadCurrentTrainingPlan::className(),
            'set-training-plan-as-current' => SetTrainingPlanAsCurrent::className(),
            'set-training-plan-as-complete' => SetTrainingPlanAsComplete::className(),
            'get-completed-training-plans' => GetCompletedTrainingPlans::className(),
            'get-completed-training-plan' => GetCompletedPlan::className(),
            'set-training-as-complete' => SetTrainingAsComplete::className(),
            'set-microcicle-as-complete' => SetMicrocicleAsComplete::className(),
            'delete-microcicle-from-training-plan' => DeleteMicrocicleFromTrainingPlan::className(),
            'get-search-result' => GetSearchResult::className(),
            'get-training-plan-rating' => GetTrainingPlanRating::className(),
            'set-rating-for-training-plan' => SetRatingForTrainingPlan::className()
        ];
    }

    /**
     * Методд выводит общую информацию о созданых тренировочных планах
    */
//    public function actionIndex($offset, $limit)
//    {
//        if(\Yii::$app->getRequest()->getIsPost()){
//
//        }
//        return $this->renderFile('@frontend/web/index.html');
//    }
    /**
     * Метод генерирует данные о тренировочном плане
    */
    public function actionViewTrainingPlan($id)
    {

    }
    /**
     * Метод генерирует страницу редактирования тренировочного плана(макроцикла)
     * $id - идентификатор макроцикла
     */
    public function actionTrainingPlanEdit($id)
    {
        $model = new TrainingManager();
        $trainingPlan = $model->getTrainingPlanById($id);
        $microciclesList = $model->getListingMicrociclesByTrainingPlanId($id);
//        $baseLayout = $model->getBaseExercise($planId);
        return $this->render('training-plan-view', [
            'plan' => $trainingPlan,
            'microcicles' => $microciclesList
        ]);
    }
    /**
     * Метод генерирует кабинет для создания микроцикла
     * $plan - идентификатор тренировочного плана(макроцикла)
    */
    public function actionCreateMicrocicle($plan)
    {
        $model = new TrainingManager();
        $trainingPlan = $model->getTrainingPlanById($plan);
        return $this->render('create-microcicle', [
            'plan' => $trainingPlan
        ]);
    }
    /**
     * Action для создания нового микроцыкла
     * Принимает по ajax объект конфигурации микроцикла(объект создаеться в кабинете генерируемом actionCreateMicrocicle)
     */
    public function actionSaveMicrocicle($data = null)
    {
        $model = new TrainingManager();
        if(\Yii::$app->request->isAjax){
//            var_dump(\json_decode(\Yii::$app->request->getRawBody(), true));
//            return;
            $data = \json_decode(\Yii::$app->request->getRawBody(), true);
            try{
                $model->saveMicrocicle($data);
                return $this->redirect(['/mentor/training/training-plan-edit', 'id' => $data['macrocicleId']]);
            } catch(\Throwable $error){
                throw $error;
            }
        };

        if(!empty($data)){
            try{
                $model->saveMicrocicle($data);
                return $this->redirect(['/mentor/training/training-plan-edit', 'id' => $data['macrocicleId']]);
            } catch (\Throwable $error) {
                throw $error;
            }
        }
    }
    /**
     * Метод для редактирования микроцикла
     * $id - идентификатор микроцыкла
    */
    public function actionEditMicrocicle($id)
    {
        $model = new TrainingManager();
        $planId = $model->getPlanIdByMicrocicleId($id);
        $trainingExercises = $model->getTrainingPlanById($planId);
        $microcicle = $model->getMicrocicleById($id);
        return $this->render('microcicle-edit', [
            'plans' => $trainingExercises,
            'microcicle' => $microcicle
        ]);
    }
    /**
     * Метод сохраняет новую редакцию микроцикла
    */
    public function actionSaveEditMicrocicle()
    {
        if(\Yii::$app->request->isAjax){
            $data = \json_decode(\Yii::$app->request->getRawBody(), true);
            $deleteResult = $this->actionDeleteMicrocicle($data['microcicleId']);
            if($deleteResult) {
                $this->actionSaveMicrocicle($data);
            }
        }
    }
    /**
     * Метод для удаления микроцикла(работает по ajax)
     * $id - идентификатор удаления микроцикла(прилетает по ajax)
     */
    public function actionDeleteMicrocicle($microcicleId = null)
    {
        $model = new TrainingManager();
        if(\Yii::$app->request->isAjax){
            $data = json_decode(\Yii::$app->request->getRawBody(), true);
            $model->invalidateMicrocicle($data['microcicleId']);
            return true;
        }
        if(!empty($microcicleId)) {
            $model->invalidateMicrocicle($microcicleId);
            return true;
        }
        return false;
    }
    /**
     * Метод возвращает данные по микроциклу в формате пригодном для использования в фронтэнде
     */
    public function actionGetMicrocicleData()
    {
        if(\Yii::$app->request->isAjax){
            $microcicleId = \Yii::$app->request->get('microcicleId');
            $model = new TrainingManager();
            $data = $model->getMicrocicleDataForFrontend($microcicleId);

            $response = \Yii::$app->response;
            $response->statusCode = 200;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = [
                'data' => $data
            ];
            return $response;
        }
    }

    public function actionCreateMacrocicle()
    {

    }

    public function getPlan($planId)
    {

    }
//    public function getStartPage($offset, $limit)
//    {
//        $model = new TrainingPlanManager();
//        $mentorId = \Yii::$app->user->identity->getId();
//        $trainingPlans = $model->getListingTrainingPlans($mentorId, $offset, $limit);
//        $countTrainingPlans = $model->getCountTrainingPlans($mentorId);
//        return [
//            'trainingPlans' => $trainingPlans,
//            'totalCount' => $countTrainingPlans
//        ];
//        // Это походу тоже не нужно
////        return $this->render('index', [
////            'trainingPlans' => $trainingPlans,
////            'countTrainingPlans' => intval($countTrainingPlans),
////            'model' => $model,
////            'pageSize' => self::PAGE_SIZE,
////            'page' => $page
////        ]);
//    }
    public function actionCreateTrainingPlanBegin()
    {
        $model = new TrainingManager();
        $model->scenario = TrainingManager::MACROCICLE_ADD;

        if(\Yii::$app->request->isPost){
            if($model->load(\Yii::$app->request->post()) && $model->validate()){
                try {
                    $result = $model->createMacrocicle();
//                    if ($result) {
                        return $this->redirect(['/mentor/training/create-training-plan-end']);
//                    }
                } catch (\Throwable $err) {
                    return $this->render('training_plan-add-error', [
                        'model' => $model,
                        'step' => 1
                    ]);
                }
            }
        }

        return $this->render('training_plan-add-begin', [
            'model' => $model,
            'step' => 1
        ]);
    }
    public function actionCreateTrainingPlanEnd()
    {
        $model = new TrainingManager();
        $currentTrainingPlan = $model->getCurrentTrainingPlan(\Yii::$app->mentor->getId());
        if(\Yii::$app->request->isPost){
            $exerciseForLayout = \json_decode(\Yii::$app->request->post('exercises'), true);
            if(!empty($exerciseForLayout)){
                $result = $model->addExercisesInMacrocicleLayout($exerciseForLayout, $currentTrainingPlan->id);
                if($result) {
                    return $this->redirect(['mentor/training/training-plan', 'plan' => $currentTrainingPlan->id]);
                }
                return $this->render('training_plan-add-error', [
                    'name' => $currentTrainingPlan->name,
                    'step' => 2
                ]);
            }
        }

        return $this->render('training_plan-add-end', [
            'name' => $currentTrainingPlan->name,
            'step' => 2
        ]);
    }

    public function actionGetExercises()
    {
        $response = \Yii::$app->getResponse();
        $response->statusCode = 200;
        $response->format = \yii\web\Response::FORMAT_JSON;

        $exercises = Exersises::getAllExercises();
        $groups = array_column($exercises, 'group_id');

        $response->data = [
            'exercises' => Exersises::getAllExercises(),
            'groups' => GroupsExersises::getActiveGroups($groups)
        ];

        return $response;
    }
}
