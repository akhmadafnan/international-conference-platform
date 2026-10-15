<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Layers, LayoutGrid } from '@lucide/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();
const { t } = useI18n();

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
          ]
        : []),
]);
</script>

<template>
    <Sidebar
        :side="sidebarSide"
        collapsible="icon"
        :variant="page.component.startsWith('admin/') ? 'sidebar' : 'inset'"
    >
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
