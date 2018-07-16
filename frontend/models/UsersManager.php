<?php

namespace frontend\models;

//use yii\base\Model;
use frontend\models\Users;
use frontend\models\Mentors;
use frontend\models\UsersMentors;
use frontend\models\UsersBaseManager;

class UsersManager extends UsersBaseManager
{
    const REGISTRATION = 'REGISTRATION';
    const USER_REGISTRATION = 'USER_REGISTRATION';
    const MENTOR_REGISTRATION = 'MENTOR_AUTHORIZATION';
    const AUTHORIZATION = 'AUTHORIZATION';
    const BINDING_FROM_MENTOR = 'BINDING_FROM_MENTOR';

    public $password;
    public $username;
    public $password_repeat;
    public $email;
    public $mentorUsername;
    public $clientUsername;
    public $mentorId;
    public $clientId;

    public function rules()
    {
        return [
            [
                ['password', 'username'],
                'required',
                'message' => $this->_setMessage('Поле должно быть заполнено'),
                'on' => self::AUTHORIZATION
            ],
            [
                ['password', 'password_repeat', 'username', 'email'],
                'required',
                'message' => $this->_setMessage('Поле должно быть заполнено'),
                'on' => self::USER_REGISTRATION
            ],
            [
                'password',
                'compare',
                'message' => $this->_setMessage('Пароли не совпадают'),
                'on' => self::USER_REGISTRATION
            ],
            [
                'username',
                function ($attribute, $params) {
                    $result = Users::find()->where([
                        'username' => $this->$attribute
                    ])->exists();
                    if ($result) {
                        $this->addError('User already exists');
                    }
                },
                'on' => self::USER_REGISTRATION
            ],
            [
                ['password', 'password_repeat', 'username', 'email'],
                'required',
                'message' => $this->_setMessage('Поле должно быть заполнено'),
                'on' => self::MENTOR_REGISTRATION
            ],
            [
                'password',
                'compare',
                'message' => $this->_setMessage('Пароли не совпадают'),
                'on' => self::MENTOR_REGISTRATION
            ],
            [
                'username',
                function ($attribute, $params) {
                    $result = Users::find()->where([
                        'username' => $this->$attribute
                    ])->exists();
                    if ($result) {
                        $this->addError('User already exists');
                    }
                },
                'on' => self::MENTOR_REGISTRATION
            ],
            [
                ['mentorId', 'clientId'],
                'required',
                'on' => self::BINDING_FROM_MENTOR
            ],
            [
                'mentorId',
                'exist',
                'targetClass' => Users::className(),
                'targetAttribute' => 'id',
                'on' => self::BINDING_FROM_MENTOR
            ],
            [
                'clientId',
                'exist',
                'targetClass' => Users::className(),
                'targetAttribute' => 'id',
                'on' => self::BINDING_FROM_MENTOR
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин:',
            'password' => ' Пароль:',
            'password_repeat' => 'Повторите пароль:',
            'email' => 'Электронный адрес:'
        ];
    }

    public function loginUser()
    {
        try {
            $user = Users::findIdentityByUsername($this->username);
            if (!empty($user) && $user->type === 'user') {
                $validate = Users::validatePassword($this->password, $user->password);
                if ($validate) {
                    $accessToken = \Yii::$app->getSecurity()->generateRandomString();
                    \Yii::$app->cache->set($accessToken, ['id' => $user->id]);
                    \Yii::$app->user->login($user);
                    return $accessToken;
                }
            }
        } catch (\Throwable $error) {
            return false;
        }
        return false;
    }
    public function loginMentor()
    {
        try {
            $mentor = Users::findIdentityByUsername($this->username);
            if (!empty($mentor) && $mentor->type === 'mentor') {
                $validate = Users::validatePassword($this->password, $mentor->password);
                if ($validate) {
                    $accessToken = \Yii::$app->getSecurity()->generateRandomString();
                    \Yii::$app->cache->set($accessToken, ['id' => $mentor->id]);
                    \Yii::$app->user->login($mentor);
                    return $accessToken;
                }
            }
        } catch (\Throwable $error) {
            return false;
        }
        return false;
    }
    public function registerUser()
    {
        try {
            $newUser = new Users();
            $bindingNewUser = $this->_bindUserData($newUser);
            $bindingNewUser->type = 'user';
            $bindingNewUser->save();

            $authManager = \Yii::$app->getAuthManager();
            $role = $authManager->getRole('user');
//            $authManager->createRole('user');
//            $authManager->add($role);
            $authManager->assign($role, $newUser->getId());

            $accessToken = \Yii::$app->getSecurity()->generateRandomString();
            \Yii::$app->cache->set($accessToken, ['id' => $bindingNewUser->id]);

            \Yii::$app->user->login($bindingNewUser);

            return $accessToken;
        } catch (\Throwable $error) {
            // Залогинить ошибку
            return false;
        }
    }
    public function registerMentor()
    {
        try {
            $newMentor = new Users();
            $bindingNewUser = $this->_bindUserData($newMentor);
            $bindingNewUser->type = 'mentor';
            $bindingNewUser->save();

            $authManager = \Yii::$app->getAuthManager();
            $role = $authManager->getRole('mentor');
//            $authManager->createRole('mentor');
//            $authManager->add($role);
            $authManager->assign($role, $newMentor->getId());

            $accessToken = \Yii::$app->getSecurity()->generateRandomString();
            \Yii::$app->cache->set($accessToken, ['id' => $bindingNewUser->id]);

            \Yii::$app->user->login($bindingNewUser);

            return $accessToken;
        } catch (\Throwable $error) {
            // Залогинить ошибку
            return false;
        }
    }

    /** Реализация менторских запросов */
    /** Метод отправляет запрос от ментора на связывание с пользователем */
    public function sendBindingRequestFromMentorToUser()
    {
        try{
            $mentor = Users::findOne($this->mentorId);
            $client = Users::findOne($this->clientId);
            if (!$mentor || !$client) { return false; }
            $this->bindingHelper($client->id, $mentor->id, UsersMentors::MENTOR_INITIATOR); // Иницыализатор ментор
            return true;
        } catch (\Throwable $error){
            if (YII_ENV_DEV) {
                throw $error;
            }
            // Залогинить ошибку
            return false;
        }
    }
    /** Метод отказа ментором от запроса на связывание с пользователем */
    public function sendUnbindingRequestFromMentorToUser()
    {
        try {
            $mentor = Users::findIdentityByUsername($this->mentorUsername);
            $client = Users::findIdentityByUsername($this->clientUsername);
            $relation = UsersMentors::find()->where(['mentors_id' => $mentor->id, 'users_id' => $client->id, 'status' => 2])->all();
            if(!empty($relation)) {
                foreach($relation as $item){
                    $item->status = 0;
                    $item->save();
                }
                return true;
            }
            return false;
        } catch (\Throwable $error) {
            // Залогинить ошибку
            return false;
        }
    }

    /** Метод ответа ментором на связь с пользователем */
    public function handlePadawanRequest($answer)
    {
        try {
//            $mentor = Users::findOne($this->mentorId);
//            $client = Users::findOne($this->clientId);
            $relation = UsersMentors::find()->where([
                'users_id' => $this->clientId,
                'mentors_id' => $this->mentorId,
                'status' => UsersMentors::PENDING_REQUEST_TO_USER,
                'initialithator' => UsersMentors::USER_INITIATOR
            ])->one();
            if (empty($relation)) return false;
            $relation->status = $answer;
            $relation->save();
            return true;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }


    /** Хэлпер ля отправки запроссов на связывание */
    public function bindingHelper($userId, $mentorId, $initialithator)
    {
        $usersMentors = new UsersMentors();
        $usersMentors->users_id = $userId;
        $usersMentors->mentors_id = $mentorId;
        $usersMentors->status = 2;
        $usersMentors->initialithator = $initialithator; // Иницыализатором являеться ментор
        $usersMentors->save();
    }

    /** Метод удаляет пользователя из уеников */
    public function removeOwnPadawan()
    {
        try {
            $transaction = \Yii::$app->getDb()->beginTransaction();

            $relation = $this->findMentorUserRelation(UsersMentors::ACTIVE_USER, true);
            if (empty($relation)) return false;
//            foreach ($relations as $relation) {
                $relation->status = 0;
                $relation->save();
//            }

            $transaction->commit();
            return true;
        } catch (\Throwable $error) {
            // Залогинить ошибку
            $transaction->rollBack();
            return false;
        }
    }

    /** Методы отправки ответа на запрос связывания */
    public function sendAnswerToBindingRequest($response)
    {
        
    }

    /** Реализация пользовательских запросов */
    /** Метод отправляет запрос от пользователя на связывание с ментором */
    public function sendBindingRequestFromUserToMentor()
    {
        try {
            $mentor = Users::findOne($this->mentorId);
            $client = Users::findOne($this->clientId);
            if(empty($client) || empty($mentor)) return false; // На всяк случай
            $relation = UsersMentors::find()->where([
                'users_id' => $client->id,
                'mentors_id' => $mentor->id,
                'initialithator' => 2
            ])->andWhere(['in', 'status', [
                UsersMentors::ACTIVE_USER,
                UsersMentors::PENDING_REQUEST_TO_USER
            ]])->all();
            if(empty($relation)) {
                $this->bindingHelper($client->id, $mentor->id, UsersMentors::USER_INITIATOR); // Иницыализатор пользователь
                return true;
            }
            return false;
        } catch (\Throwable $error) {
            // Залогинить ошибку
            return false;
        }
    }

    /**
     * Метод возвращает текущего ментора пользователя и связаную с ним информацию
     * ???
     */
    public function getCurrentMentor($userId)
    {
        try {
            $mentor = UsersMentors::findActiveMentor($userId);
            if (!empty($mentor)) {
                return \Yii::$app->userManager->getUserInfo($mentor->mentors);
            }
            return false;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
    /**
     * Метод возвращает ментора которому пользователь сделал запрос
     * ???
     */
    public function getRequestToMentor($userId)
    {
        try {
            $requestedMentor = UsersMentors::findRequestToMentor($userId);
            if (!empty($requestedMentor)) {
//            $requestedMentor = $requestedMentor->mentors;
                return \YIi::$app->userManager->getUserInfo($requestedMentor->mentors);
            }
            return false;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /**
     * method fetch all users(mentors) which send request to current user
     */
    public function getRequestFromMentor($userId, $offset, $limit)
    {
        $mentors = UsersMentors::find()
            ->where([
                'users_id' => $userId,
                'status' => UsersMentors::PENDING_REQUEST_TO_USER,
                'initialithator' => UsersMentors::MENTOR_INITIATOR
            ])
            ->with(['mentors' => function($query) {
                $query->with([
                    'rating',
                    'mentor',
                    'padawans',
                    'userMacrocicles',
                    'trainingPlans'
                ]);
            }])
            ->offset($offset)
            ->limit($limit)
            ->orderBy([
                'id' => SORT_DESC
            ])
            ->all();
        if(empty($mentors)) return false;
        $data = array_map(function($mentorObj) use ($userId) {
            return \Yii::$app->userManager->getUserInfo($mentorObj->mentors);
        }, $mentors);
        return $data;
    }

    /** Метод возвращает листинг пользователей */
    public function getAllUsers($offset, $limit)
    {
        try {
            /**
             * get own account with relations for perform filtering
             */
            $currentUser = Users::find()
                ->where([
                    'id' => \Yii::$app->user->identity->getId()
                ])->with([
                    'padawans',
                    'mentor'
                ])
                ->one();
            if (isset($currentUser)) {
                if (isset($currentUser->padawans)) {
                    $ownPadawans = array_map(function (\yii\db\ActiveRecord $item) {
                        return $item->id;
                    }, $currentUser->padawans);
                }
                if (isset($currentUser->mentor)) {
                    $ownMentorId = $currentUser->mentor->id;
                }
            }

            /**
             * getting users
             */
            $query = Users::find()->with([
                'macrocicles',
                'rating',
                'userMacrocicles' => function ($query) {
                    $query->andWhere(['in', 'state', [
                        MacrocicleUsers::CURRENT_MACROCICLE,
                        MacrocicleUsers::COMPLETED_MACROCICLE
                    ]])
                        ->with(['macrocicle' => function ($query) {
                            $query->with('rating');
                        }]);
                },
                'mentor',
                'padawans'
            ]);
            if($offset) {
                $query->offset($offset);
            }
            if($limit) {
                $query->limit($limit);
            }
            $users = $query->all();
            $data = [];
            $server = 'http://' . $_SERVER['SERVER_NAME'] . '/';
            $path = \Yii::getAlias(\Yii::$app->params['pathToAvatars']);
            foreach ($users as $user) {
                $completedPlans = [];
                $currentPlan = [];
                foreach ($user->userMacrocicles as $trainingPlanRef) {
                    if ($trainingPlanRef->state === MacrocicleUsers::CURRENT_MACROCICLE) {
                        $currentPlan[] = [
                            'id' => $trainingPlanRef->macrocicle->id,
                            'sessionId' => $trainingPlanRef->session_id,
                            'name' => $trainingPlanRef->macrocicle->name,
                            'readme' => $trainingPlanRef->macrocicle->readme,
                            'category' => $trainingPlanRef->macrocicle->category,
                            'rating' => Macrocicle::computateRating($trainingPlanRef->macrocicle),
                        ];
                    }
                    if ($trainingPlanRef->state === MacrocicleUsers::COMPLETED_MACROCICLE) {
                        $completedPlans[] = [
                            'id' => $trainingPlanRef->macrocicle->id,
                            'sessionId' => $trainingPlanRef->session_id,
                            'name' => $trainingPlanRef->macrocicle->name,
                            'readme' => $trainingPlanRef->macrocicle->readme,
                            'category' => $trainingPlanRef->macrocicle->category,
                            'rating' => Macrocicle::computateRating($trainingPlanRef->macrocicle)
                        ];
                    }
                }

                /**
                 * filtering
                 */
                if (isset($ownPadawans) && in_array($user->id, $ownPadawans)) {
                    continue;
                }
                if (isset($ownMentorId) && intval($ownMentorId) === intval($user->id)) {
                    continue;
                }
                if ($user->id === \Yii::$app->user->identity->id) {
                    continue;
                }

                $data[] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'type' => $user->type,
                    'avatar' => $user->getAvatar(),
                    'rating' => Users::computeUserRating($user),
                    'completedTrainings' => count($user->macrocicles), /* ??? */
                    'mentor' => $user->mentor,
                    'trainings' => [
                        'completedPlans' => $completedPlans,
                        'currentPlan' => $currentPlan,
                        'ownPlans' => array_map(function ($_macrocicle) {
                            return Macrocicle::getMacrocicleInfo($_macrocicle);
                        }, $user->trainingPlans)
                    ],
                    'countPadawans' => count($user->padawans)
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



    /** Метод отвязыает ментора с заданым статусом связи */
    public function handleUnbindMentor($status)
    {
        try {
            $relations = $this->findMentorUserRelation($status);
            foreach ($relations as $rel) {
                $rel->status = 0;
                $rel->save();
            }
            return true;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** Метод для поиска (связи/запроса на связь) ментора с пользователем */
    public function findMentorUserRelation($status, $one = null)
    {
        if(empty($this->clientId) || empty($this->mentorId)) return false;
        $client = Users::findOne($this->clientId);
        $mentor = Users::findOne($this->mentorId);
        if(empty($client) || empty($mentor)) return false;
        $relationRequest = UsersMentors::find()->where([
            'users_id' => $client->id,
            'mentors_id' => $mentor->id,
            'status' => $status
        ]);
        if (!$one) {
            $relation = $relationRequest->all();
        } else {
            $relation = $relationRequest->limit(1)->one();
        }
        return $relation;
    }

    /**
     * Метод для ответа пользователя на запрос ментора
     * reconstructed!
     */
    public function requestFromMentorHandler($answer)
    {
        try {
            $transaction = \Yii::$app->getDb()->beginTransaction();

            /** Если ответ положительный. Находим отсальные предложения и отклоняем их */
            $result = false;
            if($answer === true){
                $oldRelations = UsersMentors::find()
                    ->where([
                        'users_id' => \Yii::$app->user->getId(),
                        'status' => UsersMentors::ACTIVE_USER
                    ])
                    ->all();
                array_walk($oldRelations, function(&$relObj) {
                    $relObj->status = UsersMentors::UNACTIVE_USER;
                    $relObj->save();
                });
            }

            /** Находим нужного ментора и утанавливаем ответ */
            $relation = $this->findMentorUserRelation(UsersMentors::PENDING_REQUEST_TO_USER, true);
            if ($relation) {
                $relation->status = UsersMentors::ACTIVE_USER;
                $relation->save();
                $relation->date = gmdate('Y-m-d H:i:s');
                $result = true;
            }

            $transaction->commit();
            return $result;
        } catch (\Throwable $error) {
            // Залогинить ошибку
            $transaction->rollBack();
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    /** Methods for fetching user data */
    public function getEssentialUserData($userId, $currentUserId)
    {
        try {
            $server = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
            $avatarsPath = \Yii::getAlias(\Yii::$app->params['pathToAvatars']);
            $iconPath = \Yii::getAlias(\Yii::$app->params['pathToFieldsIcons']);
            $ratingQuery = (new \yii\db\Query())->select('AVG(value)')
                ->from('user_rating')
                ->where(['users_id' => $userId])
                ->createCommand()
                ->getRawSql();
            $result = (new \yii\db\Query())
                ->select(
                    'u.username, u.type, u.avatar, u.id,'
                    .'ucf.id AS identifier,'
                    .'ucf.label AS contact_label,'
                    .'ucf.icon AS contact_icon,'
                    .'ucf.group AS contact_group,'
                    .'ucv.value AS contact_value,'
                    ."($ratingQuery) AS rating,"
                    .'ucv.id AS field_value_id,'
                    ."ru.id AS relation_user_id,"
                    .'ru.username AS relation_username,'
                    .'ru.type AS relation_user_type,'
                    .'ru.avatar AS relation_user_avatar,'
                    .'uf.followed_id AS followed_id'
                )
                ->from(['u' => 'users'])
                ->leftJoin(['ucv' => 'user_contact_values'], "ucv.users_id=u.id")
                ->leftJoin(['ucf' => 'user_contact_fields'], "ucf.id=ucv.field_id AND ucf.for=IF(u.type='mentor', 1, 2)")
//                ->leftJoin(['ur' => 'user_rating'], "ur.users_id=u.id")
                ->leftJoin(['um' => 'users_mentors'], "IF(u.type='mentor', um.mentors_id=u.id, um.users_id=u.id) AND um.status=1")
                ->leftJoin(['ru' => 'users'], "IF(u.type='mentor', ru.id=um.users_id, ru.id=um.mentors_id)")
                ->leftJoin(['uf' => 'user_follow'], [
                    'uf.follower_id' => $currentUserId,
                    'uf.followed_id' => $userId
                ])
                ->where(['u.id' => $userId])
                ->all();
            if (empty($result)) { return $result; }
            $data = [];
            foreach ($result as $item) {
                $tmp = $item;
                if (isset($tmp['avatar']) && !empty($tmp['avatar'])) {
                    $tmp['avatar'] = $server . $avatarsPath . $tmp['avatar'];
                }
                if (isset($tmp['relation_user_avatar']) && !empty($tmp['relation_user_avatar'])) {
                    $tmp['relation_user_avatar'] = $server . $avatarsPath . $tmp['relation_user_avatar'];
                }
                if (isset($tmp['contact_icon']) && !empty($tmp['contact_icon'])) {
                    $tmp['contact_icon'] = $server . $iconPath . $tmp['contact_icon'];
                }
                $data[] = $tmp;
            }
            return $data;
        } catch (\Exception $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function followUser($followedId, $followerId)
    {
        $result = null;
        try {
            $connect = \Yii::$app->db->pdo;
            $result = $connect->query("SELECT follow_user($followerId, $followedId)", \PDO::FETCH_NUM);
            $result = !empty($result) ? $result->fetch()[0] : null;
            return $result;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }

    public function setRating($votedUserId, $evaluatedUserId, $value)
    {
        try {
            $result = \Yii::$app->db->createCommand('CALL set_user_rating(:voting_user, :evaluate_user, :val)', [
                ':voting_user' => $votedUserId,
                ':evaluate_user' => $evaluatedUserId,
                ':val' => $value
            ])->execute();
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


    private function _userDataFormater($userObj)
    {
        return [
            'username' => $userObj->username,
            'type' => $userObj->type
        ];
    }
    private function _bindUserData($newUser)
    {
        $newUser->username = $this->username;
        $newUser->password = Users::generatePassword($this->password);
        $newUser->auth_key = Users::generateAuthKey();
        $newUser->email = $this->email;
        $newUser->avatar = \Yii::getAlias('@frontend/web/avatars/' . \Yii::$app->params['defaultUserAvatar']);
        return $newUser;
    }
    public function _setMessage($message)
    {
        return "<div class='ui hidden fitted divider'></div><div class='ui error message'>$message</div>";
    }
}