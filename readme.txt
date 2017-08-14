=== EventsnDocs ===

Contributors: ernestortiz
Plugin URI: https://github.com/ernestortiz/eventsndocs
Donate link:
Tags: events, map, leaflet, googlemap
Requires at least: 3.0.1
Tested up to: 4.6.1
Stable tag: 1.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a simple plugin to display a list of events (OR events on a map), as well as documents related.


== Description ==

With this plugin you can display a list of events (OR events on a map), as well as the documents related to those events.

Some shortcodes are provided to cover a lot of variations. Please, find a short explanation on FAQ section, and see the screenshots to clarify it more.


== Installation ==

1. Upload unzipped plugin directory to the /wp-content/plugins/ directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.


== Frequently Asked Questions ==

= The plugin is happily installed; now what? =

As you can see in the backend of your blog, this plugin creates a new custom post type; where you can find the usual stuff as well as new boxes with some things you can expect on "Events": the dates, the location (yes, with a map to point that), etc. You simply fill all of that fields, publish the post, and voilá!: you get an Event on your blog.

= OK; but where is the Doc's part of this plugin? =

Please, note that, once installed the plugin, in the ordinary posts a new box appears, named "Post related with some event", with a dropdown with the title of existing events. If that's the case, you should select the related events.

= Events and Posts linked to them; that's all? =

Well, the idea is simple, I know... But the intention is that, using few shortcodes and some CSS to style them, you can adapt it to your own needs (basic needs of events and related docs).

For example, you can create a template page and use the shortcodes on it. There are a lot of tutorials about the use of shortcodes on the web; for example the WP Codex, at https://codex.wordpress.org/Shortcode.

= And what about widgets? =

Well, shortcodes can be used as widgets using the text widget. Just write the shortcode on the text widget content; for example <em>[eventsndocs_show_events which="next 1 rand"]</em>

= So, back to the shortcode... =

The simplest one is the shortcode to show the status (if the event is ended, or is still happening, or will occurs in the future) and meta data of the current event (the start and finish date, and its location).
For example:

    Show meta and status of the current event:
        [eventsndocs_show_metas]
    Show meta and status of the last event:
        [eventsndocs_show_metas event_id="last"]
    Show only the status of the event with ID 89:
        [eventsndocs_show_metas event_id="89" show="s"]

= And to show the documents related to some event? =

Once again, some samples clarify the way to get it:

    Show all documents related with current event:
        [eventsndocs_show_docs]
    Show two documents (title & excerpt) related to event with ID 89:
        [eventsndocs_show_docs event_id="89" q='2' show='t,e']
    Show all documents related to the last event:
        [eventsndocs_show_docs event_id="last"]

The parameter <em>show</em> in the second example is useful because decides not only what you want to show in the documents (title, author, excerpt or image) but the order.

= And to show some event? =

Well, the following examples could seems a bit straightforward the first time, but later on you realize how easy it is; specially if you dare to play with it.

    Show all published events, order by start date (higher first):
        [eventsndocs_show_events]
    Show all published events, in a map, with a filter:
        [eventsndocs_show_events type="map"]
    Show all published events, randomly:
        [eventsndocs_show_events which="all rand"]
    Show next following event (not current one):
        [eventsndocs_show_events which="next"]
    Show all events in the future (closer to current date appears first):
        [eventsndocs_show_events which="next -1"]
    Show one event in the future (randomly):
        [eventsndocs_show_events which="next 1 rand"]
    Show all events in the future (closer to current date appears last):
        [eventsndocs_show_events which="further -1"]
    Show all current events (closer to its ending time appears last):
        [eventsndocs_show_events which="current -1"]
    Show last event (closer to current date appears first):
        [eventsndocs_show_events which="last"]

So far, so good (I hope). But there are here other two parameters: <em>xtra</em>, to show some xtra info with the event (its meta, status, or related docs), and the parameter we already met: <em>show</em>.

    Show last event, followed by its status and the related docs:
        [eventsndocs_show_events which="last" xtra="s,d"]
    Show status of last event, followed by the event title:
        [eventsndocs_show_events which="last" xtra="s,e" show="t"]

Please, note in the last example, that <em>xtra</em> also includes "e" (for "events"). Events allways appears first, unless you add "e" on <em>xtra</em> parameter (and consequently events appear in the order of "e"on <em>xtra</em>).

    Show last event and its related docs (only title & excerpt on both):
        [eventsndocs_show_events which="last" xtra="d" show="t,e"]

As you can notice, the <em>show</em> parameter is the same for events and for its related docs, when we decided to show both. To avoid this, and get to show different things on events and documents using the same shortcode, you should use another parameter: <em>showfordocs</em>.

    The previos example, but now the event shows title & excerpt, and
    the related docs only shows the title:
        [eventsndocs_show_events which="last" xtra="d" show="t,e" showfordocs="t"]

= How to show Organizers &amp; sponsors? =

With the shortcode exemplified below:

    Show organizers:
        [eventsndocs_show_who]
    Show sponsors:
        [eventsndocs_show_who who="s"]
    Show sponsors, preceded by title "Our Sponsors"
        [eventsndocs_show_who who="s" title="Our Sponsors"]


== Screenshots ==

1. Events on a map.
2. Single event (at the end, the related docs)
3. Events on the backend.
4. Editing an event pn the backend.
5. Main metabox of event in backend (detail)
6. isMain metabox of event in backend (detail)


== Donations ==

If you want to help me in writing more code or better poetry, please invite me to a beer (or coffee, maybe) by sending your thanks to my PayPal account (ernestortizcu at yahoo.es). Thanks in advance.


== Changelog ==

= 1.0.0 =
* Stable Release

= 1.0.5 =
* Add shortcodes for to show organizers and sponsors
* Other minor changes
