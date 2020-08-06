# Contao Alert Reminder Bundle

This utility bundle offers functionality to remind the backend users of existing alerts in the Contao CMS.

## Features

- looks for existing (configurable) contao backend alerts (added via the hook `getSystemMessages`) and adds a backend message in order to remind the backend user of it

## Use case

You might ask what's the use case of this functionality?

The original use case was in order to remind the backend users to add missing `alt` attributes to images (accessibility).

Without the functionality, the user might forget about it. Making the `alt` mandatory has also not been an option because of contao's simple file upload functionality which doesn't contain the field at all.

Hence the need for reminding the backend user of it.

The implementation of this behavior is built into [heimrichhannot/contao-accessibility-bundle](https://github.com/heimrichhannot/contao-accessibility-bundle) which uses this bundle.

## Installation

1. Run `composer require heimrichhannot/contao-alert-reminder-bundle` and update the database.

## Technical instructions

### Add new contao alerts to the context of this bundle

1. Register a `getSystemMessages` hook as usual in contao:
   
   ```php
   // config.php (or use yaml tags in contao 4.9+)
   $GLOBALS['TL_HOOKS']['getSystemMessages']['myBundle'] = [
       \Acme\MyBundle\EventListener\GetSystemMessagesListener::class, '__invoke'
   ];
   ```
   
   ```php
   // GetSystemMessagesListener.php
   
   namespace Acme\MyBundle\EventListener;
   
   use Acme\AlertReminderBundle\Manager\AlertReminderManager;
   use Acme\RequestBundle\Component\HttpFoundation\Request;
   use Symfony\Component\Translation\TranslatorInterface;
   
   class GetSystemMessagesListener
   {
       /**
        * @var TranslatorInterface
        */
       private $translator;
       /**
        * @var Request
        */
       private $request;
       /**
        * @var AlertReminderManager
        */
       private $alertReminderManager;
   
       public function __construct(AlertReminderManager $alertReminderManager, TranslatorInterface $translator, Request $request)
       {
           $this->translator           = $translator;
           $this->request              = $request;
           $this->alertReminderManager = $alertReminderManager;
       }
   
       public function __invoke()
       {
           $message = '<p class="tl_error">My alert message</p>';
   
           return $this->alertReminderManager->prepareSystemMessage($message, 'some_alert_type_alias');
       }
   }
   ```
1. In order to activate the reminding you have to add your alert type in your project's `app/config/config.yml`:

   ```yaml
   huh_alert_reminder:
     alert_types:
       - { name: some_alert_type_alias }
   ```
