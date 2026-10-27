# Events Changelog
## Version 1.0 Final
- First public release of the module.
- Includes improved search efficiency.

## Version 1.01 Final
- Incorporated the calendar functionality added by Will.
- Add start page preference (list view vs calendar view).
- Add submenu (displays 'calendar' or 'list' depending on the start page preference).
- Improve date format handling compatibility (Debianus).

## Version 1.02 (unreleased)
- Change sort order of upcoming events block to ascending (= next events).
- Selectively flush module cache (if enabled) when new event is added/edited/deleted to keep page views updated.
- Fix bug in search function that was crashing profile views.
- Add static 'type' field identifying events as "Event" resources.
- Add support for tagging via Sprockets module, including tracking of untagged content and dynamic tagging of the block.

## Version 1.1 (ImpressCMS 2.0.4 / PHP 8.2 compatibility)
- Require ImpressCMS 2.0.4 and PHP 8.2 (requirements check, composer.json).
- Replace the removed `icms_getModuleInfo()` with the module handler.
- Fix PHP 8 issues: required parameter after optional one in `getEventsForSearch()`, `date()` TypeError on events without end date, undefined block options and variables.
- Fix wrong argument list when fetching the Sprockets taglink handler after saving an event.
- Point `pastevents.php` to an existing template.
