<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { CalendarDays, Layers, LayoutGrid, FileText } from '@lucide/vue';
import { PhFiles, PhSquaresFour, PhStack, PhX } from '@phosphor-icons/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLogo from '@/components/AppLogo.vue';
import AdminNavigation from '@/components/AdminNavigation.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import RoleSwitchDialog from '@/components/RoleSwitchDialog.vue';
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
import { useWorkspaceLyra } from '@/composables/useWorkspaceLyra';

const page = usePage();
const { t } = useI18n();
const isAdmin = computed(() => page.component.startsWith('admin/'));
const { isLyra } = useWorkspaceLyra();
const isSuperAdminView = computed(
    () =>
        page.props.auth.canAccessAdmin &&
        page.props.auth.workspaceMode === 'superadmin',
);
const modeLanding = computed(() =>
    [
        'participant',
        'non_presenting_participant',
        'author',
        'co_author',
        'presenter',
    ].includes(page.props.auth.workspaceMode)
        ? '/dashboard'
        : page.props.auth.canAccessAdmin && !isSuperAdminView.value
          ? '/workspace'
          : '/dashboard',
);
const { isMobile, setOpenMobile } = useSidebar();

const sidebarSide = computed<'left' | 'right'>(() =>
    page.props.localization.direction === 'rtl' ? 'right' : 'left',
);

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: isSuperAdminView.value
            ? t('admin.dashboard.title')
            : page.props.auth.canAccessAdmin &&
                ![
                    'participant',
                    'non_presenting_participant',
                    'author',
                    'co_author',
                    'presenter',
                ].includes(page.props.auth.workspaceMode)
              ? t('workspace.title')
              : t('navigation.dashboard'),
        href: isSuperAdminView.value ? '/admin/dashboard' : modeLanding.value,
        icon: isLyra.value ? PhSquaresFour : LayoutGrid,
    },
    ...(!isSuperAdminView.value && modeLanding.value === '/dashboard'
        ? [
              {
                  title: t('submissionWizard.navigation'),
                  href: '/dashboard/submissions',
                  icon: isLyra.value ? PhFiles : FileText,
              },
          ]
        : []),
    ...(isSuperAdminView.value
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
        :variant="isLyra ? 'sidebar' : 'inset'"
    >
        <SidebarHeader :class="{ 'flex-row items-center gap-1': isLyra }">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link
                            :aria-label="
                                isAdmin
                                    ? `ICHES — ${t('admin.shell.workspace')}`
                                    : undefined
                            "
                            @click="isLyra && setOpenMobile(false)"
                            :href="
                                isSuperAdminView
                                    ? '/admin/dashboard'
                                    : modeLanding
                            "
                        >
                            <template v-if="isLyra">
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
                                        >{{
                                            isAdmin
                                                ? t('admin.shell.workspace')
                                                : t('navigation.dashboard')
                                        }}</span
                                    >
                                </span>
                            </template>
                            <AppLogo v-else />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <Button
                v-if="isLyra && isMobile"
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
            <RoleSwitchDialog v-if="page.props.auth.canAccessAdmin" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
