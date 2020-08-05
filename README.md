# Contao Alert Reminder Bundle

This utility bundle offers functionality to remind the backend users of existing alerts in the Contao CMS.

## Features

- looks for existing contao backend alerts (added via the hook `getSystemMessages`) and adds a backend message in order to remind the backend user of it

## Use case

You might ask what's the use case of this functionality?

The original use case was in order to remind the backend users to add missing `alt` attributes to images (accessibility).

Without the functionality, the user might forget about it. Making the `alt` mandatory has also not been an option because of contao's simple file upload functionality which doesn't contain the field at all.

## Installation

1. Run `composer require heimrichhannot/contao-alert-reminder-bundle` and update the database.

## Configuration

1. TODO
