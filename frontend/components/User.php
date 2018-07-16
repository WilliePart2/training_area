<?php

namespace frontend\components;

use frontend\models\Users;
use yii\base\Component;
use frontend\helper_models\ExtendedUserModel;
use frontend\models\Macrocicle;
use frontend\models\MacrocicleUsers;

class User extends Component
{
    /**
     * @param $user
     * @return ExtendedUserModel
     * @throws \Throwable
     */
    public function getUserInfo($user) /* ExtendedUserModel */
    {
        if ($user instanceof \frontend\models\Users) {
            return $this->_getSingleUserInfo($user);
        } else {
            echo 'enother return value';
            if (is_array($user)) {
                $isArrayOfObjects = false;
                array_walk($user, function($item) use (&$isArrayOfObjects) {
                    if ($item instanceof Users) {
                        $isArrayOfObjects = true;
                        return;
                    }
                    if ($isArrayOfObjects) {
                        throw new \Exception('Array must be either condition either array of objects');
                    }
                });
                if ($isArrayOfObjects) {
                    return array_map(function($item) {
                        return $this->_getSingleUserInfo($item);
                    }, $user);
                }
                $condition = $user;
            } else {
                $condition = [
                    'id' => $user
                ];
            }
            $query = Users::find()
                ->with([
                    'rating',
                    'mentor',
                    'padawans',
                    'userMacrocicles',
                    'trainingPlans'
                ])
                ->where($condition);
            if (is_array($user)) {
                $userObj = $query->limit(1)->one();
                if ($userObj) {
                    return $this->_getSingleUserInfo($userObj);
                }
            } else {
                $userObjects = $query->all();
                if ($userObjects) {
                    return array_map(function ($userObj) {
                        return $this->_getSingleUserInfo($userObj);
                    }, $userObjects);
                }
            }
        }
        return null;

    }
    private function _getSingleUserInfo(Users $userObj): ExtendedUserModel
    {
        try {
            $receivedUser = new ExtendedUserModel([
                'id' => $userObj->id,
                'username' => $userObj->username,
                'type' => $userObj->type,
                'avatar' => $userObj->getAvatar(),
                'mentor' => (function ($mentor) {
                    return $mentor ? $this->getUserInfo($mentor) : null;
                })($userObj->mentor),
                'rating' => Users::computeUserRating($userObj),
                'trainings' => [
                    'completedPlans' => (function ($user) {
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
                    'currentPlan' => (function ($user) {
                        if ($user->userMacrocicles) {
                            $completedPlan = array_filter($user->userMacrocicles, function (\frontend\models\MacrocicleUsers $item) {
                                $item->state === MacrocicleUsers::COMPLETED_MACROCICLE ? true : false;
                            });
                            if ($completedPlan) {
                                return Macrocicle::getMacrocicleInfo($completedPlan->microcicle);
                            }
                        }
                        return null;
                    })($userObj),
                    'ownPlans' => (function ($user) {
                        if ($user->trainingPlans) {
                            $ownPlans = array_filter($user->trainingPlans, function (\frontend\models\Macrocicle $macrocicle) use ($user) {
                                return $macrocicle->mentor_id === $user->id ? true : false;
                            });
                            if ($ownPlans) {
                                return array_map(function (\frontend\models\Macrocicle $macrocicle) {
                                    return Macrocicle::getMacrocicleInfo($macrocicle);
                                }, $ownPlans);
                            }
                        }
                        return [];
                    })($userObj)
                ],
                'countPadawans' => $userObj->padawans ? count($userObj->padawans) : 0
            ]);
            return $receivedUser;
        } catch (\Throwable $error) {
            if (YII_ENV_DEV) {
                throw $error;
            }
            return false;
        }
    }
}