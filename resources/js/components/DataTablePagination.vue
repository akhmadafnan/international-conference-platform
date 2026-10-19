<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import type { PaginationMeta } from '@/types/pagination';

const props = defineProps<{ pagination: PaginationMeta }>();
const { t, locale } = useI18n();
const formatNumber = (value: number) =>
    new Intl.NumberFormat(locale.value).format(value);
const controls = computed(() => [
    { key: 'first', url: props.pagination.firstUrl },
    { key: 'previous', url: props.pagination.previousUrl },
    ...props.pagination.pages.map((item, index) => ({
        key: `page-${index}`,
        url: item.url,
        page: item.page,
    })),
    { key: 'next', url: props.pagination.nextUrl },
    { key: 'last', url: props.pagination.lastUrl },
]);
const controlClass =
    'inline-flex min-h-9 min-w-9 items-center justify-center rounded-md border px-2.5 py-1.5 text-sm font-medium leading-5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background';
</script>

<template>
    <div
        v-if="pagination.total > 0"
        class="mt-4 flex flex-col gap-3 border-t border-border pt-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <p
            role="status"
            class="text-sm leading-6 text-muted-foreground tabular-nums"
        >
            {{
                t('dataTable.showing', {
                    from: formatNumber(pagination.from ?? 0),
                    to: formatNumber(pagination.to ?? 0),
                    total: formatNumber(pagination.total),
                })
            }}
        </p>
        <nav :aria-label="t('dataTable.pagination')" class="min-w-0">
            <ul class="flex flex-wrap items-center gap-1">
                <li v-for="control in controls" :key="control.key">
                    <span
                        v-if="'page' in control && control.page === null"
                        class="inline-flex min-h-9 min-w-6 items-center justify-center text-muted-foreground"
                    >
                        <span aria-hidden="true">…</span>
                        <span class="sr-only">{{
                            t('dataTable.ellipsis')
                        }}</span>
                    </span>
                    <Link
                        v-else-if="control.url"
                        :href="control.url"
                        :aria-label="
                            'page' in control
                                ? t('dataTable.page', {
                                      page: formatNumber(control.page!),
                                  })
                                : t(`dataTable.${control.key}`)
                        "
                        :aria-current="
                            'page' in control &&
                            control.page === pagination.currentPage
                                ? 'page'
                                : undefined
                        "
                        :class="[
                            controlClass,
                            'page' in control &&
                            control.page === pagination.currentPage
                                ? 'border-green-700 bg-green-700 text-white dark:border-green-500 dark:bg-green-700'
                                : 'border-border bg-background text-foreground hover:bg-accent hover:text-accent-foreground',
                        ]"
                    >
                        {{
                            'page' in control
                                ? formatNumber(control.page!)
                                : t(`dataTable.${control.key}`)
                        }}
                    </Link>
                    <button
                        v-else
                        type="button"
                        disabled
                        :class="[
                            controlClass,
                            'cursor-not-allowed border-border bg-muted text-muted-foreground opacity-60',
                        ]"
                    >
                        {{ t(`dataTable.${control.key}`) }}
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</template>
