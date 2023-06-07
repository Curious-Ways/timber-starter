// npx tailwindcss -i ./static/css/tailwind.css -o ./dist/style.css --watch

const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./templates/**/*.twig",
    "./templates/**/*.js",
  ],
  theme: {
    extend: {     
      colors: {
        'cw-black': '#080808',  
      },
      fontFamily: {
        sans: ["Inter", ...defaultTheme.fontFamily.sans],
        // 'display': ['Oswald', 'sans-serif'],
      },
    },
  },
  plugins: [],
}