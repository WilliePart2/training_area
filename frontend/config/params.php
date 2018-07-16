<?php
return [
    'adminEmail' => 'admin@example.com',
    'accessTokenExpire' => 9000,
    'defaultUserAvatar' => 'default_user_icon.png',
    'pathToAvatars' => '@web/avatars/',
    'pathToFieldsIcons' => '@web/fields_icons/',
    'pathToCommonIconsDirectoryForRead' => '@frontend/web/user_states',
    'pathToCommonIconsDirectoryForLoad' => '@web/user_states',
    /*
     * In future will be swap to object with internally params
     * keys in array must be write in snake_case
     */
    'userMessagesParams' => [
        'limit_fetching_messages_from_db' => 10000,
        'limit_sending_messages_to_user' => 30,
        'expire_saving_fetched_messages_in_cache' => 600,
        'fetched_messages_key_for_cache' => 'fetched_messages',
        'fetched_message_members_key_for_cache' => 'fetched_message_members'
    ],
    'trainingCommentParams' => [
        'limit_fetching_comments_from_db' => 10000,
        'training_plan_comment_cache_key' => 'comment_key_'
    ],
    'searchingTrainingPlansPParams' => [
        'cache_key_' => 'search_training_plans_',
        'limit_fetching_rows' => 10000,
        'cacheDuration' => 600
    ]
];
