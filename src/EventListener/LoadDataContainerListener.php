<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\AlertReminderBundle\EventListener;

use Contao\Message;
use HeimrichHannot\RequestBundle\Component\HttpFoundation\Request;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use HeimrichHannot\UtilsBundle\String\StringUtil;
use Symfony\Component\Translation\TranslatorInterface;

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

    public function __construct(TranslatorInterface $translator, ContainerUtil $containerUtil, Request $request, StringUtil $stringUtil)
    {
        $this->translator = $translator;
        $this->containerUtil = $containerUtil;
        $this->request = $request;
        $this->stringUtil = $stringUtil;
    }

    public function __invoke($table)
    {
        // only run once
        if (static::$run) {
            return;
        }

        static::$run = true;

        // hide for certain backend views
        if (\in_array($this->containerUtil->getCurrentRequest()->get('_route'), ['contao_backend_alerts']) || ('contao_backend' === $this->containerUtil->getCurrentRequest()->get('_route') && ('maintenance' === $this->request->getGet('do')) || (!$this->request->getGet('do')))) {
            return;
        }

        if (!isset($GLOBALS['TL_HOOKS']['getSystemMessages']) || !\is_array($GLOBALS['TL_HOOKS']['getSystemMessages'])) {
            return;
        }

        foreach ($GLOBALS['TL_HOOKS']['getSystemMessages'] as $callback) {
            $message = \System::importStatic($callback[0])->{$callback[1]}();

            if ($this->stringUtil->startsWith($message, '<div class="alert-reminder">')) {
                Message::addError($this->translator->trans('huh.alert_reminder.message.issues_existing'));

                return;
            }
        }
    }
}
