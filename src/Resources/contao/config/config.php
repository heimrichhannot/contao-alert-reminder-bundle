<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer']['huhAccessibility'] = [
    \HeimrichHannot\AlertReminderBundle\EventListener\LoadDataContainerListener::class, '__invoke'
];

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['alert_reminder']['alert_queue'] = [
    'callback' => 'HeimrichHannot\AlertReminderBundle\BackendModule\AlertQueueModule',
];
