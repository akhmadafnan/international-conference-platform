<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DataTablePagination from '@/components/DataTablePagination.vue';
import DeleteConferenceRecordDialog from '@/components/DeleteConferenceRecordDialog.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { PaginationMeta } from '@/types/pagination';

defineProps<{
    series: PaginationMeta & {
        data: {
            id: string;
            code: string;
            name: string;
            acronym: string | null;
            status: string;
        }[];
    };
    created: boolean;
    deleted: boolean;
}>();

const { t, te } = useI18n();
const statusLabel = (status: string) => {
    const key = `admin.series.statuses.${status}`;
    return te(key) ? t(key) : t('admin.series.statuses.unknown');
};
</script>

<template>
    <Head :title="t('admin.series.title')" />

    <div class="flex w-full flex-col gap-6 p-4 md:p-8">
        <header class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">
                    {{ t('admin.series.title') }}
                </h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    {{ t('admin.series.description') }}
                </p>
            </div>
            <Button
                as-child
                class="bg-green-700 text-white hover:bg-green-800 dark:bg-primary dark:text-primary-foreground dark:hover:bg-primary/90"
            >
                <Link href="/admin/series/create">{{
                    t('admin.series.create')
                }}</Link>
            </Button>
        </header>

        <p
            v-if="created"
            role="status"
            class="rounded-lg border bg-card p-4 text-sm"
        >
            {{ t('admin.series.created') }}
        </p>

        <p
            v-if="deleted"
            role="status"
            class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200"
        >
            {{ t('admin.deletion.seriesDeleted') }}
        </p>

        <Card>
            <CardContent class="pt-6">
                <div
                    v-if="series.data.length === 0"
                    class="rounded-lg border border-dashed p-8 text-center"
                >
                    <h2 class="font-semibold">
                        {{ t('admin.series.emptyTitle') }}
                    </h2>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ t('admin.series.emptyDescription') }}
                    </p>
                </div>
                <div v-else class="overflow-x-auto">
                    <table
                        class="w-full border-collapse text-start text-sm leading-5"
                    >
                        <caption class="sr-only">
                            {{
                                t('admin.series.title')
                            }}
                        </caption>
                        <thead>
                            <tr
                                class="border-b border-border bg-muted/50 text-foreground"
                            >
                                <th
                                    scope="col"
                                    class="w-36 px-4 py-3 text-start font-semibold whitespace-nowrap"
                                >
                                    {{ t('admin.series.code') }}
                                </th>
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-start font-semibold"
                                >
                                    {{ t('admin.series.name') }}
                                </th>
                                <th
                                    scope="col"
                                    class="w-36 px-4 py-3 text-start font-semibold whitespace-nowrap"
                                >
                                    {{ t('admin.series.acronym') }}
                                </th>
                                <th
                                    scope="col"
                                    class="w-32 px-4 py-3 text-start font-semibold whitespace-nowrap"
                                >
                                    {{ t('admin.series.status') }}
                                </th>
                                <th
                                    scope="col"
                                    class="w-28 px-4 py-3 text-end font-semibold whitespace-nowrap"
                                >
                                    {{ t('admin.deletion.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in series.data"
                                :key="item.id"
                                class="h-14 border-b border-border align-middle transition-colors last:border-0 hover:bg-muted/40"
                            >
                                <th
                                    scope="row"
                                    class="px-4 py-3 text-start font-medium whitespace-nowrap"
                                >
                                    <bdi>{{ item.code }}</bdi>
                                </th>
                                <td class="min-w-48 px-4 py-3 text-start">
                                    <bdi>{{ item.name }}</bdi>
                                </td>
                                <td class="px-4 py-3 text-start">
                                    <bdi>{{
                                        item.acronym ??
                                        t('admin.series.notProvided')
                                    }}</bdi>
                                </td>
                                <td
                                    class="px-4 py-3 text-start whitespace-nowrap"
                                >
                                    <Badge variant="secondary">{{
                                        statusLabel(item.status)
                                    }}</Badge>
                                </td>
                                <td
                                    class="px-4 py-3 text-end whitespace-nowrap"
                                >
                                    <DeleteConferenceRecordDialog
                                        kind="series"
                                        :code="item.code"
                                        :identity="item.name"
                                        :url="`/admin/series/${item.id}`"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <DataTablePagination :pagination="series" />
            </CardContent>
        </Card>
    </div>
</template>
