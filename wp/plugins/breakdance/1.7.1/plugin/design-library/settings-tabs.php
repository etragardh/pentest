<?php

namespace Breakdance\DesignLibrary\Tab;

use function Breakdance\Setup\admin_notice;
use function Breakdance\Util\is_post_request;
use function Breakdance\Data\set_global_option;
use function Breakdance\DesignLibrary\getDesignLibraryUrl;
use function Breakdance\DesignLibrary\getPassword;
use function Breakdance\DesignLibrary\setPassword;
use function Breakdance\DesignLibrary\getRegisteredDesignSets;
use function Breakdance\DesignLibrary\getInvalidDesignSets;
use function Breakdance\DesignLibrary\setDesignSets;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\DesignLibrary\Tab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab('Design Library', 'design_library', '\Breakdance\DesignLibrary\Tab\tab', 1200);
}

function submit()
{
    $designLibraryEnabled = array_key_exists('is_copy_from_frontend_enabled', $_POST) ? 'yes' : false;
    $copyButtonEnabled = array_key_exists('is_copy_button_on_frontend_enabled', $_POST) ? 'yes' : false;
    $reliesOnGlobalSettings = array_key_exists('design_library_relies_on_global_settings', $_POST) ? 'yes' : false;
    /** @var string */
    $password = filter_input(INPUT_POST, 'design_library_password', FILTER_UNSAFE_RAW);
    /** @var string */
    $providers = filter_input(INPUT_POST, 'providers', FILTER_UNSAFE_RAW);

    set_global_option('is_copy_from_frontend_enabled', $designLibraryEnabled);
    set_global_option('is_copy_button_on_frontend_enabled', $copyButtonEnabled);
    set_global_option('design_library_relies_on_global_settings', $reliesOnGlobalSettings);

    setDesignSets($providers);
    setPassword($password);
}

function tab()
{
    $nonce_action = 'breakdance_admin_design_library_tab';

    if (is_post_request() && check_admin_referer($nonce_action)) {
        submit();
    }

    showWarningForInvalidDesignSets();

    /**
     * @var string
     */
    $designLibraryEnabled = \Breakdance\Data\get_global_option('is_copy_from_frontend_enabled');

    /**
     * @var string
     */
    $copyButtonEnabled = \Breakdance\Data\get_global_option('is_copy_button_on_frontend_enabled');

    /**
     * @var string
     */
    $reliesOnGlobalSettings = \Breakdance\Data\get_global_option('design_library_relies_on_global_settings');
    $providers = implode("\n", getRegisteredDesignSets());
    ?>

    <h2>Design Library</h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        Design Set
                    </th>
                    <td>
                        <fieldset>
                            <div>
                              <label for="is_copy_from_frontend_enabled">
                                <input type="checkbox" <?php echo $designLibraryEnabled ? 'checked' : ''; ?> name="is_copy_from_frontend_enabled" value="yes" id="is_copy_from_frontend_enabled">
                                Turn This Website Into a Design Set
                              </label>
                            </div>
                            <div>
                                <label for="is_copy_button_on_frontend_enabled">
                                    <input type="checkbox" <?php echo $copyButtonEnabled ? 'checked' : ''; ?> name="is_copy_button_on_frontend_enabled" value="yes" id="is_copy_button_on_frontend_enabled">
                                    Enable Copy From Frontend
                                </label>
                            </div>
                            <div>
                              <label for="design_library_relies_on_global_settings">
                                <input type="checkbox" <?php echo $reliesOnGlobalSettings ? 'checked' : ''; ?> name="design_library_relies_on_global_settings" value="yes" id="design_library_relies_on_global_settings">
                                This Design Set Relies On Global Settings
                              </label>
                            </div>
                        </fieldset>
                        <?php if ($designLibraryEnabled): ?>
                            <p class="description">
                                Shareable URL: <input class="breakdance-design-library-shareable-url" type="url" value="<?php echo getDesignLibraryUrl(); ?>" readonly style="width: 300px">
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Password Protection
                    </th>
                    <td>
                        <input type="text" name="design_library_password" value="<?php echo esc_attr( getPassword()); ?>" placeholder="Enter a password">
                        <p class="description">
                            <small>
                                <em>Protect your Design Set with a password. (Optional) <br /> If the <b>?password=<?php echo esc_html(getPassword()); ?></b> param is not provided, users will be prompted to enter this password.</em>
                            </small>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="providers">Custom Design Sets</label></th>
                    <td>

                        <fieldset>
                            <p>
                                <textarea class="large-text code" cols="50" id="providers" name="providers" rows="10"><?php echo $providers; ?></textarea>
                            </p>
                        </fieldset>
                        <p class="description">
                            Add custom Design Sets to your Breakdance installation.
                            By default, Breakdance provides a list of official Design Sets, but you can use this field to add any custom design sets you want.
                        </p>
                        <p><strong>Insert one URL per line.</strong></p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>

        <script>
            document.querySelector('.breakdance-design-library-shareable-url')
              ?.addEventListener('focus', (event) => {
                event.currentTarget.select();
              });
        </script>
    </form>
    <?php
}

function showWarningForInvalidDesignSets(){
  $invalidSets = getInvalidDesignSets();

  if ($invalidSets) {
    $urls = implode(", ", $invalidSets);
    $message = count($invalidSets) > 1
      ? sprintf("The following URLs are invalid design sets: <br><strong>%s</strong>", $urls)
      : "The following URL is an invalid design set: <br><strong>$urls</strong>";

    admin_notice($message, 'error');
  }
}
