<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AlertReminderBundle;

use HeimrichHannot\AlertReminderBundle\DependencyInjection\HeimrichHannotAlertReminderExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotAlertReminderBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new HeimrichHannotAlertReminderExtension();
    }
}
