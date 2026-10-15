<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

defineProps<{
    series: {
        data: {
            id: string;
            code: string;
            name: string;
            acronym: string | null;
            status: string;
        }[];
        currentPage: number;
        lastPage: number;
        previousUrl: string | null;
        nextUrl: string | null;
    };
    created: boolean;
}>();

const { t, te, locale } = useI18n();
const statusLabel = (status: string) => {
    const key = `admin.series.statuses.${status}`;
    return te(key) ? t(key) : t('admin.series.statuses.unknown');
};
const formatNumber = (value: number) =>
    new Intl.NumberFormat(locale.value).format(value);
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
                    <table class="w-full text-start text-sm">
                        <caption class="sr-only">
                            {{
                                t('admin.series.title')
                            }}
                        </caption>
                        <thead>
                            <tr class="border-b text-muted-foreground">
                                <th
                                    scope="col"
                                    class="p-3 text-start font-medium"
                                >
                                    {{ t('admin.series.code') }}
                                </th>
                                <th
                                    scope="col"
                                    class="p-3 text-start font-medium"
                                >
                                    {{ t('admin.series.name') }}
                                </th>
                                <th
                                    scope="col"
                                    class="p-3 text-start font-medium"
                                >
                                    {{ t('admin.series.acronym') }}
                                </th>
                                <th
                                    scope="col"
                                    class="p-3 text-start font-medium"
                                >
                                    {{ t('admin.series.status') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="item in series.data"
                                :key="item.id"
                                class="border-b last:border-0"
                            >
                                <th
                                    scope="row"
                                    class="p-3 text-start font-medium"
                                >
                                    <bdi>{{ item.code }}</bdi>
                                </th>
                                <td class="min-w-48 p-3">
                                    <bdi>{{ item.name }}</bdi>
                                </td>
                                <td class="p-3">
                                    <bdi>{{
                                        item.acronym ??
                                        t('admin.series.notProvided')
                                    }}</bdi>
                                </td>
                                <td class="p-3">
                                    <Badge variant="secondary">{{
                                        statusLabel(item.status)
                                    }}</Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav
                    v-if="series.lastPage > 1"
                    :aria-label="t('admin.series.pagination')"
                    class="mt-6 flex flex-wrap items-center justify-between gap-3"
                >
                    <Button
                        v-if="series.previousUrl"
                        as-child
                        variant="outline"
                    >
                        <Link :href="series.previousUrl">{{
                            t('admin.series.previous')
                        }}</Link>
                    </Button>
                    <span class="text-sm text-muted-foreground">
                        {{
                            t('admin.series.page', {
                                current: formatNumber(series.currentPage),
                                last: formatNumber(series.lastPage),
                            })
                        }}
                    </span>
                    <Button v-if="series.nextUrl" as-child variant="outline">
                        <Link :href="series.nextUrl">{{
                            t('admin.series.next')
                        }}</Link>
                    </Button>
                </nav>
            </CardContent>
        </Card>
    </div>
</template>
