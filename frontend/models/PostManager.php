<?php
/**
 * Post manager perform managing content witch include in posts and likes/dislikes
 */

namespace frontend\models;

use frontend\helper_models\AlteredListModel;
use frontend\helper_models\ArticleModel;
use frontend\helper_models\BasePostModel;
use frontend\helper_models\ListItemModel;
use frontend\helper_models\VideoModel;
use frontend\helper_models\ListModel;
use yii\base\Model;
use frontend\models\Posts;
use frontend\models\PostArticles;
use frontend\models\PostLists;
use frontend\models\PostVideos;

class PostManager extends Model
{
    /** constant displaying article types */
    const VIDEO = 'video';
    const ARTICLE = 'article';
    const ST_LIST = 'list';
    const VOTING_LIST = 'voting_list';

    const MANAGING_POSTS = 'MANAGING_POSTS';
    const CREATE_POST = 'CREATE_POST';

    public $id;
    public $header;
    public $content;
    public $type;

    public function rules()
    {
        return [
            ['id', 'required', 'on' => self::MANAGING_POSTS],
            [['header', 'content', 'type'], 'required', 'on' => self::CREATE_POST]
        ];
    }

    /**
     * @userID - user identifier witch post we receive
     * @offset - identifier of last received record
     * @limit how mach records does user agent want to receive
     */
    public function getPosts($userID, $offset, $limit)
    {
        $requestOwnerId = \Yii::$app->user->identity->getId();
        try {
            $result = (new \yii\db\Query())->select("
                    p.id, 
                    p.views, 
                    p.name,
                    p.type,
                    a.content AS article_content,
                    v.content AS video_content,
                    l.content AS list_content,
                    a.order AS article_order,
                    v.order AS video_order,
                    l.order AS list_order,
                    a.id AS article_id,
                    v.id AS video_id,
                    l.id AS list_id,
                    plk.users_id AS like,
                    pdlk.users_id AS dislike,
                    uvt.list_item_id AS votedItemId,
                    pv.post_id AS uniqueViewPostId,
                    pv.users_id AS userViewedId,
                    pv.id AS uniqueViewId
                ")
                ->from(['p' => 'posts'])
                ->leftJoin(['a' => 'post_articles'], 'a.post_id=p.id')
                ->leftJoin(['v' => 'post_videos'], 'v.post_id=p.id')
                ->leftJoin(['l' => 'post_lists'], 'l.post_id=p.id')
                ->leftJoin(['plk' => 'post_likes'], 'plk.post_id=p.id')
                ->leftJoin(['pdlk' => 'post_dislikes'], 'pdlk.post_id=p.id')
                ->leftJoin(['uvt' => 'user_post_votes'], 'uvt.posts_id=p.id AND uvt.users_id=' . $requestOwnerId)
                ->leftJoin(['pv' => 'user_post_views'], 'pv.post_id=p.id')
                ->where(['p.owner_id' => $userID]);
                if ($offset) {
                    $result->andWhere(['<', 'p.id', $offset]);
                }
                $result = $result->orderBy(['[[id]]' => SORT_DESC])
                ->limit(1000)
                ->all();
            $handledItems = [];
            $newResult = [];
            $counterHandledItems = 0;
            foreach ($result as $resultItem) {
                /** filtering already handled records */
                if (!in_array($resultItem['id'], $handledItems)) {
                    if ((int)$counterHandledItems >= (int)$limit) { break; }
                    $isCurrentUserSetDislike = false;
                    $isCurrentUserSetLike = false;
                    $allItemsOfOnePost = array_filter($result, function ($row) use ($resultItem) {
                        return $row['id'] === $resultItem['id'];
                    });
                    array_push($handledItems, $resultItem['id']);
                    $counterHandledItems += 1;

                    $handledResultItem = [
                        'postId' => $resultItem['id'],
                        'type' => $resultItem['type'],
                        'header' => $resultItem['name'],
                        'views' => $resultItem['views']
                    ];
                    switch ($resultItem['type']) {
                        case self::ARTICLE: $handledResultItem['article_id'] = $resultItem['article_id']; break;
                        case self::ST_LIST: $handledResultItem['list_id'] = $resultItem['list_id']; break;
                        case self::VOTING_LIST: $handledResultItem['list_id'] = $resultItem['list_id']; break;
                        case self::VIDEO: $handledResultItem['video_id'] = $resultItem['video_id']; break;
                    }

                    $handledLikedUserIds = [];
                    $handledDislikedUserIds = [];
                    $countLikes = 0;
                    $countDislikes = 0;
                    $content = null;
                    $handledListIds = [];
                    $handLEdVotingListIds = [];
                    $isViewed = false;
                    $countUniqueViews = 0;
                    $handledUniqueViewIds = [];
                    /** $item is one row from select list */
                    foreach ($allItemsOfOnePost as $item) {
                        /** check does current user already liked or disliked this post */
                        if ((int)$requestOwnerId === (int)$item['like']) {
                            $isCurrentUserSetLike = true;
                        }
                        if ((int)$requestOwnerId === (int)$item['dislike']) {
                            $isCurrentUserSetDislike = true;
                        }

                        /** collect content */
                        switch (strtolower($item['type'])) {
                            case self::ARTICLE: {
                                $content = $item['article_content'];
                            } break;
                            case self::VIDEO: {
                                $content = $item['video_content'];
                            } break;
                            case self::ST_LIST: {
                                if (isset($item['list_id']) && !in_array($item['list_id'], $handledListIds)) {
                                    array_push($handledListIds, $item['list_id']);
                                    $content[] = new ListItemModel([
                                        'id' => $item['list_id'],
                                        'value' => $item['list_content']
                                    ]);
                                }
                            } break;
                            case self::VOTING_LIST: {
                                if (isset($item['list_id']) && !in_array($item['list_id'], $handLEdVotingListIds)) {
                                    array_push($handLEdVotingListIds, $item['list_id']);
                                    $content[] = new ListItemModel([
                                        'id' => $item['list_id'],
                                        'value' => $item['list_content'],
                                        'vote' => isset($item['votedItemId']) && $item['list_id'] === $item['votedItemId'] ? true : false
                                    ]);
                                }
                            } break;
                        }
                        /** counting likes */
                        if (isset($item['like']) && !in_array($item['like'], $handledLikedUserIds)) {
                            array_push($handledLikedUserIds, $item['like']);
                            $countLikes += 1;
                        }
                        /** counting dislikes */
                        if (isset($item['dislike']) && !in_array($item['dislike'], $handledDislikedUserIds)) {
                            array_push($handledDislikedUserIds, $item['dislike']);
                            $countDislikes += 1;
                        }

                        /** counting unique views */
                        if (((int)$item['uniqueViewPostId'] === (int)$resultItem['id']) && !in_array($item['uniqueViewId'], $handledUniqueViewIds)) {
                            $countUniqueViews += 1;
                            array_push($handledUniqueViewIds, $item['uniqueViewId']);
                        }

                        if ((int)$item['userViewedId'] === (int)$requestOwnerId) {
                            $isViewed = true;
                        }
                    }
                    $handledResultItem['content'] = $content;
                    $handledResultItem['likes'] = $countLikes;
                    $handledResultItem['dislikes'] = $countDislikes;
                    $handledResultItem['isCurrentUserLiked'] = $isCurrentUserSetLike;
                    $handledResultItem['isCurrentUserDisliked'] = $isCurrentUserSetDislike;
                    $handledResultItem['viewed'] = $isViewed;
                    $handledResultItem['uniqueViews'] = $countUniqueViews;
                    $newResult[] = $handledResultItem;
                }
            }

            return $newResult;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
    public function createPost($userId)
    {
        $newMainPost = new Posts();
        $newMainPost->owner_id = $userId;
        $newMainPost->type = trim($this->type);
        $newMainPost->name = trim($this->header);
        $newMainPost->date = gmdate('Y-m-d H:i:s');
        $newMainPost->save();

        $className = '';
        switch($this->type) {
            case self::ARTICLE: {
                $className = PostArticles::className();
            }
            break;
            case self::VIDEO: {
                $className = PostVideos::className();
            }
            break;
            default: {
                $className = PostLists::className();
            }
        }
        try {
            $result = [];
            if ($this->type === self::ST_LIST || $this->type === self::VOTING_LIST) {
                $content = [];
                foreach($this->content as $value) {
                    $newRecord = new $className();
                    $newRecord->post_id = $newMainPost->id;
                    $newRecord->content = $value['value'];
                    $newRecord->order = $value['id'];
                    $newRecord->save();
                    $content[] = [
                        'oldId' => $value['id'],
                        'newId' => $newRecord->id
                    ];
                }
                $result['content'] = $content;
            }
            if ($this->type === self::VIDEO || $this->type === self::ARTICLE) {
                $newRecord = new $className();
                $newRecord->post_id = $newMainPost->id;
                $newRecord->content = $this->content;
                $newRecord->save();
                $result['content'] = [
                    $newRecord->id
                ];
            }
            if (!empty($result)) {
                $result['postId'] = $newMainPost->id;
                return $result;
            }
            return false;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function saveArticleEdition(ArticleModel $data)
    {
        try {
            $result = \Yii::$app->db->createCommand()
                ->update('post_articles', ['content' => $data->content], ['id' => $data->id])
                ->execute();
            if ($result != false) {
                return true;
            }
            return false;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function saveVideoEdition(VideoModel $data)
    {
        $table = 'post_videos';
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            \Yii::$app->db->createCommand()->update('posts', [
                'name' => $data->header
            ], [
                'id' => $data->postId
            ])->execute();
            \Yii::$app->db->createCommand()->update($table, [
                'content' => $data->url
            ], [
                'post_id' => $data->postId
            ])->execute();

            $transaction->commit();
            return true;
        } catch (\Throwable $error ) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function saveStdListEdition(AlteredListModel $data)
    {
        $table = 'post_lists';
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $newItems = [];
            $updatedItems = [];
            $deletedItems = [];
            if (isset($data->newItems) && is_array($data->newItems)) {
//                $maxOrder = PostLists::find()->where(['post_id' => $data->postId])->max('[[id]]');
                foreach ($data->newItems as $item) {
                    $newListItem = new PostLists();
                    $newListItem->post_id = $data->postId;
                    $newListItem->content = $item->value;
//                    $newListItem->order = $maxOrder + 1;
                    $newListItem->save();
                    $newItems[] = [
                        'oldItemId' => $item->id,
                        'newItemId' => $newListItem->id
                    ];
//                    $maxOrder += 1;
                }
            }

            if (isset($data->alteredItems) && is_array($data->alteredItems)) {
                foreach ($data->alteredItems as $item) {
                    $result = \Yii::$app->db->createCommand()->update($table, [
                        'content' => $item->value
                    ], [
                        'post_id' => $data->postId,
                        'id' => $item->id
                    ])->execute();
                    if ($result != false) { array_push($updatedItems, $item->id); }
                }
            }

            if (isset($data->removingItems) && is_array($data->removingItems)) {
                foreach ($data->removingItems as $item) {
                    $result = \Yii::$app->db->createCommand()->delete($table, [
                        'post_id' => $data->postId,
                        'id' => $item->id
                    ])->execute();
                    if ($result != false) { array_push($deletedItems, $item->id); }
                }
            }
            $transaction->commit();
            return [
                'newItems' => $newItems,
                'updatedItems' => $updatedItems,
                'deletedItems' => $deletedItems
            ];
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function saveVotingListEdition(AlteredListModel $data)
    {
        $result = $this->saveStdListEdition($data);
        if ($result === false) { return false; }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $table = 'user_post_votes';
            $queryData = [];
            if (isset($data->removingItems) && is_array($data->removingItems)) {
                foreach ($data->removingItems as $value) {
                    $queryData[] = $value->id;
                }
                $result['deletedItems'] = array_unique(
                    array_merge($result['deletedItems'], $queryData)
                );
            }

            $alteredItemsData = [];
            if (isset($data->alteredItems) && is_array($data->alteredItems)) {

                foreach ($data->alteredItems as $item) {
                    $alteredItemsData[] = $item->id;
                }
                $result['updatedItems'] = array_unique(
                    array_merge($result['updatedItems'], $alteredItemsData)
                );
            }
            \Yii::$app->db->createCommand()->delete($table, ['in', 'list_item_id', array_merge($queryData, $alteredItemsData)])->execute();

            $transaction->commit();
            return $result;
        } catch (\Throwable $error) {
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function removePost(BasePostModel $data)
    {
        try {
            $result = \Yii::$app->db->createCommand("DELETE FROM `posts` WHERE id=:record_id", [
                ':record_id' => $data->postId
            ])->execute();
            if (!empty($result)) {
                return true;
            }
            return false;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function setVoteForListItem(ListModel $data, $userId)
    {
        if (empty($data->content)) { return; }
        $listItem = $data->content[0];
        try {
            \Yii::$app->db->createCommand('CALL set_user_list_vote(:postId, :userId, :listItemId)', [
                ':postId' => $data->postId,
                ':userId' => $userId,
                ':listItemId' => $listItem->id
            ])->execute();
            return true;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function dropVoteFromListItem(ListModel $data, $userId)
    {
        if (empty($data->content)) { return; }
        $listItem = $data->content[0];
        try {
            \Yii::$app->db->createCommand()->delete('user_post_votes', [
                'posts_id' => $data->postId,
                'users_id' => $userId,
                'list_item_id' => $listItem->id
            ])->execute();
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }

    }

    public function setLike($postId, $userId)
    {
        try {
            $query = \Yii::$app->db->pdo->prepare('SELECT set_user_post_like(:userId, :postId) AS result');
            $query->bindParam(':userId', $userId);
            $query->bindParam(':postId', $postId);
            $query->execute();
            return $query->fetch(\PDO::FETCH_ASSOC)['result'];
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function setDislike($postId, $userId)
    {
        try {
            $query = \Yii::$app->db->pdo->prepare('SELECT set_user_post_dislike(:postId, :userId) AS result');
            $query->bindParam(':postId', $postId);
            $query->bindParam(':userId', $userId);
            $query->execute();
            return $query->fetch(\PDO::FETCH_ASSOC)['result'];
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function incrementViewCounter($postId, $userId)
    {
        try {
            \Yii::$app->db->pdo->exec("CALL increment_user_post_view_counter($userId, $postId)");
            return true;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
}
