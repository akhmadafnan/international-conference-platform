<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CalendarDays, Layers, LayoutGrid } from '@lucide/vue';
import { PhStack, PhX } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLogo from '@/components/AppLogo.vue';
import AdminNavigation from '@/components/AdminNavigation.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Button } from '@/components/ui/button';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const { t } = useI18n();
const isAdmin = computed(() => page.component.startsWith('admin/'));
const { isMobile, setOpenMobile } = useSidebar();

const sidebarSide = computed<'left' | 'right'>(() =>
    page.props.localization.direction === 'rtl' ? 'right' : 'left',
);

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: page.props.auth.canAccessAdmin
            ? t('admin.dashboard.title')
            : t('navigation.dashboard'),
        href: page.props.auth.canAccessAdmin ? '/admin/dashboard' : dashboard(),
        icon: LayoutGrid,
    },
    ...(page.props.auth.canAccessAdmin
        ? [
              {
                  title: t('admin.series.title'),
                  href: '/admin/series',
                  icon: Layers,
              },
              {
                  title: t('admin.editions.title'),
                  href: '/admin/editions',
                  icon: CalendarDays,
              },
          ]
        : []),
]);
</script>

<template>
    <Sidebar
        :side="sidebarSide"
        collapsible="icon"
        :variant="isAdmin ? 'sidebar' : 'inset'"
    >
        <SidebarHeader :class="{ 'flex-row items-center gap-1': isAdmin }">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link
                            :aria-label="
                                isAdmin
                                    ? `ICHES — ${t('admin.shell.workspace')}`
                                    : undefined
                            "
                            @click="isAdmin && setOpenMobile(false)"
                            :href="
                                page.props.auth.canAccessAdmin
                                    ? '/admin/dashboard'
                                    : dashboard()
                            "
                        >
                            <template v-if="isAdmin">
                                <span
                                    class="flex size-8 shrink-0 items-center justify-center bg-sidebar-primary text-sidebar-primary-foreground"
                                >
                                    <PhStack
                                        class="size-5"
                                        aria-hidden="true"
                                    />
                                </span>
                                <span
                                    class="grid min-w-0 flex-1 gap-0.5 text-start group-data-[collapsible=icon]:hidden"
                                >
                                    <span class="font-medium tracking-tight"
                                        >ICHES</span
                                    >
                                    <span
                                        class="text-xs text-muted-foreground"
                                        >{{ t('admin.shell.workspace') }}</span
                                    >
                                </span>
                            </template>
                            <AppLogo v-else />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <Button
                v-if="isAdmin && isMobile"
                type="button"
                variant="ghost"
                size="icon-sm"
                :aria-label="t('admin.shell.closeSidebar')"
                @click="setOpenMobile(false)"
            >
                <PhX class="size-4" aria-hidden="true" />
            </Button>
        </SidebarHeader>

        <SidebarContent>
            <AdminNavigation v-if="isAdmin && page.props.auth.canAccessAdmin" />
            <NavMain v-else :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
