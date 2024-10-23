# Drupal Plugin Installation Guide

Tracker and media integration.

### 1. Install the Drupal Plugin

To install the plugin, clone the repository from GitHub by running the following commands:

```bash
cd /path/to/your/drupal/site
# Clone the repository from GitHub
git clone https://github.com/fragmatic-io/drupal-plugin.git modules/custom/controltower 
# Navigate to the plugin directory
cd modules/custom/controltower
# Install dependencies
composer install
```

Since the plugin is not officially posted, you will need to install it directly from the GitHub repository.

### 2. Enable the Plugin

After installing the plugin, enable it using Drush or the Drupal admin interface:

```bash
drush en drupal_plugin -y
```

Alternatively, you can navigate to the "Extend" section in the Drupal admin panel, find the plugin, and enable it.

### 3. Add Code Snippet to the Twig File

Add the following code snippet in the Twig file that loads for every page to enable the tracker and CSS integration:

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
    defer>
  </script>
{% endif %}
<!-- CT: end -->
```

### 4. Add Preprocessing HTML Hook

Add the following preprocessing hook to either add the CT code or configure HTML preprocessing:

```php
/**
 * Implements hook_preprocess_html().
 */
function leevcb_preprocess_html(&$variables)
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

