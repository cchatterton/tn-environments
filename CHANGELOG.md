# Changelog

All notable changes to TN Environments are recorded here.

## 1.10.2 - 2026-09-26

- Lower the PHP requirement to 7.4, matching WordPress 7.0.
- Allow the PHP 7.4-compatible TN Update Controller bootstrap.

## 1.10.1 - 2026-09-26

- Process environment saves before admin output so destination redirects work on WordPress 7; explicitly check save authority.

- Require WordPress 7.0+ and PHP 8.5+ for this release.

- Replace the independent GitHub updater with the version 1 TN Update Controller integration.
- Add local Install/Activate/Check controller actions and standardise Techn author/repository metadata.
- Preserve plugin identity, feature code, settings and activation scope; no feature-plugin release discovery runs during page rendering.

## 1.10 - 2026-09-04

- Added a white background and padding to the environment tabs panel.
- Added a nonce-protected "Check for updates" action and result notices to the Plugins screen.
- Updated GitHub release discovery to use the repository manifest and public redirect before the API fallback.
- Aligned update caching, failure backoff, metadata, licensing, and WordPress readme files with the current GitHub updater standard.

## 1.9 - 2026-06-14

- Renamed the plugin package from `tn-enviroments` to `tn-environments`.
- Added compliant plugin metadata, version constant, and GitHub repository links.
- Added GitHub release update support for native WordPress plugin updates.
- Added stale update cleanup and forced update-check cache bypass handling.
- Added root `tn-environments.zip` direct upload package and release build script.
- Improved request sanitisation, escaping, activation hooks, and settings-page asset loading.

## 1.8 - 2026-01-10

- Added Build ID function.

## 1.7 - 2025-12-31

- Removed Activity Feed, linked to Stream if available, and fixed CSS.

## 1.6 - 2025-03-15

- Fixed CSS.

## 1.5 - 2024-01-11

- Extended UX/UI with summary, URLs, switching, and instructions.

## 1.4 - 2024-12-31

- Added "what you've missed" from Stream.

## 1.3 - 2024-11-01

- Added support for local environment with custom styles.

## 1.2 - 2024-10-01

- Redirected users based on environment.

## 1.1 - 2024-09-01

- Hid admin menus until logged in.

## 1.0 - 2024-01-01

- Initial plugin release.
