<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AlertReminderBundle\DataContainer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserContainer
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ContainerInterface $container, TranslatorInterface $translator)
    {
        $this->container = $container;
        $this->translator = $translator;
    }

    public function getAlertRemindersAsOptions()
    {
        $config = $this->container->getParameter('huh_alert_reminder');

        if (!isset($config['alert_types']) || !\is_array($config['alert_types'])) {
            return [];
        }

        $options = [];

        foreach ($config['alert_types'] as $type) {
            $options[$type['name']] = $this->translator->trans('huh.alert_reminder.alert.type.'.$type['name']) ?: $type['name'];
        }

        return $options;
    }
}
