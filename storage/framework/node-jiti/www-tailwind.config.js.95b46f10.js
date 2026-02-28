"use strict";Object.defineProperty(exports, "__esModule", {value: true}); function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }var _defaultTheme = require('tailwindcss/defaultTheme'); var _defaultTheme2 = _interopRequireDefault(_defaultTheme);
var _forms = require('@tailwindcss/forms'); var _forms2 = _interopRequireDefault(_forms);

/** @type {import('tailwindcss').Config} */
exports. default = {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ..._defaultTheme2.default.fontFamily.sans],
            },
        },
    },

    plugins: [_forms2.default],
};
 /* v7-03d3714ea2cc34e5 */