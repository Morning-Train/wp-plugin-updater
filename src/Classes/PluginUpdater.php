<?php namespace Morningtrain\WP\PluginUpdater\Classes;

class PluginUpdater {

    protected string $remote_url;
    protected string $plugin_slug;
    protected string $base_name;
    protected string $plugin_version;


    public function __construct($remote_url, $plugin_slug, $base_name, $plugin_version) {
        $this->remote_url = $remote_url;
        $this->plugin_slug = $plugin_slug;
        $this->base_name = $base_name;
        $this->plugin_version = $plugin_version;

        $this->init();
    }

    /**
     * @return void
     */
    protected function init() {
        add_filter('plugin_api', [$this, 'getPluginInfo'], 10, 3);
        add_filter('pre_set_site_transient_update_plugins', [$this, 'setUpdatePluginsTransient']);

        add_action('upgrader_process_complete', [$this, 'deleteTransientAfterUpdate'], 10, 2);
    }

    /**
     * Delete update info transient after plugin has been updated
     * @param $upgrader_object
     * @param $options
     * @return void
     */
    public function deleteTransientAfterUpdate($upgrader_object, $options) {
        if($options['action'] == 'update' && $options['type'] === 'plugin')  {
            delete_transient( $this->plugin_slug . '_plugin_info_json');
        }
    }

    /**
     * Get plugin info from remote
     *
     * @param $res
     * @param $action
     * @param $args
     *
     * @return object|null
     */
    public function getPluginInfo($res, $action, $args) {
        if($action !== 'plugin_information') {
            return $res;
        }

        if(!isset($args->slug) || $args->slug !== $this->plugin_slug) {
            return $res;
        }

        $res = $this->getPluginRemote();

        if(isset($res->sections)) {
            foreach($res->sections as &$section) {
                $section = html_entity_decode($section);
            }
        }

        $res->slug = $this->plugin_slug;
        $res->plugin = $this->base_name;

        return $res;
    }

    /**
     * Set plugin data to update transient
     * @param $transient
     *
     * @return mixed
     */
    public function setUpdatePluginsTransient($transient) {
        if(!isset($transient->last_checked)) {
            return $transient;
        }

        $plugin_remote = $this->getPluginRemote($transient->last_checked);

        if(empty($plugin_remote)) {
            return $transient;
        }

        if(version_compare($this->plugin_version, $plugin_remote->version, '>=') || version_compare($plugin_remote->requires, get_bloginfo('version'), '>')) {
            return $transient;
        }

        $res = new \stdClass();
        $res->slug = $this->plugin_slug;
        $res->plugin = $this->base_name;
        $res->new_version = $plugin_remote->version;
        $res->tested = $plugin_remote->tested;
        $res->package = $plugin_remote->download_link;
        $transient->response[$this->base_name] = $res;

        return $transient;
    }

    /**
     * Get active plugins for API call
     * @return array
     */
    public function extractActivePlugins() {
        $_active_plugins = get_option('active_plugins', array());

        if(empty($_active_plugins)) {
            return array();
        }
        $_all_plugins = get_plugins();
        $active_plugins = array();

        foreach($_active_plugins as $_active_plugin) {
            if(isset($_all_plugins[$_active_plugin])) {
                $active_plugins[$_active_plugin] = array(
                    'name' => $_all_plugins[$_active_plugin]['Name'],
                    'plugin_uri' => $_all_plugins[$_active_plugin]['PluginURI'],
                    'version' => $_all_plugins[$_active_plugin]['Version'],
                );
            }
        }

        return $active_plugins;
    }

    /**
     * Get active them for API call
     * @return array
     */
    public function extractActiveTheme() {
        $_theme = wp_get_theme();

        $theme = array(
            'name' => $_theme->name,
            'title' => $_theme->title,
            'version' => $_theme->version,
        );

        $_parent_theme = $_theme->parent();

        if($_parent_theme) {
            $theme['parent_theme'] = array(
                'name' => $_parent_theme->name,
                'title' => $_parent_theme->title,
                'version' => $_parent_theme->version,
            );
        }

        return $theme;
    }

    /**
     * Get Plugin info from remote
     * @param null $last_checked
     *
     * @return object|null
     */
    public function getPluginRemote($last_checked = null) {
        $transient = get_transient($this->plugin_slug . '_plugin_info');

        if(false == $transient || ($last_checked !== null && (!isset($transient['last_check']) || $last_checked > $transient['last_check']))) {
            $transient = array(
                'last_check' => time()
            );

            $plugin_remote = wp_remote_post($this->remote_url, array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/json',
                ),
                'body' => array(
                    'wp_version' => get_bloginfo('version'),
                    'website' => get_bloginfo('url'),
                    'plugin_version' => $this->plugin_version,
                    'active_plugins' => json_encode($this->extractActivePlugins()),
                    'active_theme' => json_encode($this->extractActiveTheme()),
                )
            ));

            if(is_wp_error($plugin_remote) || wp_remote_retrieve_response_code($plugin_remote) != 200 || empty(wp_remote_retrieve_body($plugin_remote))) {
                return null;
            }

            $transient['info'] = wp_remote_retrieve_body($plugin_remote);

            set_transient($this->plugin_slug . '_plugin_info', $transient, 43200);
        }

        return (object) json_decode($transient['info'], true); // Decode as array an converte to project to have a std class with arrays inside
    }

}