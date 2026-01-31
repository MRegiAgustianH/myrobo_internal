import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});


// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
//     server: {
//         host: '0.0.0.0',            // wajib agar bisa diakses jaringan
//         port: 5173,                 // port default Vite
//         strictPort: true,
//         hmr: {
//             host: '192.168.1.6',    // IP laptop Anda
//             port: 5173,
//         },
//     },
// });


// import { defineConfig } from 'vite'
// import laravel from 'laravel-vite-plugin'

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
//     server: {
//         host: 'localhost',
//         port: 5173,
//         hmr: {
//             protocol: 'wss',
//             host: new URL(process.env.APP_URL).hostname,
//         },
//     },
// })

