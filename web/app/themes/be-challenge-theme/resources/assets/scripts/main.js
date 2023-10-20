/** import external dependencies */
import $ from 'jquery/dist/jquery.js';
import "waypoints/lib/jquery.waypoints.min.js";
// import Swiper from 'swiper';

/** import local dependencies */
import Router from './util/Router';
import common from './routes/common';

/**
 * Populate Router instance with DOM routes
 * @type {Router} routes - An instance of our router
 */
const routes = new Router({common});

/**
 * Polyfill Corrections useful for Vue
 */
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = function(callback, thisArg) {
        thisArg = thisArg || window;
        for (var i = 0; i < this.length; i++) {
            callback.call(thisArg, this[i], i, this);
        }
    };
}
if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}
if (typeof NodeList.prototype.forEach !== 'function')  {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

/** Load Events */
jQuery(document).ready( routes.loadEvents() );
