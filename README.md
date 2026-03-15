# Pathway Activity Navigation

A Moodle local plugin that provides course section navigation on activity pages (quizzes, assignments, certificates, etc.) within courses using the [Pathway course format](https://github.com/volodymyrdovhan/moodle-format_pathway).

## The Problem

When a learner clicks into a quiz or other activity from a Pathway-formatted course, they leave the course view and lose access to the sidebar navigation. The only way back is through breadcrumbs or the browser back button.

## The Solution

This plugin detects when a user is on an activity page within a Pathway course and injects navigation that keeps them oriented within the course structure. Two navigation styles are available:

### Course Menu (Drawer)

A floating "Course Menu" button on the left edge of the screen. Clicking it opens a slide-out drawer with:

- Course name
- Overall completion progress bar
- "Back to [section]" quick link
- Full section list with completion status (complete, in progress, not started)
- Current section highlighted

The drawer closes via the X button, clicking the overlay, or pressing Escape.

### Progress Bar

A slim horizontal bar fixed below the Moodle navbar showing:

- Back arrow to return to the current section
- Clickable section steps connected by lines, color-coded by completion status
- Current section highlighted in blue
- Circular overall progress indicator on the right
- Auto-hides on scroll down, reappears on scroll up

Section labels are visible on desktop and hidden on tablets/phones to save space.

## Requirements

- Moodle 5.0 or later
- [Pathway course format](https://github.com/volodymyrdovhan/moodle-format_pathway) plugin installed

## Installation

1. Download or clone this repository
2. Copy the `pathwaynav` folder to `local/pathwaynav` in your Moodle installation
3. Visit Site Administration > Notifications to complete the installation
4. Configure at Site Administration > Plugins > Local plugins > Pathway activity navigation

## Configuration

Navigate to **Site Administration > Plugins > Local plugins > Pathway activity navigation**.

| Setting | Options | Description |
|---------|---------|-------------|
| Activity navigation style | Disabled / Progress bar / Course menu | Choose which navigation style appears on activity pages |

The default is **Course menu** (drawer).

## How It Works

The plugin uses Moodle's hook system (`core\hook\output\before_standard_top_of_body_html_generation`) to inject HTML on every page load. It checks three conditions before rendering:

1. The current page is an activity/module page (`mod-*` page type)
2. The activity belongs to a course using the Pathway format
3. The navigation style is not set to "disabled"

If all conditions are met, it renders the selected navigation style and loads the corresponding AMD module for interactivity.

## Features

- **Automatic section detection** — highlights the section containing the current activity
- **Completion tracking** — shows per-section and overall progress when completion is enabled
- **Keyboard accessible** — Escape closes the drawer, proper focus management
- **Dark mode support** — respects Moodle's dark theme settings
- **Mobile responsive** — drawer adapts to small screens, progress bar hides labels on tablets
- **Zero configuration per course** — works automatically on all Pathway courses
- **Lightweight** — only loads on activity pages within Pathway courses, no impact on other pages

## File Structure

```
pathwaynav/
├── amd/
│   ├── build/
│   │   ├── activity_sidebar.min.js
│   │   └── progress_bar.min.js
│   └── src/
│       ├── activity_sidebar.js
│       └── progress_bar.js
├── classes/
│   ├── hook/
│   │   └── callbacks.php
│   ├── output/
│   │   ├── activity_sidebar.php
│   │   ├── progress_bar.php
│   │   └── renderer.php
│   └── privacy/
│       └── provider.php
├── db/
│   └── hooks.php
├── lang/
│   └── en/
│       └── local_pathwaynav.php
├── templates/
│   ├── activity_sidebar.mustache
│   └── progress_bar.mustache
├── lib.php
├── settings.php
├── styles.css
├── version.php
└── README.md
```

## License

This plugin is licensed under the [GNU GPL v3 or later](http://www.gnu.org/copyleft/gpl.html).

## Credits

Developed as a companion plugin for the Pathway course format.
