<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AlertReminderBundle\EventListener;

use Contao\BackendUser;
use Contao\Message;
use HeimrichHannot\RequestBundle\Component\HttpFoundation\Request;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use HeimrichHannot\UtilsBundle\Dca\DcaUtil;
use HeimrichHannot\UtilsBundle\String\StringUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoadDataContainerListener
{
    private static $run = false;

    /**
     * @var ContainerUtil
     */
    private $containerUtil;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var StringUtil
     */
    private $stringUtil;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var DcaUtil
     */
    private $dcaUtil;

    public function __construct(ContainerInterface $container, TranslatorInterface $translator, ContainerUtil $containerUtil, Request $request, StringUtil $stringUtil, DcaUtil $dcaUtil)
    {
        $this->translator = $translator;
        $this->containerUtil = $containerUtil;
        $this->request = $request;
        $this->stringUtil = $stringUtil;
        $this->container = $container;
        $this->dcaUtil = $dcaUtil;
    }

    public function __invoke($table)
    {
        if (!$this->containerUtil->isBackend()) {
            return;
        }

        // only run once
        if (static::$run) {
            return;
        }

        static::$run = true;

        $this->initAssets();
        $this->initAlerts();
    }

    public function initAssets()
    {
        $GLOBALS['TL_CSS']['contao-alert-reminder-bundle'] = 'bundles/heimrichhannotalertreminder/js/contao-alert-reminder-bundle.css';
    }

    public function initAlerts()
    {
        // skip for certain backend views
        if (\in_array($this->containerUtil->getCurrentRequest()->get('_route'), ['contao_backend_alerts']) || ('contao_backend' === $this->containerUtil->getCurrentRequest()->get('_route') && ('maintenance' === $this->request->getGet('do')) || (!$this->request->getGet('do')))) {
            return;
        }

        // skip in popups
        if ($this->request->getGet('popup')) {
            return;
        }

        // skip in the alert queue
        if ('alert_queue' === $this->request->getGet('do')) {
            return;
        }

        if (!isset($GLOBALS['TL_HOOKS']['getSystemMessages']) || !\is_array($GLOBALS['TL_HOOKS']['getSystemMessages'])) {
            return;
        }

        $types = [];
        $config = $this->container->getParameter('huh_alert_reminder');

        if (!isset($config['alert_types']) || !\is_array($config['alert_types'])) {
            return;
        }

        $allowedTypes = array_map(function ($type) {
            return $type['name'];
        }, $config['alert_types']);

        $deactivatedAlertReminders = \Contao\StringUtil::deserialize(BackendUser::getInstance()->deactivatedAlertReminders, true);

        foreach ($GLOBALS['TL_HOOKS']['getSystemMessages'] as $callback) {
            $message = \System::importStatic($callback[0])->{$callback[1]}();

            if (preg_match('@<div class="alert-reminder" data-type="(?P<type>[^"]+)">@i', $message, $matches) &&
                isset($matches['type']) && \in_array($matches['type'], $allowedTypes) &&
                !\in_array($matches['type'], $deactivatedAlertReminders)) {
                $types[] = $this->translator->trans('huh.alert_reminder.alert.type.'.$matches['type']);
            }
        }

        $alertsLink = $this->dcaUtil->getPopupWizardLink([], [
                'title' => $this->translator->trans('huh.alert_reminder.message.solve_issue'),
                'style' => 'float: right;',
                'route' => 'contao_backend_alerts',
            ]
        );

        if (\count($types) > 0) {
            Message::addError($this->translator->trans('huh.alert_reminder.message.issues_existing', [
                '%issues%' => implode(', ', $types),
                '%alertsLink%' => $alertsLink,
            ]));
        }
    }
}
