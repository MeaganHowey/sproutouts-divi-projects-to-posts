# sproutouts-divi-projects-to-posts

Official Page: https://sproutouts.com/sproutouts-divi-projects-to-posts-plugin/
Migrates Divi Projects to regular posts while maintaining metadata and custom fields.

## What This Plugin Does

Moves every project from Divi's Custom Post Type to a regular Wordpress post in a new "Projects" category. Preserves metadata (custom fields, featured images, SEO settings, etc.).

Important: This will **not** change the Divi theme builder settings or other visual settings targeted to the custom post type. Those will have to be switched from "Projects" to the "Projects" blog post category manually.

Important: If you would still like to use the Divi **Portfolio** and **Filterable Portfolio** Modules, you will have to use custom code (or the Sproutouts Theme) to populate those modules with the "Projects" blog post category.

## How To Use

1. Activate the plugin in your dashboard
2. Go to **Tools > Migrate Projects** and check the *dry run* box.
3. Click *Migrate Projects Now* to do a dry run and make sure no errors occur.
4. When ready, uncheck *dry run* and check *unregister* before clicking *Migrate Projects Now* again.
5. This will officially migrate all Divi Projects to regular blog posts in a new category labeled "Projects". Once the migration is complete, the plugin will also unregister the old projects class (not delete any projects or posts!) in order to stop any confusion/bloat in the future.
6. That's it! You can disable and delete this plugin safely.

Need help or have a question? Contact [meagan@sproutouts.com](mailto:meagan@sproutouts.com)
