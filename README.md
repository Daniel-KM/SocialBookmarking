Social Bookmarking (plugin for Omeka)
====================================


Summary
-------

[SocialBookmarking] is a plugin for [Omeka] inserts a list of social bookmarking
sites to the header of any page or at the bottom of each item or collection in
an Omeka archive, so that public users can easily share items they like with
their social networks.

An optional AddThisId can be configured to enable the user to track shares via
their [AddThis] account analytics.

Open Graph metadata tags, used by Facebook and other sites to gather information
about the page (title, description, image to display) can be included by enabling
the option in the plugin configuration.


Installation
------------

Uncompress files and rename plugin folder "SocialBookmarking".

Then install it like any other Omeka plugin and follow the config instructions.

The plugin can use AddThis ID. You need to get key to this service and set it.


Buttons toolbar
---------------

Button toolbar can be inserted in code at any needed place just calling the `social_bookmarking_create_tollbar` function, like in the following example:
```
$item = get_current_record('item');
echo (new SocialBookmarkingPlugin)->social_bookmarking_create_toolbar(array('view' => $this, 'item' => $item), 'item');
```

Warning
-------

Use it at your own risk.

It's always recommended to backup your files and database so you can roll back
if needed.


Troubleshooting
---------------

See online [SocialBookmarking issues] on the Omeka forum.


License
-------

This plugin is published under [GNU/GPL].

This program is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
details.

You should have received a copy of the GNU General Public License along with
this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.


Contact
-------

Current maintainers:
* [Center for History & New Media]

This plugin has been built by [Jeremy Boggs], upgraded by [Center for History & New Media],
and forked by [Anuragji].


Copyright
---------

* Copyright Jeremy Boggs, 2008-2011
* Copyright Center for History & New Media, 2013
* Copyright Anuragji, 2013
* Copyright Daniel Berthereau, 2013
* Copyright Walter Rice, 2017
* Copyright Daniele Binaghi, 2021


[Omeka]: https://omeka.org
[AddThis]: http://www.addthis.com
[SocialBookmarking]: https://github.com/omeka/plugin-SocialBookmarking
[SocialBookmarking issues]: https://omeka.org/forums/forum/plugins
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html "GNU/GPL v3"
[Jeremy Boggs]: https://github.com/clioweb
[Center for History & New Media]: http://chnm.gmu.edu
[Anuragji]: https://github.com/anuragji
[Daniel Berthereau]: https://github.com/Daniel-KM
