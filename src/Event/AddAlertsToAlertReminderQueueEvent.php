<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AlertReminderBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class AddAlertsToAlertReminderQueueEvent extends Event
{
    public const NAME = 'huh.alert_reminder.add_alerts_to_alert_reminder_queue';
    /**
     * @var array
     */
    private $alerts;

    public function __construct(array $alerts)
    {
        $this->alerts = $alerts;
    }

    public function getAlerts(): array
    {
        return $this->alerts;
    }

    public function setAlerts(array $alerts): void
    {
        $this->alerts = $alerts;
    }
}
