=== TN Environments ===
Contributors: techn
Tags: administration, environment, development, staging
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.10
Requires PHP: 8.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Confirm the current environment, style WordPress admin by environment, and display a deterministic build ID.

== Description ==

TN Environments prompts administrators to confirm the current environment, applies environment-specific admin styling, and displays a deterministic build ID.

The plugin checks a public GitHub repository for release metadata when WordPress performs plugin update checks. It sends the plugin version and standard HTTP request metadata to GitHub. GitHub terms and privacy information are available at https://docs.github.com/en/site-policy and https://docs.github.com/en/site-policy/privacy-policies/github-general-privacy-statement.

== Installation ==

1. Upload the `tn-environments` folder to `/wp-content/plugins/`, or install the release ZIP through Plugins > Add New > Upload Plugin.
2. Activate TN Environments.
3. Select and save the appropriate environment when prompted.

== Changelog ==

= 1.10 =

* Added a white background and padding to the environment tabs panel.
* Added a native, nonce-protected manual update check on the Plugins screen.
* Updated GitHub release discovery and caching to match the current distribution standard.

= 1.9 =

* Added GitHub release update support and improved plugin metadata and request handling.
