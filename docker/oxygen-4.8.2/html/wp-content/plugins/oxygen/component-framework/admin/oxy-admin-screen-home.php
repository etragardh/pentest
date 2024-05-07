<div class='wrap'>
	<h1 class="oxygen-admin-screen-home-title">Oxygen</h1>

		<div class='oxygen-admin-screen-home-flex'>

		<div class='oxygen-admin-screen-home-left'>

			<div class='oxygen-admin-screen-home-section'>
				<h2>Adding New Pages</h2>
				<p>Add a new page to your site, save it, and then click <em>Edit with Oxygen</em> to visually design your page.</p>
				<a class="oxygen-admin-screen-home-link" href='<?php echo admin_url('post-new.php?post_type=page');?>'>Add A New Page</a>
			</div>


			<div class='oxygen-admin-screen-home-section'>
				<h2>Customizing Sitewide Templates</h2>
				<p>Oxygen controls the design of your entire website using templates. Customize &amp; create your templates to control your headers, footers, and layouts for any post type or archive.</p>
				<a class="oxygen-admin-screen-home-link" href='<?php echo admin_url('edit.php?post_type=ct_template');?>'>Go To Oxygen Templates</a>
			</div>

			<div class='oxygen-admin-screen-home-section'>
				<h2>Help &amp; Tutorials</h2>
				<p>New to Oxygen? Watch our short <a href='https://oxygenbuilder.com/documentation/getting-started/getting-started-tutorial/' target="_blank">getting started video</a> to get up to speed.</p>

				<ul>
					<li>
						<a class="oxygen-admin-screen-home-link" href='https://oxygenbuilder.com/documentation' target="_blank">Tutorials &amp; Documentation</a>
					</li>
					<li>
						<a class="oxygen-admin-screen-home-link" href='https://www.youtube.com/watch?v=_3tKAwbqw5Q&list=PLKW1BFmB4NOuKzsEx3VIFSfMHXCm21DhN' target="_blank">Tutorial Series on YouTube</a>
					</li>					
				</ul>


			</div>
		</div>

	<?php
		$site = get_option('ct_last_installed_default_data', false);
		global $ct_source_sites;
	?>

		<div class='oxygen-admin-screen-home-right'>

			<div class='oxygen-admin-screen-home-section'>

				<h3>Oxygen Design Library</h3>
				<p>Browse Oxygen's design sets and then choose a design set to install.</p>
				<?php if($site) { 
					$label = $site;
					if(isset($ct_source_sites[$site]) && isset($ct_source_sites[$site]['label'])) {
						$label = $ct_source_sites[$site]['label'];
					}
					?>
				<div class='oxygen-admin-screen-home-section-design-set-chooser-text'>The <?php echo esc_html($label);?> pre-built website has been installed from the Oxygen Design Library.</div>
				<?php } ?>
				<a class="oxygen-admin-screen-home-link" href='<?php echo add_query_arg('page', 'ct_install_wiz', get_admin_url());?>'>Install a <?php echo $site?'Different ':'';?>Website</a>

				<!--
				<div class='oxygen-admin-screen-home-section-design-set-chooser-text'>You can install pre-built websites in 1-click from our design library.</div>
				<a href=''>Browse Library</a>
				-->

			</div>

			<div class='oxygen-admin-screen-home-section'>
				<h2>Accessing the Design Library</h2>
				<p>Access Oxygen's Design Library by opening the Oxygen visual editor. Click <em>+Add</em> in the top left of the builder, and then click <em>Library</em>.</p>
			</div>

			<div class='oxygen-admin-screen-home-section'>
				<h2>Support</h2>
				<p>If you need a hand with something, you can either reach out in our Facebook group or contact our support team.</p>

				<ul>
					<li>
						<a class="oxygen-admin-screen-home-link" href='https://oxygenbuilder.com/facebook' target="_blank">Facebook Group (HIGHLY recommended)</a>
					</li>
					<li>
						<a class="oxygen-admin-screen-home-link" href='https://oxygenbuilder.com/support' target="_blank">Contact the Oxygen Support Team</a>
					</li>
				</ul>


			</div>			

		</div>	

	</div>


</div>

