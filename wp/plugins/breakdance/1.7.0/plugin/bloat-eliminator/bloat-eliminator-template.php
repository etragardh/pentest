<h2>Performance</h2>
<form action="" method="post">
    <?php wp_nonce_field('breakdance_admin_bloat-eliminator_tab'); ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    Gutenberg Blocks CSS
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-gutenberg-blocks-css">
                            <input type="checkbox" name="gutenberg-blocks-css" id="breakdance-bloat-eliminator-gutenberg-blocks-css" <?php echo in_array('gutenberg-blocks-css', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Remove Gutenberg Blocks CSS</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        The Gutenberg editor loads 11kb of CSS even if you don't use it. Remove it if you're designing all of your site with Breakdance.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    XML-RPC Pingbacks
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-xml-rpc">
                            <input type="checkbox" name="xml-rpc" id="breakdance-bloat-eliminator-xml-rpc" <?php echo in_array('xml-rpc', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Remove &amp; Disable Pingbacks</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        Say goodbye to pingback spam. This option removes the XML-RPC pingback information from the <code>&lt;head&gt;</code>, and disables the WP Pingback system.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    WP Emoji
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wp-emoji">
                            <input type="checkbox" name="wp-emoji" id="breakdance-bloat-eliminator-wp-emoji" <?php echo in_array('wp-emoji', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable built-in WordPress JavaScript for rendering emojis.</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        WordPress loads a 10kb JavaScript to handle emojis on every page of your website, even if you don't use emojis. Modern browsers support emojis out-of-the-box, with no need for JavaScript.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Dashicons
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wp-dashicons">
                            <input type="checkbox" name="wp-dashicons" id="breakdance-bloat-eliminator-wp-dashicons" <?php echo in_array('wp-dashicons', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable admin icons for logged out users</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        WordPress loads its admin panel icons for all users, even though they are typically only needed for logged in users that have access to the admin panel.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    OEmbed
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wp-oembed">
                            <input type="checkbox" name="wp-oembed" id="breakdance-bloat-eliminator-wp-oembed" <?php echo in_array('wp-oembed', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable OEmbed</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        Disables the automatic embedding of some content such as YouTube videos, Tweets, etc. when pasting the URL into your blog posts.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    RSD Links
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-rsd-links">
                            <input type="checkbox" name="rsd-links" id="breakdance-bloat-eliminator-rsd-links" <?php echo in_array('rsd-links', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable Really Simple Discovery feature</span>

                        </label>
                    </fieldset>
                    <p class='description'>
                        Really Simple Discovery is a spec from 2003 related to desktop blog-publishing applications. It is also often used by XML-RPC clients to find out information about your WordPress blog.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    WLW Link
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wlw-link">
                            <input type="checkbox" name="wlw-link" id="breakdance-bloat-eliminator-wlw-link" <?php echo in_array('wlw-link', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable Windows Live Writer Link</span>
                        </label>
                    </fieldset>
                    <p class="description">Windows Live Writer was a desktop blog-publishing application. It was EOL in 2012, and completely discontinued in 2017.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    REST API
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-rest-api">
                            <input type="checkbox" name="rest-api" id="breakdance-bloat-eliminator-rest-api" <?php echo in_array('rest-api', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable WP REST metadata</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        This option removes WP REST metadata from your <code>&lt;head&gt;</code>. It does not disable the REST API.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    WP Generator
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-wp-generator">
                            <input type="checkbox" name="wp-generator" id="breakdance-bloat-eliminator-wp-generator" <?php echo in_array('wp-generator', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable WordPress Generator Meta Tag</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        Stop WordPress from adding the <code>&lt;meta name="generator" content="WordPress x.x.x"&gt;</code> tag to your head.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Remove Shortlink
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-shortlink">
                            <input type="checkbox" name="shortlink" id="breakdance-bloat-eliminator-shortlink" <?php echo in_array('shortlink', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable Shortlink Tag</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        WordPress includes a <code>&lt;link rel=shortlink ...&gt;</code> into the head if a shortlink is defined for the current page.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Relational Links
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-rel-links">
                            <input type="checkbox" name="rel-links" id="breakdance-bloat-eliminator-rel-links" <?php echo in_array('rel-links', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable Relational Links For Single Posts</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        For single posts, WordPress places relational links in the head for the posts adjacent to the current post.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    RSS Feed
                </th>
                <td>
                    <fieldset>
                        <label for="breakdance-bloat-eliminator-feed-links">
                            <input type="checkbox" name="feed-links" id="breakdance-bloat-eliminator-feed-links" <?php echo in_array('feed-links', $bloatOptions) ? ' checked' : ''; ?> />
                            <span>Disable RSS Links</span>
                        </label>
                    </fieldset>
                    <p class='description'>
                        This option removes the RSS feed information from the <code>&lt;head&gt;</code>. It does not disable the RSS feeds.
                    </p>
                </td>
            </tr>

        </tbody>
    </table>

    <p class="submit">
        <input
            type="submit"
            name="breakdance-bloat-eliminator-settings"
            id="submit"
            class="button button-primary"
            value="Save Changes"
        />
    </p>
</form>
