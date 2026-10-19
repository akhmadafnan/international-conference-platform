<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowUpRight, CalendarDays, Layers, Plus } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

defineProps<{
    seriesCount: number;
    editionCount: number;
    recentEditions: {
        id: string;
        code: string;
        seriesName: string | null;
        year: number;
        status: string;
    }[];
}>();

const { t, te, locale } = useI18n();
const formatNumber = (value: number) =>
    new Intl.NumberFormat(locale.value, { useGrouping: false }).format(value);
const statusLabel = (status: string) => {
    const key = `admin.dashboard.statuses.${status}`;
    return te(key) ? t(key) : t('admin.dashboard.statuses.unknown');
};
</script>

<template>
    <Head :title="t('admin.dashboard.title')" />

    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-4 md:p-8">
        <header
            class="flex flex-wrap items-center justify-between gap-6 rounded-2xl border border-green-100 bg-white p-6 shadow-sm md:p-8 dark:border-green-900 dark:bg-card"
        >
            <div class="max-w-2xl">
                <p
                    class="text-xs font-semibold tracking-wide text-green-700 uppercase dark:text-green-400"
                >
                    {{ t('admin.dashboard.eyebrow') }}
                </p>
                <h1
                    class="mt-3 text-2xl font-semibold tracking-tight md:text-3xl"
                >
                    {{ t('admin.dashboard.title') }}
                </h1>
                <p class="mt-3 text-sm leading-6 text-muted-foreground">
                    {{ t('admin.dashboard.description') }}
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

        <section
            :aria-label="t('admin.dashboard.statistics')"
            class="grid gap-4 sm:grid-cols-2"
        >
            <Card class="border-green-100 dark:border-border">
                <CardHeader>
                    <CardTitle
                        class="flex items-center gap-3 text-sm font-medium text-muted-foreground"
                    >
                        <span
                            class="rounded-lg bg-green-50 p-2.5 text-green-700 dark:bg-green-950 dark:text-green-400"
                            ><Layers class="size-5" aria-hidden="true"
                        /></span>
                        {{ t('admin.dashboard.seriesCount') }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex items-end justify-between gap-4">
                    <p
                        class="font-statistic text-4xl font-semibold tracking-tight tabular-nums"
                    >
                        {{ formatNumber(seriesCount) }}
                    </p>
                    <Link
                        href="/admin/series"
                        class="inline-flex items-center gap-1 rounded text-sm font-medium text-green-700 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none dark:text-green-400"
                        >{{ t('admin.dashboard.manageSeries')
                        }}<ArrowUpRight
                            class="size-4 rtl:-scale-x-100"
                            aria-hidden="true"
                    /></Link>
                </CardContent>
            </Card>
            <Card class="border-green-100 dark:border-border">
                <CardHeader>
                    <CardTitle
                        class="flex items-center gap-3 text-sm font-medium text-muted-foreground"
                    >
                        <span
                            class="rounded-lg bg-green-50 p-2.5 text-green-700 dark:bg-green-950 dark:text-green-400"
                        >
                            <CalendarDays
                                class="size-5 text-green-700 dark:text-green-400"
                                aria-hidden="true"
                            />
                        </span>
                        {{ t('admin.dashboard.editionCount') }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="flex items-end justify-between gap-4">
                    <p
                        class="font-statistic text-4xl font-semibold tracking-tight tabular-nums"
                    >
                        {{ formatNumber(editionCount) }}
                    </p>
                    <Link
                        href="/admin/editions"
                        class="inline-flex items-center gap-1 rounded text-sm font-medium text-green-700 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none dark:text-green-400"
                        >{{ t('admin.dashboard.manageEditions')
                        }}<ArrowUpRight
                            class="size-4 rtl:-scale-x-100"
                            aria-hidden="true"
                    /></Link>
                </CardContent>
            </Card>
        </section>

        <div
            class="grid items-start gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(16rem,1fr)]"
        >
            <Card>
                <CardHeader>
                    <CardTitle>
                        <h2>{{ t('admin.dashboard.recentEditions') }}</h2>
                    </CardTitle>
                    <CardDescription>
                        {{ t('admin.dashboard.recentDescription') }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="recentEditions.length === 0"
                        class="flex flex-col items-center rounded-xl border border-dashed border-green-200 bg-green-50/40 px-6 py-14 text-center dark:border-green-900 dark:bg-green-950/20"
                    >
                        <span
                            class="mb-4 rounded-2xl bg-green-100 p-4 text-green-700 dark:bg-green-950 dark:text-green-400"
                            ><CalendarDays class="size-8" aria-hidden="true"
                        /></span>
                        <h3 class="font-medium">
                            {{ t('admin.dashboard.emptyTitle') }}
                        </h3>
                        <p
                            class="mt-2 max-w-sm text-sm leading-6 text-muted-foreground"
                        >
                            {{ t('admin.dashboard.emptyDescription') }}
                        </p>
                        <Button
                            as-child
                            class="mt-6 bg-green-700 text-white hover:bg-green-800 dark:bg-green-700 dark:hover:bg-green-800"
                            ><Link href="/admin/editions/create"
                                ><Plus class="size-4" aria-hidden="true" />{{
                                    t('admin.editions.create')
                                }}</Link
                            ></Button
                        >
                    </div>
                    <ul v-else class="divide-y divide-border">
                        <li
                            v-for="edition in recentEditions"
                            :key="edition.id"
                            class="grid gap-4 py-4 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-center"
                        >
                            <div class="min-w-0">
                                <h3 class="font-semibold">
                                    <bdi>{{ edition.code }}</bdi>
                                </h3>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    <bdi>{{
                                        edition.seriesName ??
                                        t('admin.dashboard.seriesUnavailable')
                                    }}</bdi>
                                </p>
                            </div>
                            <p class="text-sm tabular-nums">
                                <span class="me-2 text-muted-foreground">{{
                                    t('admin.dashboard.year')
                                }}</span>
                                <span class="font-statistic">{{
                                    formatNumber(edition.year)
                                }}</span>
                            </p>
                            <div>
                                <span class="sr-only"
                                    >{{ t('admin.dashboard.status') }}:
                                </span>
                                <Badge variant="secondary">{{
                                    statusLabel(edition.status)
                                }}</Badge>
                            </div>
                        </li>
                    </ul>
                </CardContent>
            </Card>
            <aside>
                <Card>
                    <CardHeader>
                        <CardTitle
                            ><h2>
                                {{ t('admin.dashboard.quickActions') }}
                            </h2></CardTitle
                        >
                        <CardDescription>{{
                            t('admin.dashboard.quickDescription')
                        }}</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-3">
                        <Link
                            href="/admin/series/create"
                            class="group flex items-start gap-3 rounded-xl border border-border p-4 hover:border-green-600 hover:bg-green-50 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none dark:hover:bg-green-950/40"
                        >
                            <Layers
                                class="mt-0.5 size-5 shrink-0 text-green-700 dark:text-green-400"
                                aria-hidden="true"
                            />
                            <div>
                                <h3 class="text-sm font-semibold">
                                    {{ t('admin.series.create') }}
                                </h3>
                                <p
                                    class="mt-1 text-xs leading-5 text-muted-foreground"
                                >
                                    {{ t('admin.dashboard.seriesActionHelp') }}
                                </p>
                            </div>
                        </Link>
                        <Link
                            href="/admin/editions/create"
                            class="group flex items-start gap-3 rounded-xl border border-border p-4 hover:border-green-600 hover:bg-green-50 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none dark:hover:bg-green-950/40"
                        >
                            <CalendarDays
                                class="mt-0.5 size-5 shrink-0 text-green-700 dark:text-green-400"
                                aria-hidden="true"
                            />
                            <div>
                                <h3 class="text-sm font-semibold">
                                    {{ t('admin.editions.create') }}
                                </h3>
                                <p
                                    class="mt-1 text-xs leading-5 text-muted-foreground"
                                >
                                    {{ t('admin.dashboard.editionActionHelp') }}
                                </p>
                            </div>
                        </Link>
                    </CardContent>
                </Card>
            </aside>
        </div>
    </div>
</template>
