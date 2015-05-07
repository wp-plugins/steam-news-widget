=== Steam News Widget ===
Contributors: softsultant
Tags: steam, steampowered, games, news, game news, widget, plugin.
Requires at least: 3.0.0
Tested up to: 4.2.1
Stable tag: 1.1.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Shows news for selected games. The widget regularly fetches data from http://steampowered.com.

== Description ==

The plugin adds a widget that shows news for selected games.

= Configuration Options =

* Steam Game IDs:
	Games are referenced by their steam app id.
	Just enter a comma-separated list of app ids, like "440, 570, 730" (without quotes).
	You can find the full list of steam app ids at http://api.steampowered.com/ISteamApps/GetAppList/v0002/ .

* Maximum Number of News:
	The maximum number of news to show.

* News Length:
	It limits the length of shown news.

* HTML Template:
	Allows you to define HTML layout of the widget in a way you like.
	The news data can be extracted with special tags {{some_news.some_property}}.
	Just put those in a right place. The plugin will replace them with actual data.
	To iterate through all fetched news use `<foreach>` tag,
	e.g. `<foreach iterator="{{news}}" in="{{newslist}}">{{news.text}}</foreach>`,
	where {{newslist}} is a predefined value and it points to the news set.

	Each news has following properties:
	- title, e.g. {{news.title}}.
	- url,   e.g. {{news.url}}.
	- text,  e.g. {{news.text}}.
	- date,  e.g  {{news.date}}.
	- time,  e.g. {{news.time}}.

= Other Notes =

* The date/time format as well as the timezone of the news match the ones from Wordpress "General Settings".

* The widget's content is updated about every hour.

* The plugin requires PHP 5.3.3 or higher.

* A special requirement from the data provider:
	Each page that uses the Steam Web API must contain a link to http://steampowered.com with the text "Powered by Steam".
	We suggest that you put this link in your footer so it is out of the way but still visible to interested users.

== Installation ==

1. Copy the contents of this archive to the `/wp-content/plugins/` directory
1. Activate the plugin through the "Plugins" menu in WordPress
1. Go to the "Appearance > Widgets" and add "Steam News" to the sidebar
1. Choose preferred options and press "Save".

== Frequently Asked Questions ==

Nothing here yet.

== Screenshots ==

1. Screenshot
2. Screenshot
3. Screenshot
4. Widget Options

== Changelog ==

= 1.0.0 =

* Initial release.

= 1.1.0 =

* Multiple app id support.

= 1.1.1 =

* Fixed a non-critical issue. The bug prevented news steam updates in rare cases.


== Upgrade Notice ==

Nothing here yet.
