<?php
/**
 * Plugin Name: Sproutouts Divi Projects to Posts Migrator
 * Description: Migrates Divi Projects to regular posts while maintaining metadata and custom fields.
 * Author: Meagan Howey
 * Plugin URI: https://sproutouts.com/sproutouts-divi-projects-to-posts-plugin/
 * Author URI: https://www.sproutouts.com
 */

// If the file is accessed directly, abort.
 if (!defined('ABSPATH')) {
    exit;
}

//Initialize Migration Admin Page
add_action('admin_menu', function () {
    add_management_page(
        'Sproutouts Migrate Divi Projects', // Page title
        'Migrate Projects',      // Menu title
        'manage_options',        // Capability
        'migrate-projects',      // Menu slug
        'sp_dptp_migration_page'    // Callback function
    );
});

//Create Migration Admin Page
function sp_dptp_migration_page () {

  //Ensure only admins (and similar roles) can access this page.
  if (!current_user_can('manage_options')) return;

  //initalize dry run and unregistering variables
  $sp_dptp_dry_run = false;
  $sp_dptp_unregister = false;

  //Handle form submission and run migrator with checked settings
  if (isset($_POST['sp_dptp_migrate']) && check_admin_referer('sp_dptp_migrate_action')) {
      $sp_dptp_dry_run = isset($_POST['sp_dptp_dry_run']);
      $sp_dptp_unregister = isset($_POST['sp_dptp_unregister']);
      sp_dptp_migrate_projects($sp_dptp_dry_run, $sp_dptp_unregister);
  }

  //switch to HTML format
  ?>
  <div class="wrap">
      <h1>Sproutouts Migrate Divi Projects</h1>
      <form method="post">
          <?php wp_nonce_field('sp_dptp_migrate_action'); ?>
          <p>This tool will convert all Divi <strong>Project</strong> custom post type entries into regular Wordpress posts, assign them the category "Projects", and preserve all metadata.</p>
          <p><strong>Options:</strong></p>
          <label><input type="checkbox" name="sp_dptp_dry_run" value="1" checked> Dry run (simulate only, don't save changes)</label><br>
          <label><input type="checkbox" name="sp_dptp_unregister" value="1"> Unregister the Project post type after migration (Check when running officially!)</label>
          <p><input type="submit" name="sp_dptp_migrate" class="button button-primary" value="Migrate Projects"></p>
      </form>
  </div>
  <?php
  //switch back to php format
}

//Migration Functionality
function sp_dptp_migrate_projects($sp_dptp_dry_run = true, $sp_dptp_unregister = false) {
  $sp_dptp_project_posts = get_posts(array(
          'post_type' => 'project',
          'numberposts' => -1,
          'post_status' => 'any'
      ));

      if (empty($sp_dptp_project_posts)) {
          echo '<div class="notice notice-warning"><p>No Project posts found.</p></div>';
          return;
      }

      $sp_dptp_project_cat = get_term_by('name', 'Projects', 'category');
      if (!$sp_dptp_project_cat) {
          if ($sp_dptp_dry_run) {
              echo '<p>Would create "Projects" category.</p>';
              $sp_dptp_project_cat = null;
          } else {
              $sp_dptp_project_cat = wp_create_category('Projects');
          }
      } else {
          $sp_dptp_project_cat = $sp_dptp_project_cat->term_id;
      }

      echo '<div class="notice notice-info"><p>' . ($sp_dptp_dry_run ? 'Dry run: No changes will be saved.' : 'Starting migration...') . '</p></div>';
      echo '<ul>';

      foreach ($sp_dptp_project_posts as $project) {
          echo '<li><strong>' . esc_html($project->post_title) . '</strong> (ID: ' . $project->ID . ') &rarr; ';

          if ($sp_dptp_dry_run) {
              echo 'would migrate.</li>';
          } else {
              wp_update_post(array(
                  'ID' => $project->ID,
                  'post_type' => 'post',
              ));

              if ($sp_dptp_project_cat !== null) {
                wp_set_post_categories($project->ID, array($sp_dptp_project_cat));
              }

              $sp_dptp_taxonomies = get_object_taxonomies('project');
              foreach ($sp_dptp_taxonomies as $taxonomy) {
                  $terms = wp_get_object_terms($project->ID, $taxonomy, array('fields' => 'slugs'));
                  if (!empty($terms) && !is_wp_error($terms)) {
                      wp_set_object_terms($project->ID, $terms, $taxonomy, true);
                  }
              }

              echo 'migrated.</li>';
          }
      }

      echo '</ul>';

      if (!$sp_dptp_dry_run && $sp_dptp_unregister) {
          add_action('init', function () {
              unregister_post_type('project');
          }, 100);
          echo '<div class="notice notice-info"><p>Project post type will be unregistered on next page load.</p></div>';
      } elseif (!$sp_dptp_dry_run) {
          echo '<div class="notice notice-success"><p>Migration complete.</p></div>';
      }
}
