<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AlertReminderBundle\BackendModule;

use Contao\BackendModule;
use Contao\System;
use HeimrichHannot\AlertReminderBundle\Event\AddAlertsToAlertReminderQueueEvent;

class AlertQueueModule extends BackendModule
{
    protected $strTemplate = 'be_alert_reminder_queue';

    public function generate()
    {
        return parent::generate();
    }

    protected function compile()
    {
        $eventDispatcher = System::getContainer()->get('event_dispatcher');

        $event = $eventDispatcher->dispatch(
            new AddAlertsToAlertReminderQueueEvent([]),
            AddAlertsToAlertReminderQueueEvent::NAME
        );

        $this->Template->alerts = $event->getAlerts();
    }
}
