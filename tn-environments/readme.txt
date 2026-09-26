=== TN Environments ===
Contributors: techn
Tags: administration, environment, development, staging
Requires at least: 7.0
Tested up to: 7.1.2
Stable tag: 1.10.1
Requires PHP: 8.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Confirm the current environment, style WordPress admin by environment, and display a deterministic build ID.

== Description ==

TN Environments prompts administrators to confirm the current environment, applies environment-specific admin styling, and displays a deterministic build ID.

Update discovery and release details are supplied by TN Update Controller. This plugin does not independently request release metadata, repository readmes or changelogs. The explicit controller-install action downloads its official GitHub release ZIP; see Controller installation service below.

== Installation ==

1. Upload the `tn-environments` folder to `/wp-content/plugins/`, or install the release ZIP through Plugins > Add New > Upload Plugin.
2. Activate TN Environments.
3. Select and save the appropriate environment when prompted.

== Changelog ==

= 1.10.1 =
* Handle environment saves before page output with an explicit capability check.
* Replace the independent updater with TN Update Controller integration.
* Standardise author and plugin-row links; preserve feature settings and plugin identity.
* Require WordPress 7.0+ and PHP 8.5+.

= 1.10 =

* Added a white background and padding to the environment tabs panel.
* Added a native, nonce-protected manual update check on the Plugins screen.
* Updated GitHub release discovery and caching to match the current distribution standard.

= 1.9 =

* Added GitHub release update support and improved plugin metadata and request handling.

== Managed updates ==

Install and activate TN Update Controller to discover and install updates. The plugin row offers Install Techn Update Controller, Activate Techn Update Controller, or Check for updates according to local state and permissions. Feature operation does not require the controller. No release lookup happens while rendering this plugin's row. On multisite the controller must be network active. This plugin release requires WordPress 7.0 and PHP 8.5 or later.

== Controller installation service ==

Only an explicit authorised Install Techn Update Controller action downloads the official controller ZIP from GitHub. No plugin settings or site inventory are submitted; GitHub receives the server IP address and normal request metadata. Routine update discovery is delegated to the installed controller. Repository links open GitHub when selected.
Terms: https://docs.github.com/en/site-policy/github-terms/github-terms-of-service
Privacy: https://docs.github.com/en/site-policy/privacy-policies/github-general-privacy-statement
