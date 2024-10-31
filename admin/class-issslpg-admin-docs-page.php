<?php


function issslpg_register_docs_admin_page() {
	add_submenu_page(
		'issslpg_location_settings',
		__( 'Docs', 'isslpg' ),
		__( 'Docs', 'isslpg' ),
		'manage_options',
		'docs',
		'issslpg_docs_page',
		100
	);
}

add_action( 'admin_menu', 'issslpg_register_docs_admin_page', 9999 );


function issslpg_docs_page() {
	?>
	<script>
		function openTab(evt, tabName) {
			// Declare all variables
			var i, tabcontent, tablinks;

			evt.preventDefault();

			// Get all elements with class="tabcontent" and hide them
			tabcontent = document.getElementsByClassName("tabcontent");
			for (i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}

			// Get all elements with class="tablinks" and remove the class "active"
			tablinks = document.getElementsByClassName("nav-tab");
			for (i = 0; i < tablinks.length; i++) {
				tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
			}

			// Show the current tab, and add an "active" class to the button that opened the tab
			document.getElementById(tabName).style.display = "block";
			evt.currentTarget.className += " nav-tab-active";
		}
	</script>
	<style>
		.wrap,
		.wrap p {
			max-width: 800px;
			font-size: .9rem;
			line-height: 1.6;
		}
		img {
			width: 100%;
			border: 1px solid #c3c4c7;
			margin-top: .5rem;
		}
		h2 {
			font-size: 1.4rem;
			margin-top: 3rem;
			padding-bottom: .5rem;
			border-bottom: 1px solid #c3c4c7;
		}

		.nav-tab-wrapper {
			margin-top: 1rem !important;
		}
		.tabcontent {
			display: none;
		}
		#setup {
			display: block;
		}
	</style>

	<div class="wrap">



		<h1>
			<?php esc_html_e( 'SEO Plugin Bundle Documentation', 'issslpg' ); ?>
		</h1>



		<div class="nav-tab-wrapper">
			<a class="nav-tab" onclick="openTab(event, 'installation')" href="#">Installation</a>
			<a class="nav-tab nav-tab-active" onclick="openTab(event, 'setup')" href="#">Setup</a>
			<a class="nav-tab" onclick="openTab(event, 'randomizer')" href="#">Randomizer Panels</a>
			<a class="nav-tab" onclick="openTab(event, 'settings')" href="#">Settings</a>
			<a class="nav-tab" onclick="openTab(event, 'location-settings')" href="#">Location Settings</a>
			<a class="nav-tab" onclick="openTab(event, 'shortcodes')" href="#">Shortcodes</a>
		</div>



		<div id="installation" class="tabcontent">
			<h2>Plugin Installation</h2>
			<ol>
				<li>Buy the plugin on intellasoftplugins.com.</li>
				<li>You will receive an email with the download link.</li>
				<li>Click the download link to download the plugin.</li>
				<li>Login to your WordPress site’s admin dashboard.</li>
				<li>In the admin dashboard, go to “Plugins > Add New”.</li>
				<li>Click the “Upload Plugin” button at the top of the page.</li>
				<li>Click the “Choose File” button and select the plugin file you’ve just downloaded.</li>
				<li>Click the “Install Now” button.</li>
				<li>Once the plugin has been installed successfully, click the “Activate Plugin” button.</li>
				<li>On the next screen you can opt-in to automatically receive updates (recommended), or skip.</li>
				<li>After clicking on the “Allow & Continue” button, you will receive an email with an activation link.</li>
				<li>Click the link, and the plugin is ready to be used.</li>
			</ol>
		</div>



		<div id="setup" class="tabcontent">
			<h2>Basic Plugin Setup</h2>
			<h3>Settings</h3>
			<p>
				First we need to make sure that we have the correct Landing Page Generator settings in place.
			</p>
			<ol>
				<li>Go to <b>SEO Landing Page Generator > Settings</b>.</li>
				<li>Set the <b>Default Phone Number</b>. This phone number acts as a fallback and will be displayed on landing pages that have no phone number assigned to their location.</li>
				<li>
					Make sure the <b>Heading Format</b> and <b>Page Title Format</b> are set to your liking.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/settings-1.png">
				</li>
				<li>Go to <b>SEO Landing Page Generator > Company Info</b> and fill in as much information about your company as you can. This information is used to generate Schema data for search engines.</li>
			</ol>
			<p>
				Next, we need to make sure to set up our Content Randomizer settings.
			</p>
			<ol>
				<li>Go to <b>SEO Content Randomizer</b>.</li>
				<li>Scroll to the <b>Post Type: Template Page</b> panel.</li>
				<li>
					In the Panel textareas, enter the names of panels you would like to create content versions for. By default, these are filled in for you, but you can change or add panels here.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/settings-2.png">
				</li>
				<li>Hit the <b>Save Changes</b> button when you're done.</li>
			</ol>

			<h3>Template Pages</h3>
			<p>
				The plugin uses templates to create randomized, location based landing pages.
				<br>
				In a template page, you can add many different versions of the same content sections, to give the randomizer enough data to randomize every landing page's content.
			</p>
			<ol>
				<li>Go to <b>Template Pages > Add New</b> to create a new template page.</li>
				<li>On the template page, set a title for the service you want to promote with your landing pages (e.g. "Flood Damage Repair").</li>
				<li>Hit the <b></b>Publish</li> button.</li>
				<li>Open the <b>SEO Content P1: Content</b> panel.</li>
				<li>Open the <b>P1 Content Block 1</b> block in the <b>SEO Content P1: Content</b> panel and write the first paragraph of the landing page.</li>
				<li>
					You can add location based shortcodes from the <b>SEO Landing Page Generator Shortcodes</b> button in the toolbar of the WYSIWYG editor. Each shortcode acts as a placeholder for dynamic location information to be filled in when the landing page renders.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-1.png">
				</li>
				<li>When you're done writing the first paragraph, click the <b>Add Another P1 Content Block</b> to add another content block.</li>
				<li>
					In the <b>P1 Content Block 2</b>, enter a different worded version of the first paragraph.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-2.png">
				</li>
				<li>You can repeat this and add even more variations. The more variations you add, the more unique your landing pages will be.</li>
				<li>In order for one of the blocks to actually be randomly displayed on a landing page, you need to copy the shortcode that's displayed in the description text of the panel to the <b>Content Block 1</b> in the <b>SEO Content Randomizer: Content</b> panel. We will use <b>Content Block 1</b> as aggregate for other shortcodes and we will not add any other content blocks to the <b></b>Content</li> panel.</li>
				<li>
					After this, you can add more content blocks to the P2, P3, etc. panels and then add the panel shortcodes to the <b>Content Block 1</b> in the <b></b>Content</li> panel.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-3.png">
				</li>
				<li>You can also add content to the other panel types (e.g. Images, Phrases, Keywords, etc.). The different content panel types will be explained later.</li>
				<li>
					Finally, add some meta description variants in the <b>Meta Descriptions</b> panel.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-4.png">
				</li>
				<li>Hit the <b></b>Update</li> button when you're done.</li>
			</ol>

			<h3>Landing Page Creation</h3>
			<ol>
				<li>
					Go to <b>SEO Landing Page Generator</b>.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/location-settings-1.png">
				</li>
				<li>
					Choose a country, a state, and then a county.
				</li>
				<li>
					On the county screen, select the cities you want to build landing pages for.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/location-settings-2.png">
				</li>
				<li>Now the plugin will start creating landing pages (10 per minute to not overwhelm your server).</li>
				<li>You can see the landing pages by going to <b>Landing Pages</b>.</li>
				<li>
					To view a particular landing page, hover over the landing page table row and click the <b></b>View</li> link.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/landing-page-1.png">
				</li>
			</ol>
		</div>



		<div id="randomizer" class="tabcontent">

			<h2>Randomizer Panels</h2>

			<h3>Content Panels</h3>
			<ol>
				<li>To add content panels, go to <b>SEO Content Randomizer</b> and scroll to the panel of the post type you'd like to create the panels for.</li>
				<li>
					In the <b>Content Panels</b> field, write the names of the content panels you'd like to create.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/cr-settings-content-panels.png">
				</li>
				<li>Hit the <b>Save Changes</b> button when you're done.</li>
				<li>Go to a template page.</li>
				<li>
					You can now add blocks to the content panels that you created.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-5.png">
				</li>
				<li>
					In order for one of the blocks to actually be randomly displayed on a landing page, you need to copy the shortcode that's displayed in the description text of the panel to the <b>Content Block 1</b> in the <b>SEO Content Randomizer: Content</b> panel.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-3.png">
				</li>
			</ol>

			<h4>Image Panels</h4>
			<ol>
				<li>To add image panels, go to <b>SEO Content Randomizer</b> and scroll to the panel of the post type you'd like to create the panels for.</li>
				<li>In the <b>Image Panels</b> field, write the names of the image panels you'd like to create.</li>
				<br>
				<img src="https://intellasoftplugins.com/data/doc-images/cr-settings-image-panels.png">
				<li>Hit the <b>Save Changes</b> button when you're done.</li>
				<li>Go to a template page.</li>
				<li>
					You can now add blocks to the image panels that you created.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-6.png">
				</li>
				<li>
					In order for one of the images to actually be randomly displayed on a landing page, you need to copy the shortcode that's displayed in the description text of the panel to the <b>Content Block 1</b> in the <b>SEO Content Randomizer: Content</b> panel.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-3.png">
				</li>
			</ol>

			<h4>Keyword Panels</h4>
			<ol>
				<li>To add keyword panels, go to <b>SEO Content Randomizer</b> and scroll to the panel of the post type you'd like to create the panels for.</li>
				<li>
					In the <b>Keyword Panels</b> field, write the names of the keyword panels you'd like to create.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/cr-settings-keyword-panels.png">
				</li>
				<li>Hit the <b>Save Changes</b> button when you're done.</li>
				<li>Go to a template page.</li>
				<li>
					You can now add singular and plural keywords (one per line) to the textareas of the panels you created.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-7.png">
				</li>
				<li>
					In order for one of the keywords to actually be randomly displayed on a landing page, you need to copy the shortcode that's displayed in the description text of the panel to the <b>Content Block 1</b> in the <b>SEO Content Randomizer: Content</b> panel.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-8.png">
				</li>
			</ol>

			<h4>Phrase Panels</h4>
			<ol>
				<li>To add phrase panels, go to <b>SEO Content Randomizer</b> and scroll to the panel of the post type you'd like to create the panels for.</li>
				<li>
					In the <b>Phrase Panels</b> field, write the names of the phrase panels you'd like to create.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/cr-settings-phrase-panels.png">
				</li>
				<li>Hit the <b>Save Changes</b> button when you're done.</li>
				<li>Go to a template page.</li>
				<li>
					You can now add different phrases (one per line) to the textarea.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-9.png">
				</li>
				<li>
					In order for one of the phrases to actually be randomly displayed on a landing page, you need to copy the shortcode that's displayed in the description text of the panel to the <b>Content Block 1</b> in the <b>SEO Content Randomizer: Content</b> panel.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-10.png">
				</li>
			</ol>

			<h4>List Panels</h4>
			<ol>
				<li>To add list panels, go to <b>SEO Content Randomizer</b> and scroll to the panel of the post type you'd like to create the panels for.</li>
				<li>
					In the <b>List Panels</b> field, write the names of the list panels you'd like to create.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/cr-settings-list-panels.png">
				</li>
				<li>Hit the <b>Save Changes</b> button when you're done.</li>
				<li>Go to a template page.</li>
				<li>
					You can now add different list items (one per line) to the textarea.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-11.png">
				</li>
				<li>In order for the list to actually be displayed on a landing page in randomized order, you need to copy the shortcode that's displayed in the description text of the panel to the <b>Content Block 1</b> in the <b>SEO Content Randomizer: Content</b> panel.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-12.png">
				</li>
			</ol>

			<h4>Definition List Panels</h4>
			<ol>
				<li>To add definition list panels, go to <b>SEO Content Randomizer</b> and scroll to the panel of the post type you'd like to create the panels for.</li>
				<li>
					In the <b>Definition List Panels</b> field, write the names of the list panels you'd like to create.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/cr-settings-definition-list-panels.png">
				</li>
				<li>Hit the <b>Save Changes</b> button when you're done.</li>
				<li>Go to a template page.</li>
				<li>
					You can now add blocks to the definition list panels that you created.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-13.png">
				</li>
				<li>In order for the list to actually be displayed on a landing page in randomized order, you need to copy the shortcode that's displayed in the description text of the panel to the <b>Content Block 1</b> in the <b>SEO Content Randomizer: Content</b> panel.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/template-page-14.png">
				</li>
			</ol>
		</div>



		<div class="tabcontent" id="settings">

			<h2>Settings</h2>

			<h3>Company Info</h3>
			<p>
				To enter information about your company, go to <b>SEO Landing Page Generator > Company Info</b>.
				The information entered here can be displayed on landing pages via shortcodes that are added to template pages.
				It will also be used to generate schema data for serach engines.
			</p>
			<ol>
				<li>
					In the <b>Company Info</b> panel, enter basic contact information about your company.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-1.png">
				</li>
				<li>
					In the <b>Company Address</b> panel, enter your companies headquater company address.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-2.png">
				</li>
				<li>
					In the <b>Company Branding</b> panel, add a company logo and an image of your storefront, office space, or whatever image represents your company best.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-3.png">
				</li>
				<li>
					In the <b>Company Externals</b> panel, add links to any external sites that are related to your company (e.g. Facebook pages, Instagram profiles, Twitter, etc.).
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-4.png">
				</li>
				<li>
					In the <b>Company Business Hours</b> panel, enter your business hours in military time (e.g. 5 PM as 17:00). Leave fields empty for the days the business is closed.
					If you're open for 24 hours, write 00:00 in the open field and 23:59 in the close field.
					This information will be used by search engines like Google to display your business hours directly in the search results.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-5.png">
				</li>
			</ol>

			<h3>XML Sitemap</h3>
			<p>
				To configure the XML sitemap, go to <b>SEO Landing Page Generator > XML Sitemap</b>.
			</p>
			<ol>
				<li>
					In the first panel, you can choose to deactivate the XML sitemap in case you're using a third party plugin to generate it.
					You can also regenerate the sitemap in case you moved the site to a different URL for example.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-6.png">
				</li>
				<li>
					In the <b>Supported Post Types</b> panel you can choose what post types you want to include in the XML sitmap.
					We recommend to only include post types os posts that search engine relevant content.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-7.png">
				</li>
				<li>
					In the <b>Landing Pages</b> panel you can choose what landing pages you want to include in the XML sitmap.
					Unless a template page is still under construction, we recommend to include the correlated landing pages.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-8.png">
				</li>
			</ol>

			<h3>HTML Sitemap</h3>
			<p>
				To configure the HTML sitemap, go to <b>SEO Landing Page Generator > HTML Sitemap</b>.
			</p>
			<ol>
				<li>
					In the first panel, you have to set the URL slug of the page you have the <b>[iss_sitemap]</b> shortcode on.
					By default, the plugin creates a page named "Sitemap" automatically in draft mode, that you just have to publish.
					Additionally, you can choose to also display a link to the XML sitemap in the HTML sitemap and pick a sitemap export in CSV format.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-9.png">
				</li>
				<li>
					In the <b>Landing Pages</b> panel you can choose what landing pages you want to include in the HTML sitmap.
					Unless a template page is still under construction, we recommend to include the correlated landing pages.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-10.png">
				</li>
			</ol>

			<h3>Schema</h3>
			<p>
				To configure the schema data that the plugin outputs, go to <b>SEO Landing Page Generator > Schema</b>.
				Here you can decide what type of schema data the plugin should generate.
				By default all types are activated.
				<br>
				<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-11.png">
			</p>

			<h3>FAQ</h3>
			<p>
				To configure the FAQ that the plugin outputs, go to <b>SEO Landing Page Generator > FAQ</b>.
				Here you can add FAQ items and set a question and answer for each item.
				You can then use the shortcode <b>[iss_faq]</b> or <b>[iss_faq_accordion]</b> (to get an accordion menu) to display the FAQ on any page you like.
				FAQ Schema data will be generated automatically on the page or post you add the shortcode.
				<br>
				<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-12.png">
			</p>

			<h3>Cache</h3>
			<p>
				To configure the Cache settings that get applies to landing pages, go to <b>SEO Content Randomizer > Cache</b>.
				Here you're able to completely disable the cache, delete the cache that has been created, and set the cache expiration time.
				We recommend to keep <b>Page Cache</b> enabled and have it set to at least more than 3 days.
				<br>
				<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-company-info-13.png">
			</p>

		</div>



		<div id="location-settings" class="tabcontent">

			<h2>Location Settings</h2>

			<h3>State Settings</h3>
			<ol>
				<li>
					To configure the settings of a state, go to <b>SEO Landing Page Generator</b>, select a country, and then click the <b>Edit</b> link next to the name of a state.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-14.png">
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-15.png">
				</li>
				<li>
					Here you can enter the Google Place ID of the main office location in the state.
					This location will be used as a fallback, when no closer office location is set on the county level, when you're displaying the Directions Map shortcode <b>[iss_directions_map]</b> or widget.
					You can obtain your Google Place ID for your business <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank">here</a>.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-16.png">
				</li>
			</ol>

			<h3>County Settings</h3>
			<ol>
				<li>
					To configure the settings of a county, go to <b>SEO Landing Page Generator</b>, select a country, a state, and then click the <b>Edit</b> link next to the name of a county.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-14.png">
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-15.png">
				</li>
				<li>
					In the <b>Local Office</b> section you can enter the address of a local office that operates in the county.
					This data will be used for the schema data of a landing page.
					You can also enter a Google Place ID that will be used to display the Directions Map shortcode <b>[iss_directions_map]</b> or widget.
					You can obtain your Google Place ID for your business <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank">here</a>.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-17.png">
				</li>
				<li>
					In the <b>Custom Locations</b> section you can add locations that are not already in the county, including phone number.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-18.png">
				</li>
				<li>
					In the <b>Template Pages</b> section you can select template pages of which correlated landing pages you don't want to be generated for the county.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-19.png">
				</li>
			</ol>

			<h3>City Settings</h3>
			<ol>
				<li>
					To configure the settings of a county, go to <b>SEO Landing Page Generator</b>, select a country, a state, a county, and then click the <b>Edit</b> link next to the name of a city.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-14.png">
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-15.png">
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-21.png">
				</li>
				<li>
					Here you can activate or deactivate the location for landing page creation.
					You can also set a specific phone number that will be displayed on landing pages for this location.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-20.png">
				</li>
			</ol>

		</div>



		<div class="tabcontent" id="shortcodes">

			<h2>Shortcodes</h2>

			<h3>SEO Landing Page Generator Shortcodes</h3>
			<ol>
				<li>
					You can add location based shortcodes by clicking the <b>SEO Landing Page Generator Shortcodes</b> button in the toolbar of the WYSIWYG editor.
					Each shortcode acts as a placeholder for dynamic location information to be filled in when the landing page renders.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-22.png">
				</li>
				<li>
					When you select a shortcode from the drop-down menu, most shortcodes will display a modal window that let's you enter a prefix and a suffix.
					The prefix and suffix will only be displayed when the shortcode actually has an output, so you can construct a sentence that you can be sure will be complete.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-24.png">
				</li>
			</ol>

			<h3>SEO Content Randomizer Shortcodes</h3>
			<ol>
				<li>
					You can add shortcodes by clicking the <b>SEO Content Randomizer Shortcodes</b> button in the toolbar of the WYSIWYG editor.
					Each shortcode acts as a placeholder for a randomly selected block of the panel which name you pick from the dropdown.
					Usually you aggregate the shortcodes to in the <b>P1 Content Block 1</b> block in the <b>SEO Content P1: Content</b> panel to constuct a template of your landing pages.
					<br>
					<img src="https://intellasoftplugins.com/data/doc-images/lpg-settings-23.png">
				</li>
			</ol>

		</div>



	</div>
	<!-- /.wrap -->
	<?php
}