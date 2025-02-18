import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

// Fait des confetti au click sur la page
import canvasCofetti from 'canvas-confetti'
document.body.addEventListener('click',() => {
    // canvasCofetti();
})

// Import css
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
