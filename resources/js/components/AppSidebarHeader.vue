<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { PhCaretRight, PhSidebarSimple } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import AppearanceToggle from '@/components/AppearanceToggle.vue';
import LocaleSwitcher from '@/components/LocaleSwitcher.vue';
import { Button } from '@/components/ui/button';
import { SidebarTrigger, useSidebar } from '@/components/ui/sidebar';
import type { BreadcrumbItem } from '@/types';

const props = withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const { t } = useI18n();
const { toggleSidebar } = useSidebar();
const isAdmin = computed(() => page.component.startsWith('admin/'));
const adminBreadcrumbs = computed<BreadcrumbItem[]>(() => {
    if (props.breadcrumbs.length > 0) return props.breadcrumbs;

    const items: BreadcrumbItem[] = [
        { title: t('admin.dashboard.title'), href: '/admin/dashboard' },
    ];
    const path = page.url.split('?')[0];
    const section = path.startsWith('/admin/series')
        ? 'series'
        : path.startsWith('/admin/editions')
          ? 'editions'
          : null;
    if (section) {
        items.push({
            title: t(`admin.${section}.title`),
            href: `/admin/${section}`,
        });
        if (path.endsWith('/create')) {
            items.push({ title: t(`admin.${section}.create`), href: path });
        }
    }
    return items;
});
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
        :class="{ 'admin-header': isAdmin }"
    >
        <div
            :class="
                isAdmin
                    ? 'flex min-w-0 flex-1 items-center gap-3'
                    : 'flex items-center gap-2'
            "
        >
            <Button
                v-if="isAdmin"
                variant="ghost"
                size="icon-sm"
                type="button"
                :aria-label="t('admin.shell.toggleSidebar')"
                @click="toggleSidebar"
            >
                <PhSidebarSimple
                    class="size-4 rtl:-scale-x-100"
                    aria-hidden="true"
                />
            </Button>
            <SidebarTrigger v-else class="-ms-1" />

            <nav
                v-if="isAdmin"
                :aria-label="t('admin.shell.breadcrumb')"
                class="min-w-0 border-s border-border ps-3"
            >
                <ol
                    class="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-xs"
                >
                    <li
                        v-for="(item, index) in adminBreadcrumbs"
                        :key="index"
                        class="flex min-w-0 items-center gap-1.5"
                    >
                        <PhCaretRight
                            v-if="index > 0"
                            class="size-3 shrink-0 text-muted-foreground rtl:-scale-x-100"
                            aria-hidden="true"
                        />
                        <span
                            v-if="index === adminBreadcrumbs.length - 1"
                            aria-current="page"
                            class="break-words"
                            >{{ item.title }}</span
                        >
                        <Link
                            v-else
                            :href="item.href"
                            class="break-words text-muted-foreground hover:text-foreground"
                            >{{ item.title }}</Link
                        >
                    </li>
                </ol>
            </nav>

            <template v-else-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <div class="ms-auto flex items-center gap-1">
            <AppearanceToggle :admin-pilot="isAdmin" />
            <LocaleSwitcher :admin-pilot="isAdmin" />
        </div>
    </header>
</template>
