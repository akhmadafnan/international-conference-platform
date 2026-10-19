import { readFileSync, readdirSync } from 'node:fs';
import { join, relative } from 'node:path';

const root = process.cwd();
const viewRoot = join(root, 'resources/js/pages/admin');
const files = [];
function collect(dir) {
    for (const item of readdirSync(dir, { withFileTypes: true })) {
        const path = join(dir, item.name);
        if (item.isDirectory()) collect(path);
        else if (item.isFile() && item.name.endsWith('.vue')) files.push(path);
    }
}
collect(viewRoot);
for (const path of [
    'resources/js/components/AdminNavigation.vue',
    'resources/js/components/DataTablePagination.vue',
    'resources/js/components/DeleteConferenceRecordDialog.vue',
])
    files.push(join(root, path));

const forbidden = [
    [
        'hardcoded palette utility',
        /\b(?:bg|text|border|ring|from|via|to)-(?:green|emerald|teal|lime|red|blue|yellow|gray|slate|zinc|stone|neutral)-\d{2,3}(?:\/\d+)?\b/g,
    ],
    ['legacy Lucide import in admin page', /from\s+['"]@lucide\/vue['"]/g],
];
const results = [];
for (const file of files) {
    const source = readFileSync(file, 'utf8');
    const name = relative(root, file).replaceAll('\\', '/');
    for (const [rule, regex] of forbidden) {
        for (const match of source.matchAll(regex)) {
            const line = source.slice(0, match.index).split('\n').length;
            results.push(`${name}:${line} ${rule}: ${match[0]}`);
        }
    }
    if (name.startsWith('resources/js/pages/admin/')) {
        for (const match of source.matchAll(/<(?:select|textarea)(?=[\s>])/g)) {
            const line = source.slice(0, match.index).split('\n').length;
            results.push(
                `${name}:${line} native form element: prefer shadcn-vue primitive`,
            );
        }
    }
}
if (results.length) {
    console.error(
        'ICHES Lyra Admin preset contract FAILED:\n' + results.join('\n'),
    );
    process.exitCode = 1;
} else {
    console.log(
        `ICHES Lyra Admin preset contract PASS (${files.length} Vue files scanned).`,
    );
}
