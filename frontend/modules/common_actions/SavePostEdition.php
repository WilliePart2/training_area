<?php

namespace frontend\modules\common_actions;

use frontend\helper_models\AlteredListModel;
use frontend\helper_models\ArticleModel;
use frontend\helper_models\VideoModel;
use frontend\modules\common_actions\BaseAction;
use frontend\models\PostManager;

class SavePostEdition extends BaseAction
{
    public function run()
    {
        if (!\Yii::$app->getRequest()->getIsPost()) { return; }
        $data = \json_decode(\Yii::$app->getRequest()->getRawBody(), true);
        $model = new PostManager();
        $result = null;
        switch ($data['post']['type']) {
            case PostManager::ARTICLE: $result = $model->saveArticleEdition(new ArticleModel($data['post'])); break;
            case PostManager::VIDEO: $result = $model->saveVideoEdition(new VideoModel($data['post'])); break;
            case PostManager::ST_LIST: $result = $model->saveStdListEdition(new AlteredListModel($data['post'])); break;
            case PostManager::VOTING_LIST: $result = $model->saveVotingListEdition(new AlteredListModel($data['post'])); break;
        }
        if (!empty($result)) {
            return $this->generateResponse($result);
        }
        return $this->generateResponse();
    }
}