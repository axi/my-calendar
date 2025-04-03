# Axi - My Calendar

[![Translate on Weblate](https://hosted.weblate.org/widget/my-calendar/my-calendar/language-badge.svg)](https://hosted.weblate.org/engage/my-calendar)
[![Translate on Weblate](https://hosted.weblate.org/widget/my-calendar/my-calendar/svg-badge.svg)](https://hosted.weblate.org/engage/my-calendar)

This library allows you to get information about special dates based on one person's birthday (or any date).  
It's been know to be the best tool to wish a happy 15000th day to people (no it's not).

## Installation
You can install this package by using [Composer](http://getcomposer.org), running the following command:

```sh
composer require axi/mycalendar
```

## Usage
### Basic usage

```php
<?php

use Axi\MyCalendar\Service\CalendarService;

require_once './vendor/autoload.php';

echo (new CalendarService())
    ->getEventsFromDate(
        dateTime: new DateTime("1984-01-12"),
        format: 'json'
    )->getContent();
```

### Available formats
- json
- ical (require "eluceo/ical")
- none (Internal Event object)

### Available recipes
Several recipes are already available

- **AverageAgeFirstChildren**: Women's mean age at 1st childbirth in 2022 in the OECD
- **Now**: Special recipe to dispaly the current day within the date list
- **PlanetsRevolutions**: list the dates where planets (other than earth) have made one or several revolutions
- **SleepTime**: Estimated average sleep total time in years
- **ThousandsDays**: Fancy dates where people reach multiple of thousand days

See [Issues](https://github.com/axi/my-calendar/issues) for a list of propositions for more recipes

## Translations

We use [Weblate](https://hosted.weblate.org/engage/my-calendar) for collaborative translations! You can help translate
this project into different languages by joining our Weblate project.

#### How to Contribute:

1. **Sign up or log in** on Weblate
2. Go to our project page: [Weblate Project](https://hosted.weblate.org/engage/my-calendar)
3. Pick a language and start translating!
4. Your translations will be reviewed and merged automatically

Thank you for helping make this project accessible to more people!

## Contributing
Feel free to submit new Renderers or Recipes

## Licence
This library is released under the [GPL-3.0-or-later licence](COPYING).
