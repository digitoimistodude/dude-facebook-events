# Dude Facebook events
WordPress plugin to get upcoming events for Facebook page.

Basically this plugin fetches upcoming events for Facebook page, saves those to transient and next requests will be served from there. After transient has expired and deleted, new fetch from Facebook will be made and saved to transient. This implements very simple cache.

Handcrafted with love at [Digitoimisto Dude Oy](http://dude.fi), a Finnish boutique digital agency in the center of Jyväskylä.

## Table of contents
1. [Please note before using](#please-note-before-using)
2. [License](#license)
3. [Usage](#usage)
  1. [Usage example for displaying events](#usage-example-for-displaying-events)
  2. [Limiting event count](#limiting-event-count)
4. [Hooks](#hooks)
6. [Composer](#composer)
7. [Contributing](#contributing)

## Please note before using
This plugin is not meant to be "plugin for everyone", it needs at least some basic knowledge about php and css to add it to your site and making it look beautiful.

This is a plugin in development for now, so it may update very often.

## License
Dude Facebook events is released under the GNU GPL 2 or later.

## Usage
This plugin does not have settings page or provide anything visible on front-end. So it's basically dumb plugin if you don't use any filters listed below.

Only mandatory filter to use is `dude-facebook-events/parameters/access_token`.

Get events by calling function `dude_facebook_events()->get_events()`, pass Facebook id as a only argument. It id can be obtained with [this tool](http://findmyfbid.com/).

### Usage example for displaying events

1. Go to [developers.facebook.com](https://developers.facebook.com/) and create app for your WordPress site
2. Generate access token by going [Facebook Graph API Explorer](https://developers.facebook.com/tools/explorer/). Select your app in **Application:** dropdown, select **Get Token** and **Get App Token**
3. Copy **Access Token** and create filter that returns access token to your **functions.php** (remember to change prefix *yourtexdomain* to your app/site name):

```php
<?php
/**
 * Facebook events
 */
 add_filter('dude-facebook-events/parameters/access_token', 'yourtexdomain_fb_access_token' );
 function yourtexdomain_fb_access_token() {
    return 'appid|appsecret';
 }
```

4. Get your Facebook page numeric ID from [findmyfbid.com](http://findmyfbid.com/). Go to the page from where you want your Facebook events to be displayed, for example **front-page.php** and loop the events and do stuff (it's always good idea to `var_dump` the data to see what's there to use:

```php
$events = dude_facebook_events()->get_events( '569702083062696' );

foreach ( $events as $item ) :
    echo $item['name'];
    echo $item['description'];
endforeach;
```

### Limiting event count

```php
// Limit events to 6 items
add_filter('dude-facebook-events/parameters/limit', 'yourtexdomain_fb_limit' );
function yourtexdomain_fb_limit( $parameters ) {
  return '6';
}
```

## Hooks
All the settings are set with filters, and there is also few filters to change basic functionality and manipulate data before caching.

#### `dude-facebook-events/parameters/access_token`
You need most likely global access token to get events for page, it's App ID and secret separated with |.

Defaults to empty string.

#### `dude-facebook-events/events_transient`
Change name of the transient for events. Passed arguments are default name and facebook id.

Defaults to `dude-fb-events-$fbid`.

#### `dude-facebook-events/api_call_parameters`
Modify api call parameters just before sending those. Only passed argument is array of default parameters.

Defaults to access_token and limit.

#### `dude-facebook-events/events`
Manipulate or use data before it's cached to transient. Only passed argument is array of events.

#### `dude-facebook-events/events_transient_lifetime`
Change events cache lifetime. Only passed argument is default lifetime in seconds.

Defaults to 600 (= ten minutes).

To use with composer, run `composer require digitoimistodude/dude-facebook-events dev-master` in your project directory or add `"digitoimistodude/dude-facebook-events":"dev-master"` to your composer.json require.

## Contributing
If you have ideas about the plugin or spot an issue, please let us know. Before contributing ideas or reporting an issue about "missing" features or things regarding to the nature of that matter, please read [Please note section](#please-note-before-using). Thank you very much.
