<?php Namespace WordPress\Plugin\Fancy_Gallery;

class Updates {
  public
    $plugin_file, # absolute path to the main file of the plugin
    $plugin_slug, # the slug used to identify this plugin
    $plugin_data, # the information stored in the plugin header
    $plugin_transient, # the transient name which stores the data from the last server request

    $username, # The username of the subscriber
    $password, # The password of the subscriber
    $show_notification = True; # Show update notifications to the user or not

  function __construct($plugin_file, $username = Null, $password = Null, $show_notification = True){
    # The updater will only be loaded in the dashboard
    If (!Is_Admin()) return;
    
    # Collect parameters
    $this->username = $username;
    $this->password = $password;
    $this->show_notification = $show_notification;

    $this->plugin_file = $plugin_file;
    $this->plugin_slug = BaseName(DirName($this->plugin_file));
    $this->plugin_transient = SPrintF('plugin-data-%s', $this->plugin_slug);

    $this->base_url = Get_Bloginfo('wpurl').'/'.SubStr(RealPath(DirName($this->plugin_file)), Strlen(ABSPATH));

    Add_Filter('site_transient_update_plugins', Array($this, 'Filter_Update_Plugins'));
    Add_Filter('plugins_api', Array($this, 'Filter_Plugins_API'), 10, 3);
  }
  
  function loadPluginHeaderData(){
    If (Empty($this->plugin_data))
      $this->plugin_data = (Object) Get_Plugin_Data($this->plugin_file);
  }
    
  function requestRemotePluginData(){
    # Load local plugin data
    $this->loadPluginHeaderData();

    $parameter = Array(
      #'purpose' => 'version_check',
      'format' => 'json',
      'subscriber' => RAWUrlEncode($this->username),
      'locale' => Get_Locale(),
      'referrer' => RAWUrlEncode(Home_Url())
    );
    $url = Add_Query_Arg($parameter, $this->plugin_data->PluginURI);
    $raw_response = @WP_Remote_Get($url, Array('timeout' => 1.5));
    If (!$raw_response || Is_WP_Error($raw_response)) return False;
    $response = @JSON_Decode($raw_response['body']);
    return $response;
  }

  function getRemotePluginData(){
    If ($last_plugin_remote_data = Get_Transient($this->plugin_transient)){
      return $last_plugin_remote_data;
    }
    ElseIf ($last_plugin_remote_data = $this->requestRemotePluginData()){
      Set_Transient($this->plugin_transient, $last_plugin_remote_data, 12 * HOUR_IN_SECONDS);
      return $last_plugin_remote_data;
    }
    Else {
      return False;
    }
  }
  
  function getRelativePluginPath(){
    If (!Function_Exists('Get_Plugins'))
      Require_Once(ABSPATH . 'wp-admin/includes/plugin.php');
    
    $arr_plugins = Get_Plugins();
    If (!Is_Array($arr_plugins)) return False;
    
    ForEach ($arr_plugins AS $file => $data){
      If (SubStr($this->plugin_file, -1*StrLen($file)) == $file){
        return $file;
      }
    }
    
    return False;
  }
  
  function Filter_Update_Plugins($value){
    # Find this plugin
    $relative_plugin_path = $this->getRelativePluginPath();
    If (!$relative_plugin_path) return $value;

    # Get current version from server
    $remote_plugin_data = $this->getRemotePluginData();
    If (!$remote_plugin_data) return $value;

    # Check if the update function is disabled
    If (!$this->show_notification) return $value;
    
    # Load local plugin data
    $this->loadPluginHeaderData();
    
    # Compare versions
    If (Version_Compare($this->plugin_data->Version, $remote_plugin_data->version, '<')){
      $credentials_entered = !Empty($this->username) && !Empty($this->password);
      $value->response[$relative_plugin_path] = (Object) Array(
        'id' => $remote_plugin_data->id,
        'slug' => $this->plugin_slug,
        'new_version' => $remote_plugin_data->version,
        'url' => $remote_plugin_data->url,
        'package' => $credentials_entered ? SPrintF($remote_plugin_data->download, RAWUrlEncode($this->username), RAWUrlEncode($this->password)) : False
      );
    }
    
    # Return the filter input
    return $value;
  }

  function Filter_Plugins_API($false, $action, $args){
    If ($action == 'plugin_information' && $args->slug == $this->plugin_slug){
      WP_Enqueue_Style('plugin-details', $this->base_url . '/plugin-details.css' );
      $remote_plugin_data = $this->getRemotePluginData();
      $plugin = (Object) Array(
        'name' => $remote_plugin_data->name,
        'slug' => $this->plugin_slug,
        'version' => $remote_plugin_data->version,
        'author' => SPrintF('<a href="%1$s">%2$s</a>', $remote_plugin_data->author->url, $remote_plugin_data->author->display_name),
        'author_profile' => $remote_plugin_data->author->url,
        'contributors' => Array( 'dhoppe' => $remote_plugin_data->author->url ),
        'requires' => '3.8',
        'tested' => Date('y.n.j'),
        'rating' => Round(Rand(90, 100)),
        'num_ratings' => Round( (Time() - 1262300400) / (3*24*60*60)),
        'downloaded' => Round( (Time() -  1262300400) / 600 ),
        'last_updated' => Date('Y-m-d', Time() - (1 * 24 * 3600) ),
        'homepage' => $remote_plugin_data->url,
        'download_link' => SPrintF($remote_plugin_data->download, RAWUrlEncode($this->username), RAWUrlEncode($this->password)),
        'sections' => Is_Object($remote_plugin_data->content) ? (Array) $remote_plugin_data->content : Array( __('Description') => (String) $remote_plugin_data->content),
        'external' => True
      );
      return $plugin;
    }
    Else return $false;
  }

}