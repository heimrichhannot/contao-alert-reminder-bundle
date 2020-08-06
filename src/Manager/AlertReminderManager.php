<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AlertReminderBundle\Manager;

use HeimrichHannot\UtilsBundle\Database\DatabaseUtil;

class AlertReminderManager
{
    /**
     * @var array
     */
    protected static $cache = [];
    /**
     * @var DatabaseUtil
     */
    private $databaseUtil;

    public function __construct(DatabaseUtil $databaseUtil)
    {
        $this->databaseUtil = $databaseUtil;
    }

    public function prepareSystemMessage(string $messageHtml, string $type)
    {
        return '<div class="alert-reminder" data-type="'.$type.'">'.$messageHtml.'</div>';
    }
}
