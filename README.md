# Drupal Plugin Installation Guide

Tracker and media integration.

### 1. Install the Drupal Plugin

To install the plugin, clone the repository from GitHub by running the following commands:

```bash
# Clone the repository from GitHub
cd /path/to/your/project/root
git clone https://github.com/fragmatic-io/drupal-plugin.git web/modules/custom/controltower 
```

Since the plugin is not officially posted, you will need to install it directly from the GitHub repository.

### 2. Enable the Plugin

After installing the plugin, enable it using Drush or the Drupal admin interface:

```bash
drush en dxp_utilities -y
```

Alternatively, you can navigate to the "Extend" section in the Drupal admin panel, find the plugin, and enable it.


### 3. Add Preprocessing HTML Hook

Implement a hook_preprocess_html() or update existing(If any) in YOUR_THEME_NAME.theme file. This will pass the variables to the twig template.

```php
/**
 * Implements hook_preprocess_html().
 */
function YOUR_THEME_NAME_preprocess_html(&$variables)
{
  // <---- CT: start (v2.1) ---->
  $config = \Drupal::config('dxp_utilities.middleware.settings');
  $variables['dxp_middleware_url'] = $config->get('dxp_middleware_url');
  $variables['dxp_scope'] = $config->get('dxp_scope');
  
  // Get the current path
  $current_path = \Drupal::service('path.current')->getPath();
  $internal_path = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
  
  // Checking for the front page and then assigning the values respectively
  if (\Drupal::service('path.matcher')->isFrontPage()) {
    $variables['attributes']['class'][] = 'home';
  } else {
    $class = str_replace("/", "-", $internal_path);
    $variables['attributes']['class'][] = substr($class, 1);
  }
  // <---- CT: end ---->
}
```

### 4. Add Code Snippet to the twig templates

Add the following code snippet in the head section of html.html.twig file in your theme to enable the tracker and CSS integration:

```twig
<!-- CT: start (v2.1)  -->
{% if dxp_middleware_url and dxp_scope %}
  <link
    id="ct-css"
    rel="stylesheet"
    type="text/css"
    href="{{ dxp_middleware_url }}/js-app/css/ct-no-flicker.css"
  />
  <script
    id="ct-tracker"
    src="{{ dxp_middleware_url }}/js-app/js/{{ dxp_scope }}-tracker.js"
    defer
  </script>
{% endif %}
<!-- CT: end -->
```

