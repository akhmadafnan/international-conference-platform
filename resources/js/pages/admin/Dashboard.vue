<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    PhArrowRight,
    PhCalendarBlank,
    PhPlus,
    PhStack,
} from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';

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

    <div
        class="admin-dashboard mx-auto flex w-full flex-col gap-5 p-4 lg:gap-6 lg:p-6"
    >
        <header class="flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-2xl">
                <p class="mb-1.5 text-xs text-muted-foreground">
                    {{ t('admin.dashboard.eyebrow') }}
                </p>
                <h1 class="text-xl leading-snug tracking-tight">
                    {{ t('admin.dashboard.title') }}
                </h1>
                <p class="mt-2 text-xs leading-relaxed text-muted-foreground">
                    {{ t('admin.dashboard.description') }}
                </p>
            </div>
            <Button as-child>
                <Link href="/admin/editions/create">
                    <PhPlus class="size-3.5" aria-hidden="true" />
                    {{ t('admin.editions.create') }}
                </Link>
            </Button>
        </header>

        <section
            :aria-label="t('admin.dashboard.statistics')"
            class="grid gap-4 sm:grid-cols-2"
        >
            <Card>
                <CardHeader
                    class="flex flex-row items-start justify-between gap-3"
                >
                    <h2 class="text-xs text-muted-foreground">
                        {{ t('admin.dashboard.seriesCount') }}
                    </h2>
                    <PhStack
                        class="size-4 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                </CardHeader>
                <CardContent>
                    <p class="admin-statistic tabular-nums">
                        <bdi>{{ formatNumber(seriesCount) }}</bdi>
                    </p>
                    <Link
                        href="/admin/series"
                        class="admin-text-link mt-4 text-xs"
                    >
                        {{ t('admin.dashboard.manageSeries') }}
                        <PhArrowRight
                            class="size-3.5 rtl:-scale-x-100"
                            aria-hidden="true"
                        />
                    </Link>
                </CardContent>
            </Card>
            <Card>
                <CardHeader
                    class="flex flex-row items-start justify-between gap-3"
                >
                    <h2 class="text-xs text-muted-foreground">
                        {{ t('admin.dashboard.editionCount') }}
                    </h2>
                    <PhCalendarBlank
                        class="size-4 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                </CardHeader>
                <CardContent>
                    <p class="admin-statistic tabular-nums">
                        <bdi>{{ formatNumber(editionCount) }}</bdi>
                    </p>
                    <Link
                        href="/admin/editions"
                        class="admin-text-link mt-4 text-xs"
                    >
                        {{ t('admin.dashboard.manageEditions') }}
                        <PhArrowRight
                            class="size-3.5 rtl:-scale-x-100"
                            aria-hidden="true"
                        />
                    </Link>
                </CardContent>
            </Card>
        </section>

        <div class="grid items-start gap-4 xl:grid-cols-[minmax(0,1fr)_18rem]">
            <section
                aria-labelledby="admin-recent-editions"
                class="min-w-0 border border-border bg-card text-card-foreground"
            >
                <div
                    class="flex flex-wrap items-start justify-between gap-3 p-4"
                >
                    <div>
                        <h2 id="admin-recent-editions" class="text-sm">
                            {{ t('admin.dashboard.recentEditions') }}
                        </h2>
                        <p
                            class="mt-1 text-xs leading-relaxed text-muted-foreground"
                        >
                            {{ t('admin.dashboard.recentDescription') }}
                        </p>
                    </div>
                    <Link
                        href="/admin/editions"
                        class="admin-text-link text-xs"
                    >
                        {{ t('admin.dashboard.manageEditions') }}
                        <PhArrowRight
                            class="size-3.5 rtl:-scale-x-100"
                            aria-hidden="true"
                        />
                    </Link>
                </div>

                <div
                    v-if="recentEditions.length === 0"
                    class="flex flex-col items-center gap-3 border-t border-dashed border-border px-6 py-12 text-center"
                >
                    <PhCalendarBlank
                        class="mb-1 size-7 text-muted-foreground"
                        aria-hidden="true"
                    />
                    <h3 class="text-sm">
                        {{ t('admin.dashboard.emptyTitle') }}
                    </h3>
                    <p
                        class="max-w-sm text-xs leading-relaxed text-muted-foreground"
                    >
                        {{ t('admin.dashboard.emptyDescription') }}
                    </p>
                    <Button as-child variant="outline" class="mt-2">
                        <Link href="/admin/editions/create">
                            <PhPlus class="size-3.5" aria-hidden="true" />
                            {{ t('admin.editions.create') }}
                        </Link>
                    </Button>
                </div>
                <div
                    v-else
                    role="region"
                    :aria-label="t('admin.dashboard.recentEditions')"
                    tabindex="0"
                    class="overflow-x-auto"
                >
                    <table class="admin-editions-table min-w-[30rem]">
                        <caption class="sr-only">
                            {{
                                t('admin.dashboard.recentDescription')
                            }}
                        </caption>
                        <thead>
                            <tr>
                                <th scope="col">
                                    {{ t('admin.editions.title') }}
                                </th>
                                <th scope="col">
                                    {{ t('admin.series.title') }}
                                </th>
                                <th scope="col">
                                    {{ t('admin.dashboard.year') }}
                                </th>
                                <th scope="col">
                                    {{ t('admin.dashboard.status') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="edition in recentEditions"
                                :key="edition.id"
                            >
                                <th scope="row">
                                    <bdi>{{ edition.code }}</bdi>
                                </th>
                                <td class="text-muted-foreground">
                                    <bdi>{{
                                        edition.seriesName ??
                                        t('admin.dashboard.seriesUnavailable')
                                    }}</bdi>
                                </td>
                                <td class="tabular-nums">
                                    <bdi>{{ formatNumber(edition.year) }}</bdi>
                                </td>
                                <td>
                                    <Badge variant="secondary">{{
                                        statusLabel(edition.status)
                                    }}</Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <aside
                aria-labelledby="admin-quick-actions"
                class="border border-border bg-card text-card-foreground"
            >
                <div class="p-4">
                    <h2 id="admin-quick-actions" class="text-sm">
                        {{ t('admin.dashboard.quickActions') }}
                    </h2>
                    <p
                        class="mt-1 text-xs leading-relaxed text-muted-foreground"
                    >
                        {{ t('admin.dashboard.quickDescription') }}
                    </p>
                </div>
                <div class="divide-y divide-border border-t border-border">
                    <Link href="/admin/series/create" class="admin-action">
                        <PhStack
                            class="mt-0.5 size-4 shrink-0 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <div class="min-w-0">
                            <h3 class="text-xs">
                                {{ t('admin.series.create') }}
                            </h3>
                            <p
                                class="mt-1 text-xs leading-relaxed text-muted-foreground"
                            >
                                {{ t('admin.dashboard.seriesActionHelp') }}
                            </p>
                        </div>
                        <PhArrowRight
                            class="ms-auto mt-0.5 size-3.5 shrink-0 text-muted-foreground rtl:-scale-x-100"
                            aria-hidden="true"
                        />
                    </Link>
                    <Link href="/admin/editions/create" class="admin-action">
                        <PhCalendarBlank
                            class="mt-0.5 size-4 shrink-0 text-muted-foreground"
                            aria-hidden="true"
                        />
                        <div class="min-w-0">
                            <h3 class="text-xs">
                                {{ t('admin.editions.create') }}
                            </h3>
                            <p
                                class="mt-1 text-xs leading-relaxed text-muted-foreground"
                            >
                                {{ t('admin.dashboard.editionActionHelp') }}
                            </p>
                        </div>
                        <PhArrowRight
                            class="ms-auto mt-0.5 size-3.5 shrink-0 text-muted-foreground rtl:-scale-x-100"
                            aria-hidden="true"
                        />
                    </Link>
                </div>
            </aside>
        </div>
    </div>
</template>
