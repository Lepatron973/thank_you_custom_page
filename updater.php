<?php


class GitHub_Updater
{
    private $plugin_slug;
    private $github_user;
    private $github_repo;
    private $plugin_file;

    public function __construct($plugin_file, $github_user, $github_repo)
    {
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->github_user = $github_user;
        $this->github_repo = $github_repo;
        $this->plugin_file = $plugin_file;
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
        add_filter('upgrader_post_install', [$this, 'after_update'], 10, 3);
    }

    public function check_for_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote_version = $this->get_latest_version();
        $current_version = $this->get_current_version();

        if (version_compare($current_version, $remote_version, '<')) {
            $plugin_data = get_plugin_data($this->plugin_file);

            $transient->response[$this->plugin_slug] = (object) [
                'slug'        => $this->plugin_slug,
                'new_version' => $remote_version,
                'url'         => $plugin_data['PluginURI'],
                'package'     => $this->get_download_url(),
            ];
        }

        return $transient;
    }






    public function plugin_info($res, $action, $args)
    {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) {
            return false;
        }

        $remote_version = $this->get_latest_version();
        $plugin_data = get_plugin_data($this->plugin_file);

        $res = (object) [
            'name'        => $plugin_data['Name'],
            'slug'        => $this->plugin_slug,
            'version'     => $remote_version,
            'author'      => $plugin_data['Author'],
            'homepage'    => $plugin_data['PluginURI'],
            'download_link' => $this->get_download_url(),
            'sections'    => [
                'description' => $plugin_data['Description'],
            ],
        ];

        return $res;
    }

    public function after_update($response, $hook_extra, $result)
    {
        global $wp_filesystem;
        $plugin_folder = WP_PLUGIN_DIR . '/' . dirname($this->plugin_slug);
        $wp_filesystem->move($result['destination'], $plugin_folder);
        $result['destination'] = $plugin_folder;

        // Supprime les caches
        delete_transient('github_plugin_latest_version');
        delete_transient('github_plugin_download_url');

        return $result;
    }

    private function get_latest_version()
    {
        // Vérifie si la version est déjà mise en cache
        $cached_version = get_transient('github_plugin_latest_version');

        if ($cached_version) {
            return $cached_version;
        }

        // Appelle l'API GitHub pour récupérer la dernière version
        $response = wp_remote_get("https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/releases/latest");

        if (is_wp_error($response)) {
            return false;
        }

        $release = json_decode(wp_remote_retrieve_body($response));

        if (!empty($release->tag_name)) {
            // Met en cache la version pour 12 heures
            set_transient('github_plugin_latest_version', $release->tag_name, 12 * HOUR_IN_SECONDS);
            return $release->tag_name;
        }

        return false;
    }

    private function get_download_url()
    {
        $cached_url = get_transient('github_plugin_download_url');
        if ($cached_url) {
            return $cached_url;
        }

        $latest_version = $this->get_latest_version();

        if ($latest_version) {
            $download_url = "https://github.com/{$this->github_user}/{$this->github_repo}/archive/refs/tags/{$latest_version}.zip";
            // Met en cache l'URL de téléchargement pour 12 heures
            set_transient('github_plugin_download_url', $download_url, 12 * HOUR_IN_SECONDS);
            return $download_url;
        }

        return false;
    }
    public function clear_cache()
    {
        // Supprimer le cache de la version
        delete_transient('github_plugin_latest_version');

        // Supprimer le cache de l'URL de téléchargement
        delete_transient('github_plugin_download_url');

        // Vous pouvez également ajouter d'autres transients si nécessaire
        do_action('github_updater_cache_cleared'); // Hook pour extensions futures

        return true;
    }

    private function get_current_version()
    {
        $plugin_data = get_plugin_data($this->plugin_file);
        return $plugin_data['Version'];
    }
}
