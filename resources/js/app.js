import './bootstrap';

import '@fortawesome/fontawesome-free/css/all.css';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import $ from 'jquery';
window.$ = window.jQuery = $;

import select2 from 'select2';
window.select2 = select2();

import 'select2/dist/css/select2.min.css';
