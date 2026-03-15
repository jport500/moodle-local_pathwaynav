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
 * AMD module for the Pathway progress bar.
 *
 * Handles scrolling the active step into view and
 * auto-hide on scroll down / show on scroll up behavior.
 *
 * @module     local_pathwaynav/progress_bar
 * @copyright  2025 Your Company
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import log from 'core/log';

/**
 * Initialise the progress bar module.
 */
export const init = () => {
    const container = document.querySelector('#lpwbar-container');
    if (!container) {
        log.debug('local_pathwaynav/progress_bar: Container not found.');
        return;
    }

    // Scroll the active step into view within the horizontal nav.
    const activeStep = container.querySelector('.lpwbar-step-active');
    if (activeStep) {
        const stepsNav = container.querySelector('.lpwbar-steps');
        if (stepsNav) {
            // Use a short delay to ensure layout is complete.
            setTimeout(() => {
                activeStep.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center',
                });
            }, 100);
        }
    }

    // Auto-hide bar on scroll down, show on scroll up.
    let lastScrollY = window.scrollY;
    let ticking = false;

    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const currentScrollY = window.scrollY;
                if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    container.classList.add('lpwbar-hidden');
                } else {
                    container.classList.remove('lpwbar-hidden');
                }
                lastScrollY = currentScrollY;
                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener('scroll', onScroll, {passive: true});
};
