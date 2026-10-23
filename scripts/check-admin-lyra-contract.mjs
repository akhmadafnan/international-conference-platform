import { readFileSync, readdirSync } from 'node:fs';
import { join, relative } from 'node:path';
import { parse } from '@vue/compiler-sfc';
import { baseParse } from '@vue/compiler-dom';

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
collect(join(root, 'resources/js/pages/submissions'));
collect(join(root, 'resources/js/components/submissions'));
collect(join(root, 'resources/js/pages/academic'));
collect(join(root, 'resources/js/components/academic'));
for (const path of [
    'resources/js/pages/Dashboard.vue',
    'resources/js/pages/registration/Show.vue',
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
    if (
        name.startsWith('resources/js/pages/admin/') ||
        name.startsWith('resources/js/pages/submissions/') ||
        name.startsWith('resources/js/pages/academic/')
    ) {
        for (const match of source.matchAll(/<(?:select|textarea)(?=[\s>])/g)) {
            const line = source.slice(0, match.index).split('\n').length;
            results.push(
                `${name}:${line} native form element: prefer shadcn-vue primitive`,
            );
        }
    }
}
// Keep this bounded to the corrected authenticated surfaces. Dialogs and QR
// details may have their own width, but cannot constrain the workspace itself.
const fluidViews = [
    'components/academic/AcademicPacket.vue',
    'pages/submissions/Details.vue',
    'components/submissions/SubmissionWizardSections.vue',
    'pages/submissions/Index.vue',
    'pages/academic/Submission.vue',
    'pages/academic/Review.vue',
    'pages/academic/Author.vue',
    'pages/registration/Show.vue',
    'pages/admin/series/Create.vue',
    'pages/admin/editions/Create.vue',
    'pages/admin/editions/AcademicSetup.vue',
    'layouts/settings/Layout.vue',
    'pages/documents/EventPass.vue',
    'components/AppContent.vue',
    'components/AppHeader.vue',
];
for (const view of fluidViews) {
    const source = readFileSync(join(root, 'resources/js', view), 'utf8');
    const template = parse(source).descriptor.template;
    function inspect(node, inOverlay = false) {
        if (node.type === 1) {
            inOverlay ||= /^(?:Dialog|Sheet|AlertDialog)Content$/.test(
                node.tag,
            );
            const classes =
                node.props.find(
                    (prop) => prop.type === 6 && prop.name === 'class',
                )?.value?.content ?? '';
            for (const token of classes.split(/\s+/)) {
                const utility = token.split(':').at(-1);
                const qrDetail =
                    view === 'pages/documents/EventPass.vue' &&
                    ['code', 'p'].includes(node.tag) &&
                    ['max-w-56', 'max-w-60'].includes(utility);
                const compactControl = ['Input', 'SelectTrigger'].includes(
                    node.tag,
                );
                if (
                    !inOverlay &&
                    !qrDetail &&
                    !compactControl &&
                    (utility === 'mx-auto' ||
                        (/^max-w-/.test(utility) &&
                            !['max-w-full', 'max-w-none'].includes(utility)))
                )
                    results.push(
                        `${view}: constrained workspace width: ${token}`,
                    );
            }
            if (
                view.startsWith('pages/') &&
                node.tag === 'div' &&
                node ===
                    templateRoot.children.find((child) => child.tag === 'div')
            ) {
                for (const required of [
                    'w-full',
                    'min-w-0',
                    '[overflow-wrap:anywhere]',
                ]) {
                    if (!classes.split(/\s+/).includes(required)) {
                        results.push(`${view}: page root requires ${required}`);
                    }
                }
            }
        }
        for (const child of node.children ?? []) inspect(child, inOverlay);
    }
    const templateRoot = baseParse(template.content);
    inspect(templateRoot);
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
