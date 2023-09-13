<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AlertReminderBundle\EventListener;

use Contao\BackendUser;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Message;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\UtilsBundle\Dca\DcaUtil;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Hook("loadDataContainer")
 */
class LoadDataContainerListener
{
    private static bool $run = false;

    private DcaUtil $dcaUtil;
    private Utils $utils;
    private RequestStack $requestStack;
    private ParameterBagInterface $parameterBag;
    private TranslatorInterface $translator;

    public function __construct(Utils $utils, RequestStack $requestStack, ParameterBagInterface $parameterBag, TranslatorInterface $translator, DcaUtil $dcaUtil)
    {
        $this->translator = $translator;
        $this->dcaUtil = $dcaUtil;
        $this->utils = $utils;
        $this->requestStack = $requestStack;
        $this->parameterBag = $parameterBag;
    }

    public function __invoke(string $table): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$this->utils->container()->isBackend()) {
            return;
        }

        // only run once
        if (static::$run) {
            return;
        }

        static::$run = true;

        $this->initAssets();
        $this->initAlerts($request);
    }

    protected function initAssets()
    {
        $GLOBALS['TL_CSS']['contao-alert-reminder-bundle'] = 'bundles/heimrichhannotalertreminder/js/contao-alert-reminder-bundle.css';
    }

    protected function initAlerts(Request $request)
    {
        // skip for certain backend views
        if (\in_array($request->get('_route'), ['contao_backend_alerts'])
            || ('contao_backend' === $request->get('_route') && ('maintenance' === $request->query->get('do'))
                || (!$request->query->has('do'))
            )) {
            return;
        }

        // skip in popups
        if ($request->query->has('popup')) {
            return;
        }

        // skip in the alert queue
        if ('alert_queue' === $request->query->get('do')) {
            return;
        }

        if (!isset($GLOBALS['TL_HOOKS']['getSystemMessages']) || !\is_array($GLOBALS['TL_HOOKS']['getSystemMessages'])) {
            return;
        }

        $types = [];
        $config = $this->parameterBag->get('huh_alert_reminder');

        if (!isset($config['alert_types']) || !\is_array($config['alert_types'])) {
            return;
        }

        $allowedTypes = array_map(function ($type) {
            return $type['name'];
        }, $config['alert_types']);

        $deactivatedAlertReminders = StringUtil::deserialize(BackendUser::getInstance()->deactivatedAlertReminders, true);

        foreach ($GLOBALS['TL_HOOKS']['getSystemMessages'] as $callback) {
            $message = System::importStatic($callback[0])->{$callback[1]}();

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
