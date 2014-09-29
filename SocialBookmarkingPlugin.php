<?php
/**
 * Social Bookmarking
 *
 * @copyright Copyright 2008-2013 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Social Bookmarking plugin.
 */
class SocialBookmarkingPlugin extends Omeka_Plugin_AbstractPlugin
{
    const ADDTHIS_SERVICES_URL = 'http://cache.addthiscdn.com/services/v1/sharing.en.xml';
    const SERVICE_SETTINGS_OPTION = 'social_bookmarking_services';
    const ADD_TO_HEADER_OPTION = 'social_bookmarking_add_to_header';
    const ADD_TO_OMEKA_ITEMS_OPTION = 'social_bookmarking_add_to_omeka_items';
    const ADD_TO_OMEKA_COLLECTIONS_OPTION = 'social_bookmarking_add_to_omeka_collections';
    const ADDTHIS_ACCOUNT_ID = 'social_bookmarking_addthis_account_id';
    const ADDTHIS_STYLE = 'addthis_style';

    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'initialize',
        'config_form',
        'config',
        'admin_head',
        'public_header',
        'public_items_show',
        'public_collections_show'
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        self::SERVICE_SETTINGS_OPTION => '',
        self::ADD_TO_HEADER_OPTION => '1',
        self::ADD_TO_OMEKA_ITEMS_OPTION => '1',
        self::ADD_TO_OMEKA_COLLECTIONS_OPTION => '1',
        self::ADDTHIS_ACCOUNT_ID => '',
        self::ADDTHIS_STYLE => 'addthis_toolbox addthis_default_style',
    );

    /**
     * @var array Default services.
     */
    protected $_defaultEnabledServiceCodes = array(
        'facebook',
        'twitter',
        'linkedin',
        'pinterest',
        'email',
        'google',
        'delicious',
    );

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        $this->_options[self::SERVICE_SETTINGS_OPTION] = serialize($this->_get_default_service_settings());
        $this->_installOptions();
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $this->_uninstallOptions();
    }

    /**
     * Upgrade the plugin.
     *
     * @param array $args contains: 'old_version' and 'new_version'
     */
    public function hookUpgrade($args)
    {
        $booleanFilter = new Omeka_Filter_Boolean;
        $newServiceSettings = $this->_get_default_service_settings();
        $oldServiceSettings = $this->_get_service_settings();
        foreach($newServiceSettings as $serviceCode => $value) {
            if (array_key_exists($serviceCode, $oldServiceSettings)) {
                $newServiceSettings[$serviceCode] = $booleanFilter->filter($oldServiceSettings[$serviceCode]);
            }
        }
        $this->_set_service_settings($newServiceSettings);
    }

    /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        $view = get_view();
        // Set form defaults.
        $services = $this->_get_services();
        $serviceSettings = $this->_get_service_settings();
        $setServices = array();
        foreach($services as $serviceCode => $serviceInfo) {
            $setServices[$serviceCode] = array_key_exists($serviceCode, $serviceSettings)
                ? $serviceSettings[$serviceCode]
                : false;
        }

        echo $view->partial(
            'plugins/social-bookmarking-config-form.php',
            array(
                'services' => $services,
                'setServices' => $setServices,
        ));
    }

    /**
     * Set the options from the config form input.
     */
    public function hookConfig($args)
    {
        $post = $args['post'];

        set_option(SocialBookmarkingPlugin::ADD_TO_HEADER_OPTION, $post[SocialBookmarkingPlugin::ADD_TO_HEADER_OPTION]);
        set_option(SocialBookmarkingPlugin::ADD_TO_OMEKA_ITEMS_OPTION, $post[SocialBookmarkingPlugin::ADD_TO_OMEKA_ITEMS_OPTION]);
        set_option(SocialBookmarkingPlugin::ADD_TO_OMEKA_COLLECTIONS_OPTION, $post[SocialBookmarkingPlugin::ADD_TO_OMEKA_COLLECTIONS_OPTION]);
        set_option(SocialBookmarkingPlugin::ADDTHIS_ACCOUNT_ID, $post[SocialBookmarkingPlugin::ADDTHIS_ACCOUNT_ID]);
        set_option(SocialBookmarkingPlugin::ADDTHIS_STYLE, $post[SocialBookmarkingPlugin::ADDTHIS_STYLE]);

        unset($post[SocialBookmarkingPlugin::ADD_TO_HEADER_OPTION]);
        unset($post[SocialBookmarkingPlugin::ADD_TO_OMEKA_ITEMS_OPTION]);
        unset($post[SocialBookmarkingPlugin::ADD_TO_OMEKA_COLLECTIONS_OPTION]);
        unset($post[SocialBookmarkingPlugin::ADDTHIS_ACCOUNT_ID]);
        unset($post[SocialBookmarkingPlugin::ADDTHIS_STYLE]);

        $serviceSettings = $this->_get_service_settings();
        $booleanFilter = new Omeka_Filter_Boolean;
        foreach($post as $key => $value) {
            if (array_key_exists($key, $serviceSettings)) {
                $serviceSettings[$key] = $booleanFilter->filter($value);
            }
            else {
                $serviceSettings[$key] = false;
            }
        }
        $this->_set_service_settings($serviceSettings);
    }

    public function hookAdminHead()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->getModuleName() == 'default' && $request->getControllerName() == 'plugins' && $request->getActionName() == 'config' && $request->getParam('name') == 'SocialBookmarking'){
            queue_css_url('http://cache.addthiscdn.com/icons/v1/sprites/services.css');
        }
    }

    /**
     * Hook for public header.
     */
    public function hookPublicHeader($args)
    {
        if (get_option(SocialBookmarkingPlugin::ADD_TO_HEADER_OPTION) == '1') {
            $view = $args['view'];
            $vars = $view->getVars();
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $params = $request->getParams();

            // We need absolute urls and getRequestUri() doesn't return domain.
            $url = WEB_ROOT . $request->getPathInfo();
            if ($params['action'] == 'show' && in_array($params['controller'], array(
                    'collections',
                    'items',
                    'files',
                ))) {
                $recordType = $view->singularize($params['controller']);
                $record = get_current_record($recordType);
                $title = isset($vars['title'])
                    ? $vars['title']
                    : strip_formatting(metadata($record, array('Dublin Core', 'Title')));
                $description = strip_formatting(metadata($record, array('Dublin Core', 'Description')));
            }
            else {
                $title= isset($vars['title']) ? $vars['title'] : get_option('site_title');
                $description = '';
            }

            echo '<div id="socialBookmarking" class="navbar-nav">';
            echo $view->partial('social-bookmarking/social-bookmarking-toolbar.php', array(
                'url' => $url,
                'title' => $title,
                'description' => $description,
                'services' => $this->_get_services(),
                'serviceSettings' => $this->_get_service_settings(),
                'addthisAccountID' => $this->_get_addthis_account_id(),
                'addthisStyle' => $this->_get_addthis_style(),
            ));
            echo '</div>';
        }
    }

    /**
     * Hook for public items show view.
     */
    public function hookPublicItemsShow($args)
    {
        if (get_option(SocialBookmarkingPlugin::ADD_TO_OMEKA_ITEMS_OPTION) == '1') {
            $view = $args['view'];
            $item = $args['item'];
            $url = record_url($item, 'show', true);
            $title = strip_formatting(metadata($item, array('Dublin Core', 'Title')));
            $description = strip_formatting(metadata($item, array('Dublin Core', 'Description')));
            echo '<div id="socialBookmarking" class="well">';
            echo '<h2>' . __('Share') . '</h2>';
            echo $view->partial('social-bookmarking/social-bookmarking-toolbar.php', array(
                'url' => $url,
                'title' => $title,
                'description' => $description,
                'services' => $this->_get_services(),
                'serviceSettings' => $this->_get_service_settings(),
                'addthisAccountID' => $this->_get_addthis_account_id(),
                'addthisStyle' => $this->_get_addthis_style(),
            ));
            echo '</div>';
        }
    }

    /**
     * Hook for public collections show view.
     */
    public function hookPublicCollectionsShow($args)
    {
        if (get_option(SocialBookmarkingPlugin::ADD_TO_OMEKA_COLLECTIONS_OPTION) == '1') {
            $view = $args['view'];
            $collection = $args['collection'];
            $url = record_url($collection, 'show', true);
            $title = strip_formatting(metadata($collection, array('Dublin Core', 'Title')));
            $description = strip_formatting(metadata($collection, array('Dublin Core', 'Description')));
            echo '<div id="socialBookmarking" class="well">';
            echo '<h2>' . __('Share') . '</h2>';
            echo $view->partial('social-bookmarking/social-bookmarking-toolbar.php', array(
                'url' => $url,
                'title' => $title,
                'description' => $description,
                'services' => $this->_get_services(),
                'serviceSettings' => $this->_get_service_settings(),
                'addthisAccountID' => $this->_get_addthis_account_id(),
                'addthisStyle' => $this->_get_addthis_style(),
            ));
            echo '</div>';
        }
    }

    /**
     * Gets the service settings from the database.
     */
    protected function _get_service_settings()
    {
        $serviceSettings = unserialize(get_option(SocialBookmarkingPlugin::SERVICE_SETTINGS_OPTION));
        ksort($serviceSettings);
        return $serviceSettings;
    }

    /**
     * Saves the service settings in the database.
     */
    protected function _set_service_settings($serviceSettings)
    {
        set_option(SocialBookmarkingPlugin::SERVICE_SETTINGS_OPTION, serialize($serviceSettings));
    }

    /**
     * Sets default service settings.
     */
    protected function _get_default_service_settings()
    {
        $services =  $this->_get_services();
        $serviceSettings = array();
        foreach($services as $serviceCode => $serviceInfo) {
            $serviceSettings[$serviceCode] = in_array($serviceCode, $this->_defaultEnabledServiceCodes);
        }
        return $serviceSettings;
    }

    /**
     * Gets current services from AddThis.
     */
    protected function _get_services()
    {
        static $services = null;
        $booleanFilter = new Omeka_Filter_Boolean;
        if (is_null($services)) {
            $xml = $this->_get_services_xml();
            if ($xml) {
                $services = array();
                foreach ($xml->data->services->service as $service) {
                    $serviceCode = (string)$service->code;
                    $services[$serviceCode] = array(
                        'code' => $serviceCode,
                        'name' => (string)$service->name,
                        'icon' => (string)$service->icon32,
                        'script_only' => $booleanFilter->filter((string)$service->script_only),
                    );
                }
            }
            else {
                return array();
            }
        }
        return $services;
    }

    /**
     * Gets one current service from AddThis.
     */
    protected function _get_service($serviceCode)
    {
        $services = $this->_get_services();
        if (array_key_exists($serviceCode, $services)) {
            return $services[$serviceCode];
        }
        return null;
    }

    /**
     * Gets list of services from AddThis.
     */
    protected function _get_services_xml()
    {
        static $xml = null;
        if (empty($xml)) {
            $file = file_get_contents(SocialBookmarkingPlugin::ADDTHIS_SERVICES_URL);
            $xml = $file ? new SimpleXMLElement($file) : '';
        }
        return $xml;
    }

    /**
     * Gets the addthis accound id.
     */
    protected function _get_addthis_account_id()
    {
        $thisId = get_option(SocialBookmarkingPlugin::ADDTHIS_ACCOUNT_ID);
        return $thisId;
    }

    /**
     * Gets the addthis style.
     */
    protected function _get_addthis_style()
    {
        $style = get_option(SocialBookmarkingPlugin::ADDTHIS_STYLE);
        return $style;
    }
}
