// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * AMD module for the activity sidebar drawer.
 *
 * Handles the floating button click, drawer open/close,
 * overlay interaction, and keyboard accessibility.
 *
 * @module     local_pathwaynav/activity_sidebar
 * @copyright  2025 Your Company
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import log from 'core/log';

const SELECTORS = {
    FLOAT_BTN: '#lpw-float-btn',
    DRAWER: '#lpw-drawer',
    OVERLAY: '#lpw-overlay',
    CLOSE_BTN: '#lpw-drawer-close',
};

const CSS = {
    OPEN: 'lpw-drawer-open',
    OVERLAY_VISIBLE: 'lpw-overlay-visible',
    BTN_HIDDEN: 'lpw-float-btn-hidden',
};

let isOpen = false;

/**
 * Open the drawer.
 *
 * @param {Object} elements - Cached DOM elements.
 */
const openDrawer = (elements) => {
    isOpen = true;
    elements.drawer.classList.add(CSS.OPEN);
    elements.drawer.setAttribute('aria-hidden', 'false');
    elements.overlay.classList.add(CSS.OVERLAY_VISIBLE);
    elements.floatBtn.classList.add(CSS.BTN_HIDDEN);
    elements.closeBtn.focus();
};

/**
 * Close the drawer.
 *
 * @param {Object} elements - Cached DOM elements.
 */
const closeDrawer = (elements) => {
    isOpen = false;
    elements.drawer.classList.remove(CSS.OPEN);
    elements.drawer.setAttribute('aria-hidden', 'true');
    elements.overlay.classList.remove(CSS.OVERLAY_VISIBLE);
    elements.floatBtn.classList.remove(CSS.BTN_HIDDEN);
    elements.floatBtn.focus();
};

/**
 * Initialise the activity sidebar module.
 */
export const init = () => {
    const floatBtn = document.querySelector(SELECTORS.FLOAT_BTN);
    const drawer = document.querySelector(SELECTORS.DRAWER);
    const overlay = document.querySelector(SELECTORS.OVERLAY);
    const closeBtn = document.querySelector(SELECTORS.CLOSE_BTN);

    if (!floatBtn || !drawer || !overlay || !closeBtn) {
        log.debug('local_pathwaynav/activity_sidebar: DOM elements not found.');
        return;
    }

    const elements = {floatBtn, drawer, overlay, closeBtn};

    // Open drawer on button click.
    floatBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openDrawer(elements);
    });

    // Close drawer on close button click.
    closeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        closeDrawer(elements);
    });

    // Close drawer on overlay click.
    overlay.addEventListener('click', () => {
        closeDrawer(elements);
    });

    // Close drawer on Escape key.
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isOpen) {
            closeDrawer(elements);
        }
    });

    // Keyboard support for buttons.
    floatBtn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openDrawer(elements);
        }
    });
};
