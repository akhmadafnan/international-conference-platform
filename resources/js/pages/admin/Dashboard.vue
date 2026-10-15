<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CalendarDays } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

defineProps<{
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

    <div class="flex w-full flex-col gap-6 p-4 md:p-8">
        <header>
            <p class="text-sm font-medium text-muted-foreground">
                {{ t('admin.dashboard.eyebrow') }}
            </p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight">
                {{ t('admin.dashboard.title') }}
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                {{ t('admin.dashboard.description') }}
            </p>
        </header>

        <Card class="w-full sm:max-w-sm">
            <CardHeader>
                <CardTitle class="flex items-center gap-2 text-base">
                    <CalendarDays
                        class="size-5 text-green-700 dark:text-green-400"
                        aria-hidden="true"
                    />
                    {{ t('admin.dashboard.editionCount') }}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <p class="text-3xl font-semibold tabular-nums">
                    {{ formatNumber(editionCount) }}
                </p>
            </CardContent>
        </Card>

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
                    class="rounded-lg border border-dashed p-8 text-center"
                >
                    <h3 class="font-medium">
                        {{ t('admin.dashboard.emptyTitle') }}
                    </h3>
                    <p class="mt-2 text-sm text-muted-foreground">
                        {{ t('admin.dashboard.emptyDescription') }}
                    </p>
                </div>
                <ul v-else class="divide-y">
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
                            {{ formatNumber(edition.year) }}
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
    </div>
</template>
