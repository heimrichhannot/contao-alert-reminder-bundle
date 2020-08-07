<?php

$dca = &$GLOBALS['TL_DCA']['tl_user'];

/**
 * Palettes
 */
$dca['palettes']['admin']  = str_replace('fop;', 'fop;{contao_alert_reminder_bundle_legend},deactivatedAlertReminders;', $dca['palettes']['admin']);
$dca['palettes']['custom'] = str_replace('fop;', 'fop;{contao_alert_reminder_bundle_legend},deactivatedAlertReminders;', $dca['palettes']['custom']);
$dca['palettes']['admin'] = str_replace('{admin_legend', '{contao_alert_reminder_bundle_legend},deactivatedAlertReminders;{admin_legend', $dca['palettes']['admin']);


/**
 * Fields
 */
$fields = [
    'deactivatedAlertReminders' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_user']['deactivatedAlertReminders'],
        'exclude'          => true,
        'inputType'        => 'checkbox',
        'options_callback' => ['HeimrichHannot\AlertReminderBundle\DataContainer\UserContainer', 'getAlertRemindersAsOptions'],
        'eval'             => ['tl_class' => 'w50', 'multiple' => true],
        'sql'              => "blob NULL"
    ],
];

$dca['fields'] = array_merge(is_array($dca['fields']) ? $dca['fields'] : [], $fields);
