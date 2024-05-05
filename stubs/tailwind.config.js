/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
export default {
    content: [
        // You will probably also need these lines
        "./resources/**/**/*.blade.php",
        "./resources/**/**/*.js",
        "./app/View/Components/**/**/*.php",
        "./app/Livewire/**/**/*.php",

        // Add mary
        "./vendor/robsontenorio/baldeui/src/View/Components/**/*.php"
    ],
    theme: {
        extend: {
            colors: {
                "primary": "#65a30d",

                "secondary": "#1e40af",

                "accent": "#4f46e5",

                "neutral": "#9ca3af",

                "base-100": "#e7e5e4",

                "info": "#0ea5e9",

                "success": "#22c55e",

                "warning": "#fdba74",

                "error": "#ff0000",
            },
        },
    },

    plugins: [

    ]
}
