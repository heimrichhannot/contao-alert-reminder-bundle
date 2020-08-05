<?php

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer']['huhAccessibility'] = [
    \HeimrichHannot\AlertReminderBundle\EventListener\LoadDataContainerListener::class, '__invoke'
];
