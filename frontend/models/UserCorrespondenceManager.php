<?php

namespace frontend\models;

use frontend\helper_models\MessageBaseModel;
use frontend\helper_models\MessageFullModel;
use frontend\helper_models\UserBaseModel;
use frontend\helper_models\UserViewModel;
use yii\base\Model;
use frontend\models\ChatRoom;

class UserCorrespondenceManager extends Model
{
    const CHAT_ROOM = 'chat_room';
    const CHAT_ROOM_MESSAGES = 'chat_room_messages';
    const CHAT_ROOM_MEMBERS = 'chat_room_members';
    const CHAT_ROOM_MESSAGES_READ = 'chat_room_messages_read';
    /**
     * @param $userId - user witch correspondences will received
     * @return $listing messsages from user and to user
     * @throws $exception
     */
    public function getCorrespondences($userId)
    {
        /** invalid for now */
        try {
            $queryResult = (new \yii\db\Query())->select('
                m.chat_room_id,
                _m.member_id,
                r.valid,
                ms.sender_id,
                ms.message,
                
            ')->from(['m' => 'chat_room_members'])
                ->where('m.member_id=' . $userId)
                ->leftJoin(['r' => 'chat_room'], 'r.id=m.chat_room_id AND r.valid=1')
                ->leftJoin(['ms' => 'chat_room_messages'], 'ms.room_receiver_id=r.id')
                ->leftJoin(['_m' => 'chat_room_members'], '_m.chat_room_id.r.id')
                ->leftJoin(['u' => 'users'], 'u.id=_m.member_id')
                ->all();
            return $queryResult;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function createChatRoom($creatorId, $usersInvitedToChatRoom, $message, $topic)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        $result = [];
        $members = [];
        $server = 'http://' . $_SERVER['SERVER_NAME'];
        $path = \Yii::getAlias(\Yii::$app->params['pathToAvatars']);
        try {
            $chatRoom = new ChatRoom();
            $chatRoom->creator_id = $creatorId;
            $chatRoom->valid = 1;
            $chatRoom->topic = $topic;
            $chatRoom->save();

            $chatRoomMembers = 'chat_room_members';
            array_push($usersInvitedToChatRoom, new UserBaseModel(['id' => $creatorId]));
            $rowsForInsert = ['chat_room_id', 'member_id'];
            $dataForInsert = [];
            foreach ($usersInvitedToChatRoom as $userData) {
                $dataForInsert[] = [
                    $chatRoom->id,
                    $userData->id
                ];
                $user = Users::findOne($userData->id);
                $members[] = [
                    'id' => $user->id,
                    'avatar' => $server . $path . $user->avatar,
                    'username' => $user->username,
                    'type' => $user->type
                ];
            }
            \Yii::$app->db->createCommand()->batchInsert($chatRoomMembers, $rowsForInsert, $dataForInsert)->execute();

            $chatRoomMessages = 'chat_room_messages';
            \Yii::$app->db->createCommand()->insert($chatRoomMessages, [
                'sender_id' => $creatorId,
                'room_receiver_id' => $chatRoom->id,
                'message' => $message,
                'date' => \gmdate('Y-m-d H:i:s', time()),
            ])->execute();

            $result = [
                'roomId' => $chatRoom->id,
                'members' => $members
            ];

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

    public function removeChatRoom($roomId, $userId)
    {
        try {
            \Yii::$app->db->createCommand()->update(self::CHAT_ROOM, ['valid' => 0], [
                'creator_id' => $userId,
                'id' => $roomId
            ])->execute();
            return true;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function sendMessage(MessageBaseModel $message): MessageFullModel
    {
        $newMessage = new ChatRoomMessages();
        $newMessage->sender_id = $message->senderId;
        $newMessage->room_receiver_id = $message->roomReceiverId;
        $newMessage->message = $message->message;
        $newMessage->date = gmdate('Y-m-d H:i:s');
        $newMessage->save();

        $result = new MessageFullModel([
            'senderId' => $newMessage->sender_id,
            'messageId' => $newMessage->id,
            'messageDate' => $newMessage->date,
            'message' => $newMessage->message,
            'roomReceiverId' => $newMessage->room_receiver_id
        ]);

        return $result;
    }

    public function removeMessage($messageId)
    {
        try {
            $query = \Yii::$app->db->createCommand()->delete('chat_room_messages', ['id' => $messageId])->execute();
            return !!$query;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function getMessages($roomId, $lessThenId = null, $checkNewMessages = null)
    {
        try {
            $messagesLimit = \Yii::$app->params['userMessagesParams'];
            $messageCacheKey = "{$messagesLimit['fetched_messages_key_for_cache']}_messages_ $roomId";
            $userCacheKey = "{$messagesLimit['fetched_message_members_key_for_cache']}_members_$roomId";

            if (!$checkNewMessages) {
                $messagesFromCache = \Yii::$app->cache->get($messageCacheKey);
                $membersFromChange = \Yii::$app->cache->get($userCacheKey);
                if ($messagesFromCache !== false) {
                    usort($messagesFromCache, function ($message1, $message2) {
                        return $message1['messageId'] > $message2['messageId'] ? 1 : -1;
                    });
                }
                $result = [
                    'messages' => $messagesFromCache,
                    'members' => $membersFromChange
                ];
            }

            if (!$checkNewMessages || (!isset($result) || $result['messages'] === false)) {
                $query = (new \yii\db\Query())->select('
                    m.id AS messageId,
                    m.sender_id AS senderId,
                    m.message,
                    m.date as messageDate,
                    u.id AS memberId,
                    u.avatar,
                    u.username,
                    u.type,
                    mr.message_id AS isConsidered
                ')
                    ->from(['m' => 'chat_room_messages'])
                    ->where("m.room_receiver_id=$roomId");
                if ($lessThenId) {
                    $query = $query->andWhere('m.id < ' . $lessThenId);
                }
                $query = $query->leftJoin(['mm' => 'chat_room_members'], "mm.chat_room_id=$roomId")
                    ->leftJoin(['u' => 'users'], 'u.id=mm.member_id')
                    ->leftJoin(['mr' => 'chat_room_messages_read'], 'mr.message_id=m.id')
                    ->orderBy(['m.id' => SORT_DESC])
                    ->limit($messagesLimit['limit_fetching_messages_from_db'])
                    ->all();

                $server = 'http://' . $_SERVER['SERVER_NAME'];
                $path = \Yii::getAlias(\Yii::$app->params['pathToAvatars']);

                $handledMessageIds = [];
                $handledMemberIds = [];
                $members = [];
                $messages = [];
                array_walk($query, function ($row) use (&$handledMessageIds, &$members, &$messages, &$handledMemberIds, $server, $path) {
                    if (!in_array($row['memberId'], $handledMemberIds)) {
                        array_push($members, [
                            'id' => $row['memberId'],
                            'username' => $row['username'],
                            'type' => $row['type'],
                            'avatar' => $server . $path . $row['avatar']
                        ]);
                        array_push($handledMemberIds, $row['memberId']);
                    }

                    if (!in_array($row['messageId'], $handledMessageIds)) {
                        array_push($handledMessageIds, $row['messageId']);
                        $row['avatar'] = $row['avatar'] . $server . $path;
                        array_unshift($messages, $row);
                    }
                });

                if ($messages) {
                    usort($messages, function ($message1, $message2) {
                        return $message1['messageId'] > $message2['messageId'] ? 1 : -1;
                    });
                }

                $result = [
                    'messages' => $messages,
                    'members' => $members
                ];
            }

            $parts = array_chunk($result['messages'], $messagesLimit['limit_sending_messages_to_user']);
            if (count($parts) > 1) {
                $result['messages'] = array_pop($parts);
                if (count($result['messages']) < $messagesLimit['limit_sending_messages_to_user']) {
                    $result['messages'] = array_merge(array_pop($parts), $result['messages']);
                }
                $forCache = array_reduce($parts, function($store, $item) {
                    array_merge($store, $item);
                    return $store;
                }, []);
                \Yii::$app->cache->set($messageCacheKey, $forCache, $messagesLimit['expire_saving_fetched_messages_in_cache']);
                \Yii::$app->cache->set(
                    $userCacheKey,
                    $result['members'],
                    $messagesLimit['expire_saving_fetched_messages_in_cache']
                );
            } else {
                $result['messages'] = array_shift($parts);
                \Yii::$app->cache->delete($messageCacheKey);
            }

            return $result;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function setMessagesAsConsidered(array $messages, $userId)
    {
        $dataForInsert = [];
        foreach($messages as $message) {
            array_push($dataForInsert, [$userId, $message->messageId]);
        }
        try {
            return \Yii::$app->db->createCommand()->batchInsert('chat_room_messages_read', [
                'member_id',
                'message_id'
            ], $dataForInsert)->execute();
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function removeAllMessagesForAnotherUser()
    {

    }

    public function getAddressees($userId)
    {
        try {
            $queryResult = (new \yii\db\Query())->select('
                m.chat_room_id,
                _m.member_id,
                r.valid,
                r.creator_id,
                r.topic,
                ms.sender_id,
                ms.id AS messageId,
                msr.id AS readedMessageId,
                u.id AS memberId,
                u.avatar AS memberAvatar,
                u.username AS memberName,
                u.type AS memberType
            ')->from(['m' => 'chat_room_members'])
                ->where('m.member_id=' . $userId)
                ->leftJoin(['r' => 'chat_room'], 'r.id=m.chat_room_id AND r.valid=1')
                ->leftJoin(['ms' => 'chat_room_messages'], 'ms.room_receiver_id=r.id')
                ->leftJoin(['msr' => 'chat_room_messages_read'], 'msr.message_id=ms.id AND msr.member_id=' . $userId)
                ->leftJoin(['_m' => 'chat_room_members'], '_m.chat_room_id=r.id')
                ->leftJoin(['u' => 'users'], 'u.id=_m.member_id')
                ->all();
            $newResult = [];
            $handledRoomIds = [];
            $server = 'http://' . $_SERVER['SERVER_NAME'];
            $path = \Yii::getAlias(\Yii::$app->params['pathToAvatars']);
//            return $queryResult;
            if ($queryResult) {
                foreach ($queryResult as $resultItem) {
                    $allItemsOneRoom = array_filter($queryResult, function($row) use ($resultItem) {
                        return ($row['chat_room_id'] === $resultItem['chat_room_id']) && ($row['valid'] === 1) ? true : false;
                    });
                    if (!$allItemsOneRoom) { continue; }
                    if (in_array($resultItem['chat_room_id'], $handledRoomIds)) { continue; }
                    array_push($handledRoomIds, $resultItem['chat_room_id']);
                    $handledMembersIds = [];
                    $messageHandledIds = [];
                    $members = [];
                    $roomId = '';
                    $roomOwner = '';
                    $roomTopic = '';
                    $countMessages = 0;
                    $countNewMessages = 0;
                    foreach($allItemsOneRoom as $roomItem) {
                        if (!in_array($roomItem['memberId'],$handledMembersIds)) {
                            $members[] = new UserViewModel([
                                'id' => $roomItem['memberId'],
                                'username' => $roomItem['memberName'],
                                'type' => $roomItem['memberType'],
                                'avatar' => $server . $path . $roomItem['memberAvatar']
                            ]);
                            array_push($handledMembersIds, $roomItem['memberId']);
                        }
                        if (!in_array($roomItem['messageId'], $messageHandledIds)) {
                            $countMessages += 1;
                            array_push($messageHandledIds, $roomItem['messageId']);
                            if ((int)$roomItem['sender_id'] !== (int)$userId) {
                                $countNewMessages = $roomItem['readedMessageId'] ? $countNewMessages : $countNewMessages + 1;
                            }
                        }
                        if(!$roomId && $roomItem['chat_room_id']) {
                            $roomId = $roomItem['chat_room_id'];
                        }
                        if(!$roomOwner && $roomItem['creator_id']) {
                            $roomOwner = $roomItem['creator_id'];
                        }
                        if(!$roomTopic && $roomItem['topic']) {
                            $roomTopic = $roomItem['topic'];
                        }
                    }

                    $newResult[] = [
                        'members' => $members,
                        'roomId' => $roomId,
                        'roomOwner' => $roomOwner,
                        'roomTopic' => $roomTopic,
                        'countMessages' => $countMessages,
                        'newMessages' => $countNewMessages
                    ];
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
}