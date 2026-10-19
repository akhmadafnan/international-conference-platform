<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, Plus } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import DataTablePagination from '@/components/DataTablePagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { PaginationMeta } from '@/types/pagination';

defineProps<{
    editions: PaginationMeta & {
        data: {
            id: string;
            code: string;
            number: number | null;
            hostName: string;
            seriesName: string | null;
            seriesCode: string | null;
            year: number;
            mode: string;
            timezone: string;
            startsAt: string;
            endsAt: string;
            status: string;
        }[];
    };
    created: boolean;
}>();
const { t, te, locale } = useI18n();
const number = (value: number) =>
    new Intl.NumberFormat(locale.value, { useGrouping: false }).format(value);
const date = (value: string, timezone: string) =>
    new Intl.DateTimeFormat(locale.value, {
        dateStyle: 'medium',
        timeStyle: 'short',
        timeZone: timezone,
    }).format(new Date(value));
const statusLabel = (status: string) =>
    te(`admin.dashboard.statuses.${status}`)
        ? t(`admin.dashboard.statuses.${status}`)
        : t('admin.dashboard.statuses.unknown');
const modeLabel = (mode: string) =>
    te(`admin.editions.modes.${mode}`)
        ? t(`admin.editions.modes.${mode}`)
        : t('admin.editions.unknownMode');
</script>

<template>
    <Head :title="t('admin.editions.title')" />
    <div class="flex w-full flex-col gap-6 p-4 md:p-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    {{ t('admin.editions.title') }}
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    {{ t('admin.editions.description') }}
                </p>
            </div>
            <Button
                as-child
                class="bg-green-700 text-white hover:bg-green-800 dark:bg-green-700 dark:hover:bg-green-800"
            >
                <Link href="/admin/editions/create"
                    ><Plus class="size-4" aria-hidden="true" />{{
                        t('admin.editions.create')
                    }}</Link
                >
            </Button>
        </header>
        <p
            v-if="created"
            role="status"
            class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200"
        >
            {{ t('admin.editions.created') }}
        </p>
        <Card>
            <CardContent class="pt-6">
                <div
                    v-if="editions.total === 0"
                    class="flex flex-col items-center gap-3 rounded-xl border border-dashed border-border px-6 py-14 text-center"
                >
                    <CalendarDays
                        class="size-10 text-green-700 dark:text-green-400"
                        aria-hidden="true"
                    />
                    <h2 class="text-lg font-semibold">
                        {{ t('admin.editions.emptyTitle') }}
                    </h2>
                    <p class="max-w-md text-sm leading-6 text-muted-foreground">
                        {{ t('admin.editions.emptyDescription') }}
                    </p>
                    <Button as-child variant="outline" class="mt-2"
                        ><Link href="/admin/editions/create">{{
                            t('admin.editions.create')
                        }}</Link></Button
                    >
                </div>
                <div
                    v-else
                    role="region"
                    :aria-label="t('admin.editions.title')"
                    tabindex="0"
                    class="overflow-x-auto rounded-md focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                >
                    <table
                        class="w-full border-collapse text-start text-sm leading-5"
                    >
                        <caption class="sr-only">
                            {{
                                t('admin.editions.title')
                            }}
                        </caption>
                        <thead class="border-b border-border bg-muted/50">
                            <tr>
                                <th
                                    v-for="column in [
                                        'identity',
                                        'series',
                                        'year',
                                        'mode',
                                        'dates',
                                        'status',
                                    ]"
                                    :key="column"
                                    scope="col"
                                    class="px-4 py-3 text-start font-semibold whitespace-nowrap"
                                >
                                    {{ t(`admin.editions.${column}`) }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="edition in editions.data"
                                :key="edition.id"
                                class="h-16 align-middle hover:bg-muted/40"
                            >
                                <th
                                    scope="row"
                                    class="min-w-44 px-4 py-4 text-start font-medium"
                                >
                                    <bdi>{{ edition.code }}</bdi>
                                    <span
                                        class="mt-1 block text-xs font-normal text-muted-foreground"
                                        ><bdi>{{ edition.hostName }}</bdi></span
                                    >
                                    <span
                                        v-if="edition.number !== null"
                                        class="mt-1 block text-xs font-normal text-muted-foreground"
                                        >{{
                                            t('admin.editions.number', {
                                                number: number(edition.number),
                                            })
                                        }}</span
                                    >
                                </th>
                                <td class="min-w-48 px-4 py-4">
                                    <bdi>{{
                                        edition.seriesName ??
                                        t('admin.dashboard.seriesUnavailable')
                                    }}</bdi
                                    ><span
                                        class="mt-1 block text-xs text-muted-foreground"
                                        ><bdi>{{
                                            edition.seriesCode
                                        }}</bdi></span
                                    >
                                </td>
                                <td
                                    class="px-4 py-4 font-statistic tabular-nums"
                                >
                                    {{ number(edition.year) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    {{ modeLabel(edition.mode) }}
                                </td>
                                <td class="min-w-56 px-4 py-4">
                                    <p>
                                        <span class="sr-only"
                                            >{{
                                                t(
                                                    'admin.editions.fields.starts_at',
                                                )
                                            }}: </span
                                        ><time :datetime="edition.startsAt">{{
                                            date(
                                                edition.startsAt,
                                                edition.timezone,
                                            )
                                        }}</time>
                                    </p>
                                    <p class="mt-1 text-muted-foreground">
                                        <span class="sr-only"
                                            >{{
                                                t(
                                                    'admin.editions.fields.ends_at',
                                                )
                                            }}: </span
                                        ><time :datetime="edition.endsAt">{{
                                            date(
                                                edition.endsAt,
                                                edition.timezone,
                                            )
                                        }}</time>
                                    </p>
                                    <bdi
                                        class="mt-1 block text-xs text-muted-foreground"
                                        >{{ edition.timezone }}</bdi
                                    >
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <Badge variant="secondary">{{
                                        statusLabel(edition.status)
                                    }}</Badge>
                                </td>
                            </tr>
                            <tr v-if="editions.data.length === 0">
                                <td
                                    colspan="6"
                                    class="px-4 py-8 text-center text-muted-foreground"
                                >
                                    {{ t('admin.editions.noPageResults') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <DataTablePagination :pagination="editions" />
            </CardContent>
        </Card>
    </div>
</template>
