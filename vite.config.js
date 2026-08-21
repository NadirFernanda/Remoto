import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Por omissão o Vite apaga todo o conteúdo de outDir antes de cada
        // build. Como os ficheiros têm hash no nome (ex.: app-XXXX.css), um
        // utilizador com a página já aberta de ANTES do deploy continua a
        // pedir o ficheiro da versão anterior — se ele for apagado, essa
        // pessoa fica com o site totalmente sem estilo até recarregar,
        // porque o ficheiro que a página dela precisa deixou de existir.
        // Manter os ficheiros antigos resolve isto: cada deploy só acrescenta
        // ficheiros novos, nunca destrói os que uma sessão já em curso ainda
        // possa estar a usar.
        emptyOutDir: false,
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
